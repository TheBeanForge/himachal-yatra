<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../includes/vars.php';
$csrf = admin_csrf_token(); $msg = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    require_admin_csrf();
    $action = $_POST['action']??'';
    if ($action==='save') {
        $id     = (int)($_POST['id']??0);
        $name   = trim($_POST['name']??'');
        $key    = preg_replace('/[^a-z0-9_]/','',strtolower(trim($_POST['dest_key']??'')));
        $extra  = max(0,(float)($_POST['extra_per_day']??0));
        $order  = (int)($_POST['sort_order']??0);
        $active = (int)(($_POST['active']??'1')==='1');
        if(!$name||!$key){ $msg='error:Name and destination key are required.'; }
        else {
            if($id){ $s=$conn->prepare('UPDATE destinations SET name=?,dest_key=?,extra_per_day=?,sort_order=?,active=? WHERE id=?'); $s->bind_param('ssdiii',$name,$key,$extra,$order,$active,$id); }
            else   { $s=$conn->prepare('INSERT INTO destinations (name,dest_key,extra_per_day,sort_order,active) VALUES (?,?,?,?,?)'); $s->bind_param('ssdii',$name,$key,$extra,$order,$active); }
            try{ $s->execute(); $s->close(); audit_log('dest_save',"$name ($key)"); $msg='ok:Destination saved.'; }
            catch(mysqli_sql_exception $e){ $msg='error:'.$e->getMessage(); }
        }
    }
    if ($action==='delete'){ $id=(int)($_POST['id']??0); if($id){ $conn->query("DELETE FROM destinations WHERE id=$id"); audit_log('dest_delete',"id=$id"); $msg='ok:Deleted.'; } }
    if ($action==='toggle'){ $id=(int)($_POST['id']??0); if($id){ $conn->query("UPDATE destinations SET active=IF(active=1,0,1) WHERE id=$id"); $msg='ok:Status toggled.'; } }
}
$dests = $conn->query("SELECT * FROM destinations ORDER BY sort_order ASC,name ASC")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?=h($csrf)?>"><title>Destinations — Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="assets/admin.css?v=<?php echo @filemtime(__DIR__ . '/assets/admin.css'); ?>" rel="stylesheet">
</head><body>
<?php require 'partials/topbar.php'; ?>
<div class="d-flex"><?php require 'partials/sidebar.php'; ?>
<main class="admin-main flex-grow-1 p-4">
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div><h1 class="admin-page-title mb-0">Destinations</h1><p class="admin-page-sub mb-0">Manage destinations &amp; their extra per-day charges</p></div>
    <button class="btn btn-primary-gold" onclick="openM(null)"><i class="fas fa-plus"></i> Add Destination</button>
  </div>
  <?php if($msg):[$t,$tx]=explode(':',$msg,2);?>
  <div class="alert alert-<?=$t==='ok'?'success':'danger'?> alert-dismissible fade show py-2 mb-3"><?=h($tx)?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif;?>
  <div class="admin-card">
    <div class="table-responsive">
      <table class="table mb-0" style="font-size:13.5px">
        <thead><tr>
          <th style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--muted)">#</th>
          <th style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--muted)">Name</th>
          <th style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--muted)">Key</th>
          <th style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--muted)">Extra/Day/Person</th>
          <th style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--muted)">Order</th>
          <th style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--muted)">Status</th>
          <th style="width:110px"></th>
        </tr></thead>
        <tbody>
        <?php if(!$dests):?><tr><td colspan="7" class="text-center py-4" style="color:var(--muted)">No destinations yet.</td></tr><?php endif;?>
        <?php foreach($dests as $d):?>
        <tr>
          <td style="color:var(--muted)"><?=(int)$d['id']?></td>
          <td style="font-weight:600;color:var(--ink)"><?=h($d['name'])?></td>
          <td><code style="background:var(--surface-2);padding:2px 6px;border-radius:4px;font-size:12px"><?=h($d['dest_key'])?></code></td>
          <td><?=$d['extra_per_day']>0?'₹'.number_format((float)$d['extra_per_day'],0).'/day':'<span style="color:var(--muted)">—</span>'?></td>
          <td><?=(int)$d['sort_order']?></td>
          <td><span class="badge <?=$d['active']?'bg-success':'bg-secondary'?>"><?=$d['active']?'Active':'Inactive'?></span></td>
          <td><div class="d-flex gap-1">
            <button class="btn btn-sm btn-icon" onclick='openM(<?=json_encode($d)?>)'><i class="fas fa-pen"></i></button>
            <form method="post" style="display:inline" onsubmit="return confirm('Toggle?')"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?=(int)$d['id']?>"><input type="hidden" name="csrf_token" value="<?=h($csrf)?>"><button class="btn btn-sm btn-icon" type="submit"><i class="fas fa-power-off"></i></button></form>
            <form method="post" style="display:inline" onsubmit="return confirm('Delete?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$d['id']?>"><input type="hidden" name="csrf_token" value="<?=h($csrf)?>"><button class="btn btn-sm btn-icon btn-icon-danger" type="submit"><i class="fas fa-trash"></i></button></form>
          </div></td>
        </tr>
        <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </div>
</main></div>
<div class="modal fade" id="destModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content" style="background:var(--surface);border:1px solid var(--border)">
  <form method="post"><input type="hidden" name="action" value="save"><input type="hidden" name="csrf_token" value="<?=h($csrf)?>"><input type="hidden" name="id" id="d_id" value="0">
  <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title" id="destModalTitle" style="color:var(--ink)">Add Destination</h5><button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1)"></button></div>
  <div class="modal-body d-flex flex-column gap-3">
    <div><label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Display Name *</label><input type="text" name="name" id="d_name" class="form-control admin-input" required maxlength="100" placeholder="e.g. Manali"></div>
    <div><label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Destination Key * <span style="color:var(--muted);font-weight:400">(lowercase, no spaces)</span></label><input type="text" name="dest_key" id="d_key" class="form-control admin-input" required maxlength="50" placeholder="e.g. manali"></div>
    <div class="row g-2">
      <div class="col-6"><label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Extra Charge/Day/Person (₹)</label><input type="number" name="extra_per_day" id="d_extra" class="form-control admin-input" value="0" min="0" step="50" placeholder="0 = no surcharge"></div>
      <div class="col-3"><label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Sort Order</label><input type="number" name="sort_order" id="d_order" class="form-control admin-input" value="0" min="0"></div>
      <div class="col-3"><label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Status</label><select name="active" id="d_active" class="form-select admin-input"><option value="1">Active</option><option value="0">Inactive</option></select></div>
    </div>
  </div>
  <div class="modal-footer" style="border-color:var(--border)"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary-gold">Save</button></div>
  </form>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const modal=new bootstrap.Modal(document.getElementById('destModal'));
function openM(d){
  document.getElementById('destModalTitle').textContent=d?'Edit Destination':'Add Destination';
  document.getElementById('d_id').value=d?.id||0;
  document.getElementById('d_name').value=d?.name||'';
  document.getElementById('d_key').value=d?.dest_key||'';
  document.getElementById('d_extra').value=d?.extra_per_day||0;
  document.getElementById('d_order').value=d?.sort_order||0;
  document.getElementById('d_active').value=d?.active!=null?String(d.active):'1';
  modal.show();
}
document.getElementById('d_name').addEventListener('input',function(){
  const k=document.getElementById('d_key');
  if(!k.dataset.manual) k.value=this.value.toLowerCase().replace(/\s+/g,'_').replace(/[^a-z0-9_]/g,'');
});
document.getElementById('d_key').addEventListener('input',function(){ this.dataset.manual='1'; });
</script></body></html>
