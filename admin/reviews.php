<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../includes/vars.php';

$status_filter = $_GET['status'] ?? '';
$where = ''; $params = []; $types = '';
if (in_array($status_filter, ['pending','approved','rejected'], true)) {
    $where = 'WHERE status = ?';
    $params[] = $status_filter; $types .= 's';
}
$sql = "SELECT * FROM reviews $where ORDER BY submitted_at DESC";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$counts_raw = $conn->query("SELECT status, COUNT(*) c FROM reviews GROUP BY status")->fetch_all(MYSQLI_ASSOC);
$ct = array_column($counts_raw, 'c', 'status');
$total = array_sum($ct);
$conn->close();

$page_title = 'Reviews';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<meta name="csrf-token" content="<?= htmlspecialchars(admin_csrf_token()) ?>"/>
<title>Reviews — Himachal Safar Admin</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Poppins:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>
<link href="assets/admin.css?v=<?php echo @filemtime(__DIR__ . '/assets/admin.css'); ?>" rel="stylesheet"/>
<style>
.rv-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(360px,1fr));gap:18px}
.rv-mod-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px;display:flex;flex-direction:column;gap:12px}
.rv-mod-card.is-pending{border-color:rgba(245,158,11,.30)}
.rv-mod-card.is-approved{border-color:rgba(34,197,94,.25)}
.rv-mod-card.is-rejected{opacity:.55}
.rv-mod-head{display:flex;align-items:center;justify-content:space-between;gap:10px}
.rv-mod-name{font-weight:700;font-size:14px;color:var(--ink)}
.rv-mod-meta{font-size:12px;color:var(--muted);margin-top:2px}
.rv-mod-stars{color:#c9a84c;font-size:13px;letter-spacing:1px}
.rv-mod-text{font-size:13.5px;color:var(--ink-2);line-height:1.6;flex:1}
.rv-mod-photo{margin-top:6px}
.rv-mod-photo img{width:100%;max-height:200px;object-fit:cover;border-radius:8px;border:1px solid var(--border)}
.rv-mod-foot{display:flex;align-items:center;gap:6px;flex-wrap:wrap;padding-top:10px;border-top:1px solid var(--border)}
.rv-act{font:600 12px 'Inter',sans-serif;padding:6px 12px;border-radius:6px;cursor:pointer;border:1px solid transparent;display:inline-flex;align-items:center;gap:5px}
.rv-act-approve{background:rgba(34,197,94,.14);color:#86efac;border-color:rgba(34,197,94,.30)}
.rv-act-approve:hover{background:rgba(34,197,94,.24)}
.rv-act-reject{background:rgba(245,158,11,.14);color:#fcd34d;border-color:rgba(245,158,11,.30)}
.rv-act-reject:hover{background:rgba(245,158,11,.24)}
.rv-act-delete{background:rgba(185,28,28,.14);color:#fca5a5;border-color:rgba(185,28,28,.30);margin-left:auto}
.rv-act-delete:hover{background:rgba(185,28,28,.24)}
.rv-status-tag{font:700 10px 'Inter',sans-serif;padding:3px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:.08em}
.rv-status-tag.pending{background:rgba(245,158,11,.15);color:#fcd34d}
.rv-status-tag.approved{background:rgba(34,197,94,.15);color:#86efac}
.rv-status-tag.rejected{background:rgba(185,28,28,.15);color:#fca5a5}
</style>
</head>
<body>
<div class="admin-wrap">
  <?php require_once 'partials/sidebar.php'; ?>
  <div class="admin-main">
    <?php require_once 'partials/topbar.php'; ?>
    <div class="admin-content">

      <div class="stat-grid">
        <a href="reviews.php" class="stat-card card-green">
          <i class="fas fa-star sc-bg-icon"></i>
          <div class="sc-value"><?= $total ?></div>
          <div class="sc-label">Total Reviews</div>
        </a>
        <a href="reviews.php?status=pending" class="stat-card card-amber">
          <i class="fas fa-hourglass-half sc-bg-icon"></i>
          <div class="sc-value"><?= $ct['pending'] ?? 0 ?></div>
          <div class="sc-label">Pending</div>
        </a>
        <a href="reviews.php?status=approved" class="stat-card card-emerald">
          <i class="fas fa-circle-check sc-bg-icon"></i>
          <div class="sc-value"><?= $ct['approved'] ?? 0 ?></div>
          <div class="sc-label">Approved</div>
        </a>
        <a href="reviews.php?status=rejected" class="stat-card card-red">
          <i class="fas fa-ban sc-bg-icon"></i>
          <div class="sc-value"><?= $ct['rejected'] ?? 0 ?></div>
          <div class="sc-label">Rejected</div>
        </a>
      </div>

      <?php if (empty($reviews)): ?>
      <div class="empty-state">
        <i class="fas fa-star"></i>
        <p>No reviews <?= $status_filter ? "with status \"$status_filter\"" : 'yet' ?>.</p>
      </div>
      <?php else: ?>
      <div class="rv-grid" id="rvGrid">
        <?php foreach ($reviews as $r): ?>
        <div class="rv-mod-card is-<?= $r['status'] ?>" id="rv-<?= $r['id'] ?>" data-status="<?= $r['status'] ?>">
          <div class="rv-mod-head">
            <div>
              <div class="rv-mod-name"><?= htmlspecialchars($r['name']) ?></div>
              <div class="rv-mod-meta">
                <?= htmlspecialchars($r['city'] ?: '—') ?>
                <?= $r['route'] ? ' · ' . htmlspecialchars($r['route']) : '' ?>
              </div>
            </div>
            <span class="rv-status-tag <?= $r['status'] ?>"><?= $r['status'] ?></span>
          </div>
          <div class="rv-mod-stars"><?= str_repeat('★', (int)$r['rating']) . str_repeat('☆', 5 - (int)$r['rating']) ?></div>
          <div class="rv-mod-text"><?= nl2br(htmlspecialchars($r['review_text'])) ?></div>
          <?php if (!empty($r['photo'])): ?>
          <div class="rv-mod-photo">
            <img src="../uploads/reviews/<?= htmlspecialchars($r['photo']) ?>" alt="Photo by <?= htmlspecialchars($r['name']) ?>">
          </div>
          <?php endif; ?>
          <div class="rv-mod-meta"><i class="far fa-clock"></i> <?= date('d M Y, H:i', strtotime($r['submitted_at'])) ?></div>
          <div class="rv-mod-foot">
            <?php if ($r['status'] !== 'approved'): ?>
            <button class="rv-act rv-act-approve" onclick="moderate(<?= $r['id'] ?>, 'approved')"><i class="fas fa-check"></i> Approve</button>
            <?php endif; ?>
            <?php if ($r['status'] !== 'rejected'): ?>
            <button class="rv-act rv-act-reject" onclick="moderate(<?= $r['id'] ?>, 'rejected')"><i class="fas fa-times"></i> Reject</button>
            <?php endif; ?>
            <?php if ($r['status'] !== 'pending'): ?>
            <button class="rv-act rv-act-reject" onclick="moderate(<?= $r['id'] ?>, 'pending')" title="Re-open for review"><i class="fas fa-rotate-left"></i></button>
            <?php endif; ?>
            <button class="rv-act rv-act-delete" onclick="del(<?= $r['id'] ?>, '<?= htmlspecialchars($r['name'], ENT_QUOTES) ?>')"><i class="fas fa-trash"></i></button>
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

async function moderate(id, status) {
  const fd = new FormData();
  fd.append('id', id); fd.append('status', status);
  const data = await fetch('../api/moderate_review.php', { method: 'POST', body: fd, headers: csrfHeader }).then(r => r.json());
  if (data.ok) location.reload();
  else alert(data.error || 'Failed to update status.');
}

async function del(id, name) {
  if (!confirm(`Delete review by "${name}"? This cannot be undone.`)) return;
  const fd = new FormData();
  fd.append('id', id);
  const data = await fetch('../api/delete_review.php', { method: 'POST', body: fd, headers: csrfHeader }).then(r => r.json());
  if (data.ok) document.getElementById('rv-' + id)?.remove();
  else alert(data.error || 'Failed to delete review.');
}
</script>
</body>
</html>
