<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
if ($_SESSION['admin_user']['role'] !== 'superadmin') { header('Location: dashboard.php'); exit; }

require_once '../includes/vars.php';

$filter_user   = trim($_GET['user'] ?? '');
$filter_action = trim($_GET['action'] ?? '');

$where = []; $params = []; $types = '';
if ($filter_user) {
    $where[] = 'username LIKE ?';
    $params[] = '%' . $filter_user . '%';
    $types .= 's';
}
if ($filter_action) {
    $where[] = 'action = ?';
    $params[] = $filter_action;
    $types .= 's';
}

$sql = 'SELECT * FROM audit_log';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY created_at DESC LIMIT 300';

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$actions_raw = $conn->query('SELECT DISTINCT action FROM audit_log ORDER BY action')->fetch_all(MYSQLI_ASSOC);
$conn->close();

$action_badge = [
    'login'         => 'badge-confirmed',
    'logout'        => 'badge-contacted',
    'photo_upload'  => 'badge-confirmed',
    'photo_delete'  => 'badge-cancelled',
    'status_change' => 'badge-new',
    'user_add'      => 'badge-new',
    'user_delete'   => 'badge-cancelled',
    'user_edit'     => 'badge-contacted',
];

$page_title = 'Audit Log';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Audit Log — Himachal Safar Admin</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Poppins:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
<link href="assets/admin.css?v=<?php echo @filemtime(__DIR__ . '/assets/admin.css'); ?>" rel="stylesheet"/>
<style>
.log-count{margin-left:auto;font-size:12px;color:var(--muted);white-space:nowrap}
</style>
</head>
<body>
<div class="admin-wrap">
  <?php require_once 'partials/sidebar.php'; ?>
  <div class="admin-main">
    <?php require_once 'partials/topbar.php'; ?>
    <div class="admin-content">

      <!-- Filter Bar -->
      <form method="GET" class="filter-bar">
        <input type="text" name="user" placeholder="Filter by username…" value="<?= htmlspecialchars($filter_user) ?>"/>
        <select name="action">
          <option value="">All Actions</option>
          <?php foreach ($actions_raw as $a): ?>
          <option value="<?= htmlspecialchars($a['action']) ?>" <?= $filter_action===$a['action']?'selected':'' ?>>
            <?= htmlspecialchars(str_replace('_', ' ', strtoupper($a['action']))) ?>
          </option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-filter"><i class="fas fa-magnifying-glass"></i> Filter</button>
        <?php if ($filter_user || $filter_action): ?>
        <a href="audit.php" class="btn-clear">Clear</a>
        <?php endif; ?>
        <span class="log-count"><?= count($logs) ?> entries</span>
      </form>

      <!-- Log Table -->
      <div class="table-wrap">
        <?php if (empty($logs)): ?>
        <div class="empty-state">
          <i class="fas fa-clipboard-list"></i>
          <p>No audit entries yet. Actions will appear here after login, status changes, uploads, etc.</p>
        </div>
        <?php else: ?>
        <div style="overflow-x:auto">
        <table class="admin-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Date / Time</th>
              <th>User</th>
              <th>Action</th>
              <th>Details</th>
              <th>IP Address</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($logs as $log): ?>
          <tr>
            <td style="color:var(--muted);font-size:12px"><?= $log['id'] ?></td>
            <td style="font-size:12px;white-space:nowrap;color:var(--muted)"><?= date('d M Y, H:i:s', strtotime($log['created_at'])) ?></td>
            <td><div class="td-name" style="font-size:13px"><?= htmlspecialchars($log['username'] ?? '—') ?></div></td>
            <td>
              <span class="badge <?= $action_badge[$log['action']] ?? 'badge-new' ?>">
                <?= htmlspecialchars(str_replace('_', ' ', $log['action'])) ?>
              </span>
            </td>
            <td style="font-size:12px;max-width:300px;color:var(--ink)"><?= htmlspecialchars($log['details'] ?? '—') ?></td>
            <td style="font-size:12px;color:var(--muted)"><?= htmlspecialchars($log['ip_address'] ?? '—') ?></td>
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
function openSidebar(){document.getElementById('sidebar').classList.add('open');document.getElementById('sidebarOverlay').classList.add('show');}
function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sidebarOverlay').classList.remove('show');}
</script>
</body>
</html>
