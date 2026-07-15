<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../includes/vars.php';

// Self-migration: serial display order (1 = shown first on the website and in
// the quote calculator). Seeded from the old price-ascending order on upgrade.
try {
  $conn->query("ALTER TABLE vehicles ADD COLUMN sort_order INT NOT NULL DEFAULT 0");
  $conn->query("SET @vn := 0");
  $conn->query("UPDATE vehicles SET sort_order = (@vn := @vn + 1) ORDER BY daily_rate ASC, id ASC");
} catch (mysqli_sql_exception) {} // column already exists

// Renumber every vehicle's serial to a clean 1..N sequence. When $moveId is
// given, that vehicle is slotted in at $pos (clamped) and the rest keep their
// relative order — so "make this one #1" shifts everything else down by one.
function veh_reindex(mysqli $conn, int $moveId = 0, int $pos = 0): void {
  $sql = 'SELECT id FROM vehicles' . ($moveId ? ' WHERE id<>' . $moveId : '') . ' ORDER BY sort_order ASC, id ASC';
  $ids = array_column($conn->query($sql)->fetch_all(MYSQLI_ASSOC), 'id');
  if ($moveId) {
    $pos = $pos >= 1 ? min($pos, count($ids) + 1) : count($ids) + 1;
    array_splice($ids, $pos - 1, 0, [$moveId]);
  }
  $st = $conn->prepare('UPDATE vehicles SET sort_order=? WHERE id=?');
  foreach ($ids as $i => $vid) {
    $n = $i + 1; $vid = (int)$vid;
    $st->bind_param('ii', $n, $vid);
    $st->execute();
  }
  $st->close();
}

// One-time cleanup: retire the demo vehicles the old setup script seeded, so
// the website and quote calculator only ever offer admin-added vehicles. Only
// rows still byte-identical to the seed (never renamed, re-priced, or given a
// photo) are touched; they stay listed below as Inactive for reference and can
// be re-activated or deleted here.
try {
  $done = $conn->query("SELECT setting_value FROM settings WHERE setting_key='seed_vehicles_retired'")->fetch_row()[0] ?? '';
  if ($done !== '1') {
    $seedRows = [
      [1, 'Swift Dzire', '4+1', 2500], [2, 'Toyota Etios', '4+1', 2500], [3, 'Honda Amaze', '4+1', 2600],
      [4, 'Ertiga', '6+1', 3000], [5, 'Innova', '7+1', 3500], [6, 'Innova Crysta', '7+1', 4000],
      [7, 'Urbania', '10+1', 6000], [8, 'Tempo Traveller (12 Str)', '12+1', 7000], [9, 'Tempo Traveller (17 Str)', '17+1', 8500],
    ];
    $st = $conn->prepare("UPDATE vehicles SET status='inactive'
                          WHERE id=? AND vehicle_name=? AND seating_capacity=? AND daily_rate=?
                            AND (photo IS NULL OR photo='') AND status='active'");
    $retired = 0;
    foreach ($seedRows as [$sid, $sname, $sseats, $srate]) {
      $st->bind_param('issd', $sid, $sname, $sseats, $srate);
      $st->execute();
      $retired += $st->affected_rows;
    }
    $st->close();
    $conn->query("INSERT INTO settings (setting_key, setting_value) VALUES ('seed_vehicles_retired','1')
                  ON DUPLICATE KEY UPDATE setting_value='1'");
    if ($retired > 0) audit_log('vehicles_seed_cleanup', "Retired $retired demo seed vehicle(s)");
  }
} catch (mysqli_sql_exception) {}

$csrf = admin_csrf_token();
$msg  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    // Body exceeded PHP's post_max_size (a huge photo) — PHP dropped the whole
    // form, so without this check the admin would see a baffling CSRF error.
    $msg = 'error:That photo is too large for the server — nothing was saved. Please use a photo under 5 MB and try again.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id    = (int)($_POST['id'] ?? 0);
        $name  = trim($_POST['vehicle_name'] ?? '');
        $seats = trim($_POST['seating_capacity'] ?? '');
        $rate  = max(0, (float)($_POST['daily_rate'] ?? 0));
        $order = (int)($_POST['sort_order'] ?? 0);   // requested serial; 0 → last
        $status= $_POST['status'] === 'inactive' ? 'inactive' : 'active';

        // Optional photo upload (shown on the public website fleet section)
        $vehDir   = dirname(__DIR__) . '/uploads/photos/';
        $photo    = null;        // new filename if one was uploaded
        $photoErr = '';
        $f = $_FILES['photo'] ?? null;
        if ($f && ($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if ($f['error'] !== UPLOAD_ERR_OK)            { $photoErr = 'Photo upload failed.'; }
            elseif ($f['size'] > 5 * 1024 * 1024)        { $photoErr = 'Photo too large (max 5 MB).'; }
            else {
                $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
                $ext  = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime] ?? null;
                if (!$ext) { $photoErr = 'Only JPG, PNG or WebP allowed.'; }
                else {
                    if (!is_dir($vehDir)) mkdir($vehDir, 0755, true);
                    $photo = 'veh_' . bin2hex(random_bytes(8)) . '.' . $ext;
                    if (move_uploaded_file($f['tmp_name'], $vehDir . $photo)) {
                        if (!optimize_image_for_website($vehDir . $photo, $mime, 400 * 1024, 1200, 900)) {
                            @unlink($vehDir . $photo);
                            $photo = null;
                            $photoErr = 'Could not optimize photo under 400 KB. Please upload a smaller JPG/WebP image.';
                        }
                    } else { $photo = null; $photoErr = 'Could not save photo.'; }
                }
            }
        }

        // A failed photo must never cost the admin the whole vehicle — save it
        // anyway (without the photo) and explain what happened.
        if (!$name || !$seats) { $msg = 'error:Vehicle name and seating capacity are required.'; }
        else {
            $isNew = !$id;
            if ($id) {
                if ($photo !== null) {
                    $old = $conn->query('SELECT photo FROM vehicles WHERE id=' . (int)$id)->fetch_assoc()['photo'] ?? '';
                    $s = $conn->prepare('UPDATE vehicles SET vehicle_name=?,photo=?,seating_capacity=?,daily_rate=?,status=? WHERE id=?');
                    $s->bind_param('sssdsi', $name, $photo, $seats, $rate, $status, $id);
                    $s->execute(); $s->close();
                    if ($old && $old !== $photo && is_file($vehDir . $old)) @unlink($vehDir . $old);
                } else {
                    $s = $conn->prepare('UPDATE vehicles SET vehicle_name=?,seating_capacity=?,daily_rate=?,status=? WHERE id=?');
                    $s->bind_param('ssdsi', $name, $seats, $rate, $status, $id);
                    $s->execute(); $s->close();
                }
            } else {
                $s = $conn->prepare('INSERT INTO vehicles (vehicle_name,photo,seating_capacity,daily_rate,status) VALUES (?,?,?,?,?)');
                $s->bind_param('sssds', $name, $photo, $seats, $rate, $status);
                $s->execute();
                $id = $conn->insert_id;
                $s->close();
            }
            veh_reindex($conn, $id, $order);
            audit_log('vehicles_save', ($isNew ? 'Added' : 'Updated') . " vehicle: $name");
            $msg = $photoErr
                ? 'warn:Vehicle saved, but the photo was skipped — ' . $photoErr . ' Edit the vehicle to try another photo.'
                : 'ok:Vehicle saved successfully.';
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $old = $conn->query('SELECT photo FROM vehicles WHERE id=' . $id)->fetch_assoc()['photo'] ?? '';
            $s = $conn->prepare('DELETE FROM vehicles WHERE id=?');
            $s->bind_param('i', $id); $s->execute(); $s->close();
            if ($old && is_file(dirname(__DIR__) . '/uploads/photos/' . $old)) @unlink(dirname(__DIR__) . '/uploads/photos/' . $old);
            veh_reindex($conn);   // close the gap so serials stay 1..N
            audit_log('vehicles_delete', "Deleted vehicle id=$id");
            $msg = 'ok:Vehicle deleted.';
        }
    }

    if ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) { $conn->query("UPDATE vehicles SET status=IF(status='active','inactive','active') WHERE id=$id"); $msg='ok:Status updated.'; }
    }
}

$vehicles = $conn->query("SELECT * FROM vehicles ORDER BY sort_order ASC, id ASC")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?= h($csrf) ?>">
<title>Vehicles — Admin</title>
<link rel="icon" href="../favicon.ico" sizes="32x32">
<link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="../assets/brand/mark-192.png">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="assets/admin.css?v=<?php echo @filemtime(__DIR__ . '/assets/admin.css'); ?>" rel="stylesheet">
<style>
.veh-table th { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); }
.veh-table td { font-size:13.5px; vertical-align:middle; }
.seat-badge { display:inline-flex; align-items:center; gap:4px; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:700; background:rgba(214,199,161,.10); color:#B8A16A; border:1px solid rgba(214,199,161,.20); }
.status-dot { width:8px; height:8px; border-radius:50%; display:inline-block; }
.status-dot.active   { background:#22c55e; }
.status-dot.inactive { background:#6b7280; }
.serial-chip { display:inline-grid; place-items:center; min-width:28px; height:28px; padding:0 6px; border-radius:9px; font-size:12px; font-weight:700; color:var(--accent); background:rgba(200,167,93,.10); border:1px solid rgba(200,167,93,.25); }
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
  <div class="alert alert-<?= $type==='ok'?'success':($type==='warn'?'warning':'danger') ?> alert-dismissible fade show py-2 mb-3">
    <?= h($text) ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <div class="admin-card">
    <div class="table-responsive">
      <table class="table veh-table mb-0">
        <thead>
          <tr>
            <th title="Serial — 1 shows first on the website">S.No</th>
            <th>Photo</th>
            <th>Vehicle</th>
            <th>Seating</th>
            <th>Daily Rate</th>
            <th>Status</th>
            <th style="width:130px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$vehicles): ?>
          <tr><td colspan="7" class="text-center py-4" style="color:var(--muted)">No vehicles yet.</td></tr>
          <?php endif; ?>
          <?php foreach ($vehicles as $v): ?>
          <tr>
            <td><span class="serial-chip"><?= (int)$v['sort_order'] ?></span></td>
            <td>
              <?php if (!empty($v['photo'])): ?>
                <img src="../uploads/photos/<?= h($v['photo']) ?>" alt="" style="width:64px;height:42px;object-fit:cover;border-radius:6px;border:1px solid var(--border)">
              <?php else: ?>
                <span style="display:grid;place-items:center;width:64px;height:42px;border-radius:6px;border:1px dashed var(--border);color:var(--muted)"><i class="fa-solid fa-car-side"></i></span>
              <?php endif; ?>
            </td>
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
      <form method="post" enctype="multipart/form-data">
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
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Serial No. (1 = shown first)</label>
              <input type="number" name="sort_order" id="veh_order" class="form-control admin-input" min="1" step="1">
              <small style="color:var(--muted);font-size:11px">Last serial: <?= count($vehicles) ?>. Give any position — the others shift automatically.</small>
            </div>
            <div class="col-6">
              <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Status</label>
              <select name="status" id="veh_status" class="form-select admin-input">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div>
            <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Vehicle Photo <span style="font-weight:400">(shown on the website fleet)</span></label>
            <input type="file" name="photo" id="veh_photo" class="form-control admin-input" accept="image/jpeg,image/png,image/webp" data-shrink>
            <div id="veh_photo_preview" style="margin-top:8px"></div>
            <small style="color:var(--muted);font-size:11px">JPG/PNG/WebP, saved under 400 KB. Leave empty to keep the current photo. A clear side-on shot works best.</small>
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
const VEH_COUNT = <?= count($vehicles) ?>;
function openModal(v) {
  document.getElementById('vehModalTitle').textContent = v ? 'Edit Vehicle' : 'Add Vehicle';
  document.getElementById('veh_id').value    = v?.id    || 0;
  document.getElementById('veh_name').value  = v?.vehicle_name || '';
  document.getElementById('veh_seats').value = v?.seating_capacity || '';
  document.getElementById('veh_rate').value  = v?.daily_rate || 3000;
  // Editing keeps the current serial; a new vehicle lands after the last one.
  document.getElementById('veh_order').value = v?.sort_order || (VEH_COUNT + 1);
  document.getElementById('veh_status').value= v?.status || 'active';
  document.getElementById('veh_photo').value = '';
  document.getElementById('veh_photo_preview').innerHTML = v?.photo
    ? `<img src="../uploads/photos/${v.photo}" alt="" style="height:60px;border-radius:6px;border:1px solid var(--border)"> <span style="color:var(--muted);font-size:11px;margin-left:6px">current photo</span>`
    : '<span style="color:var(--muted);font-size:12px">No photo yet</span>';
  modal.show();
}
</script>
<script src="assets/photo-shrink.js?v=<?php echo @filemtime(__DIR__ . '/assets/photo-shrink.js'); ?>"></script>
</body>
</html>
