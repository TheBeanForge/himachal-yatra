<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../api/config.php';

$status_filter = $_GET['status'] ?? '';
$search        = trim($_GET['q'] ?? '');

$where = []; $params = []; $types = '';
if ($status_filter) { $where[] = 'status = ?'; $params[] = $status_filter; $types .= 's'; }
if ($search) {
    $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ? OR destination LIKE ?)';
    $like = '%' . $search . '%';
    $params = array_merge($params, [$like, $like, $like, $like]);
    $types .= 'ssss';
}
$sql = 'SELECT * FROM bookings';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY submitted_at DESC';

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$counts_raw = $conn->query("SELECT status, COUNT(*) c FROM bookings GROUP BY status")->fetch_all(MYSQLI_ASSOC);
$ct    = array_column($counts_raw, 'c', 'status');
$total = array_sum($ct);

$wa = agency_whatsapp();
$conn->close();

$status_labels = ['new' => 'New', 'contacted' => 'Contacted', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled'];
$pkg_labels    = ['budget' => 'Budget', 'classic' => 'Classic', 'luxury' => 'Luxury'];
$page_title    = 'Leads Dashboard';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<meta name="csrf-token" content="<?= htmlspecialchars(admin_csrf_token()) ?>"/>
<title><?= htmlspecialchars($page_title) ?> — Himachal Yatra Admin</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Poppins:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
<link href="assets/admin.css" rel="stylesheet"/>
</head>
<body>
<div class="admin-wrap">

  <?php require_once 'partials/sidebar.php'; ?>

  <div class="admin-main">
    <?php require_once 'partials/topbar.php'; ?>

    <div class="admin-content">

      <!-- Stat Cards -->
      <div class="stat-grid">
        <a href="dashboard.php" class="stat-card card-green">
          <i class="fas fa-suitcase-rolling sc-bg-icon"></i>
          <div class="sc-value"><?= $total ?></div>
          <div class="sc-label">Total Leads</div>
        </a>
        <a href="dashboard.php?status=new" class="stat-card card-blue">
          <i class="fas fa-bell sc-bg-icon"></i>
          <div class="sc-value"><?= $ct['new'] ?? 0 ?></div>
          <div class="sc-label">New</div>
        </a>
        <a href="dashboard.php?status=contacted" class="stat-card card-amber">
          <i class="fas fa-phone sc-bg-icon"></i>
          <div class="sc-value"><?= $ct['contacted'] ?? 0 ?></div>
          <div class="sc-label">Contacted</div>
        </a>
        <a href="dashboard.php?status=confirmed" class="stat-card card-emerald">
          <i class="fas fa-circle-check sc-bg-icon"></i>
          <div class="sc-value"><?= $ct['confirmed'] ?? 0 ?></div>
          <div class="sc-label">Confirmed</div>
        </a>
        <a href="dashboard.php?status=cancelled" class="stat-card card-red">
          <i class="fas fa-ban sc-bg-icon"></i>
          <div class="sc-value"><?= $ct['cancelled'] ?? 0 ?></div>
          <div class="sc-label">Cancelled</div>
        </a>
      </div>

      <!-- Filter Bar -->
      <form method="GET" class="filter-bar">
        <input type="text" name="q" placeholder="Search name, phone, destination…" value="<?= htmlspecialchars($search) ?>"/>
        <select name="status">
          <option value="">All Statuses</option>
          <?php foreach ($status_labels as $val => $lbl): ?>
          <option value="<?= $val ?>" <?= $status_filter === $val ? 'selected' : '' ?>><?= $lbl ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-filter"><i class="fas fa-magnifying-glass"></i> Search</button>
        <?php if ($search || $status_filter): ?>
        <a href="dashboard.php" class="btn-clear">Clear</a>
        <?php endif; ?>
      </form>

      <!-- Leads Table -->
      <div class="table-wrap">
        <?php if (empty($bookings)): ?>
        <div class="empty-state">
          <i class="fas fa-suitcase"></i>
          <p>No leads found.</p>
        </div>
        <?php else: ?>
        <div style="overflow-x:auto">
        <table class="admin-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Traveler</th>
              <th>Destination</th>
              <th>Package</th>
              <th>Travel Date</th>
              <th>Status</th>
              <th>Submitted</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($bookings as $b): ?>
            <tr id="row-<?= $b['id'] ?>">
              <td style="color:var(--muted);font-size:12px"><?= $b['id'] ?></td>
              <td>
                <div class="td-name"><?= htmlspecialchars($b['name']) ?></div>
                <div class="td-sub"><?= htmlspecialchars($b['phone']) ?></div>
                <?php if ($b['email']): ?>
                <div class="td-sub"><?= htmlspecialchars($b['email']) ?></div>
                <?php endif; ?>
                <?php if ($b['message']): ?>
                <div class="td-msg" onclick="this.classList.toggle('open')" title="Click to expand">
                  <i class="fas fa-comment-dots"></i>
                  <span><?= htmlspecialchars($b['message']) ?></span>
                </div>
                <?php endif; ?>
              </td>
              <td>
                <?= htmlspecialchars($b['destination'] ?: '—') ?>
                <?php if (!empty($b['pickup'])): ?>
                <div class="td-sub"><i class="fas fa-location-dot" style="font-size:10px"></i> from <?= htmlspecialchars($b['pickup']) ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($b['package']): ?>
                <span class="pkg pkg-<?= $b['package'] ?>"><?= $pkg_labels[$b['package']] ?? $b['package'] ?></span>
                <?php else: ?>—<?php endif; ?>
              </td>
              <td><?= $b['travel_date'] ? date('d M Y', strtotime($b['travel_date'])) : '—' ?></td>
              <td>
                <select class="status-select status-select-<?= $b['status'] ?>" data-id="<?= $b['id'] ?>" onchange="updateStatus(this)">
                  <?php foreach ($status_labels as $val => $lbl): ?>
                  <option value="<?= $val ?>" <?= $b['status'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td style="font-size:12px;color:var(--muted)"><?= date('d M Y', strtotime($b['submitted_at'])) ?></td>
              <td>
                <?php
                $phone = preg_replace('/[^0-9]/', '', $b['phone']);
                if (strlen($phone) === 10) $phone = '91' . $phone;
                $msg = urlencode("Hi {$b['name']}! Thanks for your enquiry about a trip to {$b['destination']} with Himachal Yatra Travels. How can we help?");
                ?>
                <a href="https://wa.me/<?= $phone ?>?text=<?= $msg ?>" target="_blank" class="btn-wa">
                  <i class="fab fa-whatsapp"></i> Chat
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openSidebar(){
  document.getElementById('sidebar').classList.add('open');
  document.getElementById('sidebarOverlay').classList.add('show');
}
function closeSidebar(){
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebarOverlay').classList.remove('show');
}

function updateStatus(sel) {
  const id     = sel.dataset.id;
  const status = sel.value;
  const fd     = new FormData();
  fd.append('id', id);
  fd.append('status', status);

  sel.disabled = true;
  fetch('../api/update_status.php', {
    method: 'POST',
    body: fd,
    headers: { 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content }
  })
    .then(r => r.json())
    .then(d => {
      sel.disabled = false;
      // Update select colour class
      sel.className = `status-select status-select-${status}`;
      if (!d.ok) alert('Could not update status. Please try again.');
    })
    .catch(() => { sel.disabled = false; alert('Network error.'); });
}
</script>
</body>
</html>
