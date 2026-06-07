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
    <?php
      // Single lead inbox = booking_enquiries (shown by enquiries.php).
      $__leadNav = [
        ''          => ['All Leads', 'fa-list'],
        'new'       => ['New',       'fa-bell'],
        'contacted' => ['Contacted', 'fa-phone'],
        'quoted'    => ['Quoted',    'fa-file-invoice-dollar'],
        'confirmed' => ['Confirmed', 'fa-circle-check'],
        'closed'    => ['Closed',    'fa-box-archive'],
      ];
      $__curStatus = $_GET['status'] ?? '';
      foreach ($__leadNav as $__sk => [$__lbl, $__ic]):
        $__active = $cur === 'enquiries.php' && $__curStatus === $__sk;
    ?>
    <li>
      <a href="enquiries.php<?= $__sk ? '?status='.$__sk : '' ?>" class="sidebar-link <?= $__active ? 'active' : '' ?>">
        <i class="fas <?= $__ic ?> fa-fw"></i> <?= $__lbl ?>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>

  <div class="sidebar-section-label">QUOTE CALCULATOR</div>
  <ul class="sidebar-nav">
    <li>
      <a href="packages.php" class="sidebar-link <?= $cur==='packages.php'?'active':'' ?>">
        <i class="fas fa-suitcase-rolling fa-fw"></i> Tour Packages
      </a>
    </li>
    <li>
      <a href="destinations.php" class="sidebar-link <?= $cur==='destinations.php'?'active':'' ?>">
        <i class="fas fa-mountain fa-fw"></i> Destinations
      </a>
    </li>
    <li>
      <a href="vehicles.php" class="sidebar-link <?= $cur==='vehicles.php'?'active':'' ?>">
        <i class="fas fa-car-side fa-fw"></i> Vehicles
      </a>
    </li>
    <li>
      <a href="locations.php" class="sidebar-link <?= $cur==='locations.php'?'active':'' ?>">
        <i class="fas fa-map-pin fa-fw"></i> Pickup Locations
      </a>
    </li>
    <li>
      <a href="pricing.php" class="sidebar-link <?= $cur==='pricing.php'?'active':'' ?>">
        <i class="fas fa-tags fa-fw"></i> Pricing Rules
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
      <a href="routes.php" class="sidebar-link <?= $cur==='routes.php'?'active':'' ?>">
        <i class="fas fa-road fa-fw"></i> Route Photos
      </a>
    </li>
  </ul>

  <?php if (in_array($_SESSION['admin_user']['role'] ?? '', ['superadmin','admin'])): ?>
  <div class="sidebar-section-label">ADMIN</div>
  <ul class="sidebar-nav">
    <?php if (($_SESSION['admin_user']['role'] ?? '') === 'superadmin'): ?>
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
    <?php endif; ?>
    <li>
      <a href="settings.php" class="sidebar-link <?= $cur==='settings.php'?'active':'' ?>">
        <i class="fas fa-sliders fa-fw"></i> Settings
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
