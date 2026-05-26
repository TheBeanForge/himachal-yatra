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

$id     = (int)($_POST['id'] ?? 0);
$status = trim((string)($_POST['status'] ?? ''));

if (!$id || !in_array($status, ['pending', 'approved', 'rejected'], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid input']);
    exit;
}

$stmt = $conn->prepare('UPDATE reviews SET status = ? WHERE id = ?');
$stmt->bind_param('si', $status, $id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if ($affected > 0) {
    audit_log('review_moderate', "Review #{$id} → {$status}");
}
$conn->close();

echo json_encode(['ok' => $affected > 0]);
