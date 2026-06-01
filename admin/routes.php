<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../api/config.php';

// Assign photo to route
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin_csrf();
    $route_id = (int)($_POST['route_id'] ?? 0);
    $photo_id = (int)($_POST['photo_id'] ?? 0) ?: null;
    if ($route_id) {
        $stmt = $conn->prepare('UPDATE routes SET photo_id = ? WHERE id = ?');
        $stmt->bind_param('ii', $photo_id, $route_id);
        $stmt->execute();
        $stmt->close();
        if ($photo_id) audit_log('route_photo', "Route #{$route_id} photo set to #{$photo_id}");
        header('Location: routes.php?saved=1'); exit;
    }
}

// Load routes with their assigned photo
$routes = $conn->query("
    SELECT r.*, p.filename AS photo_filename
    FROM routes r
    LEFT JOIN photos p ON r.photo_id = p.id
    ORDER BY r.sort_order ASC
")->fetch_all(MYSQLI_ASSOC);

// Load all photos grouped by destination for the picker
$all_photos = [];
$res = $conn->query("SELECT id, destination, filename, caption, role FROM photos ORDER BY destination, sort_order ASC");
if ($res) foreach ($res->fetch_all(MYSQLI_ASSOC) as $p) $all_photos[$p['destination']][] = $p;

$dests = ['manali'=>'Manali','shimla'=>'Shimla','dharamshala'=>'Dharamshala','dalhousie'=>'Dalhousie','spiti'=>'Spiti Valley','general'=>'General'];
$conn->close();

$page_title = 'Route Photos';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<meta name="csrf-token" content="<?= htmlspecialchars(admin_csrf_token()) ?>"/>
<title>Route Photos — Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
<link href="assets/admin.css" rel="stylesheet"/>
<style>
.routes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 18px; }

.route-tile {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 14px; overflow: hidden;
  transition: border-color .2s;
}
.route-tile:hover { border-color: rgba(201,168,76,.35); }

.route-thumb {
  height: 160px; background: var(--surface-2);
  position: relative; overflow: hidden;
  cursor: pointer;
}
.route-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .4s; }
.route-tile:hover .route-thumb img { transform: scale(1.04); }
.route-thumb-empty {
  height: 100%; display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  color: var(--muted); gap: 8px; cursor: pointer;
}
.route-thumb-empty i { font-size: 28px; opacity: .4; }
.route-thumb-empty span { font-size: 12px; font-weight: 600; }
.route-thumb-overlay {
  position: absolute; inset: 0;
  background: rgba(0,0,0,.45);
  display: flex; align-items: center; justify-content: center;
  opacity: 0; transition: opacity .2s;
}
.route-thumb:hover .route-thumb-overlay { opacity: 1; }
.route-thumb-overlay span {
  background: var(--accent); color: #0d0d14;
  font: 700 12px 'Inter',sans-serif; padding: 7px 16px;
  border-radius: 8px;
}

.route-info { padding: 14px 16px; }
.route-name { font-size: 14px; font-weight: 700; color: var(--ink); margin-bottom: 4px; }
.route-meta { font-size: 11.5px; color: var(--muted); display: flex; gap: 12px; }
.route-badge-pill {
  display: inline-block; margin-top: 8px;
  background: rgba(201,168,76,.12); color: var(--accent);
  font-size: 10.5px; font-weight: 700; padding: 2px 9px;
  border-radius: 5px; text-transform: uppercase; letter-spacing: .05em;
}

/* Photo Picker Modal */
.picker-overlay {
  display: none; position: fixed; inset: 0; z-index: 1000;
  background: rgba(0,0,0,.7); backdrop-filter: blur(6px);
  align-items: center; justify-content: center; padding: 20px;
}
.picker-overlay.open { display: flex; }
.picker-modal {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 16px; width: 100%; max-width: 720px;
  max-height: 80vh; display: flex; flex-direction: column;
  overflow: hidden;
}
.picker-head {
  padding: 20px 24px 16px;
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
}
.picker-head h3 { font-size: 16px; font-weight: 700; color: var(--ink); margin: 0; }
.picker-close {
  width: 34px; height: 34px; border-radius: 8px;
  background: var(--surface-2); border: 1px solid var(--border);
  color: var(--muted); cursor: pointer; font-size: 16px;
  display: grid; place-items: center;
}
.picker-body { padding: 20px 24px; overflow-y: auto; flex: 1; }
.picker-dest-tabs { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 16px; }
.picker-tab {
  padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 600;
  border: 1.5px solid var(--border); background: transparent;
  color: var(--muted); cursor: pointer; transition: all .15s; font-family: inherit;
}
.picker-tab:hover { border-color: var(--accent); color: var(--accent); }
.picker-tab.active { background: var(--accent); color: #0d0d14; border-color: var(--accent); }
.picker-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; }
.picker-photo {
  border-radius: 10px; overflow: hidden; cursor: pointer;
  border: 2.5px solid transparent; transition: border-color .15s, transform .15s;
  position: relative;
}
.picker-photo:hover { border-color: var(--accent); transform: scale(1.03); }
.picker-photo img { width: 100%; aspect-ratio: 4/3; object-fit: cover; display: block; }
.picker-photo-label {
  position: absolute; bottom: 0; left: 0; right: 0;
  background: rgba(0,0,0,.65); padding: 5px 8px;
  font-size: 10px; color: rgba(255,255,255,.85); font-weight: 600;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.picker-photo-role {
  position: absolute; top: 6px; right: 6px;
  font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 4px;
  text-transform: uppercase;
}
.role-hero  { background: rgba(201,168,76,.9); color: #0d0d14; }
.role-about { background: rgba(59,130,246,.9);  color: #fff; }
.role-route { background: rgba(34,197,94,.9);   color: #0d0d14; }
.picker-remove {
  margin-top: 14px; padding: 8px 16px; border-radius: 8px;
  background: rgba(239,68,68,.10); color: #f87171;
  border: 1px solid rgba(239,68,68,.22); font: 600 12.5px 'Inter',sans-serif;
  cursor: pointer; transition: all .2s;
}
.picker-remove:hover { background: #dc2626; color: #fff; border-color: #dc2626; }
.flash-saved { background: rgba(34,197,94,.10); color: #4ade80; border: 1px solid rgba(34,197,94,.20); border-radius: 8px; padding: 10px 16px; font-size: 13px; font-weight: 600; margin-bottom: 20px; }
</style>
</head>
<body>
<div class="admin-wrap">
  <?php require_once 'partials/sidebar.php'; ?>
  <div class="admin-main">
    <?php require_once 'partials/topbar.php'; ?>
    <div class="admin-content">

      <?php if (!empty($_GET['saved'])): ?>
      <div class="flash-saved"><i class="fas fa-check-circle me-2"></i>Route photo updated.</div>
      <?php endif; ?>

      <p style="color:var(--muted);font-size:13px;margin-bottom:20px">
        Click any route card to assign a photo. Each route can have its own unique photo.
      </p>

      <div class="routes-grid">
        <?php foreach ($routes as $route): ?>
        <div class="route-tile">
          <div class="route-thumb" onclick="openPicker(<?= $route['id'] ?>, '<?= htmlspecialchars($route['name'], ENT_QUOTES) ?>', '<?= $route['dest_key'] ?>')">
            <?php if ($route['photo_filename']): ?>
              <img src="../uploads/photos/<?= htmlspecialchars($route['photo_filename']) ?>" alt="">
              <div class="route-thumb-overlay"><span><i class="fas fa-camera me-1"></i>Change Photo</span></div>
            <?php else: ?>
              <div class="route-thumb-empty">
                <i class="fas fa-image"></i>
                <span>Click to assign photo</span>
              </div>
            <?php endif; ?>
          </div>
          <div class="route-info">
            <div class="route-name"><?= htmlspecialchars($route['name']) ?></div>
            <div class="route-meta">
              <span><i class="fas fa-route fa-fw" style="color:var(--accent)"></i> <?= htmlspecialchars($route['km']) ?></span>
              <span><i class="far fa-clock fa-fw" style="color:var(--accent)"></i> <?= htmlspecialchars($route['duration']) ?></span>
            </div>
            <span class="route-badge-pill"><?= htmlspecialchars($route['badge']) ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

    </div>
  </div>
</div>

<!-- Photo Picker Modal -->
<div class="picker-overlay" id="pickerOverlay">
  <div class="picker-modal">
    <div class="picker-head">
      <h3 id="pickerTitle">Select Photo</h3>
      <button class="picker-close" onclick="closePicker()"><i class="fas fa-times"></i></button>
    </div>
    <div class="picker-body">
      <div class="picker-dest-tabs" id="pickerTabs"></div>
      <div class="picker-grid" id="pickerGrid"></div>
      <button class="picker-remove" id="pickerRemove" onclick="assignPhoto(null)">
        <i class="fas fa-times me-1"></i> Remove assigned photo
      </button>
    </div>
  </div>
</div>

<!-- Hidden form for submission -->
<form id="assignForm" method="POST" style="display:none">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['admin_csrf'] ?? '') ?>">
  <input type="hidden" name="route_id"  id="fRouteId">
  <input type="hidden" name="photo_id"  id="fPhotoId">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openSidebar(){document.getElementById('sidebar').classList.add('open');document.getElementById('sidebarOverlay').classList.add('show');}
function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sidebarOverlay').classList.remove('show');}

const allPhotos = <?= json_encode($all_photos) ?>;
const dests     = <?= json_encode($dests) ?>;
let activeRouteId = null;
let activeDestKey = null;

function openPicker(routeId, routeName, destKey) {
  activeRouteId = routeId;
  activeDestKey = destKey;
  document.getElementById('pickerTitle').textContent = routeName;

  // Build tabs - show dest_key first, then others
  const keys = Object.keys(allPhotos);
  const ordered = [destKey, ...keys.filter(k => k !== destKey)];
  const tabs = document.getElementById('pickerTabs');
  tabs.innerHTML = ordered.filter(k => allPhotos[k]?.length).map(k =>
    `<button class="picker-tab ${k === destKey ? 'active' : ''}" onclick="switchTab('${k}', this)">${dests[k] || k}</button>`
  ).join('');

  renderPhotos(destKey);
  document.getElementById('pickerOverlay').classList.add('open');
}

function switchTab(destKey, btn) {
  document.querySelectorAll('.picker-tab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
  renderPhotos(destKey);
}

function renderPhotos(destKey) {
  const photos = allPhotos[destKey] || [];
  const roleLabels = { hero:'Hero', about:'About', route:'Route' };
  document.getElementById('pickerGrid').innerHTML = photos.map(p =>
    `<div class="picker-photo" onclick="assignPhoto(${p.id})">
      <img src="../uploads/photos/${p.filename}" alt="">
      ${p.role !== 'gallery' ? `<span class="picker-photo-role role-${p.role}">${roleLabels[p.role]||p.role}</span>` : ''}
      <div class="picker-photo-label">${p.caption || p.filename}</div>
    </div>`
  ).join('') || '<p style="color:var(--muted);font-size:13px">No photos uploaded for this destination.</p>';
}

function assignPhoto(photoId) {
  document.getElementById('fRouteId').value = activeRouteId;
  document.getElementById('fPhotoId').value = photoId || '';
  document.getElementById('assignForm').submit();
}

function closePicker() {
  document.getElementById('pickerOverlay').classList.remove('open');
}

document.getElementById('pickerOverlay').addEventListener('click', function(e) {
  if (e.target === this) closePicker();
});
</script>
</body>
</html>
