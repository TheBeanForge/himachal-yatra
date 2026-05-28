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
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<meta name="csrf-token" content="<?= htmlspecialchars(admin_csrf_token()) ?>"/>
<title>Leads — Himachal Yatra Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
<link href="assets/admin.css" rel="stylesheet"/>
<style>
/* ── STAT STRIP ── */
.stat-strip {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 12px;
  margin-bottom: 24px;
}
.stat-tile {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 20px 20px 18px;
  text-decoration: none;
  transition: border-color .2s, background .2s;
  display: flex; flex-direction: column; gap: 6px;
  position: relative; overflow: hidden;
}
.stat-tile::after {
  content: '';
  position: absolute; left: 0; top: 0; bottom: 0;
  width: 3px;
  background: var(--border);
  transition: background .2s;
}
.stat-tile:hover, .stat-tile.active {
  background: rgba(201,168,76,.06);
  border-color: rgba(201,168,76,.35);
  text-decoration: none;
}
.stat-tile:hover::after, .stat-tile.active::after { background: var(--accent); }
.stat-tile.active .st-val, .stat-tile.active .st-label { color: var(--accent); }
.st-val {
  font-size: 32px; font-weight: 800;
  font-family: 'Poppins', sans-serif;
  color: var(--ink); line-height: 1;
}
.st-label {
  font-size: 11px; font-weight: 600; text-transform: uppercase;
  letter-spacing: .1em; color: var(--muted);
}

/* ── FILTER BAR ── */
.filter-row {
  display: flex; gap: 10px; align-items: center;
  flex-wrap: wrap; margin-bottom: 16px;
}
.filter-row input, .filter-row select {
  height: 38px; padding: 0 12px;
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 8px; color: var(--ink); font-size: 13px;
  font-family: inherit; outline: none; transition: border-color .2s;
}
.filter-row input::placeholder { color: var(--muted); }
.filter-row input:focus, .filter-row select:focus { border-color: var(--accent); }
.filter-row input { flex: 1; min-width: 200px; }
.filter-row select option { background: var(--surface-2); }
.filter-btn {
  height: 38px; padding: 0 18px;
  background: var(--accent); color: #0d0d14;
  border: none; border-radius: 8px;
  font: 700 13px 'Inter', sans-serif;
  cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
  transition: background .2s;
}
.filter-btn:hover { background: var(--accent-l); }
.clear-btn {
  height: 38px; padding: 0 14px;
  background: none; color: var(--muted);
  border: 1px solid var(--border); border-radius: 8px;
  font: 600 13px 'Inter', sans-serif; cursor: pointer;
  text-decoration: none; display: inline-flex; align-items: center;
  transition: all .2s;
}
.clear-btn:hover { border-color: var(--muted); color: var(--ink); }

/* ── TABLE ── */
.leads-table-wrap {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 12px; overflow: hidden;
}
.leads-table { width: 100%; border-collapse: collapse; }
.leads-table thead th {
  background: var(--surface-2);
  padding: 11px 16px;
  font-size: 10.5px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .12em;
  color: var(--muted); text-align: left;
  border-bottom: 1px solid var(--border);
}
.leads-table tbody tr {
  border-bottom: 1px solid var(--border);
  transition: background .15s;
}
.leads-table tbody tr:last-child { border-bottom: none; }
.leads-table tbody tr:hover { background: rgba(255,255,255,.018); }
.leads-table td { padding: 12px 16px; vertical-align: middle; font-size: 13px; }

.td-name  { font-weight: 600; color: var(--ink); font-size: 13.5px; }
.td-meta  { font-size: 11.5px; color: var(--muted); margin-top: 2px; }
.td-id    { font-size: 11px; color: var(--muted); }

.td-msg {
  margin-top: 5px; cursor: pointer;
  font-size: 11px; color: var(--accent);
  display: flex; align-items: center; gap: 4px;
}
.td-msg span {
  max-width: 180px; white-space: nowrap;
  overflow: hidden; text-overflow: ellipsis;
  color: var(--muted);
}
.td-msg.open span { white-space: normal; max-width: none; }

/* status select */
.status-sel {
  font-size: 11.5px; padding: 4px 8px;
  border: 1px solid var(--border); border-radius: 6px;
  outline: none; font-family: inherit; cursor: pointer;
  background: var(--surface-2); color: var(--ink);
  transition: border-color .2s;
}
.status-sel:focus { border-color: var(--accent); }
.ss-new       { background: rgba(59,130,246,.12);  color: #60a5fa;  border-color: rgba(59,130,246,.25); }
.ss-contacted { background: rgba(234,179,8,.12);   color: #fbbf24;  border-color: rgba(234,179,8,.25); }
.ss-confirmed { background: rgba(201,168,76,.15);  color: #c9a84c;  border-color: rgba(201,168,76,.30); }
.ss-cancelled { background: rgba(239,68,68,.12);   color: #f87171;  border-color: rgba(239,68,68,.25); }

/* pkg badge */
.pkg { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; padding: 2px 8px; border-radius: 5px; }
.pkg-budget  { background: rgba(255,255,255,.06); color: var(--muted); }
.pkg-classic { background: rgba(59,130,246,.10); color: #60a5fa; }
.pkg-luxury  { background: rgba(201,168,76,.12); color: var(--accent); }

/* wa button */
.btn-wa {
  display: inline-flex; align-items: center; gap: 5px;
  background: transparent; color: #4ade80;
  border: 1px solid rgba(37,211,102,.20); border-radius: 7px;
  padding: 5px 11px; font-size: 12px; font-weight: 600;
  text-decoration: none; transition: all .2s; white-space: nowrap;
}
.btn-wa:hover { background: #25D366; color: #fff; border-color: #25D366; }

/* empty */
.empty-state { text-align: center; padding: 64px 20px; color: var(--muted); }
.empty-state i { font-size: 32px; opacity: .3; display: block; margin-bottom: 12px; }

@media (max-width: 992px) {
  .stat-strip { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 576px) {
  .stat-strip { grid-template-columns: repeat(2, 1fr); }
  .leads-table thead { display: none; }
  .leads-table td { display: block; padding: 6px 14px; }
  .leads-table tr { display: block; padding: 10px 0; }
}
</style>
</head>
<body>
<div class="admin-wrap">
  <?php require_once 'partials/sidebar.php'; ?>
  <div class="admin-main">
    <?php require_once 'partials/topbar.php'; ?>
    <div class="admin-content">

      <!-- Stat Strip -->
      <div class="stat-strip">
        <a href="dashboard.php" class="stat-tile <?= !$status_filter ? 'active' : '' ?>">
          <div class="st-val"><?= $total ?></div>
          <div class="st-label">All Leads</div>
        </a>
        <a href="dashboard.php?status=new" class="stat-tile <?= $status_filter === 'new' ? 'active' : '' ?>">
          <div class="st-val"><?= $ct['new'] ?? 0 ?></div>
          <div class="st-label">New</div>
        </a>
        <a href="dashboard.php?status=contacted" class="stat-tile <?= $status_filter === 'contacted' ? 'active' : '' ?>">
          <div class="st-val"><?= $ct['contacted'] ?? 0 ?></div>
          <div class="st-label">Contacted</div>
        </a>
        <a href="dashboard.php?status=confirmed" class="stat-tile <?= $status_filter === 'confirmed' ? 'active' : '' ?>">
          <div class="st-val"><?= $ct['confirmed'] ?? 0 ?></div>
          <div class="st-label">Confirmed</div>
        </a>
        <a href="dashboard.php?status=cancelled" class="stat-tile <?= $status_filter === 'cancelled' ? 'active' : '' ?>">
          <div class="st-val"><?= $ct['cancelled'] ?? 0 ?></div>
          <div class="st-label">Cancelled</div>
        </a>
      </div>

      <!-- Filter -->
      <form method="GET" class="filter-row">
        <input type="text" name="q" placeholder="Search name, phone, destination…" value="<?= htmlspecialchars($search) ?>"/>
        <select name="status">
          <option value="">All Statuses</option>
          <?php foreach ($status_labels as $val => $lbl): ?>
          <option value="<?= $val ?>" <?= $status_filter === $val ? 'selected' : '' ?>><?= $lbl ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="filter-btn"><i class="fas fa-search"></i> Search</button>
        <?php if ($search || $status_filter): ?>
        <a href="dashboard.php" class="clear-btn">Clear</a>
        <?php endif; ?>
      </form>

      <!-- Table -->
      <div class="leads-table-wrap">
        <?php if (empty($bookings)): ?>
        <div class="empty-state">
          <i class="fas fa-inbox"></i>
          <p>No leads found.</p>
        </div>
        <?php else: ?>
        <div style="overflow-x:auto">
        <table class="leads-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Traveller</th>
              <th>Route</th>
              <th>Package</th>
              <th>Date</th>
              <th>Status</th>
              <th>Received</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($bookings as $b): ?>
            <tr id="row-<?= $b['id'] ?>">
              <td class="td-id"><?= $b['id'] ?></td>
              <td>
                <div class="td-name"><?= htmlspecialchars($b['name']) ?></div>
                <div class="td-meta"><?= htmlspecialchars($b['phone']) ?></div>
                <?php if ($b['email']): ?>
                <div class="td-meta"><?= htmlspecialchars($b['email']) ?></div>
                <?php endif; ?>
                <?php if ($b['message']): ?>
                <div class="td-msg" onclick="this.classList.toggle('open')">
                  <i class="fas fa-comment-dots"></i>
                  <span><?= htmlspecialchars($b['message']) ?></span>
                </div>
                <?php endif; ?>
              </td>
              <td>
                <div style="color:var(--ink);font-weight:500"><?= htmlspecialchars($b['destination'] ?: '—') ?></div>
                <?php if (!empty($b['pickup'])): ?>
                <div class="td-meta"><i class="fas fa-location-dot" style="font-size:10px;margin-right:3px"></i>from <?= htmlspecialchars($b['pickup']) ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($b['package']): ?>
                <span class="pkg pkg-<?= $b['package'] ?>"><?= $pkg_labels[$b['package']] ?? $b['package'] ?></span>
                <?php else: ?><span style="color:var(--muted)">—</span><?php endif; ?>
              </td>
              <td style="color:var(--muted);font-size:12.5px;white-space:nowrap">
                <?= $b['travel_date'] ? date('d M Y', strtotime($b['travel_date'])) : '—' ?>
              </td>
              <td>
                <select class="status-sel ss-<?= $b['status'] ?>" data-id="<?= $b['id'] ?>" onchange="updateStatus(this)">
                  <?php foreach ($status_labels as $val => $lbl): ?>
                  <option value="<?= $val ?>" <?= $b['status'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td style="color:var(--muted);font-size:12px;white-space:nowrap">
                <?= date('d M Y', strtotime($b['submitted_at'])) ?>
              </td>
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

const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function updateStatus(sel) {
  const id     = sel.dataset.id;
  const status = sel.value;
  const fd     = new FormData();
  fd.append('id', id);
  fd.append('status', status);
  sel.disabled = true;
  fetch('../api/update_status.php', {
    method: 'POST', body: fd,
    headers: { 'X-CSRF-Token': CSRF }
  })
  .then(r => r.json())
  .then(d => {
    sel.disabled = false;
    sel.className = `status-sel ss-${status}`;
    if (!d.ok) alert('Could not update status.');
  })
  .catch(() => { sel.disabled = false; alert('Network error.'); });
}
</script>
</body>
</html>
