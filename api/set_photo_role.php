<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['admin_user'])) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'Unauthorised']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); exit; }

require_once 'config.php';
require_admin_csrf();

$id    = (int)($_POST['id'] ?? 0);
$field = $_POST['field'] ?? '';   // 'is_hero' or 'is_about'
$val   = (int)(bool)($_POST['value'] ?? 0);  // 0 or 1

if (!$id || !in_array($field, ['is_hero','is_about'], true)) {
    http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Invalid input']); exit;
}

$stmt = $conn->prepare('SELECT destination FROM photos WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$row) { echo json_encode(['ok'=>false,'error'=>'Photo not found']); exit; }

$dest = $row['destination'];

$conn->begin_transaction();
try {
    // If turning ON, unset the same flag from all others in this destination
    if ($val === 1) {
        $u = $conn->prepare("UPDATE photos SET {$field} = 0 WHERE destination = ? AND id != ?");
        $u->bind_param('si', $dest, $id);
        $u->execute(); $u->close();
    }
    $u = $conn->prepare("UPDATE photos SET {$field} = ? WHERE id = ?");
    $u->bind_param('ii', $val, $id);
    $u->execute(); $u->close();
    $conn->commit();
} catch (Throwable) {
    $conn->rollback();
    echo json_encode(['ok'=>false,'error'=>'Could not update']); exit;
}

audit_log('photo_role', "Photo #{$id} {$field}=" . ($val?'on':'off') . " for {$dest}");
echo json_encode(['ok'=>true]);
