<?php
declare(strict_types=1);
// Count of UNREAD leads for the sidebar bell badge — read/unread semantics:
// a lead is unread until the admin opens the leads inbox (enquiries.php),
// regardless of its workflow status. Session-gated; polled every minute,
// so it must stay this cheap.
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
        // is_read is added by enquiries.php's self-migration; until the admin
        // first opens that page the column may not exist yet → badge stays 0.
        $count = (int)($conn->query("SELECT COUNT(*) c FROM booking_enquiries WHERE is_read=0")->fetch_assoc()['c'] ?? 0);
    } catch (mysqli_sql_exception) {}
}
echo json_encode(['count' => $count]);
