<?php
/**
 * Pre-chat WhatsApp lead capture.
 * A direct wa.me link returns no identity, so before opening WhatsApp the
 * front-end collects name + phone and posts here. We save an identified lead
 * (source = 'whatsapp') into booking_enquiries so it surfaces in admin/enquiries.php
 * alongside calculator leads. Mirrors the guards in api/get_price_estimate.php.
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/vars.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

// Honeypot — bots fill hidden fields; pretend success, save nothing.
if (!empty($_POST['website'] ?? '')) { echo json_encode(['ok' => true]); exit; }

// CSRF (shared public lead token, set in includes/vars.php)
$posted = $_POST['csrf_token'] ?? '';
$sess   = $_SESSION['lead_form_token'] ?? '';
if ($sess === '' || !hash_equals($sess, $posted)) {
    http_response_code(419);
    echo json_encode(['ok' => false, 'error' => 'Security token expired. Please refresh and try again.']);
    exit;
}

// Rate limit — separate bucket from the calculator (bk_rl)
$rl = $_SESSION['wa_rl'] ?? ['c' => 0, 'w' => time()];
if (time() - $rl['w'] > 900) $rl = ['c' => 0, 'w' => time()];
if ($rl['c'] >= 15) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'error' => 'Too many requests. Please wait a few minutes.']);
    exit;
}

function cl(string $v, int $max = 255): string {
    return mb_substr(trim($v), 0, $max, 'UTF-8');
}

$name   = cl($_POST['customer_name'] ?? '', 100);
$mobile = preg_replace('/[^0-9+]/', '', $_POST['mobile'] ?? '');
$dest   = cl($_POST['dest'] ?? '', 40);

$allowed_sources = ['hero', 'contact', 'footer', 'float', 'mobilebar', 'other'];
$source = in_array($_POST['source'] ?? '', $allowed_sources, true) ? $_POST['source'] : 'other';

$errors = [];
if (mb_strlen($name) < 3)   $errors[] = 'Name must be at least 3 characters.';
if (strlen($mobile) < 10)   $errors[] = 'Enter a valid 10-digit mobile number.';
if ($errors) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => implode(' ', $errors)]);
    exit;
}

if (!$conn instanceof mysqli) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'Service unavailable. Please try again later.']);
    exit;
}

$notes = 'WhatsApp pre-chat from ' . $source . ($dest !== '' ? '; interested in ' . $dest : '');

$s = $conn->prepare(
    'INSERT INTO booking_enquiries (customer_name, mobile, source, status, notes)
     VALUES (?, ?, ?, "new", ?)'
);
$wa = 'whatsapp';
$s->bind_param('ssss', $name, $mobile, $wa, $notes);
$s->execute();
$id = $conn->insert_id;
$s->close();

$rl['c']++;
$_SESSION['wa_rl'] = $rl;
$conn->close();

echo json_encode(['ok' => true, 'id' => $id]);
