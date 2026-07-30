<?php
/*
 * TEMPLATE — copy to db-config.php on the LIVE SERVER ONLY, then fill in the
 * real values from your hosting control panel.
 *
 * Do NOT create db-config.php on a local XAMPP machine: when the file is
 * absent, includes/vars.php falls back to the local XAMPP defaults
 * (tourismsite / root / no password), which is what you want locally.
 *
 * The real db-config.php is git-ignored, so it is never committed and it
 * survives every future `git pull` on the server.
 */
declare(strict_types=1);

define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'your_db_name');
define('DB_PORT', 3306);
