<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../includes/vars.php';

$role = $_SESSION['admin_user']['role'] ?? '';
if (!in_array($role, ['superadmin', 'admin'])) { header('Location: dashboard.php'); exit; }

$csrf = admin_csrf_token();
$msg  = '';

// Ensure the columns this page needs exist (older installs may lack them).
try { $conn->query("ALTER TABLE tour_packages ADD COLUMN sort_order INT NOT NULL DEFAULT 0"); } catch (Throwable) {}
try { $conn->query("UPDATE tour_packages SET sort_order = id WHERE sort_order = 0"); } catch (Throwable) {}
try { $conn->query("ALTER TABLE tour_packages ADD COLUMN photo VARCHAR(255) NULL"); } catch (Throwable) {}
try { $conn->query("ALTER TABLE tour_packages ADD COLUMN description VARCHAR(300) NULL"); } catch (Throwable) {}
try { $conn->query("ALTER TABLE tour_packages ADD COLUMN duration_days INT NOT NULL DEFAULT 0"); } catch (Throwable) {}
try { $conn->query("ALTER TABLE tour_packages ADD COLUMN duration_nights INT NOT NULL DEFAULT 0"); } catch (Throwable) {}
try { $conn->query("ALTER TABLE tour_packages ADD COLUMN is_bestseller TINYINT(1) NOT NULL DEFAULT 0"); } catch (Throwable) {}
try { $conn->query("ALTER TABLE tour_packages ADD COLUMN category VARCHAR(60) NULL"); } catch (Throwable) {}
try { $conn->query("ALTER TABLE tour_packages ADD COLUMN destination_key VARCHAR(50) NULL"); } catch (Throwable) {}

// Seed the starter set on a fresh, empty table.
try {
    $empty = (int)($conn->query("SELECT COUNT(*) c FROM tour_packages")->fetch_assoc()['c'] ?? 0) === 0;
    if ($empty) {
        $seed = [
            '5N/6D Shimla Manali Taxi Tour',
            '9N/10D Kinnaur Spiti Taxi Tour',
            '5N/6D Amritsar Dharamshala Dalhousie Taxi Tour',
            '4N/5D Pathankot Dharamshala Dalhousie Taxi Tour',
            '9N/10D Complete Himachal Taxi Tour',
            '8N/9D Leh & Ladakh Taxi Tour',
        ];
        $st = $conn->prepare("INSERT INTO tour_packages (package_name, sort_order) VALUES (?, ?)");
        foreach ($seed as $i => $nm) { $pos = $i + 1; $st->bind_param('si', $nm, $pos); $st->execute(); }
        $st->close();
    }
} catch (Throwable) {}

$pkgDir = dirname(__DIR__) . '/uploads/photos/';

if (!function_exists('package_dummy_image')) {
function package_dummy_image(array $package, int $index): string {
    $seed = (int)($package['id'] ?? 0);
    if ($seed <= 0) $seed = $index + 1;
    $slot = (($seed - 1) % 6) + 1;
    return '../assets/photos/package-placeholder-' . $slot . '.svg';
}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id     = (int)($_POST['id'] ?? 0);
        $name   = mb_substr(trim($_POST['package_name'] ?? ''), 0, 150);
        $desc   = mb_substr(trim($_POST['description'] ?? ''), 0, 300);
        $category = mb_substr(trim($_POST['category'] ?? ''), 0, 60);
        $days   = max(0, min(99, (int)($_POST['duration_days'] ?? 0)));
        $nights = max(0, min(99, (int)($_POST['duration_nights'] ?? 0)));
        $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';

        // Optional photo (shown on the public Tour Packages page).
        $photo = null; $photoErr = '';
        $f = $_FILES['photo'] ?? null;
        if ($f && ($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if ($f['error'] !== UPLOAD_ERR_OK)    { $photoErr = 'Photo upload failed.'; }
            elseif ($f['size'] > 5 * 1024 * 1024) { $photoErr = 'Photo too large (max 5 MB).'; }
            else {
                $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
                $ext  = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime] ?? null;
                if (!$ext) { $photoErr = 'Only JPG, PNG or WebP allowed.'; }
                else {
                    if (!is_dir($pkgDir)) mkdir($pkgDir, 0755, true);
                    $photo = 'pkg_' . bin2hex(random_bytes(8)) . '.' . $ext;
                    if (move_uploaded_file($f['tmp_name'], $pkgDir . $photo)) {
                        if (!optimize_image_for_website($pkgDir . $photo, $mime, 400 * 1024, 1280, 960)) {
                            @unlink($pkgDir . $photo); $photo = null;
                            $photoErr = 'Could not optimize photo under 400 KB. Please upload a smaller image.';
                        }
                    } else { $photo = null; $photoErr = 'Could not save photo.'; }
                }
            }
        }

        if ($name === '') {
            $msg = 'error:Package name is required.';
        } elseif ($photoErr) {
            $msg = 'error:' . $photoErr;
        } elseif ($id) {
            if ($photo !== null) {
                $old = $conn->query('SELECT photo FROM tour_packages WHERE id=' . (int)$id)->fetch_assoc()['photo'] ?? '';
                $s = $conn->prepare('UPDATE tour_packages SET package_name=?, description=?, category=?, status=?, photo=?, duration_days=?, duration_nights=? WHERE id=?');
                $s->bind_param('sssssiii', $name, $desc, $category, $status, $photo, $days, $nights, $id);
                $s->execute(); $s->close();
                if ($old && $old !== $photo && is_file($pkgDir . $old)) @unlink($pkgDir . $old);
            } else {
                $s = $conn->prepare('UPDATE tour_packages SET package_name=?, description=?, category=?, status=?, duration_days=?, duration_nights=? WHERE id=?');
                $s->bind_param('ssssiii', $name, $desc, $category, $status, $days, $nights, $id);
                $s->execute(); $s->close();
            }
            audit_log('packages_save', "Updated package: $name");
            $msg = 'ok:Package updated.';
        } else {
            $next = (int)($conn->query("SELECT COALESCE(MAX(sort_order),0)+1 n FROM tour_packages")->fetch_assoc()['n'] ?? 1);
            $s = $conn->prepare('INSERT INTO tour_packages (package_name, description, category, status, photo, sort_order, duration_days, duration_nights) VALUES (?,?,?,?,?,?,?,?)');
            $s->bind_param('sssssiii', $name, $desc, $category, $status, $photo, $next, $days, $nights);
            $s->execute(); $s->close();
            audit_log('packages_save', "Added package: $name");
            $msg = 'ok:Package added.';
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $old = $conn->query('SELECT photo FROM tour_packages WHERE id=' . $id)->fetch_assoc()['photo'] ?? '';
            $s = $conn->prepare('DELETE FROM tour_packages WHERE id=?');
            $s->bind_param('i', $id); $s->execute(); $s->close();
            if ($old && is_file($pkgDir . $old)) @unlink($pkgDir . $old);
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

    if ($action === 'bestseller') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $conn->query("UPDATE tour_packages SET is_bestseller = IF(is_bestseller=1,0,1) WHERE id=$id");
            $msg = 'ok:Bestseller updated.';
        }
    }

    if ($action === 'move') {
        $id  = (int)($_POST['id'] ?? 0);
        $dir = $_POST['dir'] === 'up' ? 'up' : 'down';
        if ($id) {
            $cur = $conn->query("SELECT sort_order FROM tour_packages WHERE id=$id")->fetch_assoc();
            if ($cur) {
                $so = (int)$cur['sort_order'];
                $cmp = $dir === 'up' ? '<' : '>';
                $ord = $dir === 'up' ? 'DESC' : 'ASC';
                $nb = $conn->query("SELECT id, sort_order FROM tour_packages WHERE sort_order $cmp $so ORDER BY sort_order $ord LIMIT 1")->fetch_assoc();
                if ($nb) {
                    $conn->query("UPDATE tour_packages SET sort_order=" . (int)$nb['sort_order'] . " WHERE id=$id");
                    $conn->query("UPDATE tour_packages SET sort_order=$so WHERE id=" . (int)$nb['id']);
                }
            }
            $msg = 'ok:Order updated.';
        }
    }
}

$packages = $conn->query("SELECT * FROM tour_packages ORDER BY sort_order ASC, id ASC")->fetch_all(MYSQLI_ASSOC);
$conn->close();
$csrf = htmlspecialchars($csrf);
$page_title = 'Tour Packages';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<meta name="csrf-token" content="<?= $csrf ?>"/>
<title>Tour Packages — Himachal Safar Admin</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Poppins:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
<link href="assets/admin.css?v=<?php echo @filemtime(__DIR__ . '/assets/admin.css'); ?>" rel="stylesheet"/>
<style>
.pk-h1 { font-family:'Poppins',sans-serif; font-size:22px; font-weight:800; letter-spacing:-.01em; color:var(--ink); margin:0 0 3px; }
.pk-sub { font-size:13.5px; color:var(--muted); margin:0; }
.pk-input { height:44px; padding:0 14px; width:100%; background:var(--surface-2); border:1.5px solid var(--border); border-radius:8px; color:var(--ink); font:500 14px 'Inter',sans-serif; outline:none; transition:border-color .18s; }
textarea.pk-input { height:auto; padding:12px 14px; resize:vertical; }
.pk-input:focus { border-color:var(--accent); background:var(--surface); }
.pk-addbar { display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:18px; }
.pk-addbar .pk-input { flex:1 1 280px; }
.pk-thumb { width:64px; height:42px; object-fit:cover; border-radius:6px; border:1px solid var(--border); display:block; }
.pk-thumb-ph { width:64px; height:42px; display:grid; place-items:center; border-radius:6px; border:1px dashed var(--border); color:var(--muted); }
.pk-label { color:var(--muted); font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; display:block; margin-bottom:6px; }
.status-dot { width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:6px; }
.status-dot.active { background:#22c55e; } .status-dot.inactive { background:#6b7280; }
</style>
</head>
<body>
<div class="admin-wrap">
  <?php require_once 'partials/sidebar.php'; ?>
  <div class="admin-main">
    <?php require_once 'partials/topbar.php'; ?>
    <div class="admin-content">

      <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
          <h1 class="pk-h1">Tour Packages</h1>
          <p class="pk-sub">These appear on the public <b>Tour Packages</b> page. Add a photo, nights/days &amp; description via Edit. Use the <i class="fas fa-star" style="font-size:10px;color:#eab308"></i> star to feature a package in the <b>Bestsellers</b> section. Reorder with the arrows; inactive packages are hidden.</p>
        </div>
        <a href="../packages.php" target="_blank" class="btn btn-sm btn-icon" title="View page"><i class="fas fa-arrow-up-right-from-square"></i></a>
      </div>

      <?php if ($msg): [$type, $text] = explode(':', $msg, 2); ?>
      <div class="alert alert-<?= $type==='ok'?'success':'danger' ?> alert-dismissible fade show py-2 mb-3">
        <?= htmlspecialchars($text) ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php endif; ?>

      <form method="post" class="pk-addbar">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input class="pk-input" type="text" name="package_name" placeholder="New package name, e.g. 5N/6D Shimla Manali Taxi Tour" maxlength="150" required>
        <button type="submit" class="btn btn-primary-gold"><i class="fas fa-plus me-1"></i> Add Package</button>
      </form>

      <div class="table-wrap">
        <?php if (empty($packages)): ?>
        <div class="empty-state"><i class="fas fa-route"></i><p>No packages yet. Add your first one above.</p></div>
        <?php else: ?>
        <div style="overflow-x:auto">
        <table class="admin-table">
          <thead>
            <tr><th style="width:60px">Order</th><th style="width:80px">Photo</th><th>Package</th><th style="width:120px">Status</th><th style="width:170px">Actions</th></tr>
          </thead>
          <tbody>
          <?php foreach ($packages as $i => $p): $pid=(int)$p['id']; ?>
          <tr>
            <td>
              <div class="d-flex gap-1">
                <form method="post" style="display:inline"><input type="hidden" name="action" value="move"><input type="hidden" name="dir" value="up"><input type="hidden" name="id" value="<?= $pid ?>"><input type="hidden" name="csrf_token" value="<?= $csrf ?>"><button class="btn btn-sm btn-icon" type="submit" <?= $i===0?'disabled':'' ?> title="Move up"><i class="fas fa-arrow-up"></i></button></form>
                <form method="post" style="display:inline"><input type="hidden" name="action" value="move"><input type="hidden" name="dir" value="down"><input type="hidden" name="id" value="<?= $pid ?>"><input type="hidden" name="csrf_token" value="<?= $csrf ?>"><button class="btn btn-sm btn-icon" type="submit" <?= $i===count($packages)-1?'disabled':'' ?> title="Move down"><i class="fas fa-arrow-down"></i></button></form>
              </div>
            </td>
            <td>
              <?php if (!empty($p['photo'])): ?>
                <img class="pk-thumb" src="../uploads/photos/<?= htmlspecialchars($p['photo']) ?>" alt="">
              <?php else: ?>
                <img class="pk-thumb" src="<?= htmlspecialchars(package_dummy_image($p, $i)) ?>" alt="">
              <?php endif; ?>
            </td>
            <td>
              <div style="font-weight:600;color:var(--ink)">
                <?= htmlspecialchars($p['package_name']) ?>
                <?php $nn=(int)($p['duration_nights']??0); $dd=(int)($p['duration_days']??0); if ($nn>0 || $dd>0): ?>
                <span style="margin-left:8px;font-size:11px;font-weight:700;color:var(--accent);background:rgba(201,168,76,.12);border:1px solid rgba(201,168,76,.25);padding:1px 7px;border-radius:6px"><?= $nn>0&&$dd>0 ? "{$dd}D/{$nn}N" : ($dd>0 ? "{$dd}D" : "{$nn}N") ?></span>
                <?php endif; ?>
                <?php if (!empty($p['is_bestseller'])): ?>
                <span style="margin-left:6px;font-size:11px;font-weight:700;color:#b8860b;background:rgba(234,179,8,.14);border:1px solid rgba(234,179,8,.30);padding:1px 8px;border-radius:6px"><i class="fas fa-star" style="font-size:9px"></i> Bestseller</span>
                <?php endif; ?>
              </div>
              <?php if (!empty($p['category'])): ?><div style="margin-top:3px"><span style="font-size:10.5px;font-weight:700;color:#7c6cd0;background:rgba(124,108,208,.12);border:1px solid rgba(124,108,208,.25);padding:1px 8px;border-radius:6px;text-transform:uppercase;letter-spacing:.04em"><?= htmlspecialchars($p['category']) ?></span></div><?php endif; ?>
              <?php if (!empty($p['description'])): ?><div style="color:var(--muted);font-size:12px;max-width:420px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:3px"><?= htmlspecialchars($p['description']) ?></div><?php endif; ?>
            </td>
            <td>
              <span class="status-dot <?= $p['status'] ?>"></span><?= $p['status']==='active'?'Active':'Inactive' ?>
            </td>
            <td>
              <div class="d-flex gap-1">
                <button class="btn btn-sm btn-icon" title="Edit / rename" onclick='editPkg(<?= json_encode(["id"=>$pid,"package_name"=>$p["package_name"],"description"=>$p["description"]??"","category"=>$p["category"]??"","status"=>$p["status"],"photo"=>$p["photo"]??"","duration_days"=>(int)($p["duration_days"]??0),"duration_nights"=>(int)($p["duration_nights"]??0)]) ?>)'><i class="fas fa-pen"></i></button>
                <form method="post" style="display:inline" title="<?= !empty($p['is_bestseller'])?'Remove from bestsellers':'Mark as bestseller' ?>"><input type="hidden" name="action" value="bestseller"><input type="hidden" name="id" value="<?= $pid ?>"><input type="hidden" name="csrf_token" value="<?= $csrf ?>"><button class="btn btn-sm btn-icon" type="submit" style="<?= !empty($p['is_bestseller'])?'color:#eab308':'' ?>"><i class="<?= !empty($p['is_bestseller'])?'fas':'far' ?> fa-star"></i></button></form>
                <form method="post" style="display:inline" title="Toggle active"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $pid ?>"><input type="hidden" name="csrf_token" value="<?= $csrf ?>"><button class="btn btn-sm btn-icon" type="submit"><i class="fas fa-power-off"></i></button></form>
                <form method="post" style="display:inline" onsubmit="return confirm('Delete this package?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $pid ?>"><input type="hidden" name="csrf_token" value="<?= $csrf ?>"><button class="btn btn-sm btn-icon btn-icon-danger" type="submit"><i class="fas fa-trash"></i></button></form>
              </div>
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

<!-- Edit Modal -->
<div class="modal fade" id="pkgModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="background:var(--surface);border:1px solid var(--border)">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="hidden" name="id" id="pk_id" value="0">
        <div class="modal-header" style="border-color:var(--border)">
          <h5 class="modal-title" style="color:var(--ink)">Edit Package</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1)"></button>
        </div>
        <div class="modal-body d-flex flex-column gap-3">
          <div>
            <label class="pk-label">Package Name</label>
            <input class="pk-input" type="text" name="package_name" id="pk_name" maxlength="150" required>
          </div>
          <div class="d-flex gap-3">
            <div style="flex:1">
              <label class="pk-label">Nights</label>
              <input class="pk-input" type="number" name="duration_nights" id="pk_nights" min="0" max="99" value="0">
            </div>
            <div style="flex:1">
              <label class="pk-label">Days</label>
              <input class="pk-input" type="number" name="duration_days" id="pk_days" min="0" max="99" value="0">
            </div>
          </div>
          <div>
            <label class="pk-label">Category <span style="text-transform:none;font-weight:400">(e.g. Honeymoon, Weekend, Anniversary)</span></label>
            <input class="pk-input" type="text" name="category" id="pk_category" list="pkgCatList" maxlength="60" placeholder="Honeymoon Package">
            <datalist id="pkgCatList">
              <option>Honeymoon Package</option>
              <option>Weekend Package</option>
              <option>Anniversary Package</option>
              <option>Family Package</option>
              <option>Adventure Package</option>
              <option>Group Package</option>
              <option>Pilgrimage Package</option>
              <option>Luxury Package</option>
            </datalist>
          </div>
          <div>
            <label class="pk-label">Description <span style="text-transform:none;font-weight:400">(shown on the card)</span></label>
            <textarea class="pk-input" name="description" id="pk_desc" rows="3" maxlength="300" placeholder="Short itinerary summary, e.g. Shimla • Kufri • Manali • Rohtang • Solang"></textarea>
          </div>
          <div>
            <label class="pk-label">Photo <span style="text-transform:none;font-weight:400">(JPG/PNG/WebP, saved under 400 KB)</span></label>
            <div class="d-flex align-items-center gap-3">
              <img id="pk_photo_prev" src="" alt="" class="pk-thumb" style="width:90px;height:60px;display:none">
              <input class="pk-input" type="file" name="photo" accept="image/jpeg,image/png,image/webp">
            </div>
          </div>
          <div>
            <label class="pk-label">Status</label>
            <select class="pk-input" name="status" id="pk_status">
              <option value="active">Active (shown on site)</option>
              <option value="inactive">Inactive (hidden)</option>
            </select>
          </div>
        </div>
        <div class="modal-footer" style="border-color:var(--border)">
          <button type="button" class="btn btn-sm" data-bs-dismiss="modal" style="color:var(--muted)">Cancel</button>
          <button type="submit" class="btn btn-primary-gold"><i class="fas fa-check me-1"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openSidebar(){document.getElementById('sidebar').classList.add('open');document.getElementById('sidebarOverlay').classList.add('show');}
function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sidebarOverlay').classList.remove('show');}
const pkgModal = new bootstrap.Modal(document.getElementById('pkgModal'));
function editPkg(p){
  document.getElementById('pk_id').value = p.id;
  document.getElementById('pk_name').value = p.package_name;
  document.getElementById('pk_category').value = p.category || '';
  document.getElementById('pk_desc').value = p.description || '';
  document.getElementById('pk_nights').value = p.duration_nights || 0;
  document.getElementById('pk_days').value = p.duration_days || 0;
  document.getElementById('pk_status').value = p.status;
  const prev = document.getElementById('pk_photo_prev');
  if (p.photo) { prev.src = '../uploads/photos/' + p.photo; prev.style.display = 'block'; }
  else { prev.src = ''; prev.style.display = 'none'; }
  pkgModal.show();
}
</script>
</body>
</html>
