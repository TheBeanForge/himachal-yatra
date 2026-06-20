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
    $fields = [
        'agency_name'     => substr(trim($_POST['agency_name'] ?? ''), 0, 100),
        'agency_phone'    => substr(trim($_POST['agency_phone'] ?? ''), 0, 30),
        'agency_whatsapp' => preg_replace('/[^0-9]/', '', $_POST['agency_whatsapp'] ?? ''),
        'agency_email'    => filter_var(trim($_POST['agency_email'] ?? ''), FILTER_VALIDATE_EMAIL) ?: '',
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
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
<link href="assets/admin.css?v=<?php echo @filemtime(__DIR__ . '/assets/admin.css'); ?>" rel="stylesheet"/>
<style>
  /* Theme-aware Apple neutrals for the form */
  :root, [data-theme="dark"] { --ap-hairline: rgba(255,255,255,.09); --ap-input: rgba(255,255,255,.04); --ap-focus: rgba(214,199,161,.20); }
  [data-theme="light"] { --ap-hairline: rgba(0,0,0,.06); --ap-input: #ffffff; --ap-focus: rgba(184,161,106,.20); }
  [data-theme="pine"]  { --ap-hairline: rgba(24,64,36,.10); --ap-input: #ffffff; --ap-focus: rgba(62,142,94,.18); }

  .ap-wrap { max-width: 640px; margin: 0 auto; }
  .ap-h1 { font-family: 'Inter', sans-serif; font-size: 26px; font-weight: 800; letter-spacing: -.02em; margin: 4px 0 4px; color: var(--ink); }
  .ap-sub { font-size: 14px; color: var(--muted); margin: 0 0 26px; }

  .ap-card {
    background: var(--surface); border: 1px solid var(--ap-hairline);
    border-radius: 16px; padding: 30px 30px 26px;
    box-shadow: 0 1px 2px rgba(0,0,0,.04), inset 0 1px 0 rgba(255,255,255,.03);
  }
  .ap-card-title { font-size: 13px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: var(--muted); margin: 0 0 22px; display: flex; align-items: center; gap: 8px; }
  .ap-card-title i { color: var(--accent); }
  .ap-field { display: flex; flex-direction: column; gap: 7px; margin-bottom: 18px; }
  .ap-field label { font-size: 12.5px; font-weight: 600; color: var(--ink-2); }
  .ap-field .hint { font-weight: 400; color: var(--muted); }
  .ap-input {
    height: 44px; padding: 0 15px; width: 100%;
    background: var(--ap-input); border: 1px solid var(--ap-hairline);
    border-radius: 11px; color: var(--ink); font-size: 14px; font-family: 'Inter', sans-serif;
    outline: none; transition: border-color .18s, box-shadow .18s;
  }
  .ap-input::placeholder { color: var(--muted); }
  .ap-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--ap-focus); }
  .ap-actions { margin-top: 26px; }
  .ap-save {
    height: 44px; padding: 0 26px; border: none; border-radius: 11px;
    background: var(--accent); color: #0d0d14; font: 600 14px 'Inter', sans-serif;
    cursor: pointer; transition: filter .15s, transform .05s;
    display: inline-flex; align-items: center; gap: 8px;
  }
  .ap-save:hover { filter: brightness(1.06); }
  .ap-save:active { transform: scale(.985); }
  .ap-flash { border-radius: 11px; padding: 12px 16px; font-size: 13.5px; font-weight: 500; margin-bottom: 20px; display: flex; align-items: center; gap: 9px; }
  .ap-flash.ok  { background: rgba(34,197,94,.10); color: #15a34a; border: 1px solid rgba(34,197,94,.20); }
  .ap-flash.err { background: rgba(239,68,68,.10); color: #dc2626; border: 1px solid rgba(239,68,68,.20); }
</style>
</head>
<body>
<div class="admin-wrap">
  <?php require_once 'partials/sidebar.php'; ?>
  <div class="admin-main">
    <?php require_once 'partials/topbar.php'; ?>
    <div class="admin-content">
      <div class="ap-wrap">

        <h1 class="ap-h1">Settings</h1>
        <p class="ap-sub">Manage your agency details. Switch the panel theme from the top-right of the bar above.</p>

        <?php if ($saved): ?>
        <div class="ap-flash ok"><i class="fas fa-circle-check"></i> Settings saved successfully.</div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="ap-flash err"><i class="fas fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" class="ap-card">
          <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
          <div class="ap-card-title"><i class="fas fa-building"></i> Agency Information</div>

          <div class="ap-field">
            <label>Agency Name</label>
            <input class="ap-input" type="text" name="agency_name" value="<?= htmlspecialchars($settings['agency_name'] ?? '') ?>" placeholder="Himachal Safar">
          </div>
          <div class="ap-field">
            <label>Phone Number</label>
            <input class="ap-input" type="text" name="agency_phone" value="<?= htmlspecialchars($settings['agency_phone'] ?? '') ?>" placeholder="+91 98765 43210">
          </div>
          <div class="ap-field">
            <label>WhatsApp Number <span class="hint">— digits only, with country code</span></label>
            <input class="ap-input" type="text" name="agency_whatsapp" value="<?= htmlspecialchars($settings['agency_whatsapp'] ?? '') ?>" placeholder="919876543210">
          </div>
          <div class="ap-field" style="margin-bottom:0">
            <label>Email Address</label>
            <input class="ap-input" type="email" name="agency_email" value="<?= htmlspecialchars($settings['agency_email'] ?? '') ?>" placeholder="info@himachalsafar.com">
          </div>

          <div class="ap-actions">
            <button type="submit" class="ap-save"><i class="fas fa-check"></i> Save Settings</button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
