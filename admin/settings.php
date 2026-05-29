<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../api/config.php';

// Only superadmin and admin can access settings
$role = $_SESSION['admin_user']['role'] ?? '';
if (!in_array($role, ['superadmin', 'admin'])) {
    header('Location: dashboard.php'); exit;
}

$saved = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin_csrf();

    $fields = [
        'agency_name'      => substr(trim($_POST['agency_name'] ?? ''), 0, 100),
        'agency_phone'     => substr(trim($_POST['agency_phone'] ?? ''), 0, 30),
        'agency_whatsapp'  => preg_replace('/[^0-9]/', '', $_POST['agency_whatsapp'] ?? ''),
        'agency_email'     => filter_var(trim($_POST['agency_email'] ?? ''), FILTER_VALIDATE_EMAIL) ?: '',
        'admin_theme'      => in_array($_POST['admin_theme'] ?? '', ['dark-gold', 'dark-blue', 'light']) ? $_POST['admin_theme'] : 'dark-gold',
    ];

    try {
        $stmt = $conn->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        foreach ($fields as $key => $value) {
            $stmt->bind_param('ss', $key, $value);
            $stmt->execute();
        }
        $stmt->close();
        audit_log('settings_update', 'Agency settings updated');
        $saved = true;

        // Update session theme
        $_SESSION['admin_theme'] = $fields['admin_theme'];
    } catch (Throwable $e) {
        $error = 'Could not save settings.';
    }
}

// Load current settings
$settings = [];
$res = $conn->query("SELECT setting_key, setting_value FROM settings");
if ($res) foreach ($res->fetch_all(MYSQLI_ASSOC) as $row) $settings[$row['setting_key']] = $row['setting_value'];
$conn->close();

$currentTheme = $_SESSION['admin_theme'] ?? ($settings['admin_theme'] ?? 'dark-gold');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<meta name="csrf-token" content="<?= htmlspecialchars(admin_csrf_token()) ?>"/>
<title>Settings — Himachal Yatra Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
<link href="assets/admin.css" rel="stylesheet"/>
<style>
.settings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
@media (max-width: 768px) { .settings-grid { grid-template-columns: 1fr; } }

.settings-card {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 14px; padding: 28px;
}
.settings-card h3 {
  font-size: 14px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .08em; color: var(--muted);
  margin-bottom: 22px; display: flex; align-items: center; gap: 8px;
}
.settings-card h3 i { color: var(--accent); }

.sf-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 18px; }
.sf-label { font-size: 12px; font-weight: 600; color: var(--muted); }
.sf-input {
  height: 42px; padding: 0 14px;
  background: var(--surface-2); border: 1px solid var(--border);
  border-radius: 8px; color: var(--ink); font-size: 13.5px;
  font-family: inherit; outline: none; transition: border-color .2s;
  width: 100%;
}
.sf-input:focus { border-color: var(--accent); }
.sf-input::placeholder { color: var(--muted); }

.save-btn {
  height: 42px; padding: 0 28px;
  background: var(--accent); color: #0d0d14;
  border: none; border-radius: 8px;
  font: 700 13.5px 'Inter', sans-serif; cursor: pointer;
  display: inline-flex; align-items: center; gap: 8px;
  transition: background .2s;
}
.save-btn:hover { background: var(--accent-l); }

.flash-ok  { background: rgba(34,197,94,.10); color: #4ade80; border: 1px solid rgba(34,197,94,.20); border-radius: 8px; padding: 10px 16px; font-size: 13px; font-weight: 600; margin-bottom: 20px; }
.flash-err { background: rgba(239,68,68,.10); color: #f87171; border: 1px solid rgba(239,68,68,.20); border-radius: 8px; padding: 10px 16px; font-size: 13px; font-weight: 600; margin-bottom: 20px; }

/* Theme selector */
.theme-options { display: flex; flex-direction: column; gap: 12px; }
.theme-option {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 16px; border-radius: 10px;
  border: 1.5px solid var(--border); cursor: pointer;
  transition: border-color .2s, background .2s;
  position: relative;
}
.theme-option:has(input:checked) {
  border-color: var(--accent);
  background: rgba(201,168,76,.06);
}
.theme-option input[type=radio] { display: none; }
.theme-preview {
  width: 48px; height: 32px; border-radius: 6px;
  border: 1px solid rgba(255,255,255,.1);
  flex-shrink: 0; overflow: hidden;
  display: grid; grid-template-columns: 30% 70%;
}
.tp-sidebar { height: 100%; }
.tp-main    { height: 100%; }

/* Dark Gold */
.tp-dg-s { background: #0d0d14; }
.tp-dg-m { background: #161623; }
/* Dark Blue */
.tp-db-s { background: #0f172a; }
.tp-db-m { background: #1e293b; }
/* Light */
.tp-lt-s { background: #1e293b; }
.tp-lt-m { background: #f8fafc; }

.theme-info { flex: 1; }
.theme-name { font-size: 13.5px; font-weight: 600; color: var(--ink); }
.theme-desc { font-size: 11.5px; color: var(--muted); margin-top: 2px; }
.theme-tick {
  width: 20px; height: 20px; border-radius: 50%;
  border: 2px solid var(--border);
  display: grid; place-items: center;
  font-size: 10px; color: transparent;
  transition: all .2s;
}
.theme-option:has(input:checked) .theme-tick {
  background: var(--accent); border-color: var(--accent); color: #0d0d14;
}
</style>
</head>
<body>
<div class="admin-wrap">
  <?php require_once 'partials/sidebar.php'; ?>
  <div class="admin-main">
    <?php require_once 'partials/topbar.php'; ?>
    <div class="admin-content">

      <?php if ($saved): ?>
      <div class="flash-ok"><i class="fas fa-check-circle me-2"></i>Settings saved successfully.</div>
      <?php endif; ?>
      <?php if ($error): ?>
      <div class="flash-err"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['admin_csrf'] ?? '') ?>">

        <div class="settings-grid">

          <!-- Agency Info -->
          <div class="settings-card">
            <h3><i class="fas fa-building"></i> Agency Information</h3>

            <div class="sf-group">
              <label class="sf-label">Agency Name</label>
              <input class="sf-input" type="text" name="agency_name"
                     value="<?= htmlspecialchars($settings['agency_name'] ?? '') ?>"
                     placeholder="Himachal Yatra Travels">
            </div>
            <div class="sf-group">
              <label class="sf-label">Phone Number</label>
              <input class="sf-input" type="text" name="agency_phone"
                     value="<?= htmlspecialchars($settings['agency_phone'] ?? '') ?>"
                     placeholder="+91 98765 43210">
            </div>
            <div class="sf-group">
              <label class="sf-label">WhatsApp Number <span style="color:var(--muted);font-weight:400">(digits only, with country code)</span></label>
              <input class="sf-input" type="text" name="agency_whatsapp"
                     value="<?= htmlspecialchars($settings['agency_whatsapp'] ?? '') ?>"
                     placeholder="919876543210">
            </div>
            <div class="sf-group" style="margin-bottom:0">
              <label class="sf-label">Email Address</label>
              <input class="sf-input" type="email" name="agency_email"
                     value="<?= htmlspecialchars($settings['agency_email'] ?? '') ?>"
                     placeholder="info@himachalyatratravels.com">
            </div>
          </div>

          <!-- Theme -->
          <div class="settings-card">
            <h3><i class="fas fa-palette"></i> Admin Theme</h3>

            <div class="theme-options">

              <label class="theme-option">
                <input type="radio" name="admin_theme" value="dark-gold" <?= $currentTheme === 'dark-gold' ? 'checked' : '' ?>>
                <div class="theme-preview">
                  <div class="tp-sidebar tp-dg-s"></div>
                  <div class="tp-main tp-dg-m"></div>
                </div>
                <div class="theme-info">
                  <div class="theme-name">Dark Gold <span style="font-size:10px;background:rgba(201,168,76,.15);color:var(--accent);padding:2px 7px;border-radius:4px;margin-left:6px">Current</span></div>
                  <div class="theme-desc">Deep dark background with gold accents</div>
                </div>
                <div class="theme-tick"><i class="fas fa-check"></i></div>
              </label>

              <label class="theme-option">
                <input type="radio" name="admin_theme" value="dark-blue" <?= $currentTheme === 'dark-blue' ? 'checked' : '' ?>>
                <div class="theme-preview">
                  <div class="tp-sidebar tp-db-s"></div>
                  <div class="tp-main tp-db-m"></div>
                </div>
                <div class="theme-info">
                  <div class="theme-name">Dark Blue</div>
                  <div class="theme-desc">Dark navy background with blue accents</div>
                </div>
                <div class="theme-tick"><i class="fas fa-check"></i></div>
              </label>

              <label class="theme-option">
                <input type="radio" name="admin_theme" value="light" <?= $currentTheme === 'light' ? 'checked' : '' ?>>
                <div class="theme-preview">
                  <div class="tp-sidebar tp-lt-s"></div>
                  <div class="tp-main tp-lt-m"></div>
                </div>
                <div class="theme-info">
                  <div class="theme-name">Light</div>
                  <div class="theme-desc">Clean white background with dark sidebar</div>
                </div>
                <div class="theme-tick"><i class="fas fa-check"></i></div>
              </label>

            </div>
          </div>

        </div>

        <div style="margin-top:24px">
          <button type="submit" class="save-btn">
            <i class="fas fa-save"></i> Save Settings
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openSidebar(){document.getElementById('sidebar').classList.add('open');document.getElementById('sidebarOverlay').classList.add('show');}
function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sidebarOverlay').classList.remove('show');}
</script>
</body>
</html>
