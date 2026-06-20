<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['admin_user'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorised']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../includes/vars.php';
require_admin_csrf();

$id        = (int)($_POST['id'] ?? 0);
$direction = trim((string)($_POST['direction'] ?? ''));

if (!$id || !in_array($direction, ['up', 'down'], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid input']);
    exit;
}

// Fetch all photos for this destination ordered by current sort
$stmt = $conn->prepare('SELECT id, destination FROM photos WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['ok' => false, 'error' => 'Photo not found']);
    exit;
}

$dest = $row['destination'];

// Get all photos for this destination in current order
$stmt = $conn->prepare('SELECT id FROM photos WHERE destination = ? ORDER BY sort_order ASC, id ASC');
$stmt->bind_param('s', $dest);
$stmt->execute();
$all = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$ids  = array_column($all, 'id');
$idx  = array_search($id, $ids);

if ($idx === false) {
    echo json_encode(['ok' => false, 'error' => 'Photo not found in list']);
    exit;
}

// Find neighbour index
$swap = $direction === 'up' ? $idx - 1 : $idx + 1;

if ($swap < 0 || $swap >= count($ids)) {
    // Already at edge
    echo json_encode(['ok' => true, 'changed' => false]);
    exit;
}

// Swap the two IDs in the array
[$ids[$idx], $ids[$swap]] = [$ids[$swap], $ids[$idx]];

// Write back sequential sort_order 0,1,2,3... for ALL photos in destination
$conn->begin_transaction();
try {
    $u = $conn->prepare('UPDATE photos SET sort_order = ? WHERE id = ?');
    foreach ($ids as $order => $pid) {
        $u->bind_param('ii', $order, $pid);
        $u->execute();
    }
    $u->close();
    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode(['ok' => false, 'error' => 'Could not reorder']);
    $conn->close();
    exit;
}

audit_log('photo_reorder', "Photo #{$id} moved {$direction} in {$dest}");
$conn->close();
echo json_encode(['ok' => true, 'changed' => true]);
