<?php $cur = basename($_SERVER['PHP_SELF']); ?>

<div class="sidebar-overlay d-lg-none" id="sidebarOverlay" onclick="closeSidebar()"></div>

<nav class="sidebar d-flex flex-column" id="sidebar">
  <div class="sidebar-section-label" style="padding-top:16px">LEADS</div>
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
      <a href="enquiries.php<?= $__sk ? '?status='.$__sk : '' ?>" class="sidebar-link <?= $__active ? 'active' : '' ?>"<?= $__sk === 'new' ? ' id="sbNewLink"' : '' ?>>
        <i class="fas <?= $__ic ?> fa-fw"></i> <?= $__lbl ?>
        <?php if ($__sk === 'new'): ?><span class="sb-newbadge" id="sbNewBadge" hidden></span><?php endif; ?>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>

  <div class="sidebar-section-label">QUOTE CALCULATOR</div>
  <ul class="sidebar-nav">
    <li>
      <a href="destinations.php" class="sidebar-link <?= $cur==='destinations.php'?'active':'' ?>">
        <i class="fas fa-mountain fa-fw"></i> Destinations
      </a>
    </li>
    <li>
      <a href="packages.php" class="sidebar-link <?= $cur==='packages.php'?'active':'' ?>">
        <i class="fas fa-route fa-fw"></i> Tour Packages
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
    <li>
      <a href="subscribers.php" class="sidebar-link <?= $cur==='subscribers.php'?'active':'' ?>">
        <i class="fas fa-envelope-open-text fa-fw"></i> Subscribers
      </a>
    </li>
  </ul>

  <?php if (($_SESSION['admin_user']['role'] ?? '') === 'superadmin'): ?>
  <!-- User Management & Audit live in the top bar on desktop; shown here only on mobile -->
  <div class="sidebar-section-label d-lg-none">ADMIN</div>
  <ul class="sidebar-nav d-lg-none">
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
    <?php if (in_array($_SESSION['admin_user']['role'] ?? '', ['superadmin','admin'])): ?>
    <a href="settings.php" class="sidebar-link <?= $cur==='settings.php'?'active':'' ?>">
      <i class="fas fa-sliders fa-fw"></i> Settings
    </a>
    <?php endif; ?>
    <a href="help.php" class="sidebar-link <?= $cur==='help.php' ? 'active' : '' ?>">
      <i class="fas fa-circle-question fa-fw"></i> Help &amp; Guide
    </a>
    <a href="logout.php" class="sidebar-link" style="color:#dc2626">
      <i class="fas fa-right-from-bracket fa-fw"></i> Logout
    </a>
  </div>
</nav>
<script>
// Mobile sidebar toggle — defined here so EVERY admin page has it
// (previously several pages were missing these and the ☰ button was dead).
function openSidebar(){
  document.getElementById('sidebar')?.classList.add('open');
  document.getElementById('sidebarOverlay')?.classList.add('show');
}
function closeSidebar(){
  document.getElementById('sidebar')?.classList.remove('open');
  document.getElementById('sidebarOverlay')?.classList.remove('show');
}
document.addEventListener('DOMContentLoaded', function () {
  var sb = document.getElementById('sidebar');
  if (!sb) return;
  var startX = 0;
  sb.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
  sb.addEventListener('touchend', function (e) {
    if (startX - e.changedTouches[0].clientX > 60) closeSidebar();
  }, { passive: true });
});

// ── New-lead alert on the "New" menu item: red badge counts 1…5 then "5+",
//    and the bell rings when a lead arrives while the admin is working. ──
(function () {
  var link  = document.getElementById('sbNewLink');
  var badge = document.getElementById('sbNewBadge');
  if (!link || !badge) return;
  var last = -1;
  function refresh() {
    fetch('../api/admin_new_leads_count.php', { cache: 'no-store' })
      .then(function (r) { return r.ok ? r.json() : null; })
      .then(function (d) {
        if (!d || typeof d.count !== 'number') return;
        var n = d.count;
        badge.hidden = n < 1;
        badge.textContent = n > 5 ? '5+' : String(n);
        link.title = n === 0 ? 'No unread leads' : (n === 1 ? '1 unread lead' : n + ' unread leads');
        if (n > 0 && last >= 0 && n > last) {
          link.classList.remove('ringing');
          void link.offsetWidth;   // restart the animation
          link.classList.add('ringing');
        }
        last = n;
      })
      .catch(function () {});
  }
  refresh();
  setInterval(refresh, 60000);
})();
</script>
