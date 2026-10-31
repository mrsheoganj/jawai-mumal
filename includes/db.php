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
    $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 2,
    ]);
} catch (Throwable $e) {
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
        "CREATE TABLE IF NOT EXISTS jm_faqs (id $pk, question VARCHAR(300) NOT NULL, answer TEXT NOT NULL, sort_order INT DEFAULT 0)$tail",
        "CREATE TABLE IF NOT EXISTS jm_slides (id $pk, image VARCHAR(500) NOT NULL, eyebrow VARCHAR(150), title VARCHAR(250), text TEXT, cta_label VARCHAR(60), cta_url VARCHAR(300), cta2_label VARCHAR(60), cta2_url VARCHAR(300), status VARCHAR(20) DEFAULT 'active', sort_order INT DEFAULT 0)$tail",
        "CREATE TABLE IF NOT EXISTS jm_routes (id $pk, name VARCHAR(200) NOT NULL, kind VARCHAR(20) DEFAULT 'rail', code VARCHAR(20), km DECIMAL(8,1) DEFAULT 0, travel_time VARCHAR(60), note VARCHAR(255), lat VARCHAR(30), lng VARCHAR(30), geom TEXT, status VARCHAR(20) DEFAULT 'active', sort_order INT DEFAULT 0)$tail",
        "CREATE TABLE IF NOT EXISTS jm_attempts (id $pk, ip VARCHAR(64), created_at INT)$tail",
        "CREATE TABLE IF NOT EXISTS jm_bookings (id $pk, code VARCHAR(30) UNIQUE, type VARCHAR(20) NOT NULL, guest_name VARCHAR(150) NOT NULL, phone VARCHAR(40) NOT NULL, email VARCHAR(150), item_id INT, item_name VARCHAR(250), date_from DATE, date_to DATE, time_slot VARCHAR(40), guests INT DEFAULT 1, nights INT DEFAULT 0, amount INT DEFAULT 0, tax INT DEFAULT 0, status VARCHAR(30) DEFAULT 'New', notes TEXT, admin_notes TEXT, created_at $ts)$tail",
    ];
    foreach ($t as $sql) { $pdo->exec($sql); }
}

jm_install_schema($pdo, $dbType);
// Older databases: add the route geometry column
try { $pdo->query('SELECT geom FROM jm_routes LIMIT 1'); } catch (Throwable $e) { try { $pdo->exec('ALTER TABLE jm_routes ADD COLUMN geom TEXT'); } catch (Throwable $e2) {} }

// First run: seed demo content
if ((int)$pdo->query('SELECT COUNT(*) FROM jm_admins')->fetchColumn() === 0) {
    $pdo->prepare('INSERT INTO jm_admins (username, password_hash, full_name) VALUES (?,?,?)')
        ->execute([DEFAULT_ADMIN_USER, password_hash(DEFAULT_ADMIN_PASS, PASSWORD_DEFAULT), 'Site Owner']);
}
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
