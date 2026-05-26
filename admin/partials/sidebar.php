<?php $cur = basename($_SERVER['PHP_SELF']); ?>

<div class="sidebar-overlay d-lg-none" id="sidebarOverlay" onclick="closeSidebar()"></div>

<nav class="sidebar d-flex flex-column" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo"><img src="../assets/logo-icon.svg" alt="" width="38" height="38" style="display:block"></div>
    <div>
      <div class="brand-name">HIMACHAL <span>YATRA</span></div>
      <div class="brand-tagline">Admin Portal</div>
    </div>
  </div>

  <!-- View Website — prominent button -->
  <div class="px-3 pt-3">
    <a href="../index.php" target="_blank" class="view-site-btn">
      <i class="fas fa-arrow-up-right-from-square"></i> View Website
    </a>
  </div>

  <div class="sidebar-section-label">LEADS</div>
  <ul class="sidebar-nav">
    <li>
      <a href="dashboard.php" class="sidebar-link <?= $cur==='dashboard.php' && empty($_GET['status'])?'active':'' ?>">
        <i class="fas fa-list fa-fw"></i> All Leads
      </a>
    </li>
    <li>
      <a href="dashboard.php?status=new" class="sidebar-link <?= ($cur==='dashboard.php' && ($_GET['status']??'')==='new')?'active':'' ?>">
        <i class="fas fa-bell fa-fw"></i> New
      </a>
    </li>
    <li>
      <a href="dashboard.php?status=contacted" class="sidebar-link <?= ($cur==='dashboard.php' && ($_GET['status']??'')==='contacted')?'active':'' ?>">
        <i class="fas fa-phone fa-fw"></i> Contacted
      </a>
    </li>
    <li>
      <a href="dashboard.php?status=confirmed" class="sidebar-link <?= ($cur==='dashboard.php' && ($_GET['status']??'')==='confirmed')?'active':'' ?>">
        <i class="fas fa-circle-check fa-fw"></i> Confirmed
      </a>
    </li>
    <li>
      <a href="dashboard.php?status=cancelled" class="sidebar-link <?= ($cur==='dashboard.php' && ($_GET['status']??'')==='cancelled')?'active':'' ?>">
        <i class="fas fa-ban fa-fw"></i> Cancelled
      </a>
    </li>
  </ul>

  <div class="sidebar-section-label">CONTENT</div>
  <ul class="sidebar-nav">
    <li>
      <a href="photos.php" class="sidebar-link <?= $cur==='photos.php'?'active':'' ?>">
        <i class="fas fa-images fa-fw"></i> Photo Gallery
      </a>
    </li>
    <li>
      <a href="reviews.php" class="sidebar-link <?= $cur==='reviews.php'?'active':'' ?>">
        <i class="fas fa-star fa-fw"></i> Reviews
      </a>
    </li>
    <li>
      <a href="locations.php" class="sidebar-link <?= $cur==='locations.php'?'active':'' ?>">
        <i class="fas fa-map-pin fa-fw"></i> Pickup Locations
      </a>
    </li>
  </ul>

  <?php if (($_SESSION['admin_user']['role'] ?? '') === 'superadmin'): ?>
  <div class="sidebar-section-label">ADMIN</div>
  <ul class="sidebar-nav">
    <li>
      <a href="users.php" class="sidebar-link <?= $cur==='users.php'?'active':'' ?>">
        <i class="fas fa-users-gear fa-fw"></i> User Management
      </a>
    </li>
    <li>
      <a href="audit.php" class="sidebar-link <?= $cur==='audit.php'?'active':'' ?>">
        <i class="fas fa-clipboard-list fa-fw"></i> Audit Log
      </a>
    </li>
  </ul>
  <?php endif; ?>

  <div class="mt-auto pb-3 px-3">
    <a href="logout.php" class="sidebar-link" style="color:#dc2626">
      <i class="fas fa-right-from-bracket fa-fw"></i> Logout
    </a>
  </div>
</nav>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var sb = document.getElementById('sidebar');
  var startX = 0;
  sb.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
  sb.addEventListener('touchend', function (e) {
    if (startX - e.changedTouches[0].clientX > 60) closeSidebar();
  }, { passive: true });
});
</script>
