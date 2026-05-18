-- WanderVista Tours — database setup
-- Run this in phpMyAdmin or: mysql -u root -p < database.sql

CREATE DATABASE IF NOT EXISTS tourismsite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tourismsite;

CREATE TABLE IF NOT EXISTS bookings (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(100) NOT NULL,
  email        VARCHAR(150) DEFAULT '',
  phone        VARCHAR(20)  NOT NULL,
  destination  VARCHAR(80)  DEFAULT '',
  package      ENUM('budget','classic','luxury') DEFAULT NULL,
  travel_date  DATE         DEFAULT NULL,
  pax          TINYINT      DEFAULT 1,
  message      TEXT,
  status       ENUM('new','contacted','confirmed','cancelled') DEFAULT 'new',
  submitted_at DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  setting_key   VARCHAR(60) UNIQUE NOT NULL,
  setting_value TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
  ('agency_whatsapp', '919876543210'),
  ('agency_name',     'India Yatra Travels'),
  ('agency_email',    'info@indiayatratravels.com'),
  ('agency_phone',    '+91 98765 43210');
