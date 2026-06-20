-- Himachal Safar — database setup
-- Run this in phpMyAdmin or: mysql -u root -p < database.sql
-- If upgrading an existing install, run the ALTER statements at the bottom of this file.

CREATE DATABASE IF NOT EXISTS tourismsite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tourismsite;

CREATE TABLE IF NOT EXISTS bookings (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(100) NOT NULL,
  email        VARCHAR(150) DEFAULT '',
  phone        VARCHAR(20)  NOT NULL,
  pickup       VARCHAR(120) DEFAULT '',
  destination  VARCHAR(80)  DEFAULT '',
  package      ENUM('budget','classic','luxury') DEFAULT NULL,
  travel_date  DATE         DEFAULT NULL,
  pax          TINYINT      DEFAULT 1,
  message      TEXT,
  status       ENUM('new','contacted','confirmed','cancelled') DEFAULT 'new',
  submitted_at DATETIME     DEFAULT CURRENT_TIMESTAMP,
  KEY idx_status (status),
  KEY idx_submitted_at (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  setting_key   VARCHAR(60) UNIQUE NOT NULL,
  setting_value TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
  ('agency_whatsapp', '919876543210'),
  ('agency_name',     'Himachal Safar'),
  ('agency_email',    'info@himachalyatratravels.com'),
  ('agency_phone',    '+91 98765 43210');

-- Admin users (multi-user login)
CREATE TABLE IF NOT EXISTS admin_users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(50) UNIQUE NOT NULL,
  full_name     VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('superadmin','admin','staff') DEFAULT 'staff',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default superadmin: username=admin  password=tour@123
-- !! CHANGE THE PASSWORD IMMEDIATELY after first login (Admin → Users → Reset PW).
-- This default is committed to the repo and is therefore public knowledge.
INSERT IGNORE INTO admin_users (username, full_name, password_hash, role) VALUES
  ('admin', 'Administrator', '$2y$10$9VJsilNVfDWJ8gGbfN2N0.HeFKVCLDjVzeZn33jXdFx0ZlTbJ3HC.', 'superadmin');

-- Audit log
CREATE TABLE IF NOT EXISTS audit_log (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT DEFAULT NULL,
  username   VARCHAR(50) DEFAULT NULL,
  action     VARCHAR(50) NOT NULL,
  details    TEXT,
  ip_address VARCHAR(45) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_audit_action (action),
  KEY idx_audit_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Upgrade scripts for existing installs ──
-- Run these if the DB already exists and tables were created without these columns:
--   ALTER TABLE bookings ADD COLUMN pickup VARCHAR(120) DEFAULT '' AFTER phone;
--   ALTER TABLE admin_users MODIFY COLUMN role ENUM('superadmin','admin','staff') DEFAULT 'staff';
--   UPDATE admin_users SET role='superadmin' WHERE username='admin';

-- Photos for destination galleries
CREATE TABLE IF NOT EXISTS photos (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  destination VARCHAR(50) NOT NULL COMMENT 'manali|shimla|dharamshala|dalhousie|spiti|general',
  caption     VARCHAR(200) DEFAULT '',
  filename    VARCHAR(255) NOT NULL,
  sort_order  TINYINT DEFAULT 0,
  role        ENUM('gallery','hero','about','route') NOT NULL DEFAULT 'gallery',
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_photos_dest (destination)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Maps photos to named "slots" (purposes). One photo can fill several slots.
--   home_hero      → homepage hero slideshow (max 5)
--   {dest}_hero    → a destination's hero image  (max 1)
--   {dest}_about   → a destination's about image (max 1)
-- Capacities are enforced in PHP (includes/vars.php); add new purposes by adding a slot key.
CREATE TABLE IF NOT EXISTS photo_assignments (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  photo_id  INT NOT NULL,
  slot      VARCHAR(40) NOT NULL,
  position  TINYINT NOT NULL DEFAULT 0,
  UNIQUE KEY uniq_photo_slot (photo_id, slot),
  KEY idx_slot (slot),
  CONSTRAINT fk_pa_photo FOREIGN KEY (photo_id) REFERENCES photos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Routes with per-route photo assignment
CREATE TABLE IF NOT EXISTS routes (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(100) NOT NULL,
  km         VARCHAR(20)  DEFAULT '',
  duration   VARCHAR(30)  DEFAULT '',
  badge      VARCHAR(60)  DEFAULT '',
  dest_key   VARCHAR(30)  DEFAULT '',
  dest_page  VARCHAR(50)  DEFAULT '',
  photo_id   INT          DEFAULT NULL,
  sort_order TINYINT      DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO routes (id, name, km, duration, badge, dest_key, dest_page, sort_order) VALUES
(1,'Delhi to Manali','550 km','12-14 hrs','Popular Route','manali','manali.php',1),
(2,'Delhi to Shimla','350 km','8-9 hrs','Weekend Escape','shimla','shimla.php',2),
(3,'Delhi to Dharamshala','480 km','10-12 hrs','McLeodganj Retreat','dharamshala','dharamshala.php',3),
(4,'Delhi to Dalhousie','560 km','11-13 hrs','Heritage Hills','dalhousie','dalhousie.php',4),
(5,'Chandigarh to Manali','300 km','8-9 hrs','Comfort Transfer','manali','manali.php',5),
(6,'Chandigarh to Shimla','115 km','3-4 hrs','Quick Mountain Run','shimla','shimla.php',6);

-- Pickup locations (origin cities for bookings)
CREATE TABLE IF NOT EXISTS pickup_locations (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  city       VARCHAR(100) NOT NULL UNIQUE,
  sort_order TINYINT DEFAULT 0,
  active     TINYINT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO pickup_locations (city, sort_order, active) VALUES
  ('Delhi', 1, 1),
  ('Chandigarh', 2, 1),
  ('Shimla', 3, 1),
  ('Manali', 4, 1);

-- Customer reviews — submitted via public form, moderated by admin before publishing
CREATE TABLE IF NOT EXISTS reviews (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  city          VARCHAR(80)  DEFAULT '',
  route         VARCHAR(120) DEFAULT '',
  rating        TINYINT      NOT NULL,
  review_text   TEXT         NOT NULL,
  photo         VARCHAR(255) DEFAULT '',
  status        ENUM('pending','approved','rejected') DEFAULT 'pending',
  submitted_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_reviews_status (status),
  KEY idx_reviews_submitted (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
