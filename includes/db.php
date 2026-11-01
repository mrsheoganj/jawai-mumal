<?php
/**
 * Database bootstrap: MySQL when reachable (Hostinger), otherwise SQLite in /data so the site always runs.
 * Creates the schema and demo content on first run.
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/functions.php';

$pdo = null;
$dbType = 'mysql';
$dataDir = __DIR__ . '/../data';
if (!is_dir($dataDir)) { @mkdir($dataDir, 0755, true); }

try {
    if (defined('DB_DRIVER') && DB_DRIVER === 'sqlite') { throw new RuntimeException('sqlite forced'); }
    $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 2,
    ]);
} catch (Throwable $e) {
    if (DB_EXPLICIT) {   // a MySQL database was configured on purpose: never hide a broken connection behind a different database
        error_log('Jawai Mumal: MySQL connection failed: ' . $e->getMessage());
        http_response_code(503); header('Retry-After: 120'); header('Content-Type: text/html; charset=utf-8');
        exit('<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Temporarily unavailable</title><body style="font-family:system-ui,sans-serif;text-align:center;padding:4rem 1rem"><h1>We will be right back</h1><p>The website cannot reach its database at the moment. Please try again in a few minutes.</p><p style="color:#777;font-size:.9rem">(Site owner: check the database name, user and password in config.local.php.)</p>');
    }
    try {
        $pdo = new PDO('sqlite:' . $dataDir . '/jawai_mumal.sqlite', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $dbType = 'sqlite';
    } catch (Throwable $e2) {
        http_response_code(500);
        exit('Database error. Please check config.php');
    }
}

function db(): PDO { global $pdo; return $pdo; }
function db_type(): string { global $dbType; return $dbType; }

function jm_install_schema(PDO $pdo, string $type): void {
    $pk = $type === 'mysql' ? 'INT AUTO_INCREMENT PRIMARY KEY' : 'INTEGER PRIMARY KEY AUTOINCREMENT';
    $tail = $type === 'mysql' ? ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4' : '';
    $ts = 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP';
    $t = [
        "CREATE TABLE IF NOT EXISTS jm_settings (skey VARCHAR(100) PRIMARY KEY, svalue TEXT)$tail",
        "CREATE TABLE IF NOT EXISTS jm_admins (id $pk, username VARCHAR(100) UNIQUE NOT NULL, password_hash VARCHAR(255) NOT NULL, full_name VARCHAR(150), created_at $ts)$tail",
        "CREATE TABLE IF NOT EXISTS jm_rooms (id $pk, slug VARCHAR(120) UNIQUE, name VARCHAR(200) NOT NULL, category VARCHAR(100), tagline VARCHAR(255), price INT NOT NULL DEFAULT 0, size VARCHAR(60), capacity VARCHAR(100), beds VARCHAR(100), view_type VARCHAR(150), image VARCHAR(500), gallery TEXT, description TEXT, amenities TEXT, inventory INT DEFAULT 1, featured INT DEFAULT 0, status VARCHAR(20) DEFAULT 'active', sort_order INT DEFAULT 0, created_at $ts)$tail",
        "CREATE TABLE IF NOT EXISTS jm_menu (id $pk, name VARCHAR(200) NOT NULL, category VARCHAR(100), price INT NOT NULL DEFAULT 0, veg INT DEFAULT 1, spice VARCHAR(30), description TEXT, image VARCHAR(500), tag VARCHAR(60), available INT DEFAULT 1, sort_order INT DEFAULT 0)$tail",
        "CREATE TABLE IF NOT EXISTS jm_safaris (id $pk, slug VARCHAR(120) UNIQUE, title VARCHAR(200) NOT NULL, duration VARCHAR(100), timing VARCHAR(200), price_pp INT NOT NULL DEFAULT 0, private_price INT NOT NULL DEFAULT 0, vehicle VARCHAR(150), difficulty VARCHAR(60), image VARCHAR(500), description TEXT, highlights TEXT, badge VARCHAR(60), status VARCHAR(20) DEFAULT 'active', sort_order INT DEFAULT 0)$tail",
        "CREATE TABLE IF NOT EXISTS jm_attractions (id $pk, name VARCHAR(200) NOT NULL, category VARCHAR(60), distance VARCHAR(80), description TEXT, image VARCHAR(500), status VARCHAR(20) DEFAULT 'active', sort_order INT DEFAULT 0)$tail",
        "CREATE TABLE IF NOT EXISTS jm_gallery (id $pk, title VARCHAR(200), category VARCHAR(60), image VARCHAR(500) NOT NULL, sort_order INT DEFAULT 0)$tail",
        "CREATE TABLE IF NOT EXISTS jm_testimonials (id $pk, name VARCHAR(150) NOT NULL, origin VARCHAR(150), rating INT DEFAULT 5, quote TEXT NOT NULL, status VARCHAR(20) DEFAULT 'hidden', sort_order INT DEFAULT 0)$tail",
        "CREATE TABLE IF NOT EXISTS jm_blog (id $pk, slug VARCHAR(160) UNIQUE, title VARCHAR(250) NOT NULL, excerpt TEXT, body MEDIUMTEXT, image VARCHAR(500), meta_title VARCHAR(250), meta_description VARCHAR(400), keywords VARCHAR(400), category VARCHAR(80), status VARCHAR(20) DEFAULT 'published', published_at DATETIME, sort_order INT DEFAULT 0)$tail",
        "CREATE TABLE IF NOT EXISTS jm_faqs (id $pk, question VARCHAR(300) NOT NULL, answer TEXT NOT NULL, sort_order INT DEFAULT 0)$tail",
        "CREATE TABLE IF NOT EXISTS jm_slides (id $pk, image VARCHAR(500) NOT NULL, eyebrow VARCHAR(150), title VARCHAR(250), text TEXT, cta_label VARCHAR(60), cta_url VARCHAR(300), cta2_label VARCHAR(60), cta2_url VARCHAR(300), status VARCHAR(20) DEFAULT 'active', sort_order INT DEFAULT 0)$tail",
        "CREATE TABLE IF NOT EXISTS jm_routes (id $pk, name VARCHAR(200) NOT NULL, kind VARCHAR(20) DEFAULT 'rail', code VARCHAR(20), km DECIMAL(8,1) DEFAULT 0, travel_time VARCHAR(60), note VARCHAR(255), lat VARCHAR(30), lng VARCHAR(30), geom TEXT, status VARCHAR(20) DEFAULT 'active', sort_order INT DEFAULT 0)$tail",
        "CREATE TABLE IF NOT EXISTS jm_mail_log (id $pk, created_at $ts, kind VARCHAR(30), to_addr VARCHAR(250), subject VARCHAR(300), status VARCHAR(12), error TEXT)$tail",
        "CREATE TABLE IF NOT EXISTS jm_attempts (id $pk, ip VARCHAR(64), created_at INT)$tail",
        "CREATE TABLE IF NOT EXISTS jm_bookings (id $pk, code VARCHAR(30) UNIQUE, type VARCHAR(20) NOT NULL, guest_name VARCHAR(150) NOT NULL, phone VARCHAR(40) NOT NULL, email VARCHAR(150), item_id INT, item_name VARCHAR(250), date_from DATE, date_to DATE, time_slot VARCHAR(40), guests INT DEFAULT 1, nights INT DEFAULT 0, amount INT DEFAULT 0, tax INT DEFAULT 0, status VARCHAR(30) DEFAULT 'New', notes TEXT, admin_notes TEXT, created_at $ts)$tail",
    ];
    foreach ($t as $sql) { $pdo->exec($sql); }
}

jm_install_schema($pdo, $dbType);
// Older databases: add the route geometry column
try { $pdo->query('SELECT geom FROM jm_routes LIMIT 1'); } catch (Throwable $e) { try { $pdo->exec('ALTER TABLE jm_routes ADD COLUMN geom TEXT'); } catch (Throwable $e2) {} }

// Admin login: never a known password. Uses DEFAULT_ADMIN_PASS from config.local.php, otherwise a random one saved to data/first-login.txt
function jm_random_password(): string { $c = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789'; $p = ''; for ($i = 0; $i < 14; $i++) $p .= $c[random_int(0, strlen($c) - 1)]; return $p; }
function jm_set_admin_password(PDO $pdo, string $pw, bool $announce): void {
    $id = (int)$pdo->query('SELECT MIN(id) FROM jm_admins')->fetchColumn();
    $pdo->prepare('UPDATE jm_admins SET password_hash = ? WHERE id = ?')->execute([password_hash($pw, PASSWORD_DEFAULT), $id]);
    if ($announce) {
        $u = $pdo->query('SELECT username FROM jm_admins WHERE id = ' . $id)->fetchColumn();
        @file_put_contents(__DIR__ . '/../data/first-login.txt', "Jawai Mumal admin login\nAddress : /admin-jm/login.php\nUsername: $u\nPassword: $pw\n\nSign in, change the password under My account, then DELETE this file.\n");
    }
}
if ((int)$pdo->query('SELECT COUNT(*) FROM jm_admins')->fetchColumn() === 0) {
    $pdo->prepare('INSERT INTO jm_admins (username, password_hash, full_name) VALUES (?,?,?)')->execute([DEFAULT_ADMIN_USER, 'x', 'Site Admin']);
    if (defined('DEFAULT_ADMIN_PASS') && DEFAULT_ADMIN_PASS !== '') jm_set_admin_password($pdo, DEFAULT_ADMIN_PASS, false); else jm_set_admin_password($pdo, jm_random_password(), true);
}
// A password set in config.local.php is (re)applied whenever you change it there
if (defined('DEFAULT_ADMIN_PASS') && DEFAULT_ADMIN_PASS !== '') {
    $fp = hash('sha256', 'jm-admin|' . DEFAULT_ADMIN_PASS);
    if (get_setting_raw('admin_pw_fp') !== $fp) { jm_set_admin_password($pdo, DEFAULT_ADMIN_PASS, false); set_setting('admin_pw_fp', $fp); @unlink(__DIR__ . '/../data/first-login.txt'); }
}
// Safety net for databases that were created with the old public default password
foreach ($pdo->query('SELECT id, password_hash FROM jm_admins')->fetchAll() as $__a) {
    if (password_verify('admin123', $__a['password_hash'])) {
        $np = (defined('DEFAULT_ADMIN_PASS') && DEFAULT_ADMIN_PASS !== '') ? DEFAULT_ADMIN_PASS : jm_random_password();
        $pdo->prepare('UPDATE jm_admins SET password_hash = ? WHERE id = ?')->execute([password_hash($np, PASSWORD_DEFAULT), $__a['id']]);
        if (!defined('DEFAULT_ADMIN_PASS') || DEFAULT_ADMIN_PASS === '') @file_put_contents(__DIR__ . '/../data/first-login.txt', "Jawai Mumal admin login\nAddress : /admin-jm/login.php\nUsername: admin\nPassword: $np\n\nSign in, change the password under My account, then DELETE this file.\n");
    }
}

// First run: seed demo content
if ((int)$pdo->query('SELECT COUNT(*) FROM jm_rooms')->fetchColumn() === 0 && !get_setting_raw('seeded')) {
    require __DIR__ . '/seed.php';
    jm_seed($pdo);
    set_setting('seeded', '1');
}

// Hero slides are seeded once (also for databases created before slides existed)
if (!get_setting_raw('slides_seeded')) {
    if ((int)$pdo->query('SELECT COUNT(*) FROM jm_slides')->fetchColumn() === 0) {
        require_once __DIR__ . '/seed.php';
        jm_seed_slides($pdo);
    }
    set_setting('slides_seeded', '1');
}

// Travel routes (distance cards) are seeded once, also for databases created earlier
if (!get_setting_raw('routes_seeded')) {
    if ((int)$pdo->query('SELECT COUNT(*) FROM jm_routes')->fetchColumn() === 0) {
        require_once __DIR__ . '/seed.php';
        jm_seed_routes($pdo);
    }
    set_setting('routes_seeded', '1');
}

require_once __DIR__ . '/updater.php';
require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/seo.php';

/** One-off clean-up: stock photos that did not belong to Jawai were replaced; point old references at the related photos. */
function jm_migrate_images(): void {
    if (get_setting_raw('img_remap_v1') === '1') return;
    $map = ['hero-savanna' => 'hills-green', 'safari-sunset' => 'leopard-log', 'culture-hawamahal' => 'hills-terrace', 'culture-fort' => 'hills-green',
            'resort-night' => 'hotel-front', 'villa-pool' => 'hotel-day', 'resort-deck' => 'hotel-front', 'dining-hall' => 'food-bowls', 'dining-fine' => 'food-thali'];
    $tables = ['jm_settings', 'jm_rooms', 'jm_menu', 'jm_safaris', 'jm_attractions', 'jm_gallery', 'jm_testimonials', 'jm_slides', 'jm_routes'];
    foreach ($tables as $t) {
        try {
            $cols = [];
            if (db()->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') { foreach (rows("PRAGMA table_info($t)") as $c) if (stripos($c['type'], 'INT') === false) $cols[] = $c['name']; }
            else { foreach (rows("SHOW COLUMNS FROM $t") as $c) if (preg_match('/char|text/i', $c['Type'])) $cols[] = $c['Field']; }
            foreach ($cols as $c) foreach ($map as $a => $b)
                q("UPDATE $t SET `$c` = REPLACE(`$c`, ?, ?) WHERE `$c` LIKE ?", ["assets/images/$a.jpg", "assets/images/$b.jpg", "%assets/images/$a.jpg%"]);
        } catch (Throwable $e) { /* table or column not present: ignore */ }
    }
    try { q("UPDATE jm_settings SET svalue = 'Reservations' WHERE skey IN ('owner1_role','owner2_role') AND svalue = 'Owner'"); } catch (Throwable $e) {}
    set_setting('img_remap_v1', '1');
}
jm_migrate_images();
if (get_setting_raw('blog_seeded') !== '1') { require_once __DIR__ . '/seed_blog.php'; jm_seed_blog(); }
upd_maybe_auto();   // live sync with GitHub (if enabled)
jm_gate();
