<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../includes/vars.php';
$csrf = admin_csrf_token(); $msg = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    require_admin_csrf();
    $action = $_POST['action']??'';

    /* ── Taxes / Fees ── */
    if ($action==='save_tax') {
        $id     = (int)($_POST['id']??0);
        $name   = trim($_POST['name']??'');
        $type   = in_array($_POST['type']??'',['percentage','flat'])?$_POST['type']:'percentage';
        $val    = max(0,(float)($_POST['value']??0));
        $apply  = in_array($_POST['apply_on']??'',['total','package_cost','vehicle_cost'])?$_POST['apply_on']:'total';
        $order  = (int)($_POST['sort_order']??0);
        $active = (int)((($_POST['active']??'1')==='1'));
        if(!$name){ $msg='error:Tax name is required.'; }
        else {
            if($id){ $s=$conn->prepare('UPDATE taxes_fees SET name=?,type=?,value=?,apply_on=?,sort_order=?,active=? WHERE id=?'); $s->bind_param('ssdsiii',$name,$type,$val,$apply,$order,$active,$id); }
            else   { $s=$conn->prepare('INSERT INTO taxes_fees (name,type,value,apply_on,sort_order,active) VALUES (?,?,?,?,?,?)'); $s->bind_param('ssdsii',$name,$type,$val,$apply,$order,$active); }
            $s->execute(); $s->close(); audit_log('tax_save',$name); $msg='ok:Tax/Fee saved.';
        }
    }
    if ($action==='delete_tax')  { $id=(int)($_POST['id']??0); if($id){ $conn->query("DELETE FROM taxes_fees WHERE id=$id"); $msg='ok:Deleted.'; } }
    if ($action==='toggle_tax')  { $id=(int)($_POST['id']??0); if($id){ $conn->query("UPDATE taxes_fees SET active=IF(active=1,0,1) WHERE id=$id"); $msg='ok:Toggled.'; } }

    /* ── Seasonal ── */
    if ($action==='save_season') {
        $id   = (int)($_POST['id']??0);
        $name = trim($_POST['name']??'');
        $sd   = trim($_POST['start_date']??'');
        $ed   = trim($_POST['end_date']??'');
        $pct  = max(0,(float)($_POST['surcharge_pct']??0));
        $active=(int)((($_POST['active']??'1')==='1'));
        if(!$name||!$sd||!$ed){ $msg='error:Name, start date and end date are required.'; }
        elseif($ed<=$sd)       { $msg='error:End date must be after start date.'; }
        else {
            if($id){ $s=$conn->prepare('UPDATE seasonal_pricing SET name=?,start_date=?,end_date=?,surcharge_pct=?,active=? WHERE id=?'); $s->bind_param('sssdii',$name,$sd,$ed,$pct,$active,$id); }
            else   { $s=$conn->prepare('INSERT INTO seasonal_pricing (name,start_date,end_date,surcharge_pct,active) VALUES (?,?,?,?,?)'); $s->bind_param('sssdi',$name,$sd,$ed,$pct,$active); }
            $s->execute(); $s->close(); audit_log('season_save',$name); $msg='ok:Seasonal pricing saved.';
        }
    }
    if ($action==='delete_season'){ $id=(int)($_POST['id']??0); if($id){ $conn->query("DELETE FROM seasonal_pricing WHERE id=$id"); $msg='ok:Deleted.'; } }
    if ($action==='toggle_season'){ $id=(int)($_POST['id']??0); if($id){ $conn->query("UPDATE seasonal_pricing SET active=IF(active=1,0,1) WHERE id=$id"); $msg='ok:Toggled.'; } }
}

$taxes   = $conn->query("SELECT * FROM taxes_fees ORDER BY sort_order ASC,id ASC")->fetch_all(MYSQLI_ASSOC);
$seasons = $conn->query("SELECT * FROM seasonal_pricing ORDER BY start_date ASC")->fetch_all(MYSQLI_ASSOC);
$conn->close();
$tab = $_GET['tab']??'taxes';
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?=h($csrf)?>"><title>Pricing Rules — Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="assets/admin.css?v=<?php echo @filemtime(__DIR__ . '/assets/admin.css'); ?>" rel="stylesheet">
</head><body>
<?php require 'partials/topbar.php'; ?>
<div class="d-flex"><?php require 'partials/sidebar.php'; ?>
<main class="admin-main flex-grow-1 p-4">
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div><h1 class="admin-page-title mb-0">Pricing Rules</h1><p class="admin-page-sub mb-0">Configure taxes, fees &amp; seasonal surcharges</p></div>
  </div>
  <?php if($msg):[$t,$tx]=explode(':',$msg,2);?>
  <div class="alert alert-<?=$t==='ok'?'success':'danger'?> alert-dismissible fade show py-2 mb-3"><?=h($tx)?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif;?>

  <ul class="nav nav-tabs mb-4" style="border-color:var(--border)">
    <li class="nav-item"><a class="nav-link <?=$tab==='taxes'?'active':''?>" href="?tab=taxes" style="<?=$tab==='taxes'?'color:var(--lime);border-bottom-color:var(--lime)':'color:var(--muted)'?>">Taxes &amp; Fees</a></li>
    <li class="nav-item"><a class="nav-link <?=$tab==='seasonal'?'active':''?>" href="?tab=seasonal" style="<?=$tab==='seasonal'?'color:var(--lime);border-bottom-color:var(--lime)':'color:var(--muted)'?>">Seasonal Pricing</a></li>
  </ul>

  <?php if($tab==='taxes'):?>
  <div class="d-flex justify-content-end mb-3"><button class="btn btn-primary-gold btn-sm" onclick="openTax(null)"><i class="fas fa-plus"></i> Add Tax / Fee</button></div>
  <div class="admin-card">
    <div class="table-responsive">
      <table class="table mb-0" style="font-size:13.5px">
        <thead><tr>
          <?php foreach(['Name','Type','Value','Apply On','Order','Status',''] as $h): ?>
          <th style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--muted)"><?=$h?></th>
          <?php endforeach;?>
        </tr></thead>
        <tbody>
        <?php if(!$taxes):?><tr><td colspan="7" class="text-center py-4" style="color:var(--muted)">No taxes configured.</td></tr><?php endif;?>
        <?php foreach($taxes as $t):?>
        <tr>
          <td style="font-weight:600;color:var(--ink)"><?=h($t['name'])?></td>
          <td><span class="badge bg-secondary"><?=ucfirst($t['type'])?></span></td>
          <td><?=$t['type']==='percentage'?h($t['value']).'%':'₹'.number_format((float)$t['value'],0)?></td>
          <td style="font-size:12px;color:var(--muted)"><?=ucwords(str_replace('_',' ',$t['apply_on']))?></td>
          <td><?=$t['sort_order']?></td>
          <td><span class="badge <?=$t['active']?'bg-success':'bg-secondary'?>"><?=$t['active']?'Active':'Off'?></span></td>
          <td><div class="d-flex gap-1">
            <button class="btn btn-sm btn-icon" onclick='openTax(<?=json_encode($t)?>)'><i class="fas fa-pen"></i></button>
            <form method="post" style="display:inline" onsubmit="return confirm('Toggle?')"><input type="hidden" name="action" value="toggle_tax"><input type="hidden" name="id" value="<?=(int)$t['id']?>"><input type="hidden" name="csrf_token" value="<?=h($csrf)?>"><button class="btn btn-sm btn-icon" type="submit"><i class="fas fa-power-off"></i></button></form>
            <form method="post" style="display:inline" onsubmit="return confirm('Delete tax?')"><input type="hidden" name="action" value="delete_tax"><input type="hidden" name="id" value="<?=(int)$t['id']?>"><input type="hidden" name="csrf_token" value="<?=h($csrf)?>"><button class="btn btn-sm btn-icon btn-icon-danger" type="submit"><i class="fas fa-trash"></i></button></form>
          </div></td>
        </tr>
        <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </div>
  <?php else:?>
  <div class="d-flex justify-content-end mb-3"><button class="btn btn-primary-gold btn-sm" onclick="openSeason(null)"><i class="fas fa-plus"></i> Add Season</button></div>
  <div class="admin-card">
    <div class="table-responsive">
      <table class="table mb-0" style="font-size:13.5px">
        <thead><tr>
          <?php foreach(['Name','Start Date','End Date','Surcharge %','Status',''] as $h):?>
          <th style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--muted)"><?=$h?></th>
          <?php endforeach;?>
        </tr></thead>
        <tbody>
        <?php if(!$seasons):?><tr><td colspan="6" class="text-center py-4" style="color:var(--muted)">No seasonal pricing configured.</td></tr><?php endif;?>
        <?php foreach($seasons as $s):?>
        <tr>
          <td style="font-weight:600;color:var(--ink)"><?=h($s['name'])?></td>
          <td><?=$s['start_date']?></td>
          <td><?=$s['end_date']?></td>
          <td><?=$s['surcharge_pct']>0?'+'.$s['surcharge_pct'].'%':'<span style="color:var(--muted)">0%</span>'?></td>
          <td><span class="badge <?=$s['active']?'bg-success':'bg-secondary'?>"><?=$s['active']?'Active':'Off'?></span></td>
          <td><div class="d-flex gap-1">
            <button class="btn btn-sm btn-icon" onclick='openSeason(<?=json_encode($s)?>)'><i class="fas fa-pen"></i></button>
            <form method="post" style="display:inline" onsubmit="return confirm('Toggle?')"><input type="hidden" name="action" value="toggle_season"><input type="hidden" name="id" value="<?=(int)$s['id']?>"><input type="hidden" name="csrf_token" value="<?=h($csrf)?>"><button class="btn btn-sm btn-icon" type="submit"><i class="fas fa-power-off"></i></button></form>
            <form method="post" style="display:inline" onsubmit="return confirm('Delete season?')"><input type="hidden" name="action" value="delete_season"><input type="hidden" name="id" value="<?=(int)$s['id']?>"><input type="hidden" name="csrf_token" value="<?=h($csrf)?>"><button class="btn btn-sm btn-icon btn-icon-danger" type="submit"><i class="fas fa-trash"></i></button></form>
          </div></td>
        </tr>
        <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif;?>
</main></div>

<!-- Tax Modal -->
<div class="modal fade" id="taxModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content" style="background:var(--surface);border:1px solid var(--border)">
  <form method="post"><input type="hidden" name="action" value="save_tax"><input type="hidden" name="csrf_token" value="<?=h($csrf)?>"><input type="hidden" name="id" id="tx_id" value="0">
  <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title" id="taxModalTitle" style="color:var(--ink)">Add Tax / Fee</h5><button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1)"></button></div>
  <div class="modal-body d-flex flex-column gap-3">
    <div><label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Name *</label><input type="text" name="name" id="tx_name" class="form-control admin-input" required placeholder="e.g. GST"></div>
    <div class="row g-2">
      <div class="col-4"><label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Type</label>
        <select name="type" id="tx_type" class="form-select admin-input"><option value="percentage">Percentage (%)</option><option value="flat">Flat Amount (₹)</option></select></div>
      <div class="col-4"><label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Value</label><input type="number" name="value" id="tx_val" class="form-control admin-input" value="5" min="0" step="0.01"></div>
      <div class="col-4"><label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Sort</label><input type="number" name="sort_order" id="tx_order" class="form-control admin-input" value="0" min="0"></div>
    </div>
    <div class="row g-2">
      <div class="col-6"><label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Apply On</label>
        <select name="apply_on" id="tx_apply" class="form-select admin-input">
          <option value="total">Grand Total</option>
          <option value="package_cost">Package Cost Only</option>
          <option value="vehicle_cost">Vehicle Cost Only</option>
        </select></div>
      <div class="col-6"><label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Status</label>
        <select name="active" id="tx_active" class="form-select admin-input"><option value="1">Active</option><option value="0">Inactive</option></select></div>
    </div>
  </div>
  <div class="modal-footer" style="border-color:var(--border)"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary-gold">Save</button></div>
  </form>
</div></div></div>

<!-- Season Modal -->
<div class="modal fade" id="seasonModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content" style="background:var(--surface);border:1px solid var(--border)">
  <form method="post"><input type="hidden" name="action" value="save_season"><input type="hidden" name="csrf_token" value="<?=h($csrf)?>"><input type="hidden" name="id" id="ss_id" value="0">
  <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title" id="seasonModalTitle" style="color:var(--ink)">Add Season</h5><button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1)"></button></div>
  <div class="modal-body d-flex flex-column gap-3">
    <div><label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Season Name *</label><input type="text" name="name" id="ss_name" class="form-control admin-input" required placeholder="e.g. Peak Summer"></div>
    <div class="row g-2">
      <div class="col-6"><label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Start Date *</label><input type="date" name="start_date" id="ss_start" class="form-control admin-input" required></div>
      <div class="col-6"><label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">End Date *</label><input type="date" name="end_date" id="ss_end" class="form-control admin-input" required></div>
    </div>
    <div class="row g-2">
      <div class="col-6"><label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Surcharge % (on subtotal)</label><input type="number" name="surcharge_pct" id="ss_pct" class="form-control admin-input" value="0" min="0" max="100" step="0.5"></div>
      <div class="col-6"><label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Status</label><select name="active" id="ss_active" class="form-select admin-input"><option value="1">Active</option><option value="0">Inactive</option></select></div>
    </div>
  </div>
  <div class="modal-footer" style="border-color:var(--border)"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary-gold">Save</button></div>
  </form>
</div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const taxModal=new bootstrap.Modal(document.getElementById('taxModal'));
const seasonModal=new bootstrap.Modal(document.getElementById('seasonModal'));
function openTax(t){
  document.getElementById('taxModalTitle').textContent=t?'Edit Tax / Fee':'Add Tax / Fee';
  document.getElementById('tx_id').value=t?.id||0;
  document.getElementById('tx_name').value=t?.name||'';
  document.getElementById('tx_type').value=t?.type||'percentage';
  document.getElementById('tx_val').value=t?.value||0;
  document.getElementById('tx_apply').value=t?.apply_on||'total';
  document.getElementById('tx_order').value=t?.sort_order||0;
  document.getElementById('tx_active').value=t?.active!=null?String(t.active):'1';
  taxModal.show();
}
function openSeason(s){
  document.getElementById('seasonModalTitle').textContent=s?'Edit Season':'Add Season';
  document.getElementById('ss_id').value=s?.id||0;
  document.getElementById('ss_name').value=s?.name||'';
  document.getElementById('ss_start').value=s?.start_date||'';
  document.getElementById('ss_end').value=s?.end_date||'';
  document.getElementById('ss_pct').value=s?.surcharge_pct||0;
  document.getElementById('ss_active').value=s?.active!=null?String(s.active):'1';
  seasonModal.show();
}
</script></body></html>
