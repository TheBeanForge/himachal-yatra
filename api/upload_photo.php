<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['admin_user'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorised']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../includes/vars.php';
require_admin_csrf();

$allowed_dests = ['manali','shimla','dharamshala','dalhousie','spiti','general'];
$destination   = trim($_POST['destination'] ?? '');
$caption       = substr(trim($_POST['caption'] ?? ''), 0, 200);

if (!in_array($destination, $allowed_dests, true)) {
    echo json_encode(['ok' => false, 'error' => 'Invalid destination']);
    exit;
}

$file = $_FILES['photo'] ?? null;
if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => false, 'error' => 'No file uploaded or upload error']);
    exit;
}

$max_size = 5 * 1024 * 1024; // 5 MB
if ($file['size'] > $max_size) {
    echo json_encode(['ok' => false, 'error' => 'File too large (max 5 MB)']);
    exit;
}

$allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);
if (!in_array($mime, $allowed_types, true)) {
    echo json_encode(['ok' => false, 'error' => 'Only JPG, PNG and WebP allowed']);
    exit;
}

$ext      = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime];
$filename = bin2hex(random_bytes(12)) . '.' . $ext;
$upload_dir = dirname(__DIR__) . '/uploads/photos/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
    echo json_encode(['ok' => false, 'error' => 'Failed to save file']);
    exit;
}

// Downscale + recompress to keep pages fast on mobile. Hero images need
// width, galleries don't — so we cap at 1920px wide. Silently skips if the
// GD extension isn't available (e.g. a host without it) and keeps the original.
if (!optimize_image_for_website($upload_dir . $filename, $mime, 400 * 1024, 1600, 1000)) {
    @unlink($upload_dir . $filename);
    echo json_encode(['ok' => false, 'error' => 'Could not optimize photo under 400 KB. Please upload a smaller JPG/WebP image.']);
    exit;
}

// Check photo limit (max 15 per destination)
try {
    $res = $conn->query("SELECT COUNT(*) as cnt FROM photos WHERE destination = '{$conn->real_escape_string($destination)}'");
    $row = $res->fetch_assoc();
    if ($row['cnt'] >= 15) {
        // Delete uploaded file and return error
        @unlink($upload_dir . $filename);
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Maximum 15 photos per destination. Please delete older photos first.']);
        exit;
    }
} catch (mysqli_sql_exception) {
    @unlink($upload_dir . $filename);
    echo json_encode(['ok' => false, 'error' => 'Database error']);
    exit;
}

$stmt = $conn->prepare('INSERT INTO photos (destination, caption, filename) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $destination, $caption, $filename);
$stmt->execute();
$id = $conn->insert_id;
$stmt->close();
audit_log('photo_upload', "Destination: {$destination}, File: {$filename}" . ($caption ? ", Caption: {$caption}" : ''));
$conn->close();

echo json_encode(['ok' => true, 'id' => $id, 'filename' => $filename]);
