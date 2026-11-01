<?php
/**
 * Input validation helpers shared by the public booking forms and every admin form.
 * Each v_* function returns an error message (string) or null when the value is fine.
 */

function v_len(string $s, int $min, int $max, string $label): ?string {
    $n = mb_strlen($s);
    if ($n < $min) return $min === 1 ? "$label is required." : "$label must be at least $min characters.";
    if ($n > $max) return "$label must be $max characters or fewer.";
    return null;
}
/** Strip control characters and collapse odd whitespace. */
function v_clean(string $s): string {
    $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $s) ?? '';
    return trim($s);
}
function v_name(string $s, string $label = 'Name'): ?string {
    if ($e = v_len($s, 2, 80, $label)) return $e;
    if (!preg_match("/^[\\p{L}\\p{M}][\\p{L}\\p{M} .'\\-]*$/u", $s)) return "$label can only contain letters, spaces and . ' -";
    return null;
}
/** Phone: any country. Indian 10-digit mobiles (optionally with +91 / 91 / 0) and international numbers (with + or 00, or with the country code typed first). Returns [normalised, error]. */
function v_phone(string $s): array {
    $s = trim($s);
    if ($s === '') return ['', 'Phone number is required.'];
    if (strlen($s) > 25 || !preg_match('/^\+?[0-9][0-9 ()\-]*$/', $s)) return ['', 'Phone number can only contain digits, spaces, + - ( ).'];
    $d = preg_replace('/\D/', '', $s);
    if (strlen($d) < 8) return ['', 'That phone number is too short.'];
    if (strlen($d) > 15) return ['', 'That phone number is too long (maximum 15 digits).'];
    $intl = $s[0] === '+' || strpos($d, '00') === 0;
    if (strpos($d, '00') === 0) $d = substr($d, 2);
    if ($intl || strlen($d) > 10) {
        if (strpos($d, '91') === 0 && ($intl || strlen($d) === 12)) {
            if (!preg_match('/^91[6-9]\d{9}$/', $d)) return ['', 'Indian numbers need 10 digits after +91, starting with 6, 7, 8 or 9.'];
            return ['+91 ' . substr($d, 2, 5) . ' ' . substr($d, 7), null];
        }
        if (!$intl && strlen($d) === 11 && $d[0] === '0' && preg_match('/^[6-9]/', substr($d, 1))) return ['+91 ' . substr($d, 1, 5) . ' ' . substr($d, 6), null];
        return ['+' . $d, null];
    }
    if (preg_match('/^[6-9]\d{9}$/', $d)) return ['+91 ' . substr($d, 0, 5) . ' ' . substr($d, 5), null];
    return [$s, null];   // another country's number typed without a + sign: keep exactly as entered
}
function v_email(string $s, bool $required = false, string $label = 'Email address'): ?string {
    $s = trim($s);
    if ($s === '') return $required ? "$label is required." : null;
    if (mb_strlen($s) > 120) return "$label must be 120 characters or fewer.";
    if (!filter_var($s, FILTER_VALIDATE_EMAIL) || !preg_match('/@[^@]+\.[A-Za-z]{2,}$/', $s)) return "$label does not look right.";
    return null;
}
/** Comma or line separated list of email addresses. */
function v_email_list(string $s, string $label): ?string {
    $s = trim($s); if ($s === '') return null;
    foreach (preg_split('/[\s,;]+/', $s, -1, PREG_SPLIT_NO_EMPTY) as $m) if ($e = v_email($m, true, $label)) return $e;
    return null;
}
function v_url(string $s, bool $required = false, string $label = 'Web address'): ?string {
    $s = trim($s);
    if ($s === '') return $required ? "$label is required." : null;
    if (mb_strlen($s) > 500) return "$label must be 500 characters or fewer.";
    if (!preg_match('#^https?://#i', $s) || !filter_var($s, FILTER_VALIDATE_URL)) return "$label must be a full link starting with http:// or https://";
    return null;
}
/** Image reference: empty, a site path (assets/...) or an https link. */
function v_media(string $s, string $label = 'Image'): ?string {
    $s = trim($s);
    if ($s === '') return null;
    if (mb_strlen($s) > 500) return "$label link is too long.";
    if (preg_match('#^https?://#i', $s)) return filter_var($s, FILTER_VALIDATE_URL) ? null : "$label link is not a valid web address.";
    if (strpos($s, '..') !== false || !preg_match('#^[A-Za-z0-9_./\\-]+$#', $s)) return "$label must be an uploaded photo, a path like assets/uploads/photo.jpg, or a full https link.";
    return null;
}
function v_host(string $s, string $label = 'Host'): ?string {
    $s = trim($s);
    if ($s === '') return "$label is required.";
    if (!preg_match('/^(?=.{1,253}$)([A-Za-z0-9]([A-Za-z0-9\-]{0,61}[A-Za-z0-9])?)(\.[A-Za-z0-9]([A-Za-z0-9\-]{0,61}[A-Za-z0-9])?)*$/', $s)) return "$label must be a server name like smtp.example.com";
    return null;
}
function v_int($s, int $min, int $max, string $label): ?string {
    $s = trim((string)$s);
    if (!preg_match('/^-?\d+$/', $s)) return "$label must be a whole number.";
    if ((int)$s < $min || (int)$s > $max) return "$label must be between $min and $max.";
    return null;
}
function v_coord(string $s, bool $lat, string $label): ?string {
    $s = trim($s); if ($s === '') return null;
    if (!preg_match('/^-?\d{1,3}(\.\d+)?$/', $s)) return "$label must be a decimal number like 25.006222";
    $lim = $lat ? 90 : 180;
    if (abs((float)$s) > $lim) return "$label must be between -$lim and $lim.";
    return null;
}
function v_date(string $s, string $label, ?string $min = null, ?string $max = null): ?string {
    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $s, $m) || !checkdate((int)$m[2], (int)$m[3], (int)$m[1])) return "Please choose a valid $label.";
    if ($min !== null && $s < $min) return ucfirst($label) . ' cannot be before ' . date('j M Y', strtotime($min)) . '.';
    if ($max !== null && $s > $max) return ucfirst($label) . ' cannot be after ' . date('j M Y', strtotime($max)) . '.';
    return null;
}
/** Free text for notes/messages: length limit and a basic spam check (too many links). */
function v_note(string $s, int $max, string $label = 'Message'): ?string {
    if (mb_strlen($s) > $max) return "$label must be $max characters or fewer.";
    if (preg_match_all('#https?://|www\.#i', $s) > 2) return "$label has too many links.";
    return null;
}
/** Maximum length of a text column (VARCHAR) read from the live database; null when unlimited/unknown. */
function db_col_max(string $table, string $col): ?int {
    static $cache = [];
    if (!isset($cache[$table])) {
        $cache[$table] = [];
        try {
            if (db()->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
                $sql = (string)val("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
                if (preg_match_all('/(\w+)\s+VARCHAR\((\d+)\)/i', $sql, $m, PREG_SET_ORDER)) foreach ($m as $x) $cache[$table][strtolower($x[1])] = (int)$x[2];
            } else {
                foreach (rows("SHOW COLUMNS FROM `$table`") as $c) if (preg_match('/^varchar\((\d+)\)/i', $c['Type'], $x)) $cache[$table][strtolower($c['Field'])] = (int)$x[1];
            }
        } catch (Throwable $e) {}
    }
    return $cache[$table][strtolower($col)] ?? null;
}
