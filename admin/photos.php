<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../api/config.php';

$dests = ['manali' => 'Manali', 'shimla' => 'Shimla', 'dharamshala' => 'Dharamshala', 'dalhousie' => 'Dalhousie', 'spiti' => 'Spiti Valley', 'general' => 'General / Homepage'];
$filter_dest = $_GET['dest'] ?? '';

$where = $filter_dest ? 'WHERE destination = ?' : '';
$sql   = "SELECT * FROM photos $where ORDER BY uploaded_at DESC";
$stmt  = $conn->prepare($sql);
if ($filter_dest) $stmt->bind_param('s', $filter_dest);
$stmt->execute();
$photos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

$page_title = 'Photo Gallery';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<meta name="csrf-token" content="<?= htmlspecialchars(admin_csrf_token()) ?>"/>
<title>Photo Gallery — Himachal Yatra Admin</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Poppins:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
<link href="assets/admin.css" rel="stylesheet"/>
<style>
.upload-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:28px;margin-bottom:28px}
.upload-card h3{font-family:'Poppins',sans-serif;font-size:16px;font-weight:800;margin-bottom:20px;color:var(--ink)}
.upload-form{display:grid;grid-template-columns:1fr 1fr auto auto;gap:12px;align-items:end;flex-wrap:wrap}
.form-field label{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;display:block;margin-bottom:6px}
.form-field select,.form-field input[type=text]{height:42px;padding:0 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:'Inter',sans-serif;outline:none;width:100%;background:var(--surface-2);color:var(--ink);transition:border .2s}
.form-field select:focus,.form-field input[type=text]:focus{border-color:var(--accent);background:var(--surface-2)}
.file-label{display:flex;align-items:center;gap:8px;height:42px;padding:0 14px;border:1.5px dashed var(--border);border-radius:8px;cursor:pointer;font-size:13px;color:var(--muted);background:var(--surface-2);white-space:nowrap;transition:border .2s}
.file-label:hover{border-color:var(--accent);color:var(--accent)}
.file-label input{display:none}
.btn-upload{height:42px;padding:0 20px;background:var(--accent);color:#0d0d14;border:none;border-radius:8px;font:700 13px 'Inter',sans-serif;cursor:pointer;display:flex;align-items:center;gap:6px;transition:background .2s;white-space:nowrap}
.btn-upload:hover{background:var(--accent-d)}
.dest-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px}
.dest-tab{padding:6px 16px;border-radius:20px;font:600 13px 'Inter',sans-serif;color:var(--muted);border:1.5px solid var(--border);background:var(--surface-2);cursor:pointer;text-decoration:none;transition:all .2s}
.dest-tab:hover,.dest-tab.active{background:var(--accent);color:#0d0d14;border-color:var(--accent)}
.photo-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px}
.photo-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden}
.photo-card img{width:100%;aspect-ratio:4/3;object-fit:cover;display:block}
.photo-card-body{padding:10px 12px;display:flex;align-items:center;justify-content:space-between;gap:8px}
.photo-caption{font-size:12px;color:var(--ink);font-weight:600;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.photo-dest{font-size:11px;color:var(--muted)}
.photo-actions{display:flex;align-items:center;gap:4px;flex-shrink:0}
.btn-move{background:rgba(201,168,76,.10);color:#e0c46a;border:1px solid rgba(201,168,76,.25);border-radius:6px;width:24px;height:24px;cursor:pointer;display:grid;place-items:center;font-size:10px;transition:background .2s}
.btn-move:hover{background:rgba(201,168,76,.22)}
.btn-move:disabled{opacity:.35;cursor:not-allowed}
.btn-del{background:rgba(185,28,28,.15);color:#fca5a5;border:1px solid rgba(185,28,28,.25);border-radius:6px;width:28px;height:28px;cursor:pointer;display:grid;place-items:center;font-size:12px;flex-shrink:0;transition:background .2s}
.btn-del:hover{background:rgba(185,28,28,.30)}
.empty-state{text-align:center;padding:60px 20px;color:var(--muted)}
.empty-state i{font-size:36px;opacity:.4;display:block;margin-bottom:12px}
.flash{padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:20px}
.flash.ok{background:rgba(34,197,94,.12);color:#86efac;border:1px solid rgba(34,197,94,.25)}
.flash.err{background:rgba(185,28,28,.12);color:#fca5a5;border:1px solid rgba(185,28,28,.25)}
@media(max-width:768px){.upload-form{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="admin-wrap">
  <?php require_once 'partials/sidebar.php'; ?>
  <div class="admin-main">
    <?php require_once 'partials/topbar.php'; ?>
    <div class="admin-content">

      <!-- Upload Card -->
      <div class="upload-card">
        <h3><i class="fas fa-cloud-arrow-up me-2" style="color:var(--accent)"></i>Upload New Photo</h3>
        <form class="upload-form" id="uploadForm" enctype="multipart/form-data">
          <div class="form-field">
            <label>Destination</label>
            <select name="destination" required>
              <?php foreach ($dests as $val => $lbl): ?>
              <option value="<?= $val ?>"><?= $lbl ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-field">
            <label>Caption (optional)</label>
            <input type="text" name="caption" placeholder="e.g. Rohtang Pass in summer" maxlength="200">
          </div>
          <div class="form-field">
            <label>Photo</label>
            <label class="file-label" id="fileLabel">
              <i class="fas fa-image"></i> <span id="fileLabelText">Choose JPG/PNG/WebP (max 5MB)</span>
              <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required onchange="updateLabel(this)">
            </label>
          </div>
          <div class="form-field">
            <label>&nbsp;</label>
            <button type="submit" class="btn-upload"><i class="fas fa-upload"></i> Upload</button>
          </div>
        </form>
        <div id="uploadMsg" style="margin-top:14px"></div>
      </div>

      <!-- Destination Filter Tabs -->
      <div class="dest-tabs">
        <a href="photos.php" class="dest-tab <?= !$filter_dest ? 'active' : '' ?>">All</a>
        <?php foreach ($dests as $val => $lbl): ?>
        <a href="photos.php?dest=<?= $val ?>" class="dest-tab <?= $filter_dest === $val ? 'active' : '' ?>"><?= $lbl ?></a>
        <?php endforeach; ?>
      </div>

      <!-- Photo Grid -->
      <?php if (empty($photos)): ?>
      <div class="empty-state"><i class="fas fa-images"></i><p>No photos uploaded yet.</p></div>
      <?php else: ?>
      <p style="color:var(--muted);font-size:12px;margin-bottom:12px"><i class="fas fa-circle-info"></i> Use the arrows to reorder photos within a destination — earlier photos appear first on the public gallery.</p>
      <div class="photo-grid" id="photoGrid">
        <?php foreach ($photos as $p): ?>
        <div class="photo-card" id="photo-<?= $p['id'] ?>">
          <img src="../uploads/photos/<?= htmlspecialchars($p['filename']) ?>" alt="<?= htmlspecialchars($p['caption']) ?>" loading="lazy">
          <div class="photo-card-body">
            <div style="min-width:0;flex:1">
              <div class="photo-caption"><?= htmlspecialchars($p['caption'] ?: 'No caption') ?></div>
              <div class="photo-dest"><?= htmlspecialchars($dests[$p['destination']] ?? $p['destination']) ?></div>
            </div>
            <div class="photo-actions">
              <button class="btn-move" onclick="reorder(<?= $p['id'] ?>, 'up')" title="Move earlier"><i class="fas fa-arrow-up"></i></button>
              <button class="btn-move" onclick="reorder(<?= $p['id'] ?>, 'down')" title="Move later"><i class="fas fa-arrow-down"></i></button>
              <button class="btn-del" onclick="deletePhoto(<?= $p['id'] ?>)" title="Delete photo">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openSidebar(){document.getElementById('sidebar').classList.add('open');document.getElementById('sidebarOverlay').classList.add('show');}
function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sidebarOverlay').classList.remove('show');}

const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const csrfHeader = { 'X-CSRF-Token': CSRF };

function updateLabel(input) {
  document.getElementById('fileLabelText').textContent = input.files[0]?.name || 'Choose JPG/PNG/WebP (max 5MB)';
}

document.getElementById('uploadForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const btn = e.target.querySelector('.btn-upload');
  const msg = document.getElementById('uploadMsg');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
  msg.innerHTML = '';

  try {
    const fd = new FormData(e.target);
    const res = await fetch('../api/upload_photo.php', { method: 'POST', body: fd, headers: csrfHeader });
    const data = await res.json();
    if (data.ok) {
      msg.innerHTML = '<div class="flash ok"><i class="fas fa-check-circle me-1"></i> Photo uploaded successfully.</div>';
      e.target.reset();
      document.getElementById('fileLabelText').textContent = 'Choose JPG/PNG/WebP (max 5MB)';
      setTimeout(() => location.reload(), 1200);
    } else {
      msg.innerHTML = `<div class="flash err"><i class="fas fa-circle-exclamation me-1"></i> ${data.error || 'Upload failed'}</div>`;
    }
  } catch {
    msg.innerHTML = '<div class="flash err">Network error. Please try again.</div>';
  }
  btn.disabled = false;
  btn.innerHTML = '<i class="fas fa-upload"></i> Upload';
});

async function deletePhoto(id) {
  if (!confirm('Delete this photo? This cannot be undone.')) return;
  const fd = new FormData();
  fd.append('id', id);
  const res  = await fetch('../api/delete_photo.php', { method: 'POST', body: fd, headers: csrfHeader });
  const data = await res.json();
  if (data.ok) {
    document.getElementById('photo-' + id)?.remove();
  } else {
    alert('Could not delete photo.');
  }
}

async function reorder(id, direction) {
  const fd = new FormData();
  fd.append('id', id);
  fd.append('direction', direction);
  const res  = await fetch('../api/reorder_photo.php', { method: 'POST', body: fd, headers: csrfHeader });
  const data = await res.json();
  if (data.ok && data.changed) {
    location.reload();
  } else if (!data.ok) {
    alert(data.error || 'Could not reorder.');
  }
}
</script>
</body>
</html>
