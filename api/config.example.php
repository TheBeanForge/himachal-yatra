<?php
// Copy this file to api/config.php and edit DB credentials + agency WhatsApp number.
// config.php is git-ignored — never commit real credentials.

mysqli_report(MYSQLI_REPORT_OFF);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tourismsite');
define('DB_PORT', 3307);

define('AGENCY_WHATSAPP', '919876543210');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

function db_connect() {
    $connection = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($connection->connect_error) {
        return null;
    }
    $connection->set_charset('utf8mb4');
    return $connection;
}

$conn = db_connect();

function app_setting($key, $default = '') {
    global $conn;
    if (!$conn instanceof mysqli) return $default;
    $stmt = $conn->prepare('SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1');
    if (!$stmt) return $default;
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row['setting_value'] ?? $default;
}

function agency_whatsapp() {
    return preg_replace('/[^0-9]/', '', app_setting('agency_whatsapp', AGENCY_WHATSAPP));
}

function admin_csrf_token(): string {
    if (empty($_SESSION['admin_csrf'])) {
        $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf'];
}

function require_admin_csrf(): void {
    $sent    = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
    $session = $_SESSION['admin_csrf'] ?? '';
    if ($session === '' || !is_string($sent) || !hash_equals($session, $sent)) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'error' => 'CSRF token invalid. Reload and try again.']);
        exit;
    }
}

function audit_log(string $action, string $details = ''): void {
    global $conn;
    if (!($conn instanceof mysqli)) return;
    $user  = $_SESSION['admin_user'] ?? [];
    $uid   = isset($user['id']) ? (int)$user['id'] : null;
    $uname = $user['username'] ?? null;
    $ip    = $_SERVER['REMOTE_ADDR'] ?? null;
    $stmt  = @$conn->prepare('INSERT INTO audit_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)');
    if (!$stmt) return;
    $stmt->bind_param('issss', $uid, $uname, $action, $details, $ip);
    $stmt->execute();
    $stmt->close();
}
