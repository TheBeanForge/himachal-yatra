<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../api/config.php';

$csrf = admin_csrf_token();
$msg  = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id       = (int)($_POST['id'] ?? 0);
        $name     = trim($_POST['package_name'] ?? '');
        $days     = max(1, (int)($_POST['duration_days'] ?? 1));
        $price    = max(0, (float)($_POST['base_price_per_day'] ?? 0));
        $desc     = trim($_POST['description'] ?? '');
        $dest_key = trim($_POST['destination_key'] ?? '');
        $status   = $_POST['status'] === 'inactive' ? 'inactive' : 'active';

        if (!$name) { $msg = 'error:Package name is required.'; }
        else {
            if ($id) {
                $s = $conn->prepare('UPDATE tour_packages SET package_name=?,duration_days=?,base_price_per_day=?,description=?,destination_key=?,status=? WHERE id=?');
                $s->bind_param('sidsssi', $name, $days, $price, $desc, $dest_key, $status, $id);
            } else {
                $s = $conn->prepare('INSERT INTO tour_packages (package_name,duration_days,base_price_per_day,description,destination_key,status) VALUES (?,?,?,?,?,?)');
                $s->bind_param('sidsss', $name, $days, $price, $desc, $dest_key, $status);
            }
            $s->execute(); $s->close();
            audit_log('packages_save', ($id ? "Updated" : "Added") . " package: $name");
            $msg = 'ok:Package saved successfully.';
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $s = $conn->prepare('DELETE FROM tour_packages WHERE id=?');
            $s->bind_param('i', $id);
            $s->execute(); $s->close();
            audit_log('packages_delete', "Deleted package id=$id");
            $msg = 'ok:Package deleted.';
        }
    }

    if ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $conn->query("UPDATE tour_packages SET status = IF(status='active','inactive','active') WHERE id=$id");
            $msg = 'ok:Status updated.';
        }
    }
}

$packages = $conn->query("SELECT * FROM tour_packages ORDER BY package_name ASC")->fetch_all(MYSQLI_ASSOC);
$conn->close();

$DESTS = [''=>'All / Multi', 'manali'=>'Manali', 'shimla'=>'Shimla', 'dharamshala'=>'Dharamshala', 'dalhousie'=>'Dalhousie', 'spiti'=>'Spiti'];
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?= h($csrf) ?>">
<title>Tour Packages — Admin</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="assets/admin.css" rel="stylesheet">
<style>
.pkg-table th { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); }
.pkg-table td { font-size:13.5px; vertical-align:middle; }
.dest-badge { display:inline-flex; align-items:center; gap:4px; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:700; background:var(--gold-light,rgba(200,167,93,.1)); color:#c8a75d; border:1px solid rgba(200,167,93,.2); }
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
      <h1 class="admin-page-title mb-0">Tour Packages</h1>
      <p class="admin-page-sub mb-0">Manage packages available in the Plan My Journey calculator</p>
    </div>
    <button class="btn btn-primary-gold" onclick="openModal(null)">
      <i class="fas fa-plus"></i> Add Package
    </button>
  </div>

  <?php if ($msg): [$type, $text] = explode(':', $msg, 2); ?>
  <div class="alert alert-<?= $type==='ok'?'success':'danger' ?> alert-dismissible fade show py-2 mb-3" role="alert">
    <?= h($text) ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <div class="admin-card">
    <div class="table-responsive">
      <table class="table pkg-table mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Package Name</th>
            <th>Destination</th>
            <th>Days</th>
            <th>Base Price/Day</th>
            <th>Status</th>
            <th style="width:130px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$packages): ?>
          <tr><td colspan="7" class="text-center py-4" style="color:var(--muted)">No packages yet. <a href="#" onclick="openModal(null)">Add one.</a></td></tr>
          <?php endif; ?>
          <?php foreach ($packages as $p): ?>
          <tr>
            <td style="color:var(--muted)"><?= (int)$p['id'] ?></td>
            <td>
              <div style="font-weight:600;color:var(--ink)"><?= h($p['package_name']) ?></div>
              <?php if ($p['description']): ?><div style="font-size:12px;color:var(--muted);margin-top:2px"><?= h(mb_strimwidth($p['description'],0,60,'…')) ?></div><?php endif; ?>
            </td>
            <td><span class="dest-badge"><?= h($DESTS[$p['destination_key']??''] ?? ($p['destination_key']?:'—')) ?></span></td>
            <td><?= (int)$p['duration_days'] ?> days</td>
            <td>₹<?= number_format((float)$p['base_price_per_day'],0) ?>/day</td>
            <td>
              <span class="status-dot <?= $p['status'] ?>"></span>
              <?= $p['status']==='active' ? 'Active' : 'Inactive' ?>
            </td>
            <td>
              <div class="d-flex gap-1">
                <button class="btn btn-sm btn-icon" title="Edit" onclick='openModal(<?= json_encode($p) ?>)'>
                  <i class="fas fa-pen"></i>
                </button>
                <form method="post" style="display:inline" onsubmit="return confirm('Toggle status?')">
                  <input type="hidden" name="action" value="toggle">
                  <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                  <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
                  <button class="btn btn-sm btn-icon" title="Toggle" type="submit"><i class="fas fa-power-off"></i></button>
                </form>
                <form method="post" style="display:inline" onsubmit="return confirm('Delete this package?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                  <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
                  <button class="btn btn-sm btn-icon btn-icon-danger" title="Delete" type="submit"><i class="fas fa-trash"></i></button>
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
<div class="modal fade" id="pkgModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="background:var(--surface);border:1px solid var(--border)">
      <form method="post">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
        <input type="hidden" name="id" id="pkg_id" value="0">
        <div class="modal-header" style="border-color:var(--border)">
          <h5 class="modal-title" id="pkgModalTitle" style="color:var(--ink)">Add Package</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1)"></button>
        </div>
        <div class="modal-body d-flex flex-column gap-3">
          <div>
            <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Package Name *</label>
            <input type="text" name="package_name" id="pkg_name" class="form-control admin-input" required maxlength="150">
          </div>
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Duration (Days)</label>
              <input type="number" name="duration_days" id="pkg_days" class="form-control admin-input" value="3" min="1" max="30">
            </div>
            <div class="col-6">
              <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Base Price/Day (₹/person)</label>
              <input type="number" name="base_price_per_day" id="pkg_price" class="form-control admin-input" value="2000" min="0" step="50">
            </div>
          </div>
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Primary Destination</label>
              <select name="destination_key" id="pkg_dest" class="form-select admin-input">
                <?php foreach ($DESTS as $k => $v): ?>
                <option value="<?= h($k) ?>"><?= h($v) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Status</label>
              <select name="status" id="pkg_status" class="form-select admin-input">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div>
            <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Description / Highlights</label>
            <input type="text" name="description" id="pkg_desc" class="form-control admin-input" maxlength="300" placeholder="e.g. Shimla • Kufri • Manali • Rohtang Pass">
          </div>
        </div>
        <div class="modal-footer" style="border-color:var(--border)">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary-gold">Save Package</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const modal = new bootstrap.Modal(document.getElementById('pkgModal'));
function openModal(pkg) {
  document.getElementById('pkgModalTitle').textContent = pkg ? 'Edit Package' : 'Add Package';
  document.getElementById('pkg_id').value    = pkg?.id    || 0;
  document.getElementById('pkg_name').value  = pkg?.package_name || '';
  document.getElementById('pkg_days').value  = pkg?.duration_days || 3;
  document.getElementById('pkg_price').value = pkg?.base_price_per_day || 2000;
  document.getElementById('pkg_desc').value  = pkg?.description || '';
  document.getElementById('pkg_dest').value  = pkg?.destination_key || '';
  document.getElementById('pkg_status').value= pkg?.status || 'active';
  modal.show();
}
</script>
</body>
</html>
