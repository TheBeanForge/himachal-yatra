<?php
// Persists the admin colour theme, then returns to the page you came from.
// Used by the segmented theme control in the admin top bar (every page).
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../includes/vars.php';

if (empty($_SESSION['admin_user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin/login.php'); exit;
}
require_admin_csrf();

$theme = in_array($_POST['admin_theme'] ?? '', ['light', 'pine'], true) ? $_POST['admin_theme'] : 'dark';

if ($conn instanceof mysqli) {
    $stmt = $conn->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    $k = 'admin_theme';
    $stmt->bind_param('ss', $k, $theme); $stmt->execute(); $stmt->close();
}
$_SESSION['admin_theme'] = $theme;
audit_log('settings_theme', "Admin theme set to {$theme}");   // uses $conn while still open

// Return to the referring admin page (same-site path only — no open redirect).
$back = parse_url($_SERVER['HTTP_REFERER'] ?? '', PHP_URL_PATH) ?: '/admin/enquiries.php';
if (strpos($back, '/admin/') === false) $back = '/admin/enquiries.php';
header('Location: ' . $back);
