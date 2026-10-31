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
function opening_soon(): bool { return setting('opening_soon', '1') === '1'; }

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
