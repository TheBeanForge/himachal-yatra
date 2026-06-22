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

  // Add extra charge column to tour_packages
  "ALTER TABLE tour_packages
     ADD COLUMN IF NOT EXISTS additional_charge_per_person DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Flat fee per person (e.g. guide, permits)'
       AFTER base_price_per_day",

  // Ensure booking_enquiries has full breakdown columns
  "ALTER TABLE booking_enquiries
     ADD COLUMN IF NOT EXISTS package_cost    DECIMAL(10,2) DEFAULT 0 AFTER estimated_price,
     ADD COLUMN IF NOT EXISTS vehicle_cost    DECIMAL(10,2) DEFAULT 0 AFTER package_cost,
     ADD COLUMN IF NOT EXISTS extra_charges   DECIMAL(10,2) DEFAULT 0 AFTER vehicle_cost,
     ADD COLUMN IF NOT EXISTS tax_amount      DECIMAL(10,2) DEFAULT 0 AFTER extra_charges,
     ADD COLUMN IF NOT EXISTS breakdown_json  JSON          DEFAULT NULL AFTER tax_amount,
     ADD COLUMN IF NOT EXISTS status         ENUM('new','contacted','confirmed','cancelled') DEFAULT 'new' AFTER breakdown_json,
     ADD COLUMN IF NOT EXISTS notes          TEXT DEFAULT NULL AFTER status,
     ADD COLUMN IF NOT EXISTS source         VARCHAR(20) DEFAULT 'calculator' AFTER notes,
     MODIFY COLUMN pickup_date DATE NULL,
     MODIFY COLUMN drop_date   DATE NULL",

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
echo "</ul><p><strong>Done: $ok succeeded, $fail failed.</strong></p>
<p>
  <a href='../admin/pricing.php'>→ Admin Pricing</a> &nbsp;
  <a href='../admin/destinations.php'>→ Admin Destinations</a> &nbsp;
  <a href='../admin/enquiries.php'>→ Admin Enquiries</a> &nbsp;
  <a href='../index.php'>→ Site</a>
</p></body></html>";
