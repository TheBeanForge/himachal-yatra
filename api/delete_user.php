<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');

if (($_SESSION['admin_user']['role'] ?? '') !== 'superadmin') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Forbidden']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../includes/vars.php';
require_admin_csrf();

$id      = (int)($_POST['id'] ?? 0);
$self_id = (int)($_SESSION['admin_user']['id'] ?? 0);

if (!$id) { echo json_encode(['ok' => false, 'error' => 'Invalid ID']); exit; }
if ($id === $self_id) {
    echo json_encode(['ok' => false, 'error' => 'Cannot delete your own account']); exit;
}

$stmt = $conn->prepare('SELECT username, role FROM admin_users WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$target = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$target) { echo json_encode(['ok' => false, 'error' => 'User not found']); exit; }
if ($target['role'] === 'superadmin') {
    echo json_encode(['ok' => false, 'error' => 'Cannot delete the superadmin account']); exit;
}

$uname = $target['username'];
$stmt  = $conn->prepare('DELETE FROM admin_users WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();
audit_log('user_delete', "Deleted user '{$uname}'");
$conn->close();

echo json_encode(['ok' => true]);
