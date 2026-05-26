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

require_once 'config.php';
require_admin_csrf();

$id     = (int)($_POST['id'] ?? 0);
$action = trim($_POST['action'] ?? '');

if (!$id) { echo json_encode(['ok' => false, 'error' => 'Invalid ID']); exit; }

$stmt = $conn->prepare('SELECT username, role FROM admin_users WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$target = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$target) { echo json_encode(['ok' => false, 'error' => 'User not found']); exit; }
if ($target['role'] === 'superadmin') {
    echo json_encode(['ok' => false, 'error' => 'Cannot edit the superadmin account']);
    exit;
}

if ($action === 'role') {
    $role = trim($_POST['role'] ?? '');
    if (!in_array($role, ['admin', 'staff'], true)) {
        echo json_encode(['ok' => false, 'error' => 'Invalid role']); exit;
    }
    $stmt = $conn->prepare('UPDATE admin_users SET role = ? WHERE id = ?');
    $stmt->bind_param('si', $role, $id);
    $stmt->execute();
    $stmt->close();
    audit_log('user_edit', "Changed '{$target['username']}' role to '{$role}'");

} elseif ($action === 'password') {
    $password = $_POST['password'] ?? '';
    if (strlen($password) < 6) {
        echo json_encode(['ok' => false, 'error' => 'Password must be at least 6 characters']); exit;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?');
    $stmt->bind_param('si', $hash, $id);
    $stmt->execute();
    $stmt->close();
    audit_log('user_edit', "Reset password for '{$target['username']}'");

} else {
    echo json_encode(['ok' => false, 'error' => 'Invalid action']); exit;
}

$conn->close();
echo json_encode(['ok' => true]);
