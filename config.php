<?php
/**
 * Jawai Mumal - base configuration.
 * Server-specific values (database login, first admin password, ...) go in config.local.php,
 * which is never committed. Anything defined there wins over the defaults below.
 */
if (is_file(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

if (session_status() === PHP_SESSION_NONE) {
    // Sessions are saved in our own folder (shared hosting otherwise deletes them after ~24 minutes) and kept for 30 days.
    $__sd = __DIR__ . '/data/sessions';
    if (!is_dir($__sd)) @mkdir($__sd, 0700, true);
    if (is_dir($__sd) && is_writable($__sd)) session_save_path($__sd);
    @ini_set('session.gc_maxlifetime', '2592000'); @ini_set('session.gc_probability', '1'); @ini_set('session.gc_divisor', '200');
    session_name('jmsess');
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS'])]);
    session_start();
}

// When config.local.php sets DB_NAME the site MUST use that MySQL database (no silent fallback to a local file).
defined('DB_EXPLICIT') || define('DB_EXPLICIT', defined('DB_NAME') && DB_NAME !== '' && !(defined('DB_DRIVER') && DB_DRIVER === 'sqlite'));

// MySQL (Hostinger -> hPanel -> Databases). If MySQL is unreachable the site falls back to SQLite in /data.
// Put  define('DB_DRIVER', 'sqlite');  in config.local.php to always use the built-in SQLite database.
defined('DB_HOST') || define('DB_HOST', 'localhost');
defined('DB_USER') || define('DB_USER', 'root');
defined('DB_PASS') || define('DB_PASS', '');
defined('DB_NAME') || define('DB_NAME', 'jawai_mumal');
defined('DB_PORT') || define('DB_PORT', 3306);

// Admin account. The username is 'admin'. The password is NEVER stored in this public repository:
//  - set it on the server in config.local.php:   define('DEFAULT_ADMIN_PASS', 'your password');
//  - or, if you do nothing, a random password is created on first run and written to data/first-login.txt
//    (open it with hPanel > File Manager, then delete the file after you have signed in and changed the password).
defined('DEFAULT_ADMIN_USER') || define('DEFAULT_ADMIN_USER', 'admin');

// Folder name of the admin panel (the site address is /admin-jm/). If you rename the folder, change this too.
defined('ADMIN_DIR') || define('ADMIN_DIR', 'admin-jm');
