<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }
require_once '../includes/vars.php';
$csrf = admin_csrf_token();

// Ensure the schema pieces this page needs exist (older installs may lack them).
try { $conn->query("ALTER TABLE tour_packages ADD COLUMN destination_key VARCHAR(50) NULL"); } catch (Throwable) {}
try { $conn->query("ALTER TABLE booking_enquiries MODIFY COLUMN status ENUM('new','contacted','quoted','confirmed','closed','cancelled') DEFAULT 'new'"); } catch (Throwable) {}

// ── Log a direct-call lead (staff-entered, e.g. customer phoned the number on the site) ──
$leadMsg='';
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='add_lead') {
    require_admin_csrf();
    $name   = mb_substr(trim($_POST['customer_name']??''),0,100);
    $mobile = preg_replace('/[^0-9+]/','', $_POST['mobile']??'');
    $email  = filter_var(trim($_POST['email']??''), FILTER_VALIDATE_EMAIL) ? trim($_POST['email']) : '';
    $destTx = mb_substr(trim($_POST['trip_destination']??''),0,500);
    $locId  = (int)($_POST['pickup_location_id']??0);
    $locCus = mb_substr(trim($_POST['pickup_custom']??''),0,120);
    $vehId  = (int)($_POST['vehicle_id']??0);
    $trav   = max(1,min(50,(int)($_POST['travelers']??1)));
    $pd     = trim($_POST['pickup_date']??'');
    $dd     = trim($_POST['drop_date']??'');
    $notes  = mb_substr(trim($_POST['notes']??''),0,1000);
    $stNew  = in_array($_POST['status']??'',['new','contacted','quoted','confirmed','closed'],true)?$_POST['status']:'contacted';

    if (mb_strlen($name)<2 || strlen($mobile)<10) {
        $leadMsg='error:Customer name and a valid mobile number are required.';
    } else {
        // Price estimate — identical math to the public calculator
        // (vehicle daily rate × days + seasonal surcharge + taxes).
        $est=null; $vehCost=0.0; $seasAmt=0.0; $taxTot=0.0; $bj=null;
        $pdOk = $pd!=='' && DateTime::createFromFormat('Y-m-d',$pd)!==false;
        $ddOk = $dd!=='' && DateTime::createFromFormat('Y-m-d',$dd)!==false;
        if ($vehId && $pdOk && $ddOk && $dd>$pd) {
            $s=$conn->prepare('SELECT daily_rate FROM vehicles WHERE id=?');
            $s->bind_param('i',$vehId); $s->execute();
            $rate=(float)($s->get_result()->fetch_assoc()['daily_rate']??0); $s->close();
            $days=max(1,(new DateTime($dd))->diff(new DateTime($pd))->days);
            if ($rate>0) {
                $vehCost=$rate*$days;
                $s=$conn->prepare('SELECT COALESCE(SUM(surcharge_pct),0) sp FROM seasonal_pricing WHERE active=1 AND start_date<=? AND end_date>=?');
                $s->bind_param('ss',$pd,$pd); $s->execute();
                $pct=(float)($s->get_result()->fetch_assoc()['sp']??0); $s->close();
                $seasAmt=round($vehCost*$pct/100,2);
                $sub=$vehCost+$seasAmt;
                $taxLines=[];
                foreach($conn->query("SELECT name,type,value,apply_on FROM taxes_fees WHERE active=1 ORDER BY sort_order ASC")->fetch_all(MYSQLI_ASSOC) as $t){
                    $tv=(float)$t['value'];
                    $base=$t['apply_on']==='vehicle_cost'?$vehCost:$sub;
                    $amt=$t['type']==='percentage'?round($base*$tv/100,2):$tv;
                    if($amt>0){ $taxLines[]=['name'=>$t['name'],'amount'=>$amt]; $taxTot+=$amt; }
                }
                $est=round($sub+$taxTot,2);
                $bj=json_encode(['package_cost'=>0,'vehicle_cost'=>$vehCost,'extra_pp_cost'=>0,'dest_charge'=>0,
                                 'seasonal_amt'=>$seasAmt,'seasonal_pct'=>$pct,'subtotal'=>$sub,
                                 'tax_lines'=>$taxLines,'tax_total'=>$taxTot,'total'=>$est,'days'=>$days,'travelers'=>$trav]);
            }
        }
        $locIdDb = ($locId>0 && $locCus==='') ? $locId : null;
        $vehIdDb = $vehId ?: null;
        $pdDb = $pdOk ? $pd : null;
        $ddDb = $ddOk ? $dd : null;
        $src = 'direct_call';
        $s=$conn->prepare(
            'INSERT INTO booking_enquiries
             (trip_destination,pickup_location_id,pickup_custom,vehicle_id,customer_name,mobile,email,
              travelers,pickup_date,drop_date,estimated_price,package_cost,vehicle_cost,extra_charges,
              tax_amount,breakdown_json,status,notes,source)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,0,?,?,?,?,?,?,?)'
        );
        $s->bind_param('sisisssissddddssss',
            $destTx,$locIdDb,$locCus,$vehIdDb,$name,$mobile,$email,
            $trav,$pdDb,$ddDb,$est,$vehCost,$seasAmt,$taxTot,$bj,$stNew,$notes,$src);
        $s->execute(); $newId=$conn->insert_id; $s->close();
        audit_log('lead_add',"Direct-call lead #$newId: $name ($mobile)");
        $leadMsg='ok:Direct-call lead saved'.($est!==null?' — estimate ₹'.number_format($est):'').'.';
    }
}

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
if(in_array($source_f,['calculator','whatsapp','direct_call'],true)){ $where[]='be.source=?'; $params[]=$source_f; $types.='s'; }
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

// Form data for the "Log Call Lead" modal (each degrades to [] on older schemas).
$fm = ['vehicles'=>[], 'locations'=>[], 'dests'=>[], 'taxes'=>[], 'seasonal'=>[]];
try { $fm['vehicles']  = $conn->query("SELECT id,vehicle_name,seating_capacity,daily_rate FROM vehicles WHERE status='active' ORDER BY daily_rate ASC")->fetch_all(MYSQLI_ASSOC); } catch(Throwable) {}
try { $fm['locations'] = $conn->query("SELECT id,city FROM pickup_locations WHERE active=1 ORDER BY sort_order ASC,city ASC")->fetch_all(MYSQLI_ASSOC); } catch(Throwable) {}
try { $fm['dests']     = array_column($conn->query("SELECT name FROM destinations WHERE active=1 ORDER BY sort_order ASC")->fetch_all(MYSQLI_ASSOC),'name'); } catch(Throwable) {}
try { $fm['taxes']     = $conn->query("SELECT name,type,value,apply_on FROM taxes_fees WHERE active=1 ORDER BY sort_order ASC")->fetch_all(MYSQLI_ASSOC); } catch(Throwable) {}
try { $fm['seasonal']  = $conn->query("SELECT start_date,end_date,surcharge_pct FROM seasonal_pricing WHERE active=1 AND end_date>=CURDATE()")->fetch_all(MYSQLI_ASSOC); } catch(Throwable) {}
$conn->close();
$STATUS_LABELS=['new'=>'New','contacted'=>'Contacted','quoted'=>'Quoted','confirmed'=>'Confirmed','closed'=>'Closed'];
$STATUS_COLORS=['new'=>'#B8A16A','contacted'=>'#f59e0b','quoted'=>'#8b5cf6','confirmed'=>'#22c55e','closed'=>'#6b7280'];
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="<?=h($csrf)?>"><title>Enquiries — Admin</title>
<link rel="icon" href="../favicon.ico" sizes="32x32">
<link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="../assets/brand/mark-192.png">
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
    <div><h1 class="admin-page-title mb-0">Leads</h1><p class="admin-page-sub mb-0">Calculator, WhatsApp &amp; direct-call leads — <?= array_sum($ct) ?> total</p></div>
    <div class="d-flex gap-2 flex-wrap">
      <button type="button" class="btn-primary-gold" data-bs-toggle="modal" data-bs-target="#addLeadModal">
        <i class="fas fa-phone-volume"></i> Log Call Lead
      </button>
      <a href="?export=csv<?=$status_f?"&status=$status_f":''?>" class="btn btn-sm d-inline-flex align-items-center" style="border:1.5px solid var(--border);color:var(--muted);font-size:12px">
        <i class="fas fa-download">&nbsp;</i> Export CSV
      </a>
    </div>
  </div>

  <?php if($leadMsg): [$lt,$ltx]=explode(':',$leadMsg,2); ?>
  <div class="alert alert-<?=$lt==='ok'?'success':'danger'?> alert-dismissible fade show py-2 mb-3"><?=h($ltx)?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>

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
      <option value="calculator"  <?=$source_f==='calculator'?'selected':''?>>Calculator</option>
      <option value="whatsapp"    <?=$source_f==='whatsapp'?'selected':''?>>WhatsApp</option>
      <option value="direct_call" <?=$source_f==='direct_call'?'selected':''?>>Direct Call</option>
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
            <?php
              $src=$r['source']??'calculator';
              $srcMap=['whatsapp'=>['WhatsApp','#25D366','#06231a'],'direct_call'=>['Direct Call','#4A90D9','#ffffff']];
              [$srcLbl,$srcBg,$srcFg]=$srcMap[$src]??['Calculator','#B8A16A','#1a1a1a'];
            ?>
            <span class="status-pill" style="background:<?=$srcBg?>;color:<?=$srcFg?>"><?=$srcLbl?></span>
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

<!-- Log Call Lead Modal -->
<div class="modal fade" id="addLeadModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content" style="background:var(--surface);border:1px solid var(--border)">
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?=h($csrf)?>">
    <input type="hidden" name="action" value="add_lead">
    <div class="modal-header" style="border-color:var(--border)">
      <h5 class="modal-title" style="color:var(--ink)"><i class="fas fa-phone-volume" style="color:var(--accent)"></i> &nbsp;Log a Direct-Call Lead</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1)"></button>
    </div>
    <div class="modal-body" style="font-size:13.5px">
      <p style="color:var(--muted);font-size:12.5px;margin:0 0 16px">Customer called on the phone? Capture their details here — pick the vehicle and dates to read them the same estimate the website gives, then save. The lead lands in this inbox tagged <b>Direct Call</b>.</p>

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted)">Customer name *</label>
          <input type="text" name="customer_name" class="form-control admin-input" required maxlength="100" placeholder="Full name">
        </div>
        <div class="col-md-3">
          <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted)">Mobile *</label>
          <input type="tel" name="mobile" class="form-control admin-input" required minlength="10" maxlength="15" placeholder="98765 43210">
        </div>
        <div class="col-md-3">
          <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted)">Email</label>
          <input type="email" name="email" class="form-control admin-input" placeholder="Optional">
        </div>

        <div class="col-md-6">
          <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted)">Destination(s)</label>
          <input type="text" name="trip_destination" class="form-control admin-input" list="alDests" placeholder="e.g. Manali, Kasol">
          <datalist id="alDests">
            <?php foreach($fm['dests'] as $dn):?><option value="<?=h($dn)?>"></option><?php endforeach;?>
          </datalist>
        </div>
        <div class="col-md-3">
          <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted)">Pickup city</label>
          <select name="pickup_location_id" class="form-select admin-input">
            <option value="0">— Select —</option>
            <?php foreach($fm['locations'] as $l):?><option value="<?=(int)$l['id']?>"><?=h($l['city'])?></option><?php endforeach;?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted)">…or custom city</label>
          <input type="text" name="pickup_custom" class="form-control admin-input" maxlength="120" placeholder="If not listed">
        </div>

        <div class="col-md-4">
          <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted)">Vehicle</label>
          <select name="vehicle_id" id="alVeh" class="form-select admin-input">
            <option value="0">— Select for estimate —</option>
            <?php foreach($fm['vehicles'] as $v):?>
            <option value="<?=(int)$v['id']?>"><?=h($v['vehicle_name'])?> — <?=h($v['seating_capacity'])?> (₹<?=number_format((float)$v['daily_rate'])?>/day)</option>
            <?php endforeach;?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted)">Pickup date</label>
          <input type="date" name="pickup_date" id="alPd" class="form-control admin-input">
        </div>
        <div class="col-md-3">
          <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted)">Drop date</label>
          <input type="date" name="drop_date" id="alDd" class="form-control admin-input">
        </div>
        <div class="col-md-2">
          <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted)">Travelers</label>
          <input type="number" name="travelers" id="alTrav" class="form-control admin-input" value="2" min="1" max="50">
        </div>

        <div class="col-12">
          <div id="alEstBox" style="border:1px solid var(--border);border-left:4px solid var(--accent);border-radius:12px;padding:12px 16px;background:var(--surface-2)">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted);margin-bottom:4px">Live estimate — read this to the customer</div>
            <div id="alEstRows" style="color:var(--ink-2);font-size:12.5px">Select a vehicle and both dates to see the estimate.</div>
            <div id="alEstTotal" style="font:800 20px 'Poppins','Inter',sans-serif;color:var(--accent);margin-top:4px"></div>
          </div>
        </div>

        <div class="col-md-9">
          <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted)">Call notes</label>
          <input type="text" name="notes" class="form-control admin-input" maxlength="1000" placeholder="e.g. Prefers evening pickup, will confirm by Friday">
        </div>
        <div class="col-md-3">
          <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted)">Status</label>
          <select name="status" class="form-select admin-input">
            <?php foreach($STATUS_LABELS as $sk=>$sl):?>
            <option value="<?=$sk?>" <?=$sk==='contacted'?'selected':''?>><?=$sl?></option>
            <?php endforeach;?>
          </select>
        </div>
      </div>
    </div>
    <div class="modal-footer" style="border-color:var(--border)">
      <button type="button" class="btn-clear" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn-primary-gold"><i class="fas fa-floppy-disk"></i> Save Lead</button>
    </div>
  </form>
</div></div></div>

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
  const srcLabel=(r.source==='whatsapp')?'WhatsApp pre-chat':(r.source==='direct_call'?'Direct call (staff-entered)':'Calculator');
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

// ── Live estimate in the Log Call Lead modal (same math as the public calculator) ──
(function(){
  var FM = <?= json_encode([
      'vehicles' => array_map(fn($v)=>['id'=>(int)$v['id'],'rate'=>(float)$v['daily_rate']], $fm['vehicles']),
      'taxes'    => array_map(fn($t)=>['name'=>$t['name'],'type'=>$t['type'],'value'=>(float)$t['value'],'apply_on'=>$t['apply_on']], $fm['taxes']),
      'seasonal' => array_map(fn($s)=>['start'=>$s['start_date'],'end'=>$s['end_date'],'pct'=>(float)$s['surcharge_pct']], $fm['seasonal']),
  ]) ?>;
  var veh=document.getElementById('alVeh'), pd=document.getElementById('alPd'), dd=document.getElementById('alDd');
  if(!veh) return;
  var rows=document.getElementById('alEstRows'), total=document.getElementById('alEstTotal');
  var fmt=function(n){ return '₹'+Math.round(n).toLocaleString('en-IN'); };
  function recalc(){
    var v=FM.vehicles.find(function(x){return String(x.id)===veh.value;});
    if(!v||!pd.value||!dd.value||dd.value<=pd.value){
      rows.textContent='Select a vehicle and both dates to see the estimate.'; total.textContent=''; return;
    }
    var days=Math.max(1,Math.round((new Date(dd.value)-new Date(pd.value))/864e5));
    var vehCost=v.rate*days, pct=0;
    FM.seasonal.forEach(function(s){ if(pd.value>=s.start&&pd.value<=s.end) pct+=s.pct; });
    var seas=Math.round(vehCost*pct/100), sub=vehCost+seas, taxT=0, parts=[];
    parts.push('Vehicle ('+days+'d × '+fmt(v.rate)+') = '+fmt(vehCost));
    if(seas>0) parts.push('Season +'+pct+'% = '+fmt(seas));
    FM.taxes.forEach(function(t){
      var base=t.apply_on==='vehicle_cost'?vehCost:sub;
      var amt=t.type==='percentage'?Math.round(base*t.value/100):t.value;
      if(amt>0){ taxT+=amt; parts.push(t.name+' = '+fmt(amt)); }
    });
    rows.textContent=parts.join('  ·  ');
    total.textContent='Total: '+fmt(sub+taxT);
  }
  [veh,pd,dd].forEach(function(el){ el.addEventListener('change',recalc); el.addEventListener('input',recalc); });
})();

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
