<?php
/** Shared helpers for public site and admin. */
require_once __DIR__ . '/settings_schema.php';

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

/** Base path of the site (works at domain root or in a sub folder). */
function base_path(): string {
    static $b = null;
    if ($b !== null) return $b;
    $root = str_replace('\\', '/', realpath(__DIR__ . '/..'));
    $doc  = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '');
    $b = ($doc !== '' && strpos($root, $doc) === 0) ? substr($root, strlen($doc)) : '';
    return rtrim($b, '/');
}
function url(string $path = ''): string { return base_path() . '/' . ltrim($path, '/'); }
function img(?string $p): string {
    $p = (string)$p;
    if ($p === '') return url('assets/brand/emblem-512.png');
    return preg_match('#^https?://#', $p) ? $p : url($p);
}
function redirect(string $to): void { header('Location: ' . $to); exit; }

// ---------- DB helpers
function q(string $sql, array $params = []): PDOStatement { $s = db()->prepare($sql); $s->execute($params); return $s; }
function rows(string $sql, array $params = []): array { return q($sql, $params)->fetchAll(); }
function row(string $sql, array $params = []): ?array { $r = q($sql, $params)->fetch(); return $r ?: null; }
function val(string $sql, array $params = []) { return q($sql, $params)->fetchColumn(); }

// ---------- Settings
function settings_all(bool $reload = false): array {
    static $cache = null;
    if ($cache === null || $reload) {
        $cache = [];
        foreach (rows('SELECT skey, svalue FROM jm_settings') as $r) $cache[$r['skey']] = $r['svalue'];
    }
    return $cache;
}
function get_setting_raw(string $k) { return settings_all()[$k] ?? null; }
function setting(string $k, string $fallback = ''): string {
    $all = settings_all();
    if (array_key_exists($k, $all) && $all[$k] !== null) return (string)$all[$k];
    $schema = settings_schema();
    return isset($schema[$k]) ? (string)$schema[$k][3] : $fallback;
}
function set_setting(string $k, string $v): void {
    q('DELETE FROM jm_settings WHERE skey = ?', [$k]);
    q('INSERT INTO jm_settings (skey, svalue) VALUES (?, ?)', [$k, $v]);
    settings_all(true);
}
function lines(string $t): array {
    return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $t)), 'strlen'));
}

// ---------- Formatting
function money($n): string {
    $n = (int)round((float)$n);
    $s = (string)abs($n);
    if (strlen($s) > 3) {
        $last = substr($s, -3); $rest = substr($s, 0, -3);
        $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
        $s = $rest . ',' . $last;
    }
    return '₹' . ($n < 0 ? '-' : '') . $s;
}
function slugify(string $t): string {
    $t = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $t), '-'));
    return $t !== '' ? $t : 'item-' . bin2hex(random_bytes(3));
}
function tel(string $p): string { return 'tel:' . preg_replace('/[^0-9+]/', '', $p); }
function wa_link(string $msg = ''): string {
    return 'https://wa.me/' . preg_replace('/\D/', '', setting('whatsapp')) . ($msg !== '' ? '?text=' . rawurlencode($msg) : '');
}
function fmt_date(?string $d): string { return $d ? date('d M Y', strtotime($d)) : ''; }

// ---------- CSRF & flash
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(24));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">'; }
function csrf_ok(): bool {
    $t = $_POST['_csrf'] ?? '';
    return is_string($t) && $t !== '' && hash_equals($_SESSION['csrf'] ?? '', $t);
}
function flash(string $type, string $msg): void { $_SESSION['flash'][] = [$type, $msg]; }
function flashes(): array { $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f; }

// ---------- Uploads
function upload_dir(): string {
    $d = __DIR__ . '/../assets/uploads';
    if (!is_dir($d)) @mkdir($d, 0755, true);
    return $d;
}
/** Saves an uploaded image from $_FILES[$field]. Returns relative path, '' if none sent, or null on error (message in $err). */
function save_upload(string $field, ?string &$err = null, int $maxW = 2000): ?string {
    $err = null;
    if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? 4) === UPLOAD_ERR_NO_FILE) return '';
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) { $err = 'Upload failed (code ' . $f['error'] . ').'; return null; }
    if ($f['size'] > 8 * 1024 * 1024) { $err = 'Image is larger than 8 MB.'; return null; }
    $info = @getimagesize($f['tmp_name']);
    $map = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
    if (!$info || !isset($map[$info[2]])) { $err = 'Only JPG, PNG, WEBP or GIF images are allowed.'; return null; }
    $ext = $map[$info[2]];
    $name = date('Ymd') . '-' . bin2hex(random_bytes(5)) . '.' . $ext;
    $dest = upload_dir() . '/' . $name;
    $done = false;
    // Downscale big photos when GD is available
    if (function_exists('imagecreatefromjpeg') && $info[0] > $maxW && in_array($ext, ['jpg', 'png', 'webp'], true)) {
        $src = $ext === 'jpg' ? @imagecreatefromjpeg($f['tmp_name']) : ($ext === 'png' ? @imagecreatefrompng($f['tmp_name']) : @imagecreatefromwebp($f['tmp_name']));
        if ($src) {
            $nw = $maxW; $nh = (int)round($info[1] * $maxW / $info[0]);
            $dst = imagecreatetruecolor($nw, $nh);
            if ($ext !== 'jpg') { imagealphablending($dst, false); imagesavealpha($dst, true); }
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $info[0], $info[1]);
            $done = $ext === 'jpg' ? imagejpeg($dst, $dest, 86) : ($ext === 'png' ? imagepng($dst, $dest, 6) : imagewebp($dst, $dest, 86));
        }
    }
    if (!$done && !move_uploaded_file($f['tmp_name'], $dest)) { $err = 'Could not save the file. Check folder permissions on assets/uploads.'; return null; }
    return 'assets/uploads/' . $name;
}

// ---------- Admin auth
function admin_logged_in(): bool { return !empty($_SESSION['admin_id']); }
function require_admin(): void { if (!admin_logged_in()) redirect(url('admin/login.php')); }

// ---------- Bookings
function booking_code(string $type): string {
    $p = ['stay' => 'STY', 'dine' => 'DIN', 'safari' => 'SAF', 'event' => 'EVT', 'enquiry' => 'ENQ'][$type] ?? 'JM';
    return 'JM-' . $p . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
}
function booking_statuses(): array { return ['New', 'Contacted', 'Confirmed', 'Completed', 'Cancelled']; }
function booking_types(): array { return ['stay' => 'Stay', 'dine' => 'Table', 'safari' => 'Safari', 'event' => 'Party / Event', 'enquiry' => 'Enquiry']; }

function page_url(): string { return strtok($_SERVER['REQUEST_URI'] ?? '', '?'); }

/** Dark banner at the top of inner pages. */
function page_head(string $eyebrow, string $title, string $sub, string $image): void {
    echo '<section class="pagehead"><img src="' . e(img($image)) . '" alt="" fetchpriority="high"><div class="wrap">'
       . '<nav class="crumbs" aria-label="Breadcrumb"><a href="' . e(url('index.php')) . '">Home</a> / ' . e($eyebrow) . '</nav>'
       . '<span class="eyebrow">' . e($eyebrow) . '</span><h1 style="font-size:clamp(2.4rem,5.5vw,4.2rem)">' . e($title) . '</h1>'
       . ($sub !== '' ? '<p>' . e($sub) . '</p>' : '') . '</div></section>';
}
/** Amenity text -> icon */
function amenity_icon(string $t): string {
    $t = strtolower($t);
    foreach (['wi-fi' => 'wifi', 'wifi' => 'wifi', 'air' => 'wind', 'hot water' => 'droplets', 'tea' => 'coffee', 'coffee' => 'coffee', 'terrace' => 'sunset', 'view' => 'mountain', 'window' => 'mountain', 'pool' => 'waves', 'desk' => 'list', 'fridge' => 'droplets', 'housekeeping' => 'sparkles', 'seating' => 'bed', 'lounge' => 'bed', 'parking' => 'car-park'] as $k => $i)
        if (strpos($t, $k) !== false) return $i;
    return 'check';
}

// =====================================================================
// Editable page text & images.
//   t('home.hero.title', 'Default text')      -> plain text (escape with e() or use et())
//   et('home.hero.title', 'Default text')     -> HTML-escaped text
//   im('home.about.image', 'assets/images/x.jpg')  -> image path
//   imgu('home.about.image', 'assets/images/x.jpg') -> escaped image URL
// Admin > "Page text & images" finds every call below by scanning the PHP files, so
// anything written with these helpers is automatically editable. Empty value = default.
// =====================================================================
function t(string $key, string $default = ''): string {
    $all = settings_all();
    return (isset($all['txt.' . $key]) && $all['txt.' . $key] !== '') ? (string)$all['txt.' . $key] : $default;
}
function et(string $key, string $default = ''): string { return e(t($key, $default)); }
function im(string $key, string $default = ''): string {
    $all = settings_all();
    return (isset($all['img.' . $key]) && $all['img.' . $key] !== '') ? (string)$all['img.' . $key] : $default;
}
function imgu(string $key, string $default = ''): string { return e(img(im($key, $default))); }

/** Scan templates for t()/et()/im()/imgu() calls. Returns [ ['kind'=>'txt|img','key'=>..,'default'=>..,'file'=>..], ... ] */
function content_registry(): array {
    $root = realpath(__DIR__ . '/..');
    $files = array_merge(glob($root . '/*.php'), glob($root . '/includes/*.php'));
    $seen = []; $out = [];
    foreach ($files as $f) {
        if (basename($f) === 'functions.php') continue;
        $src = file_get_contents($f);
        if (!preg_match_all("/\\b(e?t|im|imgu)\\(\\s*'([a-z0-9_.]+)'\\s*,\\s*'((?:[^'\\\\]|\\\\.)*)'/s", $src, $m, PREG_SET_ORDER)) continue;
        foreach ($m as $x) {
            $kind = $x[1] === 'im' || $x[1] === 'imgu' ? 'img' : 'txt';
            $id = $kind . '.' . $x[2];
            if (isset($seen[$id])) continue;
            $seen[$id] = true;
            $out[] = ['kind' => $kind, 'key' => $x[2], 'default' => stripcslashes(str_replace(["\\'"], ["'"], $x[3])), 'file' => basename($f)];
        }
    }
    return $out;
}


// ---------- Maps & routes (coordinates come from Admin)
function has_coords(?string $lat, ?string $lng): bool {
    return is_numeric($lat) && is_numeric($lng) && abs((float)$lat) <= 90 && abs((float)$lng) <= 180 && ((float)$lat != 0 || (float)$lng != 0);
}
function hotel_point(): string {   // "lat,lng" or the search text
    return has_coords(setting('map_lat'), setting('map_lng')) ? trim(setting('map_lat')) . ',' . trim(setting('map_lng')) : setting('map_query');
}
function map_embed_src(): string { return 'https://www.google.com/maps?q=' . rawurlencode(hotel_point()) . '&z=14&output=embed'; }
function maps_open_url(): string { return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode(hotel_point()); }
/** Driving directions from a route point to the hotel. */
function route_link(array $r): string {
    $o = has_coords($r['lat'] ?? '', $r['lng'] ?? '') ? trim($r['lat']) . ',' . trim($r['lng']) : $r['name'];
    return 'https://www.google.com/maps/dir/?api=1&travelmode=driving&origin=' . rawurlencode($o) . '&destination=' . rawurlencode(hotel_point());
}
function route_kind_icon(string $k): string { return ['airport' => 'plane', 'rail' => 'train', 'city' => 'building', 'bus' => 'car'][$k] ?? 'map-pin'; }
function route_kind_plural(string $k): string { return ['airport' => 'Airports', 'rail' => 'Railway stations', 'city' => 'Cities', 'bus' => 'Bus stands'][$k] ?? 'Other places'; }
function route_kind_label(string $k): string { return ['airport' => 'Airport', 'rail' => 'Railway station', 'city' => 'City', 'bus' => 'Bus stand'][$k] ?? 'Place'; }


// ---------- WhatsApp booking messages & GST
function wa_digits(string $phone): string {
    $d = preg_replace('/\D/', '', $phone);
    return strlen($d) === 10 ? '91' . $d : ltrim($d, '0');
}
/** Plain-text summary of a booking row, ready for WhatsApp (uses *bold*). */
function booking_wa_message(array $b, bool $toGuest = false): string {
    $types = booking_types();
    $L = [];
    if ($toGuest) {
        $L[] = 'Hello ' . $b['guest_name'] . ', thank you for choosing ' . setting('site_name') . '.';
        $L[] = 'This is about your request *' . $b['code'] . '*:';
    } else {
        $L[] = 'Hello ' . setting('site_name') . ', I would like to send this request:';
    }
    $L[] = '*' . ($types[$b['type']] ?? $b['type']) . '* — ' . $b['item_name'];
    if ($b['date_from']) {
        $L[] = ($b['type'] === 'stay' ? 'Check-in: ' : 'Date: ') . date('D, d M Y', strtotime($b['date_from']));
        if ($b['date_to']) $L[] = 'Check-out: ' . date('D, d M Y', strtotime($b['date_to'])) . ' (' . (int)$b['nights'] . ' night' . ($b['nights'] > 1 ? 's' : '') . ')';
    }
    if ($b['time_slot']) $L[] = 'Time: ' . $b['time_slot'];
    $L[] = 'Guests: ' . (int)$b['guests'];
    if ((int)$b['amount'] > 0) $L[] = 'Estimate: ' . money($b['amount'] + $b['tax']) . ($b['tax'] ? ' (incl. GST)' : '');
    if (!$toGuest) {
        $L[] = 'Name: ' . $b['guest_name'];
        $L[] = 'Phone: ' . $b['phone'];
        if (!empty($b['email'])) $L[] = 'Email: ' . $b['email'];
    }
    if (!empty($b['notes'])) $L[] = 'Notes: ' . $b['notes'];
    if (!$toGuest) $L[] = 'Reference: ' . $b['code'];
    return implode("\n", $L);
}
function wa_to(string $phone, string $msg): string { return 'https://wa.me/' . wa_digits($phone) . '?text=' . rawurlencode($msg); }
/** GST on room tariff: one rate up to the threshold, another above it (set in Admin). */
function gst_rate($nightly): float {
    return (float)($nightly <= (float)setting('tax_threshold', '7500') ? setting('tax_percent', '5') : setting('tax_percent_high', '18'));
}
function notify_booking(array $b): void {
    $to = trim(setting('notify_email'));
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) return;
    $from = 'noreply@' . preg_replace('#^https?://(www\.)?#', '', rtrim(setting('site_url'), '/'));
    @mail($to, 'New ' . (booking_types()[$b['type']] ?? 'booking') . ' request ' . $b['code'] . ' — ' . $b['guest_name'],
        booking_wa_message($b) . "\n\nManage: " . rtrim(setting('site_url'), '/') . '/admin/bookings.php?q=' . $b['code'],
        "From: " . setting('site_name') . " <$from>\r\nContent-Type: text/plain; charset=UTF-8");
}


// ---------- Fonts (choose the pairing in Admin > Contact, logo & rules)
function font_presets(): array {
    return [
        'playfair-inter' => ['label' => 'Playfair Display + Inter (classic hotel, recommended)', 'serif' => "'Playfair Display',Georgia,'Times New Roman',serif", 'sans' => "'Inter',system-ui,-apple-system,'Segoe UI',sans-serif",
            'url' => 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap'],
        'baskerville-source' => ['label' => 'Libre Baskerville + Source Sans 3 (editorial)', 'serif' => "'Libre Baskerville',Georgia,serif", 'sans' => "'Source Sans 3',system-ui,sans-serif",
            'url' => 'https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Source+Sans+3:wght@400;500;600;700&display=swap'],
        'dmserif-dmsans' => ['label' => 'DM Serif Display + DM Sans (modern boutique)', 'serif' => "'DM Serif Display',Georgia,serif", 'sans' => "'DM Sans',system-ui,sans-serif",
            'url' => 'https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap'],
        'cormorant-jost' => ['label' => 'Cormorant Garamond + Jost (light & elegant)', 'serif' => "'Cormorant Garamond',Georgia,serif", 'sans' => "'Jost',system-ui,sans-serif",
            'url' => 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Jost:wght@400;500;600&display=swap'],
    ];
}
function font_head(): string {
    $p = font_presets(); $k = setting('font_pair', 'playfair-inter'); $f = $p[$k] ?? $p['playfair-inter'];
    return '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="' . e($f['url']) . '" rel="stylesheet">'
         . '<style>:root{--serif:' . $f['serif'] . ';--sans:' . $f['sans'] . '}</style>';
}


// ---------- Site mode: live / coming soon / maintenance (+ secret preview link)
/** A site-mode setting: what Admin saved, else the server default from config.local.php (DEFAULT_SITE_MODE / DEFAULT_LAUNCH_AT). */
function mode_cfg(string $key, string $fallback = ''): string {
    $v = get_setting_raw($key);
    if ($v !== null) return (string)$v;
    $const = ['site_mode' => 'DEFAULT_SITE_MODE', 'launch_at' => 'DEFAULT_LAUNCH_AT', 'maint_until' => 'DEFAULT_MAINT_UNTIL'][$key] ?? '';
    return ($const !== '' && defined($const)) ? (string)constant($const) : $fallback;
}
function site_mode(): string {
    $m = mode_cfg('site_mode', 'live');
    if (!in_array($m, ['coming_soon', 'maintenance'], true)) return 'live';
    if ($m === 'coming_soon' && get_setting_raw('auto_launch') !== '0') {
        $t = launch_ts(); if ($t && $t <= time()) return 'live';   // launch time passed -> go live automatically
    }
    return $m;
}
/** Launch time (stored as local India time) as a unix timestamp, or 0. */
function launch_ts(string $key = 'launch_at'): int {
    $v = trim(mode_cfg($key)); if ($v === '') return 0;
    $d = date_create($v, new DateTimeZone('Asia/Kolkata')); return $d ? $d->getTimestamp() : 0;
}
function preview_key(): string {
    $k = (string)get_setting_raw('preview_key');
    if ($k === '') { $k = bin2hex(random_bytes(8)); set_setting('preview_key', $k); }
    return $k;
}
function preview_token(string $key): string { return hash_hmac('sha256', $key, 'jm-preview|' . DB_NAME . '|' . __DIR__); }
function preview_url(): string { return rtrim(setting('site_url'), '/') . '/?preview=' . preview_key(); }
function jm_gate(): void {
    if (PHP_SAPI === 'cli') return;
    if (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/admin/') !== false) return;
    $mode = site_mode(); if ($mode === 'live') return;
    if (admin_logged_in()) { $GLOBALS['jm_preview'] = $mode; return; }
    $key = preview_key(); $tok = preview_token($key);
    $cookie = ['expires' => time() + 86400 * 30, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS'])];
    if (isset($_GET['exit_preview'])) { setcookie('jm_preview', '', ['expires' => 1] + $cookie); redirect(strtok($_SERVER['REQUEST_URI'], '?')); }
    if (isset($_GET['preview']) && hash_equals($key, (string)$_GET['preview'])) {
        setcookie('jm_preview', $tok, $cookie);
        $q = $_GET; unset($q['preview']); redirect(strtok($_SERVER['REQUEST_URI'], '?') . ($q ? '?' . http_build_query($q) : ''));
    }
    if (!empty($_COOKIE['jm_preview']) && hash_equals($tok, (string)$_COOKIE['jm_preview'])) { $GLOBALS['jm_preview'] = $mode; return; }
    $GLOBALS['jm_gate_mode'] = $mode;
    include __DIR__ . '/gate_page.php';
    exit;
}
require_once __DIR__ . '/geo.php';
