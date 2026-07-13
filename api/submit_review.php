<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
require_once __DIR__ . '/../includes/vars.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// A body bigger than PHP's post_max_size arrives with $_POST and $_FILES
// completely empty — without this check that surfaces as a baffling CSRF
// failure. Typical trigger: multi-MB phone-camera photos on Android.
if (empty($_POST) && empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    http_response_code(413);
    echo json_encode(['success' => false, 'error' => 'Your photo is too large for the server. Please pick a smaller photo, or submit without one.']);
    exit;
}

// Honeypot — silently accept and discard if filled (a bot field)
if (!empty($_POST['website'] ?? '')) {
    echo json_encode(['success' => true]);
    exit;
}

// Reuse the lead form's CSRF token — same per-session token covers both forms.
$postedToken  = $_POST['csrf_token'] ?? '';
$sessionToken = $_SESSION['lead_form_token'] ?? '';
if ($sessionToken === '' || !hash_equals($sessionToken, $postedToken)) {
    http_response_code(419);
    echo json_encode(['success' => false, 'error' => 'Security token expired. Please refresh the page and try again.']);
    exit;
}

$name   = trim((string)($_POST['name']   ?? ''));
$city   = trim((string)($_POST['city']   ?? ''));
$route  = trim((string)($_POST['route']  ?? ''));
$rating = (int)($_POST['rating'] ?? 0);
$text   = trim((string)($_POST['text']   ?? ''));

if ($name === '' || $text === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'Name and review text are required.']);
    exit;
}
if ($rating < 1 || $rating > 5) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'Please select a rating between 1 and 5 stars.']);
    exit;
}

// Clip to schema lengths
$name  = mb_substr($name,  0, 100, 'UTF-8');
$city  = mb_substr($city,  0,  80, 'UTF-8');
$route = mb_substr($route, 0, 120, 'UTF-8');
$text  = mb_substr($text,  0, 1500, 'UTF-8');

// Optional photo upload
$photo_filename = '';
$file = $_FILES['photo'] ?? null;
$fileErr = $file['error'] ?? UPLOAD_ERR_NO_FILE;
if (in_array($fileErr, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
    // Photo exceeded upload_max_filesize — tell the user instead of silently
    // dropping their photo (the review text alone would otherwise be saved).
    http_response_code(413);
    echo json_encode(['success' => false, 'error' => 'Your photo is too large for the server. Please pick a smaller photo, or submit without one.']);
    exit;
}
if ($file && $fileErr === UPLOAD_ERR_OK) {
    if ($file['size'] > 5 * 1024 * 1024) {
        http_response_code(413);
        echo json_encode(['success' => false, 'error' => 'Photo too large (max 5 MB).']);
        exit;
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime    = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime])) {
        http_response_code(415);
        echo json_encode(['success' => false, 'error' => 'Photo must be JPG, PNG or WebP.']);
        exit;
    }
    $dir = dirname(__DIR__) . '/uploads/reviews/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $photo_filename = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . $photo_filename)) {
        $photo_filename = '';   // Don't fail the whole submission for a photo glitch
    } elseif (!optimize_image_for_website($dir . $photo_filename, $mime, 400 * 1024, 900, 900)) {
        @unlink($dir . $photo_filename);
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'Could not optimize photo under 400 KB. Please upload a smaller JPG/WebP image.']);
        exit;
    }
}

if (!$conn instanceof mysqli) {
    http_response_code(503);
    echo json_encode(['success' => false, 'error' => 'Service temporarily unavailable. Please try again.']);
    exit;
}

$stmt = $conn->prepare(
    'INSERT INTO reviews (name, city, route, rating, review_text, photo, status)
     VALUES (?, ?, ?, ?, ?, ?, "pending")'
);
$stmt->bind_param('sssiss', $name, $city, $route, $rating, $text, $photo_filename);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Could not save your review. Please try again.']);
}
$stmt->close();
$conn->close();
