<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/vars.php';

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

$trip_destination   = cl($_POST['trip_destination']   ?? '', 500);  // all chosen destinations, comma-joined (info only — does not affect price)
$custom_destinations = cl($_POST['custom_destinations'] ?? '', 500); // only the custom (non-DB) ones, for admin review
$location_id   = (int)($_POST['pickup_location_id'] ?? 0);
$pickup_custom = cl($_POST['pickup_custom'] ?? '', 120);   // free-typed city not in our DB
$vehicle_id    = (int)($_POST['vehicle_id']         ?? 0);
$name        = cl($_POST['customer_name'] ?? '', 100);
$mobile      = preg_replace('/[^0-9+]/', '', $_POST['mobile'] ?? '');
$email       = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ? trim($_POST['email']) : '';
$travelers   = max(1, min(50, (int)($_POST['travelers'] ?? 1)));
$pickup_date = trim($_POST['pickup_date'] ?? '');
$drop_date   = trim($_POST['drop_date']   ?? '');

// Validate
$errors = [];
if ($trip_destination === '')                $errors[] = 'Please tell us where you want to go.';
if (!$location_id && $pickup_custom === '')  $errors[] = 'Please select or enter a pickup city.';
if (!$vehicle_id)                            $errors[] = 'Please select a vehicle.';
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

// No fixed packages — itineraries are built per customer. Pricing below is vehicle-only.

// Load location — only when a DB city was chosen; otherwise the typed city stands in.
$loc = null;
if ($location_id) {
    $s = $conn->prepare('SELECT city FROM pickup_locations WHERE id = ? AND active = 1');
    $s->bind_param('i', $location_id);
    $s->execute();
    $loc = $s->get_result()->fetch_assoc();
    $s->close();
}
// Resolved pickup city for the response/record: DB city, else the free-typed one.
$pickup_city = $loc ? $loc['city'] : $pickup_custom;

// Load vehicle
$s = $conn->prepare('SELECT vehicle_name, seating_capacity, daily_rate FROM vehicles WHERE id = ? AND status = "active"');
$s->bind_param('i', $vehicle_id);
$s->execute();
$veh = $s->get_result()->fetch_assoc();
$s->close();

if (!$veh || ($location_id && !$loc) || $pickup_city === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Invalid selection. Please refresh and try again.']);
    exit;
}

// Capacity guard — the vehicle must seat at least the number of travelers.
// Seating capacity is stored as a leading number (e.g. "7+1" → 7 passengers).
$veh_capacity = (int)$veh['seating_capacity'];
if ($veh_capacity > 0 && $travelers > $veh_capacity) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => "The selected vehicle seats only {$veh_capacity}. Please choose a vehicle with at least {$travelers} seats."]);
    exit;
}

// Helper: safe float conversion — prevents NaN in responses
function sf($v): float {
    $n = is_numeric($v) ? (float)$v : 0.0;
    return is_nan($n) || is_infinite($n) ? 0.0 : $n;
}

// Vehicle-only estimate — destinations/itineraries are quoted by our team, not priced here.
$veh_rate = sf($veh['daily_rate']);
if ($veh_rate <= 0) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Pricing not configured for this vehicle. Please contact us directly.']);
    exit;
}

$vehicle_cost  = $veh_rate * $days;
$package_cost  = 0.0;   // no fixed packages
$extra_pp_cost = 0.0;
$dest_charge   = 0.0;   // destination does not affect the estimate

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
    $seasonal_amt = round($vehicle_cost * $seasonal_pct / 100, 2);
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
    // No package cost anymore — legacy 'package_cost' taxes fall through to the subtotal
    // (mirrors the front-end calc in calc_modal.php).
    $base = $t['apply_on'] === 'vehicle_cost' ? $vehicle_cost : $subtotal;
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
$location_id_db = $location_id ?: null;        // store NULL (not 0) when the city is free-typed
$package_id_db  = ((int)($_POST['package_id'] ?? 0)) ?: null;  // set when the quote came from a Tour Package
$s = $conn->prepare(
    'INSERT INTO booking_enquiries
     (package_id, trip_destination, pickup_location_id, pickup_custom, vehicle_id, customer_name, mobile, email,
      travelers, pickup_date, drop_date, estimated_price,
      package_cost, vehicle_cost, extra_charges, tax_amount, breakdown_json, status)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "new")'
);
$s->bind_param(
    'isisisssissddddds',
    $package_id_db, $trip_destination, $location_id_db, $pickup_custom, $vehicle_id,
    $name, $mobile, $email,
    $travelers, $pickup_date, $drop_date, $total,
    $package_cost, $vehicle_cost, $extra, $tax_total, $bj
);
$s->execute();
$enquiry_id = $conn->insert_id;
$s->close();

// Record any custom (non-DB) destinations for admin review — never let this break the response.
if ($custom_destinations !== '') {
    try {
        $sug = $conn->prepare(
            'INSERT INTO destination_suggestions (name, name_norm)
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE request_count = request_count + 1, last_requested_at = NOW()'
        );
        $seen = [];
        foreach (explode(',', $custom_destinations) as $raw) {
            $nm = cl($raw, 120);
            if ($nm === '') continue;
            $norm = mb_strtolower($nm, 'UTF-8');
            if (isset($seen[$norm])) continue;   // de-dupe within a single submission
            $seen[$norm] = true;
            $sug->bind_param('ss', $nm, $norm);
            $sug->execute();
        }
        $sug->close();
    } catch (mysqli_sql_exception) { /* suggestions are best-effort */ }
}

$rl['c']++;
$_SESSION['bk_rl'] = $rl;
$conn->close();

// Return raw numbers — NO number_format() — JS handles formatting
echo json_encode([
    'ok'          => true,
    'enquiry_id'  => $enquiry_id,
    'destination' => $trip_destination,
    'pickup'      => $pickup_city,
    'vehicle'     => $veh['vehicle_name'] . ' (' . $veh['seating_capacity'] . ')',
    'travelers'   => $travelers,
    'days'        => $days,
    'breakdown'   => $breakdown,   // nested object with all raw floats
]);
