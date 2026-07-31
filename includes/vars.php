<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

/*
 * Database credentials.
 *
 * The live server keeps its real credentials in includes/db-config.php, which
 * is git-ignored and therefore never committed. When that file is absent —
 * local XAMPP, or a fresh clone — we fall back to the local defaults below,
 * so the project runs immediately after checkout with no secrets in the repo.
 *
 * Deploying: create includes/db-config.php on the server once. Being ignored,
 * it survives every later git pull.
 */
if (is_file(__DIR__ . '/db-config.php')) {
    require __DIR__ . '/db-config.php';
} else {
    // Local XAMPP
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'tourismsite');
    define('DB_PORT', 3306);
}

define('AGENCY_WHATSAPP', '919876543210');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

if (!function_exists('h')) {
  function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
  }
}

function db_connect(): ?mysqli {
  try {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    $connection->set_charset('utf8mb4');
    return $connection;
  } catch (mysqli_sql_exception) {
    return null;
  }
}

$conn = db_connect();

// Admin pages can't do anything without the database — show a clear, friendly
// notice instead of a fatal "Call to a member function on null" further down.
// (Public pages and APIs degrade gracefully on their own and are unaffected.)
if (!($conn instanceof mysqli)
    && strpos(str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? ''), '/admin/') !== false) {
    http_response_code(503);
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Database unavailable</title>'
       . '<style>body{margin:0;min-height:100vh;display:grid;place-items:center;background:#070F1E;font-family:system-ui,sans-serif}'
       . '.box{max-width:440px;text-align:center;padding:44px 40px;background:#0D1930;border:1px solid rgba(255,255,255,.12);border-radius:20px}'
       . '.box .ic{font-size:40px}h1{font-size:19px;color:#F2F6FC;margin:14px 0 10px}p{font-size:14px;line-height:1.7;color:#C4D0E2;margin:0 0 6px}'
       . 'code{background:#13233F;padding:2px 8px;border-radius:6px;color:#EED9A6;font-size:12.5px}'
       . 'a{display:inline-block;margin-top:20px;padding:11px 24px;border-radius:999px;background:linear-gradient(135deg,#E4C685,#A9822F);color:#10151d;font-weight:700;font-size:13px;text-decoration:none}</style></head>'
       . '<body><div class="box"><div class="ic">&#128268;</div><h1>Database not reachable</h1>'
       . '<p>The admin panel could not connect to MySQL.</p>'
       . '<p>If this is your local machine, start <strong>MySQL</strong> from the XAMPP Control Panel. On hosting, check the credentials in <code>includes/vars.php</code>.</p>'
       . '<a href="javascript:location.reload()">Try again</a></div></body></html>';
    exit;
}

function app_setting($key, $default = '') {
  global $conn;
  if (!$conn instanceof mysqli) return $default;
  $stmt = $conn->prepare('SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1');
  if (!$stmt) return $default;
  $stmt->bind_param('s', $key);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();
  $stmt->close();
  return $row['setting_value'] ?? $default;
}

function agency_whatsapp() {
  return preg_replace('/[^0-9]/', '', app_setting('agency_whatsapp', AGENCY_WHATSAPP));
}

function admin_csrf_token(): string {
  if (empty($_SESSION['admin_csrf'])) {
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['admin_csrf'];
}

function require_admin_csrf(): void {
  $sent    = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
  $session = $_SESSION['admin_csrf'] ?? '';
  if ($session === '' || !is_string($sent) || !hash_equals($session, $sent)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'CSRF token invalid. Reload and try again.']);
    exit;
  }
}

function admin_theme(): string {
  $normalize = fn($t) => in_array($t, ['light', 'pine', 'sky'], true) ? $t : 'dark';
  if (!empty($_SESSION['admin_theme'])) return $normalize($_SESSION['admin_theme']);
  global $conn;
  if (!($conn instanceof mysqli)) $conn = db_connect();
  try { $conn->ping(); } catch (Throwable) { $conn = db_connect(); }
  $t = $normalize(app_setting('admin_theme', 'dark'));
  $_SESSION['admin_theme'] = $t;
  return $t;
}

function audit_log(string $action, string $details = ''): void {
  global $conn;
  if (!($conn instanceof mysqli)) return;
  $user  = $_SESSION['admin_user'] ?? [];
  $uid   = isset($user['id']) ? (int)$user['id'] : null;
  $uname = $user['username'] ?? null;
  $ip    = $_SERVER['REMOTE_ADDR'] ?? null;
  $stmt  = @$conn->prepare('INSERT INTO audit_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)');
  if (!$stmt) return;
  $stmt->bind_param('issss', $uid, $uname, $action, $details, $ip);
  $stmt->execute();
  $stmt->close();
}

const PHOTO_DESTS    = ['manali','shimla','dharamshala','dalhousie','spiti','general'];
const PHOTO_SLOT_CAP = ['home_hero' => 5];

/*
 * Which season the site is dressed in. One adaptive experience, not four
 * separate sites: this resolves to a single token that CSS reads off
 * <html data-season>, exactly as data-theme already works.
 *
 * The month bands match the ones the destination pages already publish in
 * their "Best Time to Visit" grids, so the site never contradicts its own
 * travel advice.
 *
 * Resolution order:
 *   1. ?season= — for QA and campaign landing pages
 *   2. visitor's date (server month)
 * Destination-aware overrides (Spiti reading winter-closed in January
 * regardless of date) come with the destination templating work.
 */
const SEASONS = ['summer', 'monsoon', 'autumn', 'winter'];

function current_season(?int $month = null): string {
    $q = strtolower(trim($_GET['season'] ?? ''));
    if (in_array($q, SEASONS, true)) return $q;

    $m = $month ?? (int) date('n');
    if ($m >= 3  && $m <= 6)  return 'summer';   // Mar–Jun · passes open, peak
    if ($m >= 7  && $m <= 9)  return 'monsoon';  // Jul–Sep · rain, landslide risk
    if ($m >= 10 && $m <= 11) return 'autumn';   // Oct–Nov · clear, few crowds
    return 'winter';                             // Dec–Feb · snow
}

// Extra single-photo homepage slots (general bucket) — admin-replaceable decorative images.
const PHOTO_HOME_EXTRA = ['home_story', 'home_banner', 'home_contact', 'tirthan_hero'];

// Per-destination fallback covers — used ONLY when no admin photo is uploaded yet,
// so destination pages never render a broken <img>.
const DEST_DEFAULT_IMG = [
  'manali'      => 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=1600&q=85',
  'shimla'      => 'https://images.unsplash.com/photo-1597074866923-dc0589150358?auto=format&fit=crop&w=1600&q=85',
  'dharamshala' => 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=1600&q=85',
  'dalhousie'   => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1600&q=85',
  'spiti'       => 'https://images.unsplash.com/photo-1504457047772-27faf1c00561?auto=format&fit=crop&w=1600&q=85',
];

function photo_slot_cap(string $slot): int {
  return PHOTO_SLOT_CAP[$slot] ?? 1;
}

function photo_slot_valid(string $slot): bool {
  if ($slot === 'home_hero') return true;
  if (in_array($slot, PHOTO_HOME_EXTRA, true)) return true;
  foreach (PHOTO_DESTS as $d) {
    if ($slot === "{$d}_hero" || $slot === "{$d}_about") return true;
  }
  return false;
}

// Resolve a destination hero/about image to a usable src, falling back to the
// destination's default cover so the page is never broken.
function dest_img_src(?array $photo, string $dest): string {
  if ($photo && !empty($photo['filename'])) return 'uploads/photos/' . $photo['filename'];
  return DEST_DEFAULT_IMG[$dest] ?? DEST_DEFAULT_IMG['manali'];
}

// First photo filename assigned to a slot, as a website src, or the given fallback.
function slot_img_src(?mysqli $conn, string $slot, string $fallback): string {
  $f = photos_in_slot($conn, $slot)[0]['filename'] ?? null;
  return $f ? 'uploads/photos/' . $f : $fallback;
}

function photos_in_slot(?mysqli $conn, string $slot): array {
  if (!($conn instanceof mysqli)) return [];
  try {
    $stmt = $conn->prepare(
      'SELECT p.id, p.filename, p.caption, p.destination
         FROM photo_assignments a
         JOIN photos p ON p.id = a.photo_id
        WHERE a.slot = ?
        ORDER BY a.position ASC, p.sort_order ASC, a.id ASC'
    );
    $stmt->bind_param('s', $slot);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
  } catch (mysqli_sql_exception) {
    return [];
  }
}

function photos_in_bucket(?mysqli $conn, string $bucket): array {
  if (!($conn instanceof mysqli)) return [];
  try {
    $stmt = $conn->prepare('SELECT id, filename, caption, destination FROM photos WHERE destination = ? ORDER BY sort_order ASC, id ASC');
    $stmt->bind_param('s', $bucket);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
  } catch (mysqli_sql_exception) {
    return [];
  }
}

function load_dest_photos(?mysqli $conn, string $dest): array {
  $bucket = photos_in_bucket($conn, $dest);
  $hero   = photos_in_slot($conn, "{$dest}_hero")[0]  ?? null;
  $about  = photos_in_slot($conn, "{$dest}_about")[0] ?? null;
  if (!$hero && count($bucket) > 0) $hero = $bucket[0];
  if (!$about && count($bucket) > 1) {
    foreach ($bucket as $b) {
      if (!$hero || $b['id'] !== $hero['id']) {
        $about = $b;
        break;
      }
    }
  }
  $exclude = [];
  if ($hero)  $exclude[$hero['id']]  = true;
  if ($about) $exclude[$about['id']] = true;
  $gallery = array_values(array_filter($bucket, fn($p) => !isset($exclude[$p['id']])));
  return [$hero, $about, $gallery];
}

if (!function_exists('resize_image_in_place')) {
  function resize_image_in_place(string $path, string $mime, int $maxW, int $maxH): void {
    if (!extension_loaded('gd')) return;
    $info = @getimagesize($path);
    if (!$info) return;
    [$w, $h] = $info;
    if ($w < 1 || $h < 1) return;
    $scale = min($maxW / $w, $maxH / $h, 1);
    if ($scale >= 1) return;
    $newW = max(1, (int)round($w * $scale));
    $newH = max(1, (int)round($h * $scale));
    switch ($mime) {
      case 'image/jpeg': $src = @imagecreatefromjpeg($path); break;
      case 'image/png':  $src = @imagecreatefrompng($path);  break;
      case 'image/webp': $src = @imagecreatefromwebp($path); break;
      default: return;
    }
    if (!$src) return;
    $dst = imagecreatetruecolor($newW, $newH);
    if ($mime === 'image/png' || $mime === 'image/webp') {
      imagealphablending($dst, false);
      imagesavealpha($dst, true);
    }
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
    switch ($mime) {
      case 'image/jpeg': imagejpeg($dst, $path, 82); break;
      case 'image/png':  imagepng($dst, $path, 6);   break;
      case 'image/webp': imagewebp($dst, $path, 82); break;
    }
    imagedestroy($src);
    imagedestroy($dst);
  }
}

if (!function_exists('optimize_image_for_website')) {
  function optimize_image_for_website(string $path, string $mime, int $maxBytes = 409600, int $maxW = 1600, int $maxH = 1200): bool {
    resize_image_in_place($path, $mime, $maxW, $maxH);
    clearstatcache(true, $path);
    if (is_file($path) && filesize($path) <= $maxBytes) return true;
    if (!extension_loaded('gd')) return false;

    $info = @getimagesize($path);
    if (!$info) return false;
    [$w, $h] = $info;

    switch ($mime) {
      case 'image/jpeg':
        $src = @imagecreatefromjpeg($path);
        break;
      case 'image/png':
        $src = @imagecreatefrompng($path);
        break;
      case 'image/webp':
        if (!function_exists('imagecreatefromwebp')) return false;
        $src = @imagecreatefromwebp($path);
        break;
      default:
        return false;
    }
    if (!$src) return false;

    $tmp = $path . '.tmp';
    $qualities = $mime === 'image/png' ? [6, 8, 9] : [82, 74, 66, 58, 50, 44];

    for ($pass = 0; $pass < 8; $pass++) {
      foreach ($qualities as $q) {
        if ($mime === 'image/jpeg') {
          imagejpeg($src, $tmp, $q);
        } elseif ($mime === 'image/webp') {
          imagewebp($src, $tmp, $q);
        } else {
          imagepng($src, $tmp, $q);
        }

        clearstatcache(true, $tmp);
        if (is_file($tmp) && filesize($tmp) <= $maxBytes) {
          copy($tmp, $path);
          @unlink($tmp);
          imagedestroy($src);
          return true;
        }
      }

      $newW = max(360, (int)round($w * 0.85));
      $newH = max(260, (int)round($h * 0.85));
      if ($newW === $w && $newH === $h) break;

      $dst = imagecreatetruecolor($newW, $newH);
      if ($mime === 'image/png' || $mime === 'image/webp') {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
      }
      imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
      imagedestroy($src);
      $src = $dst;
      $w = $newW;
      $h = $newH;
    }

    @unlink($tmp);
    imagedestroy($src);
    clearstatcache(true, $path);
    return is_file($path) && filesize($path) <= $maxBytes;
  }
}

// Fetch all public-facing contact/social settings in one query.
$_settings = [];
if ($conn instanceof mysqli) {
    try {
        $res = $conn->query(
            "SELECT setting_key, setting_value FROM settings WHERE setting_key IN (
                'agency_whatsapp','agency_phone','agency_phone2','agency_email','agency_location',
                'social_facebook','social_instagram','social_youtube'
            )"
        );
        if ($res) foreach ($res->fetch_all(MYSQLI_ASSOC) as $row) $_settings[$row['setting_key']] = $row['setting_value'];
    } catch (mysqli_sql_exception) {}
}
$whatsappNumber = preg_replace('/[^0-9]/', '', $_settings['agency_whatsapp'] ?? AGENCY_WHATSAPP) ?: AGENCY_WHATSAPP;
$phoneRaw       = $_settings['agency_phone'] ?? '+91 98765 43210';
$phoneDisplay   = $phoneRaw;
$phoneTel       = '+' . preg_replace('/[^0-9]/', '', $phoneRaw);

// Optional second phone number — shown only when set.
$phone2Raw      = trim($_settings['agency_phone2'] ?? '');
$phone2Display  = $phone2Raw;
$phone2Tel      = $phone2Raw !== '' ? '+' . preg_replace('/[^0-9]/', '', $phone2Raw) : '';

// Email, location and social links — admin-editable in Settings, with sensible fallbacks.
$bizEmail       = trim($_settings['agency_email'] ?? '') ?: (getenv('AGENCY_EMAIL') ?: 'info@himachalsafar.com');
$agencyLocation = trim($_settings['agency_location'] ?? '') ?: 'Bilaspur, Himachal Pradesh';
$socialLinks = array_filter([
    'facebook'  => trim($_settings['social_facebook']  ?? '') ?: (getenv('SOCIAL_FACEBOOK')  ?: ''),
    'instagram' => trim($_settings['social_instagram'] ?? '') ?: (getenv('SOCIAL_INSTAGRAM') ?: ''),
    'youtube'   => trim($_settings['social_youtube']   ?? '') ?: (getenv('SOCIAL_YOUTUBE')   ?: ''),
]);

$defaultMessage = rawurlencode('Hi Himachal Safar, I want a private Himachal trip quote.');
if (empty($_SESSION['lead_form_token'])) {
  $_SESSION['lead_form_token'] = bin2hex(random_bytes(16));
}
