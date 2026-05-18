<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

$phoneDisplay    = '+91 98765 43210';
$phoneTel        = '+919876543210';
$whatsappNumber  = '919876543210';
$defaultMessage  = rawurlencode('Hi Himachal Yatra Travels, I want cab or tour package details.');
$_SESSION['lead_form_token'] = bin2hex(random_bytes(16));

if (!function_exists('h')) {
  function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
  }
}
