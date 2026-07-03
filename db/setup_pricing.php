<?php
// Setup script: CLI only, or a signed-in superadmin in the browser.
if (PHP_SAPI !== 'cli') {
    session_start();
    if (($_SESSION['admin_user']['role'] ?? '') !== 'superadmin') {
        http_response_code(403);
        exit('Forbidden — run from the CLI or sign in as superadmin.');
    }
}
require_once __DIR__ . '/../includes/vars.php';
if (!$conn instanceof mysqli) die('<p style="color:red;font-family:sans-serif">DB connection failed.</p>');

$queries = [
  // Destinations (admin-managed)
  "CREATE TABLE IF NOT EXISTS destinations (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(100) NOT NULL,
    dest_key     VARCHAR(50)  NOT NULL UNIQUE,
    extra_per_day DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Extra charge per person per day for remote areas',
    active       TINYINT(1) DEFAULT 1,
    sort_order   INT DEFAULT 0,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

  // Taxes & fees (admin-managed)
  "CREATE TABLE IF NOT EXISTS taxes_fees (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    type       ENUM('percentage','flat') DEFAULT 'percentage',
    value      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    apply_on   ENUM('total','package_cost','vehicle_cost') DEFAULT 'total',
    active     TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

  // Seasonal pricing (admin-managed)
  "CREATE TABLE IF NOT EXISTS seasonal_pricing (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(100) NOT NULL,
    start_date     DATE NOT NULL,
    end_date       DATE NOT NULL,
    surcharge_pct  DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Percentage surcharge on subtotal',
    active         TINYINT(1) DEFAULT 1
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

  // Traveller-suggested custom destinations (collected from the quote form for admin review)
  "CREATE TABLE IF NOT EXISTS destination_suggestions (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    name              VARCHAR(120) NOT NULL,
    name_norm         VARCHAR(120) NOT NULL UNIQUE COMMENT 'lower(trim(name)) for dedupe',
    request_count     INT NOT NULL DEFAULT 1,
    status            ENUM('pending','added','dismissed') DEFAULT 'pending',
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

  // Seed destinations
  "INSERT IGNORE INTO destinations (name, dest_key, extra_per_day, sort_order) VALUES
    ('Manali',       'manali',      0.00, 1),
    ('Shimla',       'shimla',      0.00, 2),
    ('Dharamshala',  'dharamshala', 0.00, 3),
    ('Dalhousie',    'dalhousie',   0.00, 4),
    ('Spiti Valley', 'spiti',     250.00, 5),
    ('Kasol',        'kasol',       0.00, 6),
    ('Kullu',        'kullu',       0.00, 7),
    ('Leh Ladakh',   'leh',       350.00, 8)",

  // Seed taxes
  "INSERT IGNORE INTO taxes_fees (name, type, value, apply_on, sort_order) VALUES
    ('GST',            'percentage', 5.00,   'total',         1),
    ('Service Charge', 'flat',       0.00,   'total',         2)",

  // Seed seasonal pricing (example)
  "INSERT IGNORE INTO seasonal_pricing (name, start_date, end_date, surcharge_pct, active) VALUES
    ('Peak Summer (May–Jun)',  '2025-05-01', '2025-06-30', 15.00, 1),
    ('Peak Winter (Dec–Jan)',  '2025-12-15', '2026-01-15', 10.00, 1)",
];

echo '<!doctype html><html><head><meta charset="utf-8"><title>Pricing Setup</title>
<style>body{font-family:sans-serif;max-width:780px;margin:2rem auto;padding:0 1rem}
li{margin:.3rem 0;padding:.4rem .75rem;border-radius:6px;font-size:.88rem}
.ok{background:#d1fae5;color:#065f46}.err{background:#fee2e2;color:#991b1b}
h2{margin-bottom:1rem}a{color:#c8a75d}</style></head><body>
<h2>⚙️ Pricing Tables Setup</h2><ul>';

$ok=0;$fail=0;
foreach($queries as $q){
  try { $conn->query($q); echo "<li class='ok'>✓ ".htmlspecialchars(substr(trim($q),0,80))."…</li>"; $ok++; }
  catch(mysqli_sql_exception $e){ echo "<li class='err'>✗ ".htmlspecialchars($e->getMessage())."</li>"; $fail++; }
}

/* ── Column upgrades for existing installs ──
   MySQL (unlike MariaDB) has no "ADD COLUMN IF NOT EXISTS", so each column is
   checked against information_schema and added individually. */
function col_exists(mysqli $c, string $table, string $col): bool {
  $t = $c->real_escape_string($table); $co = $c->real_escape_string($col);
  $r = $c->query("SELECT 1 FROM information_schema.COLUMNS
                   WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$t' AND COLUMN_NAME = '$co' LIMIT 1");
  return $r && $r->num_rows > 0;
}

$columnUpgrades = [
  // table, column, definition
  ['tour_packages',     'additional_charge_per_person', "DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Flat fee per person (e.g. guide, permits)' AFTER base_price_per_day"],
  ['tour_packages',     'destination_key',              "VARCHAR(50) NULL"],
  ['booking_enquiries', 'trip_destination',             "VARCHAR(500) DEFAULT NULL AFTER package_id"],
  ['booking_enquiries', 'pickup_custom',                "VARCHAR(120) DEFAULT NULL AFTER pickup_location_id"],
  ['booking_enquiries', 'package_cost',                 "DECIMAL(10,2) DEFAULT 0 AFTER estimated_price"],
  ['booking_enquiries', 'vehicle_cost',                 "DECIMAL(10,2) DEFAULT 0 AFTER package_cost"],
  ['booking_enquiries', 'extra_charges',                "DECIMAL(10,2) DEFAULT 0 AFTER vehicle_cost"],
  ['booking_enquiries', 'tax_amount',                   "DECIMAL(10,2) DEFAULT 0 AFTER extra_charges"],
  ['booking_enquiries', 'breakdown_json',               "JSON DEFAULT NULL AFTER tax_amount"],
  ['booking_enquiries', 'status',                       "ENUM('new','contacted','quoted','confirmed','closed','cancelled') DEFAULT 'new' AFTER breakdown_json"],
  ['booking_enquiries', 'notes',                        "TEXT DEFAULT NULL AFTER status"],
  ['booking_enquiries', 'source',                       "VARCHAR(20) DEFAULT 'calculator' AFTER notes"],
];
foreach ($columnUpgrades as [$tbl, $col, $def]) {
  try {
    if (col_exists($conn, $tbl, $col)) {
      echo "<li class='ok'>✓ $tbl.$col already present</li>"; $ok++;
    } else {
      $conn->query("ALTER TABLE `$tbl` ADD COLUMN `$col` $def");
      echo "<li class='ok'>✓ Added $tbl.$col</li>"; $ok++;
    }
  } catch (mysqli_sql_exception $e) {
    echo "<li class='err'>✗ $tbl.$col — " . htmlspecialchars($e->getMessage()) . '</li>'; $fail++;
  }
}

// Widen/relax columns that changed meaning over time. Safe to re-run.
foreach ([
  "ALTER TABLE booking_enquiries MODIFY COLUMN status ENUM('new','contacted','quoted','confirmed','closed','cancelled') DEFAULT 'new'",
  "ALTER TABLE booking_enquiries MODIFY COLUMN trip_destination VARCHAR(500) NULL",
  "ALTER TABLE booking_enquiries MODIFY COLUMN pickup_date DATE NULL",
  "ALTER TABLE booking_enquiries MODIFY COLUMN drop_date   DATE NULL",
] as $q) {
  try { $conn->query($q); echo "<li class='ok'>✓ ".htmlspecialchars(substr(trim($q),0,80))."…</li>"; $ok++; }
  catch(mysqli_sql_exception $e){ echo "<li class='err'>✗ ".htmlspecialchars($e->getMessage())."</li>"; $fail++; }
}
echo "</ul><p><strong>Done: $ok succeeded, $fail failed.</strong></p>
<p>
  <a href='../admin/pricing.php'>→ Admin Pricing</a> &nbsp;
  <a href='../admin/destinations.php'>→ Admin Destinations</a> &nbsp;
  <a href='../admin/enquiries.php'>→ Admin Enquiries</a> &nbsp;
  <a href='../index.php'>→ Site</a>
</p></body></html>";
