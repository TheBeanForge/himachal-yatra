<?php
declare(strict_types=1);
// Count of unactioned leads for the admin topbar bell. Session-gated;
// polled every minute, so it must stay this cheap.
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-store');
if (empty($_SESSION['admin_user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'auth']);
    exit;
}
require_once __DIR__ . '/../includes/vars.php';
$count = 0;
if ($conn instanceof mysqli) {
    try {
        $count = (int)($conn->query("SELECT COUNT(*) c FROM booking_enquiries WHERE status='new'")->fetch_assoc()['c'] ?? 0);
    } catch (mysqli_sql_exception) {}
}
echo json_encode(['count' => $count]);
