<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../api/config.php';

$csrf = admin_csrf_token();
$msg  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id    = (int)($_POST['id'] ?? 0);
        $name  = trim($_POST['vehicle_name'] ?? '');
        $seats = trim($_POST['seating_capacity'] ?? '');
        $rate  = max(0, (float)($_POST['daily_rate'] ?? 0));
        $status= $_POST['status'] === 'inactive' ? 'inactive' : 'active';

        if (!$name || !$seats) { $msg = 'error:Vehicle name and seating capacity are required.'; }
        else {
            if ($id) {
                $s = $conn->prepare('UPDATE vehicles SET vehicle_name=?,seating_capacity=?,daily_rate=?,status=? WHERE id=?');
                $s->bind_param('ssdsi', $name, $seats, $rate, $status, $id);
            } else {
                $s = $conn->prepare('INSERT INTO vehicles (vehicle_name,seating_capacity,daily_rate,status) VALUES (?,?,?,?)');
                $s->bind_param('ssds', $name, $seats, $rate, $status);
            }
            $s->execute(); $s->close();
            audit_log('vehicles_save', ($id ? 'Updated' : 'Added') . " vehicle: $name");
            $msg = 'ok:Vehicle saved successfully.';
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $s = $conn->prepare('DELETE FROM vehicles WHERE id=?');
            $s->bind_param('i', $id); $s->execute(); $s->close();
            audit_log('vehicles_delete', "Deleted vehicle id=$id");
            $msg = 'ok:Vehicle deleted.';
        }
    }

    if ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) { $conn->query("UPDATE vehicles SET status=IF(status='active','inactive','active') WHERE id=$id"); $msg='ok:Status updated.'; }
    }
}

$vehicles = $conn->query("SELECT * FROM vehicles ORDER BY daily_rate ASC")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?= h($csrf) ?>">
<title>Vehicles — Admin</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="assets/admin.css" rel="stylesheet">
<style>
.veh-table th { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); }
.veh-table td { font-size:13.5px; vertical-align:middle; }
.seat-badge { display:inline-flex; align-items:center; gap:4px; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:700; background:rgba(59,130,246,.1); color:#3b82f6; border:1px solid rgba(59,130,246,.2); }
.status-dot { width:8px; height:8px; border-radius:50%; display:inline-block; }
.status-dot.active   { background:#22c55e; }
.status-dot.inactive { background:#6b7280; }
</style>
</head>
<body>
<?php require 'partials/topbar.php'; ?>
<div class="d-flex">
<?php require 'partials/sidebar.php'; ?>
<main class="admin-main flex-grow-1 p-4">

  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="admin-page-title mb-0">Vehicles</h1>
      <p class="admin-page-sub mb-0">Manage vehicles shown in the Plan My Journey calculator</p>
    </div>
    <button class="btn btn-primary-gold" onclick="openModal(null)">
      <i class="fas fa-plus"></i> Add Vehicle
    </button>
  </div>

  <?php if ($msg): [$type, $text] = explode(':', $msg, 2); ?>
  <div class="alert alert-<?= $type==='ok'?'success':'danger' ?> alert-dismissible fade show py-2 mb-3">
    <?= h($text) ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <div class="admin-card">
    <div class="table-responsive">
      <table class="table veh-table mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Vehicle</th>
            <th>Seating</th>
            <th>Daily Rate</th>
            <th>Status</th>
            <th style="width:130px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$vehicles): ?>
          <tr><td colspan="6" class="text-center py-4" style="color:var(--muted)">No vehicles yet.</td></tr>
          <?php endif; ?>
          <?php foreach ($vehicles as $v): ?>
          <tr>
            <td style="color:var(--muted)"><?= (int)$v['id'] ?></td>
            <td style="font-weight:600;color:var(--ink)"><i class="fa-solid fa-car-side me-1" style="color:#c8a75d;font-size:12px"></i><?= h($v['vehicle_name']) ?></td>
            <td><span class="seat-badge"><i class="fa-solid fa-users" style="font-size:10px"></i><?= h($v['seating_capacity']) ?></span></td>
            <td>₹<?= number_format((float)$v['daily_rate'],0) ?>/day</td>
            <td>
              <span class="status-dot <?= $v['status'] ?>"></span>
              <?= $v['status']==='active' ? 'Active' : 'Inactive' ?>
            </td>
            <td>
              <div class="d-flex gap-1">
                <button class="btn btn-sm btn-icon" title="Edit" onclick='openModal(<?= json_encode($v) ?>)'>
                  <i class="fas fa-pen"></i>
                </button>
                <form method="post" style="display:inline" onsubmit="return confirm('Toggle status?')">
                  <input type="hidden" name="action" value="toggle">
                  <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
                  <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
                  <button class="btn btn-sm btn-icon" type="submit"><i class="fas fa-power-off"></i></button>
                </form>
                <form method="post" style="display:inline" onsubmit="return confirm('Delete this vehicle?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
                  <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
                  <button class="btn btn-sm btn-icon btn-icon-danger" type="submit"><i class="fas fa-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
</div>

<!-- Modal -->
<div class="modal fade" id="vehModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="background:var(--surface);border:1px solid var(--border)">
      <form method="post">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
        <input type="hidden" name="id" id="veh_id" value="0">
        <div class="modal-header" style="border-color:var(--border)">
          <h5 class="modal-title" id="vehModalTitle" style="color:var(--ink)">Add Vehicle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1)"></button>
        </div>
        <div class="modal-body d-flex flex-column gap-3">
          <div>
            <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Vehicle Name *</label>
            <input type="text" name="vehicle_name" id="veh_name" class="form-control admin-input" required maxlength="100" placeholder="e.g. Innova Crysta">
          </div>
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Seating Capacity *</label>
              <input type="text" name="seating_capacity" id="veh_seats" class="form-control admin-input" required maxlength="20" placeholder="e.g. 7+1">
            </div>
            <div class="col-6">
              <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Daily Rate (₹/day)</label>
              <input type="number" name="daily_rate" id="veh_rate" class="form-control admin-input" value="3000" min="0" step="100">
            </div>
          </div>
          <div>
            <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Status</label>
            <select name="status" id="veh_status" class="form-select admin-input">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer" style="border-color:var(--border)">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary-gold">Save Vehicle</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const modal = new bootstrap.Modal(document.getElementById('vehModal'));
function openModal(v) {
  document.getElementById('vehModalTitle').textContent = v ? 'Edit Vehicle' : 'Add Vehicle';
  document.getElementById('veh_id').value    = v?.id    || 0;
  document.getElementById('veh_name').value  = v?.vehicle_name || '';
  document.getElementById('veh_seats').value = v?.seating_capacity || '';
  document.getElementById('veh_rate').value  = v?.daily_rate || 3000;
  document.getElementById('veh_status').value= v?.status || 'active';
  modal.show();
}
</script>
</body>
</html>
