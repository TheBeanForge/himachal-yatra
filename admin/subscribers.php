<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../includes/vars.php';
$csrf = admin_csrf_token();
$msg  = '';

// Table may not exist until the first footer signup — create it here too.
try {
    $conn->query(
        "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            email      VARCHAR(190) NOT NULL UNIQUE,
            source     VARCHAR(30) DEFAULT 'footer',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
} catch (Throwable) {}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    require_admin_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $s = $conn->prepare('DELETE FROM newsletter_subscribers WHERE id = ?');
        $s->bind_param('i', $id); $s->execute(); $s->close();
        audit_log('subscriber_delete', "id=$id");
        $msg = 'ok:Subscriber removed.';
    }
}

// CSV export
if (($_GET['export'] ?? '') === 'csv') {
    $rows = $conn->query('SELECT email, source, created_at FROM newsletter_subscribers ORDER BY created_at DESC')->fetch_all(MYSQLI_ASSOC);
    $conn->close();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="subscribers_' . date('Y-m-d') . '.csv"');
    $f = fopen('php://output', 'w');
    fputcsv($f, ['Email', 'Source', 'Subscribed At']);
    foreach ($rows as $r) fputcsv($f, [$r['email'], $r['source'], $r['created_at']]);
    fclose($f); exit;
}

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = $conn->prepare('SELECT * FROM newsletter_subscribers WHERE email LIKE ? ORDER BY created_at DESC LIMIT 500');
    $like = "%$q%";
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $subs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $subs = $conn->query('SELECT * FROM newsletter_subscribers ORDER BY created_at DESC LIMIT 500')->fetch_all(MYSQLI_ASSOC);
}
$total = (int)($conn->query('SELECT COUNT(*) c FROM newsletter_subscribers')->fetch_assoc()['c'] ?? 0);
$last30 = (int)($conn->query('SELECT COUNT(*) c FROM newsletter_subscribers WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)')->fetch_assoc()['c'] ?? 0);
$conn->close();
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?=h($csrf)?>"><title>Subscribers — Admin</title>
<link rel="icon" href="../favicon.ico" sizes="32x32">
<link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="../assets/brand/mark-192.png">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="assets/admin.css?v=<?php echo @filemtime(__DIR__ . '/assets/admin.css'); ?>" rel="stylesheet">
</head><body>
<?php require 'partials/topbar.php'; ?>
<div class="d-flex"><?php require 'partials/sidebar.php'; ?>
<main class="admin-main flex-grow-1 p-4">
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h1 class="admin-page-title mb-0">Newsletter Subscribers</h1>
      <p class="admin-page-sub mb-0"><?= $total ?> total · <?= $last30 ?> in the last 30 days — collected by the footer "Journey Notes" form</p>
    </div>
    <a class="btn-primary-gold" href="?export=csv"><i class="fas fa-download"></i> Export CSV</a>
  </div>
  <?php if($msg):[$t,$tx]=explode(':',$msg,2);?>
  <div class="alert alert-<?=$t==='ok'?'success':'danger'?> alert-dismissible fade show py-2 mb-3"><?=h($tx)?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif;?>

  <form class="admin-toolbar" method="get">
    <input class="admin-input form-control" style="max-width:320px" type="text" name="q" value="<?=h($q)?>" placeholder="Search email…">
    <button class="btn-filter" type="submit"><i class="fas fa-magnifying-glass"></i> Search</button>
    <?php if($q!==''):?><a class="btn-clear" href="subscribers.php">Clear</a><?php endif;?>
  </form>

  <div class="admin-card p-0" style="overflow:hidden">
    <div class="table-responsive">
      <table class="table mb-0" style="font-size:13.5px">
        <thead><tr>
          <th style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted)">Email</th>
          <th style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted)">Source</th>
          <th style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted)">Subscribed</th>
          <th style="width:70px"></th>
        </tr></thead>
        <tbody>
        <?php if(!$subs):?>
        <tr><td colspan="4"><div class="empty-state"><i class="fas fa-envelope-open-text"></i><p><?= $q !== '' ? 'No subscribers match that search.' : 'No subscribers yet — the footer form on every page feeds this list.' ?></p></div></td></tr>
        <?php endif;?>
        <?php foreach($subs as $s):?>
        <tr>
          <td class="td-name"><?=h($s['email'])?></td>
          <td><span class="badge badge-new"><?=h($s['source'])?></span></td>
          <td style="color:var(--muted)"><?=h(date('d M Y, h:i A', strtotime($s['created_at'])))?></td>
          <td>
            <form method="post" onsubmit="return confirm('Remove this subscriber?')" style="margin:0">
              <input type="hidden" name="csrf_token" value="<?=h($csrf)?>">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?=(int)$s['id']?>">
              <button class="btn-icon btn-icon-danger" type="submit" title="Remove"><i class="fas fa-trash"></i></button>
            </form>
          </td>
        </tr>
        <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </div>
</main></div>
</body></html>
