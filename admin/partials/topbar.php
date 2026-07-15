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
    <?php
      $_themeMeta = [
        'light' => ['fa-sun',   'Ivory White'],
        'dark'  => ['fa-moon',  'Midnight Blue'],
        'pine'  => ['fa-tree',  'Pine Green'],
        'sky'   => ['fa-cloud', 'Ocean Blue'],
      ];
      $_cur_meta = $_themeMeta[$_theme] ?? $_themeMeta['dark'];
    ?>
    <form method="post" action="../api/set_admin_theme.php" id="adminThemeForm" class="tb-theme-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_csrf, ENT_QUOTES) ?>">
      <input type="hidden" name="admin_theme" id="admThemeVal" value="<?= htmlspecialchars($_theme, ENT_QUOTES) ?>">
      <div class="adm-theme-dd" id="admThemeDd">
        <button type="button" class="adm-theme-btn" id="admThemeBtn"
                aria-haspopup="listbox" aria-expanded="false" aria-label="Change admin theme">
          <i class="fas <?= $_cur_meta[0] ?>"></i>
          <span class="adm-theme-txt d-none d-md-inline"><?= $_cur_meta[1] ?></span>
          <i class="fas fa-chevron-down adm-theme-arr"></i>
        </button>
        <div class="adm-theme-menu" role="listbox" aria-label="Admin theme">
          <?php foreach ($_themeMeta as $_tk => [$_ti, $_tl]): ?>
          <button type="button" class="adm-theme-opt<?= $_tk === $_theme ? ' active' : '' ?>"
                  role="option" aria-selected="<?= $_tk === $_theme ? 'true' : 'false' ?>"
                  data-set-theme="<?= $_tk ?>">
            <i class="fas <?= $_ti ?>"></i> <?= $_tl ?>
          </button>
          <?php endforeach; ?>
        </div>
      </div>
    </form>
    <a href="../index.php" target="_blank" class="topbar-site-btn">
      <i class="fas fa-arrow-up-right-from-square"></i> <span class="tb-vs-text">View Site</span>
    </a>
  </div>
  <script>
    (function () {
      var dd  = document.getElementById('admThemeDd');
      var btn = document.getElementById('admThemeBtn');
      if (!dd || !btn) return;
      function setOpen(open) {
        dd.classList.toggle('open', open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      }
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        setOpen(!dd.classList.contains('open'));
      });
      dd.querySelectorAll('.adm-theme-opt').forEach(function (opt) {
        opt.addEventListener('click', function () {
          var theme = opt.dataset.setTheme;
          document.documentElement.setAttribute('data-theme', theme);
          document.getElementById('admThemeVal').value = theme;
          document.getElementById('adminThemeForm').submit();
        });
      });
      document.addEventListener('click', function (e) {
        if (dd.classList.contains('open') && !dd.contains(e.target)) setOpen(false);
      });
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && dd.classList.contains('open')) { setOpen(false); btn.focus(); }
      });
    })();
  </script>
</header>
