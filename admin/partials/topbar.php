<?php
$_theme = admin_theme();
echo "<script>document.documentElement.setAttribute('data-theme','" . htmlspecialchars($_theme, ENT_QUOTES) . "');</script>";
?>
<header class="topbar">
  <button class="topbar-ham d-lg-none" onclick="openSidebar()">
    <i class="fas fa-bars"></i>
  </button>
  <div class="topbar-title"><?= htmlspecialchars($page_title ?? 'Admin') ?></div>
  <div class="topbar-right">
    <?php
    $user = $_SESSION['admin_user'] ?? null;
    if ($user):
    ?>
    <span class="topbar-user">
      <i class="fas fa-circle-user"></i>
      <?= htmlspecialchars($user['full_name']) ?>
      <span class="topbar-role"><?= match($user['role']) { 'superadmin' => 'Superadmin', 'admin' => 'Admin', default => 'Staff' } ?></span>
    </span>
    <?php endif; ?>
    <a href="../index.php" target="_blank" class="topbar-site-btn">
      <i class="fas fa-arrow-up-right-from-square"></i> View Site
    </a>
  </div>
</header>
