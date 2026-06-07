<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!empty($_POST['website'] ?? '')) { echo json_encode(['ok' => true]); exit; }

$posted = $_POST['csrf_token'] ?? '';
$sess   = $_SESSION['lead_form_token'] ?? '';
if ($sess === '' || !hash_equals($sess, $posted)) {
    http_response_code(419);
    echo json_encode(['ok' => false, 'error' => 'Security token expired. Please refresh and try again.']);
    exit;
}

$rl = $_SESSION['bk_rl'] ?? ['c' => 0, 'w' => time()];
if (time() - $rl['w'] > 900) $rl = ['c' => 0, 'w' => time()];
if ($rl['c'] >= 10) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'error' => 'Too many requests. Please wait a few minutes.']);
    exit;
}

function cl(string $v, int $max = 255): string {
    return mb_substr(trim($v), 0, $max, 'UTF-8');
}

$package_id  = (int)($_POST['package_id']          ?? 0);
$location_id = (int)($_POST['pickup_location_id']  ?? 0);
$vehicle_id  = (int)($_POST['vehicle_id']           ?? 0);
$name        = cl($_POST['customer_name'] ?? '', 100);
$mobile      = preg_replace('/[^0-9+]/', '', $_POST['mobile'] ?? '');
$email       = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ? trim($_POST['email']) : '';
$travelers   = max(1, min(50, (int)($_POST['travelers'] ?? 1)));
$pickup_date = trim($_POST['pickup_date'] ?? '');
$drop_date   = trim($_POST['drop_date']   ?? '');

// Validate
$errors = [];
if (!$package_id)         $errors[] = 'Please select a tour package.';
if (!$location_id)        $errors[] = 'Please select a pickup location.';
if (!$vehicle_id)         $errors[] = 'Please select a vehicle.';
if (mb_strlen($name) < 3) $errors[] = 'Name must be at least 3 characters.';
if (strlen($mobile) < 10) $errors[] = 'Enter a valid 10-digit mobile number.';
if (!$pickup_date)        $errors[] = 'Select a pickup date.';
if (!$drop_date)          $errors[] = 'Select a drop date.';

if ($errors) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => implode(' ', $errors)]);
    exit;
}

$today  = new DateTime('today');
$pickup = DateTime::createFromFormat('Y-m-d', $pickup_date);
$drop   = DateTime::createFromFormat('Y-m-d', $drop_date);

if (!$pickup || $pickup < $today) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Pickup date cannot be in the past.']);
    exit;
}
if (!$drop || $drop <= $pickup) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Drop date must be after pickup date.']);
    exit;
}
$days = max(1, $drop->diff($pickup)->days);

if (!$conn instanceof mysqli) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'Database unavailable. Please try again later.']);
    exit;
}

// Load package
$s = $conn->prepare(
    'SELECT package_name, duration_days, base_price_per_day,
            COALESCE(additional_charge_per_person, 0) AS extra_pp,
            destination_key
     FROM tour_packages WHERE id = ? AND status = "active"'
);
$s->bind_param('i', $package_id);
$s->execute();
$pkg = $s->get_result()->fetch_assoc();
$s->close();

// Load location
$s = $conn->prepare('SELECT city FROM pickup_locations WHERE id = ? AND active = 1');
$s->bind_param('i', $location_id);
$s->execute();
$loc = $s->get_result()->fetch_assoc();
$s->close();

// Load vehicle
$s = $conn->prepare('SELECT vehicle_name, seating_capacity, daily_rate FROM vehicles WHERE id = ? AND status = "active"');
$s->bind_param('i', $vehicle_id);
$s->execute();
$veh = $s->get_result()->fetch_assoc();
$s->close();

if (!$pkg || !$loc || !$veh) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Invalid selection. Please refresh and try again.']);
    exit;
}

// Helper: safe float conversion — prevents NaN in responses
function sf($v): float {
    $n = is_numeric($v) ? (float)$v : 0.0;
    return is_nan($n) || is_infinite($n) ? 0.0 : $n;
}

// Core prices — all cast to float defensively
$ppd          = sf($pkg['base_price_per_day']);
$extra_pp_raw = sf($pkg['extra_pp']);
$veh_rate     = sf($veh['daily_rate']);

if ($ppd <= 0 && $veh_rate <= 0) {
    // Pricing not configured — save lead but warn
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Pricing not configured for this package. Please contact us directly.']);
    exit;
}

// Costs
$package_cost  = $ppd * $travelers * $days;
$vehicle_cost  = $veh_rate * $days;
$extra_pp_cost = $extra_pp_raw * $travelers;

// Destination charge
$dest_charge = 0.0;
if (!empty($pkg['destination_key'])) {
    $s = $conn->prepare('SELECT extra_per_day FROM destinations WHERE dest_key = ? AND active = 1 LIMIT 1');
    $s->bind_param('s', $pkg['destination_key']);
    $s->execute();
    $dr = $s->get_result()->fetch_assoc();
    $s->close();
    if ($dr) $dest_charge = sf($dr['extra_per_day']) * $travelers * $days;
}

// Seasonal surcharge
$seasonal_pct = 0.0;
$seasonal_amt = 0.0;
$s = $conn->prepare(
    'SELECT COALESCE(SUM(surcharge_pct), 0) AS sp
     FROM seasonal_pricing
     WHERE active = 1 AND start_date <= ? AND end_date >= ?'
);
$s->bind_param('ss', $pickup_date, $pickup_date);
$s->execute();
$sr = $s->get_result()->fetch_assoc();
$s->close();
if ($sr && $sr['sp']) {
    $seasonal_pct = sf($sr['sp']);
    $seasonal_amt = round(($package_cost + $vehicle_cost) * $seasonal_pct / 100, 2);
}

$subtotal = $package_cost + $vehicle_cost + $extra_pp_cost + $dest_charge + $seasonal_amt;

// Taxes
$taxes_rows = $conn->query(
    "SELECT name, type, value, apply_on FROM taxes_fees WHERE active = 1 ORDER BY sort_order ASC"
)->fetch_all(MYSQLI_ASSOC);

$tax_lines = [];
$tax_total = 0.0;
foreach ($taxes_rows as $t) {
    $tval = sf($t['value']);
    $base = match ($t['apply_on']) {
        'package_cost' => $package_cost,
        'vehicle_cost' => $vehicle_cost,
        default        => $subtotal,
    };
    $amt = $t['type'] === 'percentage' ? round($base * $tval / 100, 2) : $tval;
    if ($amt > 0) {
        $tax_lines[] = ['name' => $t['name'], 'amount' => $amt];
        $tax_total  += $amt;
    }
}

$total = round($subtotal + $tax_total, 2);

// Build breakdown — all values are raw floats, never formatted strings
$breakdown = [
    'package_cost'  => round($package_cost,  2),
    'vehicle_cost'  => round($vehicle_cost,  2),
    'extra_pp_cost' => round($extra_pp_cost, 2),
    'dest_charge'   => round($dest_charge,   2),
    'seasonal_amt'  => round($seasonal_amt,  2),
    'seasonal_pct'  => $seasonal_pct,
    'subtotal'      => round($subtotal,      2),
    'tax_lines'     => $tax_lines,
    'tax_total'     => round($tax_total,     2),
    'total'         => $total,
    'days'          => $days,
    'travelers'     => $travelers,
];

// Save enquiry
$bj    = json_encode($breakdown);
$extra = round($extra_pp_cost + $dest_charge + $seasonal_amt, 2);
$s = $conn->prepare(
    'INSERT INTO booking_enquiries
     (package_id, pickup_location_id, vehicle_id, customer_name, mobile, email,
      travelers, pickup_date, drop_date, estimated_price,
      package_cost, vehicle_cost, extra_charges, tax_amount, breakdown_json, status)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "new")'
);
$s->bind_param(
    'iiisssissddddds',
    $package_id, $location_id, $vehicle_id,
    $name, $mobile, $email,
    $travelers, $pickup_date, $drop_date, $total,
    $package_cost, $vehicle_cost, $extra, $tax_total, $bj
);
$s->execute();
$enquiry_id = $conn->insert_id;
$s->close();

$rl['c']++;
$_SESSION['bk_rl'] = $rl;
$conn->close();

// Return raw numbers — NO number_format() — JS handles formatting
echo json_encode([
    'ok'          => true,
    'enquiry_id'  => $enquiry_id,
    'package'     => $pkg['package_name'],
    'pickup'      => $loc['city'],
    'vehicle'     => $veh['vehicle_name'] . ' (' . $veh['seating_capacity'] . ')',
    'travelers'   => $travelers,
    'days'        => $days,
    'breakdown'   => $breakdown,   // nested object with all raw floats
]);
