<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../includes/vars.php';

$dests = ['manali' => 'Manali', 'shimla' => 'Shimla', 'dharamshala' => 'Dharamshala', 'dalhousie' => 'Dalhousie', 'spiti' => 'Spiti Valley', 'general' => 'General / Homepage'];
$filter_dest = $_GET['dest'] ?? '';

// Get photo counts per destination
$dest_counts = [];
$res = $conn->query("SELECT destination, COUNT(*) as cnt FROM photos GROUP BY destination");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $dest_counts[$row['destination']] = (int)$row['cnt'];
    }
}

$where = $filter_dest ? 'WHERE destination = ?' : '';
$sql   = "SELECT * FROM photos $where ORDER BY destination, sort_order ASC";
$stmt  = $conn->prepare($sql);
if ($filter_dest) $stmt->bind_param('s', $filter_dest);
$stmt->execute();
$photos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Slot assignments: photo_id => [slot => true], plus per-slot counts.
$assign = []; $slot_counts = [];
if ($ares = $conn->query("SELECT photo_id, slot FROM photo_assignments")) {
    while ($r = $ares->fetch_assoc()) {
        $assign[(int)$r['photo_id']][$r['slot']] = true;
        $slot_counts[$r['slot']] = ($slot_counts[$r['slot']] ?? 0) + 1;
    }
}

// ALL photos (unfiltered) → build slot => [photos in order] for the overview panel.
$allPhotos = [];
if ($r = $conn->query("SELECT id, filename, destination FROM photos ORDER BY destination, sort_order ASC, id ASC")) {
    $allPhotos = $r->fetch_all(MYSQLI_ASSOC);
}
$slotPhotos = [];
foreach ($allPhotos as $p) {
    $pid = (int)$p['id'];
    if (empty($assign[$pid])) continue;
    foreach (array_keys($assign[$pid]) as $slot) $slotPhotos[$slot][] = $p;
}
$conn->close();

$page_title = 'Photo Gallery';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<meta name="csrf-token" content="<?= htmlspecialchars(admin_csrf_token()) ?>"/>
<title>Photo Gallery — Himachal Safar Admin</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Poppins:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
<link href="assets/admin.css?v=<?php echo @filemtime(__DIR__ . '/assets/admin.css'); ?>" rel="stylesheet"/>
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
.photo-card-body{padding:9px 11px;display:flex;flex-direction:column;gap:7px}
.photo-caption{font-size:12px;color:var(--ink);font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.photo-dest{font-size:10.5px;color:var(--muted)}
/* Bottom row: chips on left, actions on right — all same height */
.photo-footer{display:flex;align-items:center;justify-content:space-between;gap:6px}
.photo-chips{display:flex;align-items:center;gap:4px}
.chip{height:24px;padding:0 9px;border-radius:5px;font-size:10.5px;font-weight:700;
  border:1.5px solid var(--border);background:transparent;color:var(--muted);
  cursor:pointer;transition:all .15s;font-family:inherit;white-space:nowrap}
.chip:hover{border-color:var(--accent);color:var(--accent)}
.chip.chip-on-hero {background:rgba(201,168,76,.15);color:var(--accent);border-color:var(--accent)}
.chip.chip-on-about{background:rgba(214,199,161,.15);color:#D6C7A1;border-color:rgba(214,199,161,.45)}
.chip.chip-active{background:rgba(201,168,76,.18);color:var(--accent);border-color:var(--accent)}
.photo-actions{display:flex;align-items:center;gap:3px;flex-shrink:0}
.btn-move{background:rgba(201,168,76,.08);color:#e0c46a;border:1px solid rgba(201,168,76,.20);
  border-radius:5px;width:24px;height:24px;cursor:pointer;display:grid;place-items:center;
  font-size:10px;transition:background .2s}
.btn-move:hover{background:rgba(201,168,76,.22)}
.btn-move:disabled{opacity:.35;cursor:not-allowed}
.btn-del{background:rgba(185,28,28,.12);color:#fca5a5;border:1px solid rgba(185,28,28,.22);
  border-radius:5px;width:24px;height:24px;cursor:pointer;display:grid;place-items:center;
  font-size:11px;flex-shrink:0;transition:background .2s}
.btn-del:hover{background:rgba(185,28,28,.30)}
.empty-state{text-align:center;padding:60px 20px;color:var(--muted)}
.empty-state i{font-size:36px;opacity:.4;display:block;margin-bottom:12px}
.flash{padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:20px}
.flash.ok{background:rgba(34,197,94,.12);color:#86efac;border:1px solid rgba(34,197,94,.25)}
.flash.err{background:rgba(185,28,28,.12);color:#fca5a5;border:1px solid rgba(185,28,28,.25)}
.photo-num{
  position:absolute;top:8px;left:8px;
  width:26px;height:26px;border-radius:6px;
  background:rgba(13,13,20,.75);backdrop-filter:blur(4px);
  color:var(--accent);font-size:12px;font-weight:800;
  display:grid;place-items:center;
  border:1px solid rgba(201,168,76,.35);
  pointer-events:none;
}
.role-badge {
  position:absolute;top:8px;right:8px;
  padding:3px 8px;border-radius:5px;font-size:10px;font-weight:800;
  text-transform:uppercase;letter-spacing:.06em;pointer-events:none;
}
.role-badge.hero  { background:rgba(201,168,76,.9);color:#0d0d14; }
.role-badge.about { background:rgba(184,161,106,.9);color:#fff; }
.role-badge.route { background:rgba(34,197,94,.9);color:#0d0d14; }
/* Purpose badges (overlay on each photo) */
.pbadge{position:absolute;right:8px;padding:3px 8px;border-radius:5px;font-size:10px;font-weight:800;
  text-transform:uppercase;letter-spacing:.06em;pointer-events:none;color:#0d0d14}
.pbadge.cover{background:#3b82f6;color:#fff}
.pbadge.slideshow{background:#e0b84a}
.pbadge.about{background:#22c55e}
.pbadge.gallery{background:rgba(120,120,140,.92);color:#fff}
.pbadge.spare{background:rgba(120,120,140,.5);color:#fff}
/* "Live on your site" overview panel */
.ov-panel{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:18px 20px;margin-bottom:24px}
.ov-title{font-family:'Poppins',sans-serif;font-size:12.5px;font-weight:800;letter-spacing:.05em;text-transform:uppercase;color:var(--muted);margin-bottom:6px;display:flex;align-items:center;gap:8px}
.ov-legend{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:8px}
.ov-key{display:inline-flex;align-items:center;gap:5px;font-size:11px;color:var(--muted);font-weight:600}
.ov-dot{width:11px;height:11px;border-radius:3px;display:inline-block}
.ov-row{display:flex;align-items:center;gap:14px;padding:10px 0;border-top:1px solid var(--border);flex-wrap:wrap}
.ov-label{min-width:150px;font-weight:700;font-size:13px;color:var(--ink);display:flex;align-items:center;gap:8px}
.ov-slots{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.ov-slot{display:flex;flex-direction:column;align-items:center;gap:4px}
.ov-slot small{font-size:9px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted);font-weight:700}
.ov-thumb{width:56px;height:40px;border-radius:6px;object-fit:cover;border:1.5px solid var(--border);display:block}
/* Clickable "add a photo here" box */
.ov-add{width:56px;height:40px;border-radius:6px;border:1.5px dashed var(--accent);
  background:rgba(201,168,76,.08);color:var(--accent);font-size:8.5px;font-weight:800;
  text-transform:uppercase;letter-spacing:.04em;cursor:pointer;display:grid;place-items:center;
  gap:1px;line-height:1;padding:0;transition:background .15s,transform .1s}
.ov-add:hover{background:rgba(201,168,76,.22)}
.ov-add:active{transform:scale(.96)}
.ov-add i{font-size:11px}
/* Filled slot that can be replaced by clicking */
.ov-up{position:relative;padding:0;border:none;background:none;cursor:pointer;display:block;border-radius:6px;line-height:0}
.ov-up .ov-thumb{display:block}
.ov-replace{position:absolute;inset:0;display:grid;place-items:center;border-radius:6px;
  background:rgba(13,13,20,.55);color:#fff;font-size:8.5px;font-weight:800;text-transform:uppercase;
  letter-spacing:.04em;opacity:0;transition:opacity .15s}
.ov-up:hover .ov-replace{opacity:1}
.ov-note{display:flex;align-items:flex-start;gap:7px;font-size:11.5px;line-height:1.5;color:var(--muted);
  background:var(--surface-2);border:1px solid var(--border);border-radius:8px;padding:8px 11px;margin-bottom:14px}
.ov-note i{color:var(--accent);margin-top:1px}
.ov-count{font-size:12px;color:var(--muted)}
.ov-count b{color:var(--ink)}
@media(max-width:768px){.upload-form{grid-template-columns:1fr}.ov-label{min-width:0;width:100%}}
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
            <select name="destination" id="destSelect" required onchange="updatePhotoCount()">
              <?php foreach ($dests as $val => $lbl): ?>
              <option value="<?= $val ?>" data-count="<?= $dest_counts[$val] ?? 0 ?>"><?= $lbl ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div style="grid-column: 1 / -1; display: flex; gap: 20px; align-items: center;">
            <div style="font-size: 13px; color: var(--ink);">
              <strong id="photoCount">0</strong> / 15 photos
              <span id="photoWarning" style="display: none; color: #fca5a5; margin-left: 12px;">
                <i class="fas fa-exclamation-circle"></i> Limit reached. Delete photos to upload more.
              </span>
            </div>
          </div>
          <div class="form-field">
            <label>Caption (optional)</label>
            <input type="text" name="caption" placeholder="e.g. Rohtang Pass in summer" maxlength="200">
          </div>
          <div class="form-field">
            <label>Photo</label>
            <label class="file-label" id="fileLabel">
              <i class="fas fa-image"></i> <span id="fileLabelText">Choose JPG/PNG/WebP (saved under 400KB)</span>
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

      <!-- Live overview: what each photo is used for -->
      <div class="ov-panel">
        <div class="ov-title"><i class="fas fa-eye" style="color:var(--accent)"></i> Live on your site — what's used where</div>
        <div class="ov-legend">
          <span class="ov-key"><span class="ov-dot" style="background:#e0b84a"></span> Slideshow</span>
          <span class="ov-key"><span class="ov-dot" style="background:#3b82f6"></span> Cover</span>
          <span class="ov-key"><span class="ov-dot" style="background:#22c55e"></span> About</span>
          <span class="ov-key"><span class="ov-dot" style="background:rgba(120,120,140,.92)"></span> Gallery (internal)</span>
        </div>
        <div class="ov-note">
          <i class="fas fa-circle-info"></i>
          <span>Click a <strong>+ Add</strong> box to upload a photo straight into that spot (Slideshow / Cover / About). Hover a filled Cover/About photo and click <strong>Replace</strong> to swap it. New images are uploaded and assigned in one step.</span>
        </div>

        <!-- Homepage slideshow -->
        <div class="ov-row">
          <div class="ov-label"><i class="fas fa-house"></i> Homepage</div>
          <div class="ov-slots">
            <?php $hh = $slotPhotos['home_hero'] ?? []; for ($k = 0; $k < 5; $k++): ?>
            <div class="ov-slot">
              <?php if (isset($hh[$k])): ?>
                <img class="ov-thumb" src="../uploads/photos/<?= h($hh[$k]['filename']) ?>" alt="">
              <?php else: ?>
                <button type="button" class="ov-add" onclick="pickForSlot('home_hero')" title="Upload a photo into the homepage slideshow"><i class="fas fa-plus"></i> Add</button>
              <?php endif; ?>
              <small><?= $k + 1 ?></small>
            </div>
            <?php endfor; ?>
            <span class="ov-count">Slideshow <b><?= count($hh) ?>/5</b></span>
          </div>
        </div>

        <!-- Destinations -->
        <?php foreach (['manali','shimla','dharamshala','dalhousie','spiti'] as $d):
          $cover = $slotPhotos["{$d}_hero"][0]  ?? null;
          $abt   = $slotPhotos["{$d}_about"][0] ?? null;
          $gal   = max(0, ($dest_counts[$d] ?? 0) - ($cover ? 1 : 0) - ($abt ? 1 : 0));
        ?>
        <div class="ov-row">
          <div class="ov-label"><i class="fas fa-location-dot"></i> <?= h($dests[$d]) ?></div>
          <div class="ov-slots">
            <div class="ov-slot">
              <?php if ($cover): ?>
                <button type="button" class="ov-up" onclick="pickForSlot('<?= $d ?>_hero')" title="Click to replace the cover photo">
                  <img class="ov-thumb" src="../uploads/photos/<?= h($cover['filename']) ?>" alt=""><span class="ov-replace">Replace</span>
                </button>
              <?php else: ?>
                <button type="button" class="ov-add" onclick="pickForSlot('<?= $d ?>_hero')" title="Upload this destination's cover photo"><i class="fas fa-plus"></i> Add</button>
              <?php endif; ?>
              <small>Cover</small>
            </div>
            <div class="ov-slot">
              <?php if ($abt): ?>
                <button type="button" class="ov-up" onclick="pickForSlot('<?= $d ?>_about')" title="Click to replace the about photo">
                  <img class="ov-thumb" src="../uploads/photos/<?= h($abt['filename']) ?>" alt=""><span class="ov-replace">Replace</span>
                </button>
              <?php else: ?>
                <button type="button" class="ov-add" onclick="pickForSlot('<?= $d ?>_about')" title="Upload this destination's about photo"><i class="fas fa-plus"></i> Add</button>
              <?php endif; ?>
              <small>About</small>
            </div>
            <span class="ov-count">Gallery <b><?= $gal ?></b> photo<?= $gal === 1 ? '' : 's' ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <input type="file" id="slotUpload" accept="image/jpeg,image/png,image/webp" style="display:none">

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
      <p style="color:var(--muted);font-size:12px;margin-bottom:12px"><i class="fas fa-circle-info"></i> Tick a photo's purpose below (the badge updates instantly). Use the arrows to reorder — earlier photos appear first in the gallery &amp; slideshow.</p>
      <div class="photo-grid" id="photoGrid">
        <?php foreach ($photos as $i => $p):
          $pid       = (int)$p['id'];
          $bucket    = $p['destination'];
          $isHome    = $bucket === 'general';
          $slotHero  = $isHome ? 'home_hero' : "{$bucket}_hero";
          $slotAbout = "{$bucket}_about";
          $onHero    = isset($assign[$pid][$slotHero]);
          $onAbout   = !$isHome && isset($assign[$pid][$slotAbout]);
          // Which badge(s) this photo wears
          $badges = [];
          if ($isHome) {
              $badges[] = $onHero ? ['slideshow','Slideshow'] : ['spare','Not used'];
          } else {
              if ($onHero)  $badges[] = ['cover','Cover'];
              if ($onAbout) $badges[] = ['about','About'];
              if (!$onHero && !$onAbout) $badges[] = ['gallery','Gallery'];
          }
        ?>
        <div class="photo-card" id="photo-<?= $pid ?>">
          <div style="position:relative">
            <img src="../uploads/photos/<?= htmlspecialchars($p['filename']) ?>" alt="<?= htmlspecialchars($p['caption']) ?>" loading="lazy">
            <span class="photo-num"><?= $i + 1 ?></span>
            <?php foreach ($badges as $bi => $b): ?>
            <span class="pbadge <?= $b[0] ?>" style="top:<?= 8 + $bi * 26 ?>px"><?= $b[1] ?></span>
            <?php endforeach; ?>
          </div>
          <div class="photo-card-body">
            <div class="photo-caption"><?= htmlspecialchars($p['caption'] ?: 'No caption') ?></div>
            <div class="photo-dest"><?= htmlspecialchars($dests[$bucket] ?? $bucket) ?></div>
            <div class="photo-footer">
              <div class="photo-chips">
                <?php if ($isHome): ?>
                <button class="chip <?= $onHero?'chip-active':'' ?>" data-slot="home_hero"
                  onclick="toggleSlot(<?= $pid ?>,'home_hero',this)"
                  title="Show in homepage hero slideshow (max 5)">Slideshow</button>
                <?php else: ?>
                <button class="chip <?= $onHero?'chip-active':'' ?>" data-slot="<?= $slotHero ?>"
                  onclick="toggleSlot(<?= $pid ?>,'<?= $slotHero ?>',this)"
                  title="Use as this destination's cover (banner) photo">Cover</button>
                <button class="chip <?= $onAbout?'chip-active':'' ?>" data-slot="<?= $slotAbout ?>"
                  onclick="toggleSlot(<?= $pid ?>,'<?= $slotAbout ?>',this)"
                  title="Use in this destination's about section">About</button>
                <?php endif; ?>
              </div>
              <div class="photo-actions">
                <button class="btn-move" onclick="reorder(<?= $pid ?>, 'up')" title="Move earlier"><i class="fas fa-arrow-up"></i></button>
                <button class="btn-move" onclick="reorder(<?= $pid ?>, 'down')" title="Move later"><i class="fas fa-arrow-down"></i></button>
                <button class="btn-del" onclick="deletePhoto(<?= $pid ?>)" title="Delete"><i class="fas fa-trash"></i></button>
              </div>
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

// ── Upload straight into a slot from the "Live on your site" panel ──
let _targetSlot = null;
function pickForSlot(slot) {
  _targetSlot = slot;
  const inp = document.getElementById('slotUpload');
  inp.value = '';
  inp.click();
}
document.getElementById('slotUpload').addEventListener('change', async function () {
  if (!this.files[0] || !_targetSlot) return;
  const panel = document.querySelector('.ov-panel');
  if (panel) { panel.style.opacity = '.5'; panel.style.pointerEvents = 'none'; }
  const fd = new FormData();
  fd.append('photo', this.files[0]);
  fd.append('slot', _targetSlot);
  try {
    const res  = await fetch('../api/upload_to_slot.php', { method: 'POST', body: fd, headers: csrfHeader });
    const data = await res.json();
    if (data.ok) { location.reload(); return; }
    alert(data.error || 'Upload failed');
  } catch { alert('Network error. Please try again.'); }
  if (panel) { panel.style.opacity = '1'; panel.style.pointerEvents = 'auto'; }
});

function updateLabel(input) {
  document.getElementById('fileLabelText').textContent = input.files[0]?.name || 'Choose JPG/PNG/WebP (saved under 400KB)';
}

function updatePhotoCount() {
  const select = document.getElementById('destSelect');
  const count = parseInt(select.options[select.selectedIndex].dataset.count) || 0;
  const countEl = document.getElementById('photoCount');
  const warningEl = document.getElementById('photoWarning');

  countEl.textContent = count;

  if (count >= 15) {
    warningEl.style.display = 'inline';
    document.querySelector('.btn-upload').disabled = true;
    document.querySelector('.btn-upload').style.opacity = '0.5';
    document.querySelector('.btn-upload').style.cursor = 'not-allowed';
  } else {
    warningEl.style.display = 'none';
    document.querySelector('.btn-upload').disabled = false;
    document.querySelector('.btn-upload').style.opacity = '1';
    document.querySelector('.btn-upload').style.cursor = 'pointer';
  }
}

// Initialize on page load
window.addEventListener('DOMContentLoaded', updatePhotoCount);

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
      document.getElementById('fileLabelText').textContent = 'Choose JPG/PNG/WebP (saved under 400KB)';
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

async function toggleSlot(id, slot, btn) {
  const isOn   = btn.classList.contains('chip-active');
  const newVal = isOn ? 0 : 1;
  const fd = new FormData();
  fd.append('id', id); fd.append('slot', slot); fd.append('value', newVal);
  btn.disabled = true;
  try {
    const res  = await fetch('../api/set_photo_role.php', { method:'POST', body:fd, headers:csrfHeader });
    const data = await res.json();
    if (data.ok) {
      // Reload so the badges and the "Live on your site" overview stay in sync
      // (e.g. swapping a Cover clears the previous one everywhere at once).
      location.reload();
      return;
    }
    alert(data.error || 'Could not update.');
  } catch { alert('Network error.'); }
  btn.disabled = false;
}

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

  if (!data.ok) { alert(data.error || 'Could not reorder.'); return; }
  if (!data.changed) return;

  // Swap cards in the DOM without reloading
  const grid    = document.getElementById('photoGrid');
  const cards   = [...grid.querySelectorAll('.photo-card')];
  const current = document.getElementById('photo-' + id);
  const idx     = cards.indexOf(current);

  if (direction === 'up' && idx > 0) {
    grid.insertBefore(current, cards[idx - 1]);
  } else if (direction === 'down' && idx < cards.length - 1) {
    grid.insertBefore(cards[idx + 1], current);
  }

  // Renumber all badges
  [...grid.querySelectorAll('.photo-card')].forEach((card, i) => {
    card.querySelector('.photo-num').textContent = i + 1;
  });
}
</script>
</body>
</html>
