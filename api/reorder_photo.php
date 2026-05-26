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

require_once 'config.php';
require_admin_csrf();

$id        = (int)($_POST['id'] ?? 0);
$direction = trim((string)($_POST['direction'] ?? ''));

if (!$id || !in_array($direction, ['up', 'down'], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid input']);
    exit;
}

// Fetch the target row
$stmt = $conn->prepare('SELECT id, destination, sort_order, uploaded_at FROM photos WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['ok' => false, 'error' => 'Photo not found']);
    exit;
}

$dest = $row['destination'];

// The gallery sort is `ORDER BY sort_order ASC, uploaded_at ASC` — so the "neighbour"
// is the photo immediately before/after in that ordering within the same destination.
$cmp = $direction === 'up'
    ? '(sort_order < ? OR (sort_order = ? AND uploaded_at < ?))'
    : '(sort_order > ? OR (sort_order = ? AND uploaded_at > ?))';
$ord = $direction === 'up' ? 'DESC' : 'ASC';

$sql = "SELECT id, sort_order FROM photos
        WHERE destination = ? AND $cmp
        ORDER BY sort_order $ord, uploaded_at $ord
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('siis', $dest, $row['sort_order'], $row['sort_order'], $row['uploaded_at']);
$stmt->execute();
$neighbour = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$neighbour) {
    // Already at the edge — no-op, not an error.
    $conn->close();
    echo json_encode(['ok' => true, 'changed' => false]);
    exit;
}

// If both have the same sort_order, bump the target to neighbour ± 1 so the swap takes effect
// against the secondary `uploaded_at ASC` sort.
$a = (int)$row['sort_order'];
$b = (int)$neighbour['sort_order'];
if ($a === $b) {
    $b = $direction === 'up' ? max(0, $a - 1) : $a + 1;
}

$conn->begin_transaction();
try {
    $u = $conn->prepare('UPDATE photos SET sort_order = ? WHERE id = ?');
    $u->bind_param('ii', $b, $row['id']);
    $u->execute();
    $u->bind_param('ii', $a, $neighbour['id']);
    $u->execute();
    $u->close();
    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode(['ok' => false, 'error' => 'Could not reorder']);
    $conn->close();
    exit;
}

audit_log('photo_reorder', "Photo #{$row['id']} moved $direction");
$conn->close();
echo json_encode(['ok' => true, 'changed' => true]);
