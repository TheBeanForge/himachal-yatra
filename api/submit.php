<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!empty($_POST['website'] ?? '')) {
    // Honeypot tripped — pretend everything is fine.
    echo json_encode(['ok' => true]);
    exit;
}

$postedToken    = $_POST['csrf_token'] ?? '';
$sessionToken   = $_SESSION['lead_form_token'] ?? '';
if ($sessionToken === '' || !hash_equals($sessionToken, $postedToken)) {
    http_response_code(419);
    echo json_encode(['ok' => false, 'error' => 'Security token expired. Please refresh the page and try again.']);
    exit;
}

function clean_text($value, $max = 500) {
    $value = trim((string)$value);
    $value = preg_replace('/\s+/', ' ', $value);
    if (function_exists('mb_substr')) {
        return mb_substr($value, 0, $max, 'UTF-8');
    }
    return substr($value, 0, $max);
}

$name        = clean_text($_POST['name'] ?? '', 100);
$email       = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ? trim($_POST['email']) : '';
$phone       = preg_replace('/[^0-9+]/', '', $_POST['phone'] ?? '');
$pickup      = clean_text($_POST['pickup'] ?? '', 120);
$destination = clean_text($_POST['destination'] ?? '', 120);
$package     = clean_text($_POST['package'] ?? '', 30);
$travel_date = trim($_POST['travel_date'] ?? '');
$pax         = max(1, min(60, (int)($_POST['pax'] ?? 1)));
$message     = clean_text($_POST['message'] ?? '', 900);

if (!$name || strlen($phone) < 8) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Name and phone are required.']);
    exit;
}

// $pickup is stored in its own column; keep $message as the user's note only

$allowed_packages = ['budget', 'classic', 'luxury'];
if ($package && !in_array($package, $allowed_packages)) $package = '';

$date_val = null;
if ($travel_date) {
    $d = DateTime::createFromFormat('Y-m-d', $travel_date);
    if ($d) $date_val = $d->format('Y-m-d');
}

// Rate limit: max 5 successful submissions per 15 minutes per session
$rl     = $_SESSION['submit_rl'] ?? ['count' => 0, 'window_start' => time()];
$window = 15 * 60;
if (time() - $rl['window_start'] > $window) {
    $rl = ['count' => 0, 'window_start' => time()];
}
if ($rl['count'] >= 5) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'error' => 'Too many requests. Please wait a few minutes and try again.']);
    exit;
}

if (!$conn instanceof mysqli) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'Database temporarily unavailable. Please continue on WhatsApp.']);
    exit;
}

$stmt = $conn->prepare(
    'INSERT INTO bookings (name, email, phone, pickup, destination, package, travel_date, pax, message)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Could not prepare enquiry.']);
    exit;
}
$stmt->bind_param('sssssssis', $name, $email, $phone, $pickup, $destination, $package, $date_val, $pax, $message);

if ($stmt->execute()) {
    $rl['count']++;
    $_SESSION['submit_rl'] = $rl;
    $wa = agency_whatsapp();
    $wa_msg = urlencode("Hi Himachal Yatra Travels! I just submitted an enquiry. Name: $name, Phone: $phone, Pickup: $pickup, Destination: $destination, Service: $package, Date: $travel_date");
    echo json_encode(['ok' => true, 'wa' => "https://wa.me/$wa?text=$wa_msg"]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Could not save your enquiry. Please try again.']);
}
$stmt->close();
$conn->close();
