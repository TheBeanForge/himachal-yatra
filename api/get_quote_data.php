<?php
header('Content-Type: application/json');
header('Cache-Control: public, max-age=120');
require_once __DIR__ . '/../includes/vars.php';

if (!$conn instanceof mysqli) {
    http_response_code(503);
    echo json_encode(['error' => 'Database unavailable']);
    exit;
}

function cast_package(array $p): array {
    return [
        'id'          => (int)$p['id'],
        'name'        => $p['package_name'],
        'days'        => (int)$p['duration_days'],
        'ppd'         => (float)$p['base_price_per_day'],
        'extra_pp'    => (float)($p['additional_charge_per_person'] ?? 0),
        'dest_key'    => $p['destination_key'] ?? '',
        'description' => $p['description'] ?? '',
    ];
}
function cast_vehicle(array $v): array {
    return ['id'=>(int)$v['id'],'name'=>$v['vehicle_name'],'seats'=>$v['seating_capacity'],'rate'=>(float)$v['daily_rate']];
}
function cast_tax(array $t): array {
    return ['id'=>(int)$t['id'],'name'=>$t['name'],'type'=>$t['type'],'value'=>(float)$t['value'],'apply_on'=>$t['apply_on']];
}
function cast_dest(array $d): array {
    return ['id'=>(int)$d['id'],'name'=>$d['name'],'dest_key'=>$d['dest_key'],'extra_per_day'=>(float)$d['extra_per_day']];
}
function cast_seasonal(array $s): array {
    return ['name'=>$s['name'],'start'=>$s['start_date'],'end'=>$s['end_date'],'pct'=>(float)$s['surcharge_pct']];
}

$packages  = array_map('cast_package',  $conn->query("SELECT id,package_name,duration_days,base_price_per_day,COALESCE(additional_charge_per_person,0) AS additional_charge_per_person,destination_key,description FROM tour_packages WHERE status='active' ORDER BY package_name ASC")->fetch_all(MYSQLI_ASSOC));
$locations = $conn->query("SELECT id,city AS name FROM pickup_locations WHERE active=1 ORDER BY sort_order ASC,city ASC")->fetch_all(MYSQLI_ASSOC);
$vehicles  = array_map('cast_vehicle',  $conn->query("SELECT id,vehicle_name,seating_capacity,daily_rate FROM vehicles WHERE status='active' ORDER BY daily_rate ASC")->fetch_all(MYSQLI_ASSOC));
$taxes     = array_map('cast_tax',      $conn->query("SELECT id,name,type,value,apply_on FROM taxes_fees WHERE active=1 ORDER BY sort_order ASC")->fetch_all(MYSQLI_ASSOC));
$dests     = array_map('cast_dest',     $conn->query("SELECT id,name,dest_key,extra_per_day FROM destinations WHERE active=1 ORDER BY sort_order ASC")->fetch_all(MYSQLI_ASSOC));
$seasonal  = array_map('cast_seasonal', $conn->query("SELECT name,start_date,end_date,surcharge_pct FROM seasonal_pricing WHERE active=1 AND end_date >= CURDATE() ORDER BY start_date ASC")->fetch_all(MYSQLI_ASSOC));

$conn->close();
echo json_encode(compact('packages','locations','vehicles','taxes','dests','seasonal'), JSON_UNESCAPED_UNICODE);
