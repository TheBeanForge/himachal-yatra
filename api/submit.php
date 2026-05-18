<?php
session_start();
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!empty($_POST['website'] ?? '')) {
    echo json_encode(['success' => true]);
    exit;
}

$postedToken = $_POST['csrf_token'] ?? '';
if (!empty($_SESSION['lead_form_token']) && !hash_equals($_SESSION['lead_form_token'], $postedToken)) {
    http_response_code(419);
    echo json_encode(['error' => 'Security token expired. Please refresh the page and try again.']);
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
    echo json_encode(['error' => 'Name and phone are required.']);
    exit;
}

$messageParts = [];
if ($pickup) $messageParts[] = "Pickup: $pickup";
if ($message) $messageParts[] = "Message: $message";
$message = implode("\n", $messageParts);

$allowed_packages = ['budget', 'classic', 'luxury'];
if ($package && !in_array($package, $allowed_packages)) $package = '';

$date_val = null;
if ($travel_date) {
    $d = DateTime::createFromFormat('Y-m-d', $travel_date);
    if ($d) $date_val = $d->format('Y-m-d');
}

if (!$conn instanceof mysqli) {
    http_response_code(503);
    echo json_encode(['error' => 'Database temporarily unavailable. Please continue on WhatsApp.']);
    exit;
}

$stmt = $conn->prepare(
    'INSERT INTO bookings (name, email, phone, destination, package, travel_date, pax, message)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Could not prepare enquiry.']);
    exit;
}
$stmt->bind_param('ssssssis', $name, $email, $phone, $destination, $package, $date_val, $pax, $message);

if ($stmt->execute()) {
    $wa = agency_whatsapp();
    $wa_msg = urlencode("Hi India Yatra Travels, I need a quote. Name: $name, Phone: $phone, Pickup: $pickup, Destination: $destination, Vehicle/Service: $package, Date: $travel_date");
    echo json_encode(['success' => true, 'wa' => "https://wa.me/$wa?text=$wa_msg"]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Could not save your enquiry. Please try again.']);
}
$stmt->close();
$conn->close();
