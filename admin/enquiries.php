<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../includes/vars.php';
$csrf = admin_csrf_token();

// Ensure the schema pieces this page needs exist (older installs may lack them).
try { $conn->query("ALTER TABLE tour_packages ADD COLUMN destination_key VARCHAR(50) NULL"); } catch (Throwable) {}
try { $conn->query("ALTER TABLE booking_enquiries MODIFY COLUMN status ENUM('new','contacted','quoted','confirmed','closed','cancelled') DEFAULT 'new'"); } catch (Throwable) {}

// Status update
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='update_status') {
    require_admin_csrf();
    $id  = (int)($_POST['id']??0);
    $st  = in_array($_POST['status']??'',['new','contacted','quoted','confirmed','closed'])?$_POST['status']:'new';
    if($id){ $s=$conn->prepare('UPDATE booking_enquiries SET status=? WHERE id=?'); $s->bind_param('si',$st,$id); $s->execute(); $s->close(); audit_log('enquiry_status',"id=$id status=$st"); }
    header('Content-Type: application/json'); echo json_encode(['ok'=>true]); exit;
}

// CSV Export
if (($_GET['export']??'')==='csv') {
    $rows=$conn->query("SELECT be.*,COALESCE(tp.package_name,be.trip_destination) AS package_name,COALESCE(pl.city,be.pickup_custom) AS pickup_city,v.vehicle_name FROM booking_enquiries be LEFT JOIN tour_packages tp ON be.package_id=tp.id LEFT JOIN pickup_locations pl ON be.pickup_location_id=pl.id LEFT JOIN vehicles v ON be.vehicle_id=v.id ORDER BY be.created_at DESC")->fetch_all(MYSQLI_ASSOC);
    $conn->close();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="enquiries_'.date('Y-m-d').'.csv"');
    $f=fopen('php://output','w');
    fputcsv($f,['ID','Date','Source','Name','Mobile','Email','Destination','Pickup City','Vehicle','Travelers','Pickup Date','Drop Date','Total (₹)','Status']);
    foreach($rows as $r) fputcsv($f,[$r['id'],$r['created_at'],$r['source']??'calculator',$r['customer_name'],$r['mobile'],$r['email']??'',$r['package_name']??'',$r['pickup_city']??'',$r['vehicle_name']??'',$r['travelers'],$r['pickup_date']??'',$r['drop_date']??'',number_format((float)($r['estimated_price']??0),2),$r['status']??'new']);
    fclose($f); exit;
}

// Filters
$status_f  = $_GET['status']??'';
$dest_f    = trim($_GET['dest']??'');
$source_f  = $_GET['source']??'';
$search_f  = trim($_GET['q']??'');
$where=[]; $params=[]; $types='';
if(in_array($status_f,['new','contacted','quoted','confirmed','closed'],true)){ $where[]='be.status=?'; $params[]=$status_f; $types.='s'; }
if(in_array($source_f,['calculator','whatsapp'],true)){ $where[]='be.source=?'; $params[]=$source_f; $types.='s'; }
if($dest_f){ $where[]='tp.destination_key=?'; $params[]=$dest_f; $types.='s'; }
if($search_f){ $where[]='(be.customer_name LIKE ? OR be.mobile LIKE ? OR tp.package_name LIKE ? OR be.trip_destination LIKE ?)'; $l="%$search_f%"; $params=array_merge($params,[$l,$l,$l,$l]); $types.='ssss'; }
$wsql=implode(' AND ',$where);
$sql="SELECT be.*,COALESCE(tp.package_name,be.trip_destination) AS package_name,tp.destination_key,COALESCE(pl.city,be.pickup_custom) AS pickup_city,v.vehicle_name,v.seating_capacity FROM booking_enquiries be LEFT JOIN tour_packages tp ON be.package_id=tp.id LEFT JOIN pickup_locations pl ON be.pickup_location_id=pl.id LEFT JOIN vehicles v ON be.vehicle_id=v.id".($wsql?" WHERE $wsql":'')." ORDER BY be.created_at DESC LIMIT 200";
$stmt=$conn->prepare($sql);
if($params) $stmt->bind_param($types,...$params);
$stmt->execute(); $rows=$stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();
$counts=$conn->query("SELECT status,COUNT(*) c FROM booking_enquiries GROUP BY status")->fetch_all(MYSQLI_ASSOC);
$ct=array_column($counts,'c','status');
try { $dests=array_column($conn->query("SELECT DISTINCT destination_key FROM tour_packages WHERE destination_key IS NOT NULL AND destination_key!='' ORDER BY destination_key")->fetch_all(MYSQLI_NUM), 0); }
catch(mysqli_sql_exception){ $dests=[]; }

// ── 30-day analytics: enquiries per day + headline numbers ──
$chart = array_fill(0, 30, 0);          // index 0 = 29 days ago … 29 = today
$chartDates = [];
for ($i = 0; $i < 30; $i++) $chartDates[$i] = date('Y-m-d', strtotime('-' . (29 - $i) . ' days'));
$sumEst30 = 0.0; $count30 = 0;
try {
    $r = $conn->query(
        "SELECT DATE(created_at) d, COUNT(*) c, COALESCE(SUM(estimated_price),0) s
           FROM booking_enquiries
          WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
          GROUP BY DATE(created_at)"
    )->fetch_all(MYSQLI_ASSOC);
    $byDay = array_column($r, null, 'd');
    foreach ($chartDates as $i => $d) $chart[$i] = (int)($byDay[$d]['c'] ?? 0);
    foreach ($r as $row) { $count30 += (int)$row['c']; $sumEst30 += (float)$row['s']; }
} catch (mysqli_sql_exception) {}
$chartMax = max(1, max($chart));
$conn->close();
$STATUS_LABELS=['new'=>'New','contacted'=>'Contacted','quoted'=>'Quoted','confirmed'=>'Confirmed','closed'=>'Closed'];
$STATUS_COLORS=['new'=>'#B8A16A','contacted'=>'#f59e0b','quoted'=>'#8b5cf6','confirmed'=>'#22c55e','closed'=>'#6b7280'];
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?=h($csrf)?>"><title>Enquiries — Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="assets/admin.css?v=<?php echo @filemtime(__DIR__ . '/assets/admin.css'); ?>" rel="stylesheet">
<style>
.enq-stat{
  background:var(--surface);border:1px solid var(--border);border-left:4px solid var(--accent);
  border-radius:12px;padding:14px 20px;min-width:112px;
  box-shadow:var(--card-shadow);transition:transform .25s cubic-bezier(.22,1,.36,1),box-shadow .25s;
}
.enq-stat:hover{transform:translateY(-2px)}

/* ── 30-day trend chart — single-series bars in the theme accent ── */
.enq-chart-card{position:relative;padding:20px 24px 14px}
.enq-chart-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:14px}
.enq-chart-title{font:700 13.5px 'Inter',sans-serif;color:var(--ink)}
.enq-chart-sub{font-size:12px;color:var(--muted);margin-top:2px}
.enq-chart{
  display:flex;align-items:flex-end;gap:2px;height:120px;
  border-bottom:1px solid var(--border);
  background:linear-gradient(to top,transparent 0,transparent calc(50% - 1px),color-mix(in srgb,var(--border) 55%,transparent) 50%,transparent calc(50% + 1px));
}
.enq-bar-slot{position:relative;flex:1;height:100%;display:flex;flex-direction:column;justify-content:flex-end;align-items:center;cursor:default}
.enq-bar-slot:hover{background:color-mix(in srgb,var(--accent) 7%,transparent)}
.enq-bar{width:100%;max-width:22px;border-radius:4px 4px 0 0;background:var(--accent);min-height:0;transition:filter .15s}
.enq-bar-slot:hover .enq-bar{filter:brightness(1.15)}
.enq-bar-lbl{font:700 10.5px 'Inter',sans-serif;color:var(--ink);margin-bottom:3px}
.enq-chart-axis{display:flex;justify-content:space-between;font-size:10.5px;color:var(--muted);padding-top:6px}
.enq-tip{
  position:absolute;z-index:5;pointer-events:none;white-space:nowrap;
  background:var(--ink);color:var(--surface);font:600 11.5px 'Inter',sans-serif;
  padding:5px 10px;border-radius:7px;box-shadow:0 6px 18px rgba(0,0,0,.25);
  transform:translate(-50%,-100%)
}
.enq-stat-val{font-size:1.6rem;font-weight:900;font-family:'Montserrat',sans-serif;color:var(--ink);line-height:1}
.enq-stat-lbl{font-size:11px;font-weight:600;text-transform:uppercase;color:var(--muted);margin-top:4px}
.status-pill{display:inline-block;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;color:#fff}
.bd-preview{font-size:11.5px;color:var(--muted);max-width:260px}

/* Sleek dark-theme selects — filters ("All Status"/"All Destinations") + inline status.
   .admin-input wasn't defined, so these fell back to Bootstrap's default (dark bg +
   invisible dark arrow). Match them to the card surface with a visible chevron. */
.form-select.admin-input{
  appearance:none;-webkit-appearance:none;
  background-color:var(--surface-2);
  color:var(--ink);
  border:1px solid var(--border);
  border-radius:8px;
  padding:.45rem 1.9rem .45rem .7rem;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none' stroke='%2394A3B8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 6l4 4 4-4'/%3E%3C/svg%3E");
  background-repeat:no-repeat;background-position:right .6rem center;background-size:14px;
  transition:border-color .15s,box-shadow .15s,background-color .15s;
}
.form-select.admin-input:hover{border-color:var(--accent)}
.form-select.admin-input:focus{
  outline:none;border-color:var(--accent);
  background-color:var(--surface-2);color:var(--ink);
  box-shadow:0 0 0 3px rgba(214,199,161,.18);
}
.form-select.admin-input option{background:var(--surface-2);color:var(--ink)}
.status-sel{font-weight:600}
</style>
</head><body>
<?php require 'partials/topbar.php'; ?>
<div class="d-flex"><?php require 'partials/sidebar.php'; ?>
<main class="admin-main flex-grow-1 p-4">

  <!-- Header -->
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div><h1 class="admin-page-title mb-0">Quote Enquiries</h1><p class="admin-page-sub mb-0">All calculator submissions — <?= array_sum($ct) ?> total</p></div>
    <a href="?export=csv<?=$status_f?"&status=$status_f":''?>" class="btn btn-sm" style="border:1.5px solid var(--border);color:var(--muted);font-size:12px">
      <i class="fas fa-download"></i> Export CSV
    </a>
  </div>

  <!-- Stats -->
  <div class="d-flex gap-3 flex-wrap mb-4">
    <?php $total=array_sum($ct); ?>
    <div class="enq-stat"><div class="enq-stat-val"><?=$total?></div><div class="enq-stat-lbl">Total</div></div>
    <?php foreach($STATUS_LABELS as $sk=>$sl):?>
    <div class="enq-stat" style="border-left-color:<?=$STATUS_COLORS[$sk]?>">
      <div class="enq-stat-val" style="color:<?=$STATUS_COLORS[$sk]?>"><?=$ct[$sk]??0?></div>
      <div class="enq-stat-lbl"><?=$sl?></div>
    </div>
    <?php endforeach;?>
  </div>

  <!-- 30-day trend -->
  <div class="admin-card enq-chart-card mb-4">
    <div class="enq-chart-head">
      <div>
        <div class="enq-chart-title">Enquiries — last 30 days</div>
        <div class="enq-chart-sub"><?= $count30 ?> enquiries · est. value ₹<?= number_format($sumEst30) ?></div>
      </div>
    </div>
    <div class="enq-chart" id="enqChart" role="img" aria-label="Bar chart of enquiries per day for the last 30 days. <?= $count30 ?> total.">
      <?php foreach ($chart as $i => $c):
        $hpct = $c > 0 ? max(6, (int)round($c / $chartMax * 100)) : 0;
        $isPeak = $c > 0 && $c === $chartMax; ?>
      <div class="enq-bar-slot" data-date="<?= date('d M', strtotime($chartDates[$i])) ?>" data-count="<?= $c ?>">
        <?php if ($isPeak): ?><span class="enq-bar-lbl"><?= $c ?></span><?php endif; ?>
        <div class="enq-bar" style="height:<?= $hpct ?>%"></div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="enq-chart-axis">
      <span><?= date('d M', strtotime($chartDates[0])) ?></span>
      <span><?= date('d M', strtotime($chartDates[14])) ?></span>
      <span>Today</span>
    </div>
    <div class="enq-tip" id="enqTip" hidden></div>
  </div>

  <!-- Filters -->
  <form method="get" class="d-flex gap-2 flex-wrap mb-3">
    <input type="text" name="q" value="<?=h($search_f)?>" placeholder="Search name, phone, package…" class="form-control admin-input" style="max-width:220px">
    <select name="status" class="form-select admin-input" style="max-width:140px">
      <option value="">All Status</option>
      <?php foreach($STATUS_LABELS as $k=>$v):?><option value="<?=$k?>" <?=$status_f===$k?'selected':''?>><?=$v?></option><?php endforeach;?>
    </select>
    <select name="dest" class="form-select admin-input" style="max-width:150px">
      <option value="">All Destinations</option>
      <?php foreach($dests as $dk):?><option value="<?=h($dk)?>" <?=$dest_f===$dk?'selected':''?>><?=ucfirst($dk)?></option><?php endforeach;?>
    </select>
    <select name="source" class="form-select admin-input" style="max-width:140px">
      <option value="">All Sources</option>
      <option value="calculator" <?=$source_f==='calculator'?'selected':''?>>Calculator</option>
      <option value="whatsapp"   <?=$source_f==='whatsapp'?'selected':''?>>WhatsApp</option>
    </select>
    <button class="btn btn-primary-gold btn-sm" type="submit"><i class="fas fa-search"></i> Filter</button>
    <a href="enquiries.php" class="btn btn-sm" style="border:1px solid var(--border);color:var(--muted)">Clear</a>
  </form>

  <div class="admin-card">
    <div class="table-responsive">
      <table class="table mb-0" style="font-size:13px">
        <thead><tr>
          <?php foreach(['#','Date','Source','Customer','Destination','Trip','Vehicle','Est. Total','Status','Actions'] as $h):?>
          <th style="font-size:11px;font-weight:600;text-transform:uppercase;color:var(--muted);white-space:nowrap<?= $h==='Actions'?';text-align:center':'' ?>"><?=$h?></th>
          <?php endforeach;?>
        </tr></thead>
        <tbody>
        <?php if(!$rows):?><tr><td colspan="10" class="text-center py-5" style="color:var(--muted)">No enquiries found.</td></tr><?php endif;?>
        <?php foreach($rows as $r):
          $bd=!empty($r['breakdown_json'])?json_decode($r['breakdown_json'],true):null;
          $st=$r['status']??'new';
        ?>
        <tr>
          <td style="color:var(--muted);font-size:12px"><?=(int)$r['id']?></td>
          <td style="white-space:nowrap;font-size:12px;color:var(--muted)"><?=date('d M Y',strtotime($r['created_at']))?><br><?=date('h:i A',strtotime($r['created_at']))?></td>
          <td>
            <?php $src=$r['source']??'calculator'; $isWa=($src==='whatsapp'); ?>
            <span class="status-pill" style="background:<?=$isWa?'#25D366':'#B8A16A'?>;color:<?=$isWa?'#06231a':'#1a1a1a'?>">
              <?=$isWa?'WhatsApp':'Calculator'?>
            </span>
          </td>
          <td>
            <div style="font-weight:600;color:var(--ink)"><?=h($r['customer_name'])?></div>
            <div style="font-size:11.5px;color:var(--muted)"><?=h($r['mobile'])?></div>
            <?php if($r['email']??''):?><div style="font-size:11px;color:var(--muted)"><?=h($r['email'])?></div><?php endif;?>
          </td>
          <td>
            <div style="font-weight:600;color:var(--ink);font-size:12.5px"><?=h($r['package_name']??'—')?></div>
            <div style="font-size:11.5px;color:var(--muted)"><?=h($r['pickup_city']??'—')?></div>
          </td>
          <td style="white-space:nowrap;font-size:12px">
            <?php if(!empty($r['pickup_date']) && !empty($r['drop_date'])):?>
            <?=h($r['pickup_date'])?> → <?=h($r['drop_date'])?>
            <div style="color:var(--muted)"><?=(int)$r['travelers']?> travelers</div>
            <?php else:?><span style="color:var(--muted)">—</span><?php endif;?>
          </td>
          <td style="font-size:12.5px"><?=h($r['vehicle_name']??'—')?></td>
          <td style="font-weight:800;color:var(--lime);font-size:14px;white-space:nowrap">
            <?php if($r['estimated_price']!==null && $r['estimated_price']!==''):?>
            ₹<?=number_format((float)$r['estimated_price'],0)?>
            <?php if($bd):?>
            <div class="bd-preview"><?=$bd['days']??0?> days · ₹<?=number_format($bd['package_cost']??0,0)?> pkg</div>
            <?php endif;?>
            <?php else:?><span style="color:var(--muted);font-weight:400">—</span><?php endif;?>
          </td>
          <td>
            <select class="status-sel form-select admin-input" data-id="<?=(int)$r['id']?>" style="width:140px;font-size:12px">
              <?php foreach($STATUS_LABELS as $sk=>$sl):?>
              <option value="<?=$sk?>" <?=$st===$sk?'selected':''?>><?=$sl?></option>
              <?php endforeach;?>
            </select>
          </td>
          <td class="text-center">
            <button class="btn btn-sm btn-icon" title="View Breakdown" onclick='viewBreakdown(<?=json_encode($r)?>)'><i class="fas fa-eye"></i></button>
          </td>
        </tr>
        <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </div>

</main></div>

<!-- Breakdown Modal -->
<div class="modal fade" id="bdModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content" style="background:var(--surface);border:1px solid var(--border)">
  <div class="modal-header" style="border-color:var(--border)"><h5 class="modal-title" style="color:var(--ink)">Enquiry Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1)"></button></div>
  <div class="modal-body" id="bdModalBody" style="font-size:13.5px"></div>
</div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const bdModal   = new bootstrap.Modal(document.getElementById('bdModal'));

// Status change
document.querySelectorAll('.status-sel').forEach(sel=>{
  sel.addEventListener('change',async function(){
    const id=this.dataset.id, status=this.value;
    const fd=new FormData(); fd.append('action','update_status'); fd.append('id',id); fd.append('status',status); fd.append('csrf_token',csrfToken);
    await fetch('enquiries.php',{method:'POST',body:fd});
  });
});

// Breakdown viewer
function viewBreakdown(r){
  const bd=r.breakdown_json?JSON.parse(r.breakdown_json):{};
  const fmt=n=>'₹'+Math.round(n||0).toLocaleString('en-IN');
  const srcLabel=(r.source==='whatsapp')?'WhatsApp pre-chat':'Calculator';
  const dates=(r.pickup_date&&r.drop_date)?`${r.pickup_date} → ${r.drop_date}<br>${r.travelers} travelers`:'—';
  let html=`<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
    <div><b>Customer</b><br>${r.customer_name}<br>${r.mobile}${r.email?'<br>'+r.email:''}</div>
    <div><b>Trip</b><br>${r.package_name||'—'}<br>From: ${r.pickup_city||'—'}<br>${r.vehicle_name||'—'}</div>
    <div><b>Dates</b><br>${dates}</div>
    <div><b>Submitted</b><br>${new Date(r.created_at).toLocaleString('en-IN')}<br><span style="color:var(--muted)">${srcLabel}</span></div>
    ${r.notes?`<div style="grid-column:1/-1"><b>Notes</b><br>${r.notes}</div>`:''}
  </div>`;
  if(Object.keys(bd).length){
    html+=`<hr style="border-color:var(--border)"><b>Price Breakdown</b><table class="table table-sm mt-2" style="font-size:13px">
      <tr><td>Package Cost</td><td class="text-end">${fmt(bd.package_cost)}</td></tr>
      <tr><td>Vehicle Cost</td><td class="text-end">${fmt(bd.vehicle_cost)}</td></tr>
      ${bd.extra_pp_cost>0?`<tr><td>Extra Charges</td><td class="text-end">${fmt(bd.extra_pp_cost)}</td></tr>`:''}
      ${bd.dest_charge>0?`<tr><td>Destination Surcharge</td><td class="text-end">${fmt(bd.dest_charge)}</td></tr>`:''}
      ${bd.seasonal_amt>0?`<tr><td>Seasonal Surcharge (${bd.seasonal_pct}%)</td><td class="text-end">${fmt(bd.seasonal_amt)}</td></tr>`:''}
      <tr style="font-weight:600"><td>Subtotal</td><td class="text-end">${fmt(bd.subtotal)}</td></tr>
      ${(bd.tax_lines||[]).map(t=>`<tr style="color:var(--muted)"><td>${t.name}</td><td class="text-end">${fmt(t.amount)}</td></tr>`).join('')}
      <tr style="font-weight:900;font-size:15px;color:var(--lime)"><td>TOTAL</td><td class="text-end">${fmt(bd.total)}</td></tr>
    </table>`;
  }
  document.getElementById('bdModalBody').innerHTML=html;
  bdModal.show();
}

// ── 30-day chart tooltip ──
(function(){
  var chart=document.getElementById('enqChart'), tip=document.getElementById('enqTip');
  if(!chart||!tip) return;
  var card=chart.closest('.enq-chart-card');
  chart.addEventListener('pointermove',function(e){
    var slot=e.target.closest('.enq-bar-slot');
    if(!slot){ tip.hidden=true; return; }
    var n=slot.dataset.count, d=slot.dataset.date;
    tip.textContent=d+' — '+n+' '+(n==='1'?'enquiry':'enquiries');
    var cr=card.getBoundingClientRect(), sr=slot.getBoundingClientRect();
    tip.style.left=(sr.left-cr.left+sr.width/2)+'px';
    tip.style.top=(sr.top-cr.top-6)+'px';
    tip.hidden=false;
  });
  chart.addEventListener('pointerleave',function(){ tip.hidden=true; });
})();
</script></body></html>
