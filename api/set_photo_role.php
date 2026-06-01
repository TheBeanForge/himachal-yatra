<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['admin_user'])) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'Unauthorised']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); exit; }

require_once 'config.php';
require_admin_csrf();

$id   = (int)($_POST['id'] ?? 0);
$role = $_POST['role'] ?? '';

if (!$id || !in_array($role, ['hero','about','route','gallery'], true)) {
    http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Invalid input']); exit;
}

// Get destination of this photo
$stmt = $conn->prepare('SELECT destination FROM photos WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) { echo json_encode(['ok'=>false,'error'=>'Photo not found']); exit; }

$dest = $row['destination'];

$conn->begin_transaction();
try {
    // Unset the same role from all other photos in this destination
    if ($role !== 'gallery') {
        $u = $conn->prepare("UPDATE photos SET role = 'gallery' WHERE destination = ? AND role = ?");
        $u->bind_param('ss', $dest, $role);
        $u->execute();
        $u->close();
    }
    // Set the new role on this photo
    $u = $conn->prepare('UPDATE photos SET role = ? WHERE id = ?');
    $u->bind_param('si', $role, $id);
    $u->execute();
    $u->close();
    $conn->commit();
} catch (Throwable) {
    $conn->rollback();
    echo json_encode(['ok'=>false,'error'=>'Could not update role']); exit;
}

audit_log('photo_role', "Photo #{$id} set as {$role} for {$dest}");
echo json_encode(['ok'=>true]);
