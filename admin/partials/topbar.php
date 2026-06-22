<?php
$_theme = admin_theme();
echo "<script>document.documentElement.setAttribute('data-theme','" . htmlspecialchars($_theme, ENT_QUOTES) . "');</script>";
$_cur     = basename($_SERVER['PHP_SELF']);
$_role    = $_SESSION['admin_user']['role'] ?? 'staff';
$_isSuper = $_role === 'superadmin';
$_csrf    = admin_csrf_token();
?>
<header class="topbar">
  <button class="topbar-ham d-lg-none" onclick="openSidebar()" aria-label="Menu">
    <i class="fas fa-bars"></i>
  </button>

  <a class="topbar-brand" href="enquiries.php" title="Dashboard">
    <img src="../assets/logo-icon.svg" alt="" width="26" height="26">
    <b>Himachal <span>Safar</span></b>
  </a>

  <!-- Admin links (desktop) — moved out of the side menu -->
  <nav class="topbar-adminnav d-none d-lg-flex">
    <?php if ($_isSuper): ?>
    <a class="tb-link <?= $_cur==='users.php' ? 'active' : '' ?>" href="users.php">User Management</a>
    <a class="tb-link <?= $_cur==='audit.php' ? 'active' : '' ?>" href="audit.php">Audit Log</a>
    <?php endif; ?>
    <a class="tb-link <?= $_cur==='settings.php' ? 'active' : '' ?>" href="settings.php">Settings</a>
  </nav>

  <div class="topbar-right">
    <form method="post" action="../api/set_admin_theme.php" id="adminThemeForm" class="tb-seg-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_csrf, ENT_QUOTES) ?>">
      <div class="tb-seg" role="group" aria-label="Theme">
        <input type="radio" name="admin_theme" id="tbth-dark"  value="dark"  <?= $_theme==='dark'  ? 'checked' : '' ?>>
        <label for="tbth-dark"  title="Dark"><i class="fas fa-moon"></i></label>
        <input type="radio" name="admin_theme" id="tbth-light" value="light" <?= $_theme==='light' ? 'checked' : '' ?>>
        <label for="tbth-light" title="Light"><i class="fas fa-sun"></i></label>
        <input type="radio" name="admin_theme" id="tbth-pine"  value="pine"  <?= $_theme==='pine'  ? 'checked' : '' ?>>
        <label for="tbth-pine"  title="Pine"><i class="fas fa-tree"></i></label>
        <input type="radio" name="admin_theme" id="tbth-sky"   value="sky"   <?= $_theme==='sky'   ? 'checked' : '' ?>>
        <label for="tbth-sky"   title="Sky (blue &amp; white)"><i class="fas fa-cloud"></i></label>
      </div>
    </form>
    <a href="../index.php" target="_blank" class="topbar-site-btn">
      <i class="fas fa-arrow-up-right-from-square"></i> <span class="tb-vs-text">View Site</span>
    </a>
  </div>
  <script>
    document.querySelectorAll('#adminThemeForm input[type=radio]').forEach(function (r) {
      r.addEventListener('change', function () {
        document.documentElement.setAttribute('data-theme', this.value);
        document.getElementById('adminThemeForm').submit();
      });
    });
  </script>
</header>
