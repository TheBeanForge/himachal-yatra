<?php
session_start();
require_once '../api/config.php';

// Check admin auth
if (empty($_SESSION['admin_id'])) {
    http_response_code(302);
    header('Location: login.php');
    exit;
}

// Handle AJAX POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_admin_csrf();

    $action = $_POST['action'];

    if ($action === 'add') {
        $city = trim($_POST['city'] ?? '');
        if (!$city || strlen($city) > 100) {
            exit(json_encode(['ok' => false, 'error' => 'City name required (max 100 chars)']));
        }

        $stmt = $conn->prepare('INSERT INTO pickup_locations (city, sort_order, active) VALUES (?, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM (SELECT * FROM pickup_locations) AS t), 1)');
        if (!$stmt) exit(json_encode(['ok' => false, 'error' => 'Database error']));

        $stmt->bind_param('s', $city);
        if ($stmt->execute()) {
            audit_log('location_add', "Added pickup location: $city");
            exit(json_encode(['ok' => true]));
        } else {
            exit(json_encode(['ok' => false, 'error' => 'Could not add location (duplicate?)']));
        }
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) exit(json_encode(['ok' => false, 'error' => 'Invalid ID']));

        // Prevent deleting if it's the only location
        $count = $conn->query("SELECT COUNT(*) as cnt FROM pickup_locations WHERE active = 1")->fetch_assoc()['cnt'];
        if ($count <= 1) {
            exit(json_encode(['ok' => false, 'error' => 'Cannot delete the last location']));
        }

        $stmt = $conn->prepare('UPDATE pickup_locations SET active = 0 WHERE id = ?');
        if (!$stmt) exit(json_encode(['ok' => false, 'error' => 'Database error']));

        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            audit_log('location_delete', "Deleted pickup location ID: $id");
            exit(json_encode(['ok' => true]));
        } else {
            exit(json_encode(['ok' => false, 'error' => 'Could not delete']));
        }
    }

    if ($action === 'reorder') {
        $locations = $_POST['locations'] ?? [];
        if (!is_array($locations) || empty($locations)) {
            exit(json_encode(['ok' => false, 'error' => 'Invalid input']));
        }

        foreach ($locations as $order => $id) {
            $id = (int)$id;
            $sort = (int)$order;
            $conn->query("UPDATE pickup_locations SET sort_order = $sort WHERE id = $id");
        }

        audit_log('location_reorder', 'Reordered pickup locations');
        exit(json_encode(['ok' => true]));
    }

    exit(json_encode(['ok' => false, 'error' => 'Unknown action']));
}

// Fetch locations
$locations = [];
if ($conn instanceof mysqli) {
    try {
        $res = $conn->query("SELECT id, city, sort_order FROM pickup_locations ORDER BY sort_order ASC");
        if ($res) $locations = $res->fetch_all(MYSQLI_ASSOC);
    } catch (mysqli_sql_exception) {}
}
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Pickup Locations — Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<?php require 'partials/sidebar.php'; ?>
<?php require 'partials/topbar.php'; ?>

<div class="admin-main">
  <div class="admin-content">
    <div style="margin-bottom:28px">
      <h2><i class="fas fa-map-pin me-2" style="color:var(--accent)"></i>Manage Pickup Locations</h2>
    </div>

    <!-- Add Location Card -->
    <div class="card" style="background:var(--surface);border:1px solid var(--border);margin-bottom:24px">
      <div class="card-body">
        <h5 class="card-title"><i class="fas fa-plus me-2" style="color:var(--accent)"></i>Add New Location</h5>
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:12px">
          <input type="text" id="newCity" placeholder="City name (e.g. Pathankot)"
                 style="flex:1;min-width:180px;padding:8px 12px;background:var(--bg);border:1px solid var(--border);color:var(--ink);border-radius:6px">
          <button id="addBtn" style="padding:8px 20px;background:var(--accent);color:#0d0d14;border:0;border-radius:6px;cursor:pointer;font-weight:600;transition:background .2s">
            <i class="fas fa-check me-1"></i>Add Location
          </button>
        </div>
        <div id="addMsg" style="margin-top:8px;font-size:13px"></div>
      </div>
    </div>

    <!-- Locations List -->
    <div class="card" style="background:var(--surface);border:1px solid var(--border)">
      <div class="card-body">
        <h5 class="card-title"><i class="fas fa-list me-2" style="color:var(--accent)"></i>Pickup Locations</h5>
        <div id="locationsList" style="margin-top:16px">
          <?php if (empty($locations)): ?>
          <p style="color:var(--muted);font-size:14px">No locations found</p>
          <?php else: ?>
          <div style="display:flex;flex-direction:column;gap:8px">
            <?php foreach ($locations as $loc): ?>
            <div class="location-item" data-id="<?= $loc['id'] ?>"
                 style="display:flex;align-items:center;justify-content:space-between;padding:12px;background:var(--surface-2);border-radius:6px;border-left:3px solid var(--accent)">
              <div style="display:flex;align-items:center;gap:12px;flex:1">
                <i class="fas fa-grip-vertical" style="color:var(--muted);cursor:move;font-size:14px"></i>
                <span style="color:var(--ink);font-weight:500"><?= h($loc['city']) ?></span>
              </div>
              <button class="delete-btn" data-id="<?= $loc['id'] ?>"
                      style="background:rgba(220,38,38,.15);color:#dc2626;border:0;padding:6px 12px;border-radius:4px;cursor:pointer;font-size:13px;transition:background .2s">
                <i class="fas fa-trash-alt me-1"></i>Delete
              </button>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const csrfHeader = { 'X-CSRF-Token': '<?= h($_SESSION['admin_csrf']); ?>' };

// Add location
document.getElementById('addBtn').addEventListener('click', async () => {
  const city = document.getElementById('newCity').value.trim();
  const msg = document.getElementById('addMsg');

  if (!city) {
    msg.innerHTML = '<span style="color:#dc2626">City name required</span>';
    return;
  }

  const fd = new FormData();
  fd.append('action', 'add');
  fd.append('city', city);

  try {
    const res = await fetch(location.href, { method: 'POST', body: fd, headers: csrfHeader });
    const data = await res.json();
    if (data.ok) {
      location.reload();
    } else {
      msg.innerHTML = `<span style="color:#dc2626">${data.error || 'Error'}</span>`;
    }
  } catch {
    msg.innerHTML = '<span style="color:#dc2626">Network error</span>';
  }
});

// Delete location
document.querySelectorAll('.delete-btn').forEach(btn => {
  btn.addEventListener('click', async (e) => {
    if (!confirm('Delete this location?')) return;

    const id = e.currentTarget.dataset.id;
    const fd = new FormData();
    fd.append('action', 'delete');
    fd.append('id', id);

    try {
      const res = await fetch(location.href, { method: 'POST', body: fd, headers: csrfHeader });
      const data = await res.json();
      if (data.ok) {
        location.reload();
      } else {
        alert(data.error || 'Error');
      }
    } catch {
      alert('Network error');
    }
  });
});

// Drag-and-drop reordering
let draggedItem = null;
const items = document.querySelectorAll('.location-item');

items.forEach(item => {
  item.draggable = true;
  item.addEventListener('dragstart', (e) => {
    draggedItem = item;
    item.style.opacity = '0.5';
  });
  item.addEventListener('dragend', () => {
    item.style.opacity = '1';
  });
  item.addEventListener('dragover', (e) => {
    e.preventDefault();
    if (draggedItem && draggedItem !== item) {
      item.parentNode.insertBefore(draggedItem, item);
    }
  });
});

// Save order on drop
document.getElementById('locationsList').addEventListener('drop', async () => {
  const order = {};
  document.querySelectorAll('.location-item').forEach((item, idx) => {
    order[idx] = item.dataset.id;
  });

  const fd = new FormData();
  fd.append('action', 'reorder');
  Object.entries(order).forEach(([idx, id]) => {
    fd.append('locations[]', id);
  });

  try {
    await fetch(location.href, { method: 'POST', body: fd, headers: csrfHeader });
  } catch {}
});
</script>
</body>
</html>
