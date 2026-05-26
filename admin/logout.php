<?php
session_start();
if (!empty($_SESSION['admin_user'])) {
    require_once '../api/config.php';
    audit_log('logout', 'Logged out');
    $conn->close();
}
session_destroy();
header('Location: login.php');
exit;
