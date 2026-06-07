<?php
// Setup script: CLI only, or a signed-in superadmin in the browser.
if (PHP_SAPI !== 'cli') {
    session_start();
    if (($_SESSION['admin_user']['role'] ?? '') !== 'superadmin') {
        http_response_code(403);
        exit('Forbidden — run from the CLI or sign in as superadmin.');
    }
}
require_once __DIR__ . '/../api/config.php';
if (!$conn instanceof mysqli) die('<p style="font-family:sans-serif;color:red">DB connection failed.</p>');

$queries = [

  // Tour packages
  "CREATE TABLE IF NOT EXISTS tour_packages (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    package_name     VARCHAR(150) NOT NULL,
    duration_days    INT NOT NULL DEFAULT 3,
    base_price_per_day DECIMAL(10,2) NOT NULL DEFAULT 2000.00,
    description      VARCHAR(300) DEFAULT NULL,
    status           ENUM('active','inactive') DEFAULT 'active',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

  // Vehicles
  "CREATE TABLE IF NOT EXISTS vehicles (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_name     VARCHAR(100) NOT NULL,
    seating_capacity VARCHAR(20)  NOT NULL,
    daily_rate       DECIMAL(10,2) NOT NULL DEFAULT 3000.00,
    status           ENUM('active','inactive') DEFAULT 'active'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

  // Booking enquiries (price calculator leads)
  "CREATE TABLE IF NOT EXISTS booking_enquiries (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    package_id          INT DEFAULT NULL,
    pickup_location_id  INT DEFAULT NULL,
    vehicle_id          INT DEFAULT NULL,
    customer_name       VARCHAR(100) NOT NULL,
    mobile              VARCHAR(20)  NOT NULL,
    email               VARCHAR(255) DEFAULT NULL,
    travelers           INT NOT NULL DEFAULT 1,
    pickup_date         DATE NOT NULL,
    drop_date           DATE NOT NULL,
    estimated_price     DECIMAL(10,2) DEFAULT NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_created (created_at)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

  // Seed tour packages
  "INSERT IGNORE INTO tour_packages (id, package_name, duration_days, base_price_per_day, description) VALUES
    (1,  'Shimla Manali Tour',           6,  2500, 'Shimla • Kufri • Manali • Rohtang Pass • Solang Valley'),
    (2,  'Kinnaur Spiti Tour',           9,  3000, 'Sarahan • Sangla • Chitkul • Nako • Tabo • Kaza • Pin Valley'),
    (3,  'Spiti Valley Tour',            7,  2800, 'Kaza • Key Monastery • Kibber • Chandratal • Rohtang'),
    (4,  'Dharamshala McLeod Ganj Tour', 4,  2000, 'McLeod Ganj • Triund Trek • Kangra • Palampur • Bir Billing'),
    (5,  'Dalhousie Khajjiar Tour',      4,  1800, 'Dalhousie • Khajjiar • Chamba • Dainkund Peak'),
    (6,  'Shimla Tour',                  3,  1800, 'Shimla • Kufri • Chail • Naldehra • Tattapani'),
    (7,  'Manali Tour',                  4,  2200, 'Manali • Solang Valley • Rohtang • Kullu • Old Manali'),
    (8,  'Amritsar Dharamshala Tour',    5,  2200, 'Amritsar • Golden Temple • Wagah Border • Dharamshala'),
    (9,  'Leh Ladakh Tour',              8,  3500, 'Manali • Jispa • Sarchu • Leh • Pangong Lake • Nubra Valley'),
    (10, 'Kasol Kheerganga Tour',        4,  1600, 'Kasol • Manikaran • Kheerganga • Tosh • Chalal'),
    (11, 'Kullu Manali Tour',            5,  2300, 'Kullu • Kasol • Manikaran • Manali • Solang Valley'),
    (12, 'Chail Kufri Tour',             3,  1700, 'Shimla • Chail • Kufri • Naldehra')",

  // Seed vehicles
  "INSERT IGNORE INTO vehicles (id, vehicle_name, seating_capacity, daily_rate) VALUES
    (1, 'Swift Dzire',              '4+1', 2500),
    (2, 'Toyota Etios',             '4+1', 2500),
    (3, 'Honda Amaze',              '4+1', 2600),
    (4, 'Ertiga',                   '6+1', 3000),
    (5, 'Innova',                   '7+1', 3500),
    (6, 'Innova Crysta',            '7+1', 4000),
    (7, 'Urbania',                  '10+1',6000),
    (8, 'Tempo Traveller (12 Str)', '12+1',7000),
    (9, 'Tempo Traveller (17 Str)', '17+1',8500)",

  // Seed pickup locations (add if not present — uses existing table)
  "INSERT IGNORE INTO pickup_locations (city, sort_order, active) VALUES
    ('Amritsar',   5, 1),
    ('Pathankot',  6, 1),
    ('Ambala',     7, 1),
    ('Kalka',      8, 1),
    ('Ludhiana',   9, 1),
    ('Jalandhar',  10,1),
    ('Patiala',    11,1),
    ('Dehradun',   12,1)",
];

echo '<!doctype html><html><head><meta charset="utf-8"><title>Booking Calculator Setup</title>
<style>body{font-family:sans-serif;max-width:780px;margin:2rem auto;padding:0 1rem}
h2{color:#0b0d12;font-size:1.4rem}ul{padding-left:0;list-style:none}
li{margin:.3rem 0;padding:.45rem .75rem;border-radius:6px;font-size:.9rem}
.ok{background:#d1fae5;color:#065f46}.err{background:#fee2e2;color:#7f1d1d}
.actions{margin-top:1.5rem;display:flex;gap:1rem;flex-wrap:wrap}
.actions a{padding:.6rem 1.2rem;background:#0b0d12;color:#c8a75d;text-decoration:none;border-radius:8px;font-weight:600;font-size:.9rem}
</style></head><body>';
echo '<h2>&#9889; Booking Calculator — Database Setup</h2><ul>';

$ok = 0; $fail = 0;
foreach ($queries as $q) {
    try {
        $conn->query($q);
        echo "<li class='ok'>&#10003; " . htmlspecialchars(rtrim(substr($q, 7, 68))) . '…</li>';
        $ok++;
    } catch (mysqli_sql_exception $e) {
        echo "<li class='err'>&#10007; " . htmlspecialchars($e->getMessage()) . '</li>';
        $fail++;
    }
}

echo "</ul><p><strong>&#10003; Done &mdash; {$ok} succeeded, {$fail} failed.</strong></p>";
echo '<div class="actions">
  <a href="../booking.php">&#8594; Booking Calculator</a>
  <a href="../index.php">&#8594; Back to Site</a>
</div>';
echo '</body></html>';
