<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['admin_user'])) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'Unauthorised']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); exit; }

require_once __DIR__ . '/../includes/vars.php';
require_admin_csrf();

$slot = trim($_POST['slot'] ?? '');
if (!photo_slot_valid($slot)) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Invalid slot']); exit; }

// Which bucket (destination) does this slot's photo belong to?
$dest = $slot === 'home_hero' ? 'general' : preg_replace('/_(hero|about)$/', '', $slot);
if (!in_array($dest, PHOTO_DESTS, true)) { echo json_encode(['ok'=>false,'error'=>'Invalid destination']); exit; }

$cap = photo_slot_cap($slot);

// Multi-photo slot full? (single-photo slots just replace.)
if ($cap > 1) {
    $c = $conn->prepare('SELECT COUNT(*) n FROM photo_assignments WHERE slot=?');
    $c->bind_param('s', $slot); $c->execute();
    $n = (int)($c->get_result()->fetch_assoc()['n'] ?? 0); $c->close();
    if ($n >= $cap) { echo json_encode(['ok'=>false,'error'=>"That section is full (max {$cap}). Remove one first."]); exit; }
}

$file = $_FILES['photo'] ?? null;
if (!$file || $file['error'] !== UPLOAD_ERR_OK) { echo json_encode(['ok'=>false,'error'=>'No file uploaded or upload error']); exit; }
if ($file['size'] > 5 * 1024 * 1024) { echo json_encode(['ok'=>false,'error'=>'File too large (max 5 MB)']); exit; }

$mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
$ext  = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime] ?? null;
if (!$ext) { echo json_encode(['ok'=>false,'error'=>'Only JPG, PNG and WebP allowed']); exit; }

// 15-photos-per-bucket cap (matches the gallery uploader)
$res = $conn->query("SELECT COUNT(*) n FROM photos WHERE destination='" . $conn->real_escape_string($dest) . "'");
if (((int)$res->fetch_assoc()['n']) >= 15) { echo json_encode(['ok'=>false,'error'=>'This destination already has 15 photos. Delete some first.']); exit; }

$dir = dirname(__DIR__) . '/uploads/photos/';
if (!is_dir($dir)) mkdir($dir, 0755, true);
$filename = bin2hex(random_bytes(12)) . '.' . $ext;
if (!move_uploaded_file($file['tmp_name'], $dir . $filename)) { echo json_encode(['ok'=>false,'error'=>'Failed to save file']); exit; }

// Heroes/covers/slideshow need width; about can be smaller.
$maxW = str_ends_with($slot, '_about') ? 1200 : 1600;
if (!optimize_image_for_website($dir . $filename, $mime, 400 * 1024, $maxW, $maxW)) {
    @unlink($dir . $filename);
    echo json_encode(['ok'=>false,'error'=>'Could not optimize photo under 400 KB. Please upload a smaller JPG/WebP image.']);
    exit;
}

$caption = substr(trim($_POST['caption'] ?? ''), 0, 200);

$conn->begin_transaction();
try {
    $s = $conn->prepare('INSERT INTO photos (destination, caption, filename) VALUES (?,?,?)');
    $s->bind_param('sss', $dest, $caption, $filename); $s->execute();
    $pid = $conn->insert_id; $s->close();

    if ($cap === 1) {   // single-photo slot: clear the previous occupant
        $d = $conn->prepare('DELETE FROM photo_assignments WHERE slot=?');
        $d->bind_param('s', $slot); $d->execute(); $d->close();
    }
    $a = $conn->prepare('INSERT INTO photo_assignments (photo_id, slot, position) VALUES (?,?,0)');
    $a->bind_param('is', $pid, $slot); $a->execute(); $a->close();
    $conn->commit();
} catch (Throwable) {
    $conn->rollback();
    @unlink($dir . $filename);
    echo json_encode(['ok'=>false,'error'=>'Could not save assignment']); exit;
}

audit_log('photo_upload_slot', "Uploaded {$filename} → slot {$slot} ({$dest})");
echo json_encode(['ok'=>true, 'filename'=>$filename, 'slot'=>$slot]);
