<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../includes/vars.php';

$role = $_SESSION['admin_user']['role'] ?? 'staff';
$name = $_SESSION['admin_user']['full_name'] ?? 'there';
$page_title = 'Help & Guide';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Help &amp; Guide — Himachal Safar Admin</title>
<link rel="icon" href="../favicon.ico" sizes="32x32">
<link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="../assets/brand/mark-192.png">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Poppins:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
<link href="assets/admin.css?v=<?php echo @filemtime(__DIR__ . '/assets/admin.css'); ?>" rel="stylesheet"/>
<style>
.guide-hero{background:linear-gradient(135deg, rgba(62,142,94,.10), rgba(201,168,76,.08));
  border:1px solid var(--border);border-radius:16px;padding:26px 28px;margin-bottom:26px}
.guide-hero h1{font-family:'Poppins',sans-serif;font-size:22px;font-weight:800;color:var(--ink);margin:0 0 8px}
.guide-hero p{font-size:14px;color:var(--ink-2);margin:0;max-width:680px;line-height:1.6}
.guide-section-title{font-family:'Poppins',sans-serif;font-size:15px;font-weight:800;color:var(--ink);
  margin:30px 0 14px;display:flex;align-items:center;gap:9px}
.guide-section-title i{color:var(--accent)}
.flow{display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-bottom:6px}
.flow-step{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:20px;
  font-size:12px;font-weight:700;background:var(--surface-2);border:1px solid var(--border);color:var(--ink)}
.flow-arrow{color:var(--muted);font-size:11px}
.guide-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px}
.guide-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:18px 20px;
  display:flex;flex-direction:column;gap:8px}
.guide-card .gc-head{display:flex;align-items:center;gap:11px}
.gc-icon{width:38px;height:38px;flex-shrink:0;border-radius:10px;display:grid;place-items:center;
  background:rgba(62,142,94,.12);color:var(--accent);font-size:16px}
.gc-title{font-size:14.5px;font-weight:700;color:var(--ink)}
.gc-where{font-size:11px;color:var(--muted);margin-top:1px}
.guide-card p{font-size:13px;color:var(--ink-2);line-height:1.55;margin:0}
.gc-tip{font-size:12px;color:var(--ink-2);background:var(--surface-2);border-left:3px solid var(--accent);
  border-radius:0 8px 8px 0;padding:8px 11px;margin-top:2px}
.gc-tip b{color:var(--ink)}
.gc-open{margin-top:auto;font-size:12.5px;font-weight:700;color:var(--accent);text-decoration:none;
  display:inline-flex;align-items:center;gap:6px}
.gc-open:hover{color:var(--accent-d)}
.badge-pill{display:inline-block;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;
  padding:2px 8px;border-radius:5px;color:#0d0d14}
.bp-slideshow{background:#e0b84a}.bp-cover{background:#3b82f6;color:#fff}.bp-about{background:#22c55e}
.bp-gallery{background:rgba(120,120,140,.92);color:#fff}
.photo-explain{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px 22px}
.photo-explain ul{margin:10px 0 0;padding-left:0;list-style:none;display:flex;flex-direction:column;gap:10px}
.photo-explain li{display:flex;gap:10px;font-size:13px;color:var(--ink-2);line-height:1.5}
.photo-explain li i{color:var(--accent);margin-top:3px}
.role-note{font-size:12px;color:var(--muted);margin-top:6px}
</style>
</head>
<body>
<div class="admin-wrap">
  <?php require_once 'partials/sidebar.php'; ?>
  <div class="admin-main">
    <?php require_once 'partials/topbar.php'; ?>
    <div class="admin-content">

      <div class="guide-hero">
        <h1><i class="fas fa-hand-sparkles" style="color:var(--accent)"></i> Welcome, <?= h($name) ?> 👋</h1>
        <p>This is your control panel for the Himachal Safar website. Everything customers see — enquiries you receive, the photos on each page, the fleet and prices — is managed from the menu on the left. This page explains what each section does and how your changes show up on the live site.</p>
      </div>

      <!-- Lead lifecycle -->
      <div class="guide-section-title"><i class="fas fa-inbox"></i> How a customer enquiry flows</div>
      <div class="flow">
        <span class="flow-step"><i class="fas fa-bell" style="color:#e0b84a"></i> New</span>
        <span class="flow-arrow"><i class="fas fa-arrow-right"></i></span>
        <span class="flow-step"><i class="fas fa-phone" style="color:#3b82f6"></i> Contacted</span>
        <span class="flow-arrow"><i class="fas fa-arrow-right"></i></span>
        <span class="flow-step"><i class="fas fa-file-invoice-dollar" style="color:#a855f7"></i> Quoted</span>
        <span class="flow-arrow"><i class="fas fa-arrow-right"></i></span>
        <span class="flow-step"><i class="fas fa-circle-check" style="color:#22c55e"></i> Confirmed</span>
        <span class="flow-arrow"><i class="fas fa-arrow-right"></i></span>
        <span class="flow-step"><i class="fas fa-box-archive" style="color:#6b7280"></i> Closed</span>
      </div>
      <p style="font-size:12.5px;color:var(--muted);margin:0 0 4px">When someone submits the “Plan My Journey” form on the website, it lands in <b style="color:var(--ink)">Leads</b> as <b style="color:var(--ink)">New</b>. Update the status as you call, quote and confirm the trip — the sidebar counters help you see what needs attention.</p>

      <!-- Feature cards -->
      <div class="guide-section-title"><i class="fas fa-layer-group"></i> What each section does</div>
      <div class="guide-grid">

        <div class="guide-card">
          <div class="gc-head"><div class="gc-icon"><i class="fas fa-list"></i></div>
            <div><div class="gc-title">Leads</div><div class="gc-where">Sidebar → Leads</div></div></div>
          <p>Your inbox of customer enquiries from the website. Open one to see trip details, then move it through New → Contacted → Quoted → Confirmed → Closed.</p>
          <a class="gc-open" href="enquiries.php">Open Leads <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="guide-card">
          <div class="gc-head"><div class="gc-icon"><i class="fas fa-images"></i></div>
            <div><div class="gc-title">Photo Gallery</div><div class="gc-where">Sidebar → Photo Gallery</div></div></div>
          <p>Upload photos and choose where each one appears on the website — the homepage slideshow, a destination cover, the about image, or a gallery. See the detailed explainer below.</p>
          <a class="gc-open" href="photos.php">Open Photo Gallery <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="guide-card">
          <div class="gc-head"><div class="gc-icon"><i class="fas fa-car-side"></i></div>
            <div><div class="gc-title">Vehicles</div><div class="gc-where">Sidebar → Vehicles</div></div></div>
          <p>Manage the cabs used in the quote calculator. Add a <b>photo</b> to a vehicle and it appears in the website’s “Our Fleet” section (once at least one vehicle has a photo).</p>
          <a class="gc-open" href="vehicles.php">Open Vehicles <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="guide-card">
          <div class="gc-head"><div class="gc-icon"><i class="fas fa-mountain"></i></div>
            <div><div class="gc-title">Destinations</div><div class="gc-where">Sidebar → Destinations</div></div></div>
          <p>The destinations (Manali, Shimla, etc.) available in the calculator. Each destination also has its own page on the website with a cover, about image and gallery (managed in Photo Gallery).</p>
          <a class="gc-open" href="destinations.php">Open Destinations <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="guide-card">
          <div class="gc-head"><div class="gc-icon"><i class="fas fa-map-pin"></i></div>
            <div><div class="gc-title">Pickup Locations</div><div class="gc-where">Sidebar → Pickup Locations</div></div></div>
          <p>The cities/points customers can choose as a pickup in the “Plan My Journey” calculator.</p>
          <a class="gc-open" href="locations.php">Open Locations <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="guide-card">
          <div class="gc-head"><div class="gc-icon"><i class="fas fa-tags"></i></div>
            <div><div class="gc-title">Pricing Rules</div><div class="gc-where">Sidebar → Pricing Rules</div></div></div>
          <p>Rates, taxes and seasonal pricing that drive the automatic quote estimates.</p>
          <a class="gc-open" href="pricing.php">Open Pricing <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="guide-card">
          <div class="gc-head"><div class="gc-icon"><i class="fas fa-road"></i></div>
            <div><div class="gc-title">Route Photos</div><div class="gc-where">Sidebar → Route Photos</div></div></div>
          <p>Assign a photo to each popular route shown on the homepage routes section.</p>
          <a class="gc-open" href="routes.php">Open Route Photos <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="guide-card">
          <div class="gc-head"><div class="gc-icon"><i class="fas fa-sliders"></i></div>
            <div><div class="gc-title">Settings</div><div class="gc-where">Sidebar → Settings</div></div></div>
          <p>Agency contact details (phone, WhatsApp, email) and the admin colour theme — <b>Dark</b>, <b>Light</b> or <b>Pine</b> (sage green).</p>
          <a class="gc-open" href="settings.php">Open Settings <i class="fas fa-arrow-right"></i></a>
        </div>

        <?php if (in_array($role, ['superadmin'], true)): ?>
        <div class="guide-card">
          <div class="gc-head"><div class="gc-icon"><i class="fas fa-users-gear"></i></div>
            <div><div class="gc-title">User Management</div><div class="gc-where">Sidebar → User Management</div></div></div>
          <p>Add staff accounts and set their role. <b>Staff</b> handle leads &amp; content; <b>Admin</b> can do more; <b>Superadmin</b> manages users and sees the audit log.</p>
          <a class="gc-open" href="users.php">Open Users <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="guide-card">
          <div class="gc-head"><div class="gc-icon"><i class="fas fa-clipboard-list"></i></div>
            <div><div class="gc-title">Audit Log</div><div class="gc-where">Sidebar → Audit Log</div></div></div>
          <p>A record of who changed what and when — useful for tracking activity across the team.</p>
          <a class="gc-open" href="audit.php">Open Audit Log <i class="fas fa-arrow-right"></i></a>
        </div>
        <?php else: ?>
        <p class="role-note"><i class="fas fa-circle-info"></i> You are signed in as <b><?= h(ucfirst($role)) ?></b>. Some sections (User Management, Audit Log) are only visible to a Superadmin.</p>
        <?php endif; ?>

      </div>

      <!-- Photo explainer -->
      <div class="guide-section-title"><i class="fas fa-circle-question"></i> How photos show up on the website</div>
      <div class="photo-explain">
        <p style="font-size:13px;color:var(--ink-2);margin:0;line-height:1.6">In <b style="color:var(--ink)">Photo Gallery</b>, every photo you upload can be given a <b>purpose</b>. The “Live on your site” panel at the top shows exactly what is being used where — and you can click an <b>＋ Add</b> box there to upload straight into a spot.</p>
        <ul>
          <li><i class="fas fa-photo-film"></i><span><span class="badge-pill bp-slideshow">Slideshow</span> &nbsp;The big rotating images in the homepage banner. Pick up to <b>5</b>.</span></li>
          <li><i class="fas fa-image"></i><span><span class="badge-pill bp-cover">Cover</span> &nbsp;The large banner photo at the top of a destination page (e.g. Manali). One per destination.</span></li>
          <li><i class="fas fa-circle-info"></i><span><span class="badge-pill bp-about">About</span> &nbsp;The photo beside the “About this destination” text. One per destination.</span></li>
          <li><i class="fas fa-grip"></i><span><span class="badge-pill bp-gallery">Gallery</span> &nbsp;Any other photo uploaded to a destination automatically appears in that page’s photo gallery.</span></li>
        </ul>
        <div class="gc-tip" style="margin-top:14px"><b>Two ways to assign:</b> either click an <b>＋ Add</b> box in the “Live on your site” panel to upload directly into that spot, or upload a photo first and then click its purpose button (Slideshow / Cover / About) on the photo card below.</div>
        <a class="gc-open" href="photos.php" style="margin-top:14px">Go to Photo Gallery <i class="fas fa-arrow-right"></i></a>
      </div>

      <!-- Tips -->
      <div class="guide-section-title"><i class="fas fa-lightbulb"></i> Good to know</div>
      <div class="photo-explain">
        <ul>
          <li><i class="fas fa-up-right-from-square"></i><span>The <b>View Site</b> button (top-right) opens the live website in a new tab so you can check your changes.</span></li>
          <li><i class="fas fa-mobile-screen"></i><span>This panel works on a phone — tap the <b>☰</b> menu (top-left) to open the navigation.</span></li>
          <li><i class="fas fa-image"></i><span>For best results upload <b>wide, high-quality</b> photos (at least 1600px wide for slideshow &amp; cover images). They are resized and compressed under <b>400 KB</b> automatically.</span></li>
          <li><i class="fas fa-palette"></i><span>Change the panel’s look in <b>Settings → Appearance</b> (Dark, Light or Pine).</span></li>
          <li><i class="fas fa-right-from-bracket"></i><span>Always <b>Log out</b> (bottom of the sidebar) when you’re done on a shared computer.</span></li>
        </ul>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
