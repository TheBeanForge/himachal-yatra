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

$username  = trim($_POST['username'] ?? '');
$full_name = trim($_POST['full_name'] ?? '');
$password  = $_POST['password'] ?? '';
$role      = trim($_POST['role'] ?? '');

if (!preg_match('/^[a-z0-9_]{3,30}$/', $username)) {
    echo json_encode(['ok' => false, 'error' => 'Username: 3–30 lowercase letters, numbers or underscore']);
    exit;
}
if (strlen($full_name) < 2 || strlen($full_name) > 100) {
    echo json_encode(['ok' => false, 'error' => 'Full name required (2–100 chars)']);
    exit;
}
if (strlen($password) < 6) {
    echo json_encode(['ok' => false, 'error' => 'Password must be at least 6 characters']);
    exit;
}
if (!in_array($role, ['admin', 'staff'], true)) {
    echo json_encode(['ok' => false, 'error' => 'Invalid role']);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare('INSERT INTO admin_users (username, full_name, password_hash, role) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $username, $full_name, $hash, $role);

if (!$stmt->execute()) {
    $err = $conn->error;
    $stmt->close();
    $msg = strpos($err, 'Duplicate') !== false ? "Username '{$username}' already exists" : 'Database error';
    echo json_encode(['ok' => false, 'error' => $msg]);
    $conn->close();
    exit;
}

$new_id = $conn->insert_id;
$stmt->close();
audit_log('user_add', "Created user '{$username}' with role '{$role}'");
$conn->close();

echo json_encode(['ok' => true, 'id' => $new_id]);
