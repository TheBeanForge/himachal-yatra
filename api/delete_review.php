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

$id = (int)($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['ok' => false, 'error' => 'Invalid ID']);
    exit;
}

// Look up the photo file first so we can clean it up
$stmt = $conn->prepare('SELECT photo FROM reviews WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['ok' => false, 'error' => 'Review not found']);
    exit;
}

if (!empty($row['photo'])) {
    $file = dirname(__DIR__) . '/uploads/reviews/' . $row['photo'];
    if (is_file($file)) @unlink($file);
}

$stmt = $conn->prepare('DELETE FROM reviews WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();
audit_log('review_delete', "Review #{$id} deleted");
$conn->close();

echo json_encode(['ok' => true]);
