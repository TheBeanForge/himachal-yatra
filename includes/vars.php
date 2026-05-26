<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../api/config.php';

// Fetch both settings in one query instead of two separate calls.
$_settings = [];
if ($conn instanceof mysqli) {
    try {
        $res = $conn->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('agency_whatsapp','agency_phone')");
        if ($res) foreach ($res->fetch_all(MYSQLI_ASSOC) as $row) $_settings[$row['setting_key']] = $row['setting_value'];
    } catch (mysqli_sql_exception) {}
}
$whatsappNumber = preg_replace('/[^0-9]/', '', $_settings['agency_whatsapp'] ?? AGENCY_WHATSAPP) ?: AGENCY_WHATSAPP;
$phoneRaw       = $_settings['agency_phone'] ?? '+91 98765 43210';
$phoneDisplay   = $phoneRaw;
$phoneTel       = '+' . preg_replace('/[^0-9]/', '', $phoneRaw);
$defaultMessage = rawurlencode('Hi Himachal Yatra Travels, I want a private Himachal trip quote.');
if (empty($_SESSION['lead_form_token'])) {
  $_SESSION['lead_form_token'] = bin2hex(random_bytes(16));
}

if (!function_exists('h')) {
  function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
  }
}
