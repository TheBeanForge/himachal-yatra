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

// Each list degrades to [] if its table/column is missing (older installs) —
// a partial dataset must never take down the whole quote form.
function safe_rows(mysqli $conn, array $sqls): array {
    foreach ($sqls as $sql) {
        try { return $conn->query($sql)->fetch_all(MYSQLI_ASSOC); }
        catch (mysqli_sql_exception) { continue; }
    }
    return [];
}

$packages  = array_map('cast_package', safe_rows($conn, [
    "SELECT id,package_name,duration_days,base_price_per_day,COALESCE(additional_charge_per_person,0) AS additional_charge_per_person,destination_key,description FROM tour_packages WHERE status='active' ORDER BY package_name ASC",
    // Older installs: no destination_key / additional_charge_per_person columns yet.
    "SELECT id,package_name,duration_days,base_price_per_day,description FROM tour_packages WHERE status='active' ORDER BY package_name ASC",
]));
$locations = safe_rows($conn, ["SELECT id,city AS name FROM pickup_locations WHERE active=1 ORDER BY sort_order ASC,city ASC"]);
$vehicles  = array_map('cast_vehicle',  safe_rows($conn, ["SELECT id,vehicle_name,seating_capacity,daily_rate FROM vehicles WHERE status='active' ORDER BY daily_rate ASC"]));
$taxes     = array_map('cast_tax',      safe_rows($conn, ["SELECT id,name,type,value,apply_on FROM taxes_fees WHERE active=1 ORDER BY sort_order ASC"]));
$dests     = array_map('cast_dest',     safe_rows($conn, ["SELECT id,name,dest_key,extra_per_day FROM destinations WHERE active=1 ORDER BY sort_order ASC"]));
$seasonal  = array_map('cast_seasonal', safe_rows($conn, ["SELECT name,start_date,end_date,surcharge_pct FROM seasonal_pricing WHERE active=1 AND end_date >= CURDATE() ORDER BY start_date ASC"]));

$conn->close();
echo json_encode(compact('packages','locations','vehicles','taxes','dests','seasonal'), JSON_UNESCAPED_UNICODE);
