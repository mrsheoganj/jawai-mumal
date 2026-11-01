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
        "CREATE TABLE IF NOT EXISTS jm_slides (id $pk, image VARCHAR(500) NOT NULL, eyebrow VARCHAR(150), title VARCHAR(250), text TEXT, cta_label VARCHAR(60), cta_url VARCHAR(300), cta2_label VARCHAR(60), cta2_url VARCHAR(300), status VARCHAR(20) DEFAULT 'active', sort_order INT DEFAULT 0, device VARCHAR(10) DEFAULT 'both', image_mobile VARCHAR(500))$tail",
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

// The site only takes plain text parameters: drop array-style input (?x[]=1) so it can never cause type errors.
if (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/' . ADMIN_DIR . '/') === false) {   // (admin forms legitimately post arrays)
    foreach ($_GET as $k => $v) if (is_array($v)) unset($_GET[$k]);
    foreach ($_POST as $k => $v) if (is_array($v)) unset($_POST[$k]);
}
if (PHP_SAPI !== 'cli' && !headers_sent()) {   // basic security headers (also set in .htaccess)
    header('X-Content-Type-Options: nosniff'); header('X-Frame-Options: SAMEORIGIN'); header('Referrer-Policy: strict-origin-when-cross-origin');
}
require_once __DIR__ . '/updater.php';
require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/seo.php';
require_once __DIR__ . '/validate.php';

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
/** v2: no generic stock landscapes – only real hotel photos, the leopard and the food. Other places show the emblem until real photos are uploaded. */
function jm_migrate_images_v2(): void {
    if (get_setting_raw('img_remap_v2') === '1') return;
    $gen = ['assets/images/hills-green.jpg', 'assets/images/hills-terrace.jpg'];
    try {
        q("UPDATE jm_slides SET image = ? WHERE image IN (?, ?)", array_merge(['assets/images/hotel-front.jpg'], $gen));
        q("DELETE FROM jm_gallery WHERE image IN (?, ?)", $gen);
        q("UPDATE jm_safaris SET image = ? WHERE slug = ?", ['assets/images/leopard-log.jpg', 'wildlife-photography-safari']);
        foreach (['jm_safaris', 'jm_attractions', 'jm_blog', 'jm_rooms', 'jm_menu'] as $t) q("UPDATE $t SET image = '' WHERE image IN (?, ?)", $gen);
        foreach (['best-time-to-visit-jawai' => 'assets/images/hotel-day.jpg', 'how-to-reach-jawai-from-udaipur-jodhpur-jaipur' => 'assets/images/hotel-front.jpg', 'romantic-getaway-jawai-couples-honeymoon' => 'assets/images/hotel-front.jpg', 'jawai-wildlife-photography-tips' => 'assets/images/leopard-log.jpg'] as $sl => $im) q("UPDATE jm_blog SET image = ? WHERE slug = ? AND (image = '' OR image IS NULL)", [$im, $sl]);
        q("UPDATE jm_gallery SET title = 'Rajasthani bowls' WHERE title = 'Restaurant hall'");
    } catch (Throwable $e) { /* optional tables may not exist */ }
    set_setting('img_remap_v2', '1');
}
/** v3: safari and table rates are no longer published (enquiry only). Update the one article that mentioned them. */
function jm_migrate_pricing_v3(): void {
    if (get_setting_raw('pricing_v3') === '1') return;
    try {
        $rep = ['Prices change with the season, so we avoid quoting numbers that go out of date.' => 'Safari rates depend on the season, the group size and the kind of drive, so we share them on enquiry rather than print numbers that go out of date.',
                'See the live prices and timings on our <a href="{{safari}}">safari page</a>.' => 'See the timings and what each drive covers on our <a href="{{safari}}">safari page</a>, then ask for the current rate.',
                'We confirm availability and the final rate on WhatsApp.' => 'We confirm availability and share the rate on WhatsApp.'];
        foreach ($rep as $a => $b) q("UPDATE jm_blog SET body = REPLACE(body, ?, ?) WHERE slug = ?", [$a, $b, 'jawai-safari-cost-booking-what-is-included']);
    } catch (Throwable $e) {}
    set_setting('pricing_v3', '1');
}
/** v5: separate banner slides for phones and for computers / big screens. */
function jm_migrate_slides_device(): void {
    try { q('SELECT image_mobile FROM jm_slides LIMIT 1'); } catch (Throwable $e) { try { q('ALTER TABLE jm_slides ADD COLUMN image_mobile VARCHAR(500)'); } catch (Throwable $e2) {} }
    try { q('SELECT device FROM jm_slides LIMIT 1'); }
    catch (Throwable $e) { try { q("ALTER TABLE jm_slides ADD COLUMN device VARCHAR(10) DEFAULT 'both'"); } catch (Throwable $e2) {} }
    try { q("UPDATE jm_slides SET device = 'both' WHERE device IS NULL OR device = ''"); } catch (Throwable $e) {}
}
jm_migrate_slides_device();
jm_migrate_images();
jm_migrate_images_v2();
/** v4: calmer copy. Replace spaced em dashes in saved content with commas. */
function jm_migrate_copy_v4(): void {
    if (get_setting_raw('copy_v4') === '1') return;
    $tables = ['jm_settings' => ['svalue'], 'jm_rooms' => ['tagline', 'description'], 'jm_menu' => ['description'], 'jm_safaris' => ['description', 'highlights'], 'jm_attractions' => ['description'], 'jm_faqs' => ['question', 'answer'], 'jm_slides' => ['text', 'title'], 'jm_blog' => ['body', 'excerpt', 'meta_description']];
    foreach ($tables as $t => $cols) foreach ($cols as $c) {
        try { q("UPDATE $t SET $c = REPLACE($c, ?, ?) WHERE $c LIKE ?", [' — ', ', ', '% — %']); } catch (Throwable $e) {}
    }
    set_setting('copy_v4', '1');
}
jm_migrate_pricing_v3();
/** v6: one room type, the Premium Room (rate starts from the price and varies by season). */
function jm_migrate_single_room(): void {
    if (get_setting_raw('single_room_v6') === '1') return;
    try {
        $std = row("SELECT * FROM jm_rooms WHERE slug = 'standard-room'");
        if ($std && $std['name'] === 'Standard Room') {
            q("UPDATE jm_rooms SET slug='premium-room', name='Premium Room', category='Premium', tagline=?, description=?, image=?, featured=1, sort_order=1 WHERE id=?",
              ['Calm, bright and made for easy stays', 'Our one room type: a comfortable air-conditioned Premium Room with a king bed, soft lighting and a quiet work corner, a good base for travellers and couples exploring Jawai. Rates start from the price shown and vary with the season; we confirm the exact rate for your dates on WhatsApp.', 'assets/images/room-modern.jpg', $std['id']]);
        }
        foreach (['deluxe-room' => 'Deluxe Room', 'heritage-suite' => 'Heritage Suite', 'granite-hill-room' => 'Granite Hill Room', 'family-room' => 'Family Room', 'terrace-suite' => 'Terrace Suite'] as $sl => $nm)
            q("UPDATE jm_rooms SET status='hidden', featured=0 WHERE slug = ? AND name = ?", [$sl, $nm]);
        q("UPDATE jm_gallery SET title='Premium Room' WHERE title IN ('Heritage suite','Granite Hill Room','Terrace Suite','Deluxe Room')");
    } catch (Throwable $e) {}
    set_setting('single_room_v6', '1');
}
jm_migrate_copy_v4();
/** v7: room types Premium (from 2500), Deluxe 3000, Super Deluxe 3500, Family 4500. */
function jm_migrate_room_types_v7(): void {
    if (get_setting_raw('room_types_v7') === '1') return;
    try {
        $am = "Air conditioning\nFree Wi-Fi\nHot water 24 hours\nTea & coffee maker\nWork desk\nDaily housekeeping";
        q("UPDATE jm_rooms SET description = ?, featured = 1, sort_order = 1 WHERE slug = 'premium-room' AND description LIKE ?", ['A comfortable air-conditioned room with a king bed, soft lighting and a quiet work corner. Our starting rate; rates vary with the season and we confirm the exact rate for your dates on WhatsApp.', 'Our one room type%']);
        $def = [
            ['deluxe-room', 'Deluxe Room', 'Deluxe', 'A little more space to spread out', 3000, '280 sq ft', '2 adults + 1 child', '1 King bed', 'Garden side', 'assets/images/room-bed.jpg', 'assets/images/room-modern.jpg', 'A roomier air-conditioned room with a king bed, sitting corner and good natural light, a relaxed base for a few days around Jawai.', 6, 1, 2],
            ['super-deluxe-room', 'Super Deluxe Room', 'Super Deluxe', 'Our most spacious double room', 3500, '320 sq ft', '2 adults + 2 children', '1 King bed + sofa', 'Hill side', 'assets/images/room-heritage.jpg', 'assets/images/room-desert-suite.jpg', 'Our most spacious double room, with a king bed, sofa seating and a large window, good for longer stays and small families.', 4, 1, 3],
            ['family-room', 'Family Room', 'Family', 'Space for everyone, close together', 4500, '380 sq ft', '4 adults', '2 Queen beds', 'Garden side', 'assets/images/room-desert-suite.jpg', 'assets/images/room-heritage.jpg', 'Two queen beds and extra seating, so the whole family stays together comfortably.', 2, 0, 4],
        ];
        foreach ($def as $r) {
            $ex = row('SELECT * FROM jm_rooms WHERE slug = ?', [$r[0]]);
            if (!$ex) {
                q("INSERT INTO jm_rooms (slug,name,category,tagline,price,size,capacity,beds,view_type,image,gallery,description,amenities,inventory,featured,status,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [$r[0], $r[1], $r[2], $r[3], $r[4], $r[5], $r[6], $r[7], $r[8], $r[9], $r[10], $r[11], $am, $r[12], $r[13], 'active', $r[14]]);
            } elseif ($ex['status'] === 'hidden' && $ex['name'] === $r[1]) {   // the old hidden demo room: bring it back with the new rate
                q("UPDATE jm_rooms SET status='active', price=?, category=?, tagline=?, description=?, featured=?, sort_order=? WHERE id=?", [$r[4], $r[2], $r[3], $r[11], $r[13], $r[14], $ex['id']]);
            }
        }
    } catch (Throwable $e) {}
    set_setting('room_types_v7', '1');
}
jm_migrate_single_room();
jm_migrate_room_types_v7();
if (get_setting_raw('blog_seeded') !== '1') { require_once __DIR__ . '/seed_blog.php'; jm_seed_blog(); }
upd_maybe_auto();   // live sync with GitHub (if enabled)
jm_gate();
