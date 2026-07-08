<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../includes/vars.php';

$role = $_SESSION['admin_user']['role'] ?? '';
if (!in_array($role, ['superadmin', 'admin'])) { header('Location: dashboard.php'); exit; }

$saved = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin_csrf();
    // Social URLs: keep only well-formed http(s) links, otherwise store empty (no dead links on the site).
    $clean_url = function ($v) {
        $v = trim($v ?? '');
        if ($v === '') return '';
        if (!preg_match('~^https?://~i', $v)) $v = 'https://' . $v;
        return filter_var($v, FILTER_VALIDATE_URL) ?: '';
    };
    $fields = [
        'agency_name'      => substr(trim($_POST['agency_name'] ?? ''), 0, 100),
        'agency_phone'     => substr(trim($_POST['agency_phone'] ?? ''), 0, 30),
        'agency_phone2'    => substr(trim($_POST['agency_phone2'] ?? ''), 0, 30),
        'agency_whatsapp'  => preg_replace('/[^0-9]/', '', $_POST['agency_whatsapp'] ?? ''),
        'agency_email'     => filter_var(trim($_POST['agency_email'] ?? ''), FILTER_VALIDATE_EMAIL) ?: '',
        'agency_location'  => substr(trim($_POST['agency_location'] ?? ''), 0, 150),
        'social_facebook'  => substr($clean_url($_POST['social_facebook']  ?? ''), 0, 255),
        'social_instagram' => substr($clean_url($_POST['social_instagram'] ?? ''), 0, 255),
        'social_youtube'   => substr($clean_url($_POST['social_youtube']   ?? ''), 0, 255),
        // Advance collected to confirm a booking — % of the estimated total (5–100).
        'advance_percent'  => (string)max(5, min(100, (int)($_POST['advance_percent'] ?? 25))),
    ];
    try {
        $stmt = $conn->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        foreach ($fields as $key => $value) { $stmt->bind_param('ss', $key, $value); $stmt->execute(); }
        $stmt->close();
        audit_log('settings_update', 'Agency settings updated');
        $saved = true;
    } catch (Throwable $e) {
        $error = 'Could not save settings.';
    }
}

$settings = [];
$res = $conn->query("SELECT setting_key, setting_value FROM settings");
if ($res) foreach ($res->fetch_all(MYSQLI_ASSOC) as $row) $settings[$row['setting_key']] = $row['setting_value'];
$conn->close();

$csrf = htmlspecialchars(admin_csrf_token());
$page_title = 'Settings';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<meta name="csrf-token" content="<?= $csrf ?>"/>
<title>Settings — Himachal Safar Admin</title>
<link rel="icon" href="../favicon.ico" sizes="32x32">
<link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="../assets/brand/mark-192.png">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Poppins:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
<link href="assets/admin.css?v=<?php echo @filemtime(__DIR__ . '/assets/admin.css'); ?>" rel="stylesheet"/>
<style>
.set-h1 { font-family:'Poppins',sans-serif; font-size:22px; font-weight:800; letter-spacing:-.01em; color:var(--ink); margin:0 0 3px; }
.set-sub { font-size:13.5px; color:var(--muted); margin:0; }
.set-section { font-family:'Poppins',sans-serif; font-size:12.5px; font-weight:800; letter-spacing:.04em; text-transform:uppercase; color:var(--muted); margin:0 0 18px; display:flex; align-items:center; gap:8px; }
.set-section i { color:var(--accent); }
.set-section.mt { margin-top:28px; padding-top:24px; border-top:1px solid var(--border); }
.set-grid { display:grid; grid-template-columns:1fr 1fr; gap:18px 20px; }
.set-field { display:flex; flex-direction:column; gap:7px; }
.set-field.full { grid-column:1 / -1; }
.set-field label { font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; }
.set-field label .hint { text-transform:none; letter-spacing:0; font-weight:400; }
.set-input { height:44px; padding:0 14px; width:100%; background:var(--surface-2); border:1.5px solid var(--border); border-radius:8px; color:var(--ink); font:500 14px 'Inter',sans-serif; outline:none; transition:border-color .18s, background .18s; }
.set-input::placeholder { color:var(--muted); }
.set-input:focus { border-color:var(--accent); background:var(--surface); }
@media(max-width:640px){ .set-grid{ grid-template-columns:1fr; } }
</style>
</head>
<body>
<div class="admin-wrap">
  <?php require_once 'partials/sidebar.php'; ?>
  <div class="admin-main">
    <?php require_once 'partials/topbar.php'; ?>
    <div class="admin-content">

      <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
          <h1 class="set-h1">Settings</h1>
          <p class="set-sub">Contact details, location and social links shown across your website.</p>
        </div>
      </div>

      <?php if ($saved): ?>
      <div class="alert alert-success alert-dismissible fade show py-2 mb-3">
        <i class="fas fa-circle-check me-1"></i> Settings saved successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php endif; ?>
      <?php if ($error): ?>
      <div class="alert alert-danger alert-dismissible fade show py-2 mb-3">
        <i class="fas fa-circle-exclamation me-1"></i> <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php endif; ?>

      <form method="post" class="admin-card" style="padding:26px 28px;">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

        <div class="set-section"><i class="fas fa-building"></i> Agency Information</div>
        <div class="set-grid">
          <div class="set-field full">
            <label>Agency Name</label>
            <input class="set-input" type="text" name="agency_name" value="<?= htmlspecialchars($settings['agency_name'] ?? '') ?>" placeholder="Himachal Safar">
          </div>
          <div class="set-field">
            <label>Phone Number 1</label>
            <input class="set-input" type="text" name="agency_phone" value="<?= htmlspecialchars($settings['agency_phone'] ?? '') ?>" placeholder="+91 98765 43210">
          </div>
          <div class="set-field">
            <label>Phone Number 2 <span class="hint">— optional</span></label>
            <input class="set-input" type="text" name="agency_phone2" value="<?= htmlspecialchars($settings['agency_phone2'] ?? '') ?>" placeholder="+91 91234 56789">
          </div>
          <div class="set-field">
            <label>WhatsApp Number <span class="hint">— digits only, with country code</span></label>
            <input class="set-input" type="text" name="agency_whatsapp" value="<?= htmlspecialchars($settings['agency_whatsapp'] ?? '') ?>" placeholder="919876543210">
          </div>
          <div class="set-field">
            <label>Email Address</label>
            <input class="set-input" type="email" name="agency_email" value="<?= htmlspecialchars($settings['agency_email'] ?? '') ?>" placeholder="info@himachalsafar.com">
          </div>
          <div class="set-field full">
            <label>Location <span class="hint">— shown in the footer &amp; SEO</span></label>
            <input class="set-input" type="text" name="agency_location" value="<?= htmlspecialchars($settings['agency_location'] ?? '') ?>" placeholder="Bilaspur, Himachal Pradesh">
          </div>
        </div>

        <div class="set-section mt"><i class="fas fa-indian-rupee-sign"></i> Booking</div>
        <div class="set-grid">
          <div class="set-field">
            <label>Advance to Confirm <span class="hint">— % of the estimated total collected when a booking is confirmed. Shown in every quote and used by the cancellation policy.</span></label>
            <input class="set-input" type="number" name="advance_percent" min="5" max="100" step="1"
                   value="<?= (int)($settings['advance_percent'] ?? 25) ?>" placeholder="25">
          </div>
        </div>

        <div class="set-section mt"><i class="fas fa-share-nodes"></i> Social Media Links</div>
        <div class="set-grid">
          <div class="set-field full">
            <label>Facebook URL <span class="hint">— leave blank to hide the icon</span></label>
            <input class="set-input" type="url" name="social_facebook" value="<?= htmlspecialchars($settings['social_facebook'] ?? '') ?>" placeholder="https://facebook.com/yourpage">
          </div>
          <div class="set-field full">
            <label>Instagram URL <span class="hint">— leave blank to hide the icon</span></label>
            <input class="set-input" type="url" name="social_instagram" value="<?= htmlspecialchars($settings['social_instagram'] ?? '') ?>" placeholder="https://instagram.com/yourhandle">
          </div>
          <div class="set-field full">
            <label>YouTube URL <span class="hint">— leave blank to hide the icon</span></label>
            <input class="set-input" type="url" name="social_youtube" value="<?= htmlspecialchars($settings['social_youtube'] ?? '') ?>" placeholder="https://youtube.com/@yourchannel">
          </div>
        </div>

        <div class="mt-4">
          <button type="submit" class="btn btn-primary-gold"><i class="fas fa-check me-1"></i> Save Settings</button>
        </div>
      </form>

    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
