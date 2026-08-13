-- ═══════════════════════════════════════════════════════════════════════════
-- Remove the reviews feature — database side
-- ═══════════════════════════════════════════════════════════════════════════
-- The review section has been removed from the public site and the admin
-- panel, and all its PHP/JS/CSS is gone. This drops the table it used.
--
-- IRREVERSIBLE. Take a backup first — if the table holds real customer
-- reviews, dropping it destroys them permanently:
--
--   mysqldump -u USER -p DBNAME reviews > reviews-backup.sql
--
-- In phpMyAdmin: select the `reviews` table -> Export -> Go, before running
-- anything below.
--
-- Run this on EACH environment (local and production) once you are satisfied
-- the site works without the feature.
-- ═══════════════════════════════════════════════════════════════════════════

DROP TABLE IF EXISTS `reviews`;

-- Uploaded review photos, if any, live in uploads/reviews/ on disk.
-- Delete that folder manually after confirming the backup is good.
