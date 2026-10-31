<?php
/**
 * Jawai Mumal - base configuration.
 * Put real credentials in config.local.php (not committed) to override anything here.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_name('jmsess');
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS'])]);
    session_start();
}

// MySQL (Hostinger -> hPanel -> Databases). If MySQL is unreachable the site falls back to SQLite in /data.
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'jawai_mumal');
define('DB_PORT', 3306);

// First-run admin account. Change the password from Admin > Account right after first login.
define('DEFAULT_ADMIN_USER', 'admin');
define('DEFAULT_ADMIN_PASS', 'admin123');

if (is_file(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}
