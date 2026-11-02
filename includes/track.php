<?php
/**
 * Privacy-friendly visit statistics (shown in /pk-insights).
 * No cookies, no IP address stored: a visitor is a one-way hash of IP + browser + the day, so it cannot be traced back
 * and changes every day. Humans are counted by a tiny script (assets/js/site.js -> hit.php); search-engine crawlers are
 * logged on the server when they load a page, which shows you when Google and Bing visit.
 */
function track_ua(): string { return (string)($_SERVER['HTTP_USER_AGENT'] ?? ''); }
function track_ip(): string { return (string)($_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '0'); }
function track_sid(): string {
    $salt = (string)get_setting_raw('track_salt');
    if ($salt === '') { $salt = bin2hex(random_bytes(12)); set_setting('track_salt', $salt); }
    return substr(hash('sha256', track_ip() . '|' . track_ua() . '|' . date('Y-m-d') . '|' . $salt), 0, 16);
}
/** Known search / AI crawlers -> short name, or '' for anything else. */
function track_crawler_name(string $ua): string {
    static $map = ['Googlebot' => 'Googlebot', 'Google-InspectionTool' => 'Googlebot', 'GoogleOther' => 'Googlebot', 'AdsBot-Google' => 'Googlebot', 'bingbot' => 'Bingbot', 'BingPreview' => 'Bingbot', 'DuckDuckBot' => 'DuckDuckBot',
        'YandexBot' => 'Yandex', 'Baiduspider' => 'Baidu', 'Applebot' => 'Applebot', 'GPTBot' => 'GPTBot', 'ChatGPT-User' => 'ChatGPT', 'OAI-SearchBot' => 'ChatGPT', 'PerplexityBot' => 'Perplexity', 'ClaudeBot' => 'ClaudeBot', 'facebookexternalhit' => 'Facebook preview',
        'WhatsApp' => 'WhatsApp preview', 'Twitterbot' => 'X preview', 'LinkedInBot' => 'LinkedIn preview', 'AhrefsBot' => 'AhrefsBot', 'SemrushBot' => 'SemrushBot', 'bot' => 'Other bot', 'spider' => 'Other bot', 'crawl' => 'Other bot'];
    if ($ua === '') return '';
    foreach ($map as $k => $v) if (stripos($ua, $k) !== false) return $v;
    return '';
}
function track_parse_ua(string $ua, int $sw = 0): array {
    $dev = preg_match('/iPad|Tablet|PlayBook|Silk/i', $ua) || (preg_match('/Android/i', $ua) && !preg_match('/Mobile/i', $ua)) ? 'tablet' : (preg_match('/Mobi|iPhone|iPod|Android/i', $ua) ? 'mobile' : 'desktop');
    $br = 'Other';
    foreach (['Instagram' => 'Instagram app', 'FBAN' => 'Facebook app', 'FBAV' => 'Facebook app', 'EdgA' => 'Edge', 'Edg/' => 'Edge', 'OPR/' => 'Opera', 'SamsungBrowser' => 'Samsung Internet', 'CriOS' => 'Chrome', 'FxiOS' => 'Firefox', 'Firefox' => 'Firefox', 'Chrome' => 'Chrome', 'Safari' => 'Safari'] as $k => $v) if (stripos($ua, $k) !== false) { $br = $v; break; }
    $os = 'Other';
    foreach (['Android' => 'Android', 'iPhone' => 'iOS', 'iPad' => 'iOS', 'iPod' => 'iOS', 'Windows' => 'Windows', 'Mac OS X' => 'macOS', 'CrOS' => 'ChromeOS', 'Linux' => 'Linux'] as $k => $v) if (stripos($ua, $k) !== false) { $os = $v; break; }
    return [$dev, $br, $os];
}
/** Referrer host (or utm_source) -> traffic source label. */
function track_source(string $refHost, string $utm = ''): string {
    $x = strtolower($utm !== '' ? $utm : $refHost);
    if ($x === '') return 'Direct';
    $map = ['google' => 'Google', 'bing' => 'Bing', 'duckduckgo' => 'DuckDuckGo', 'yahoo' => 'Yahoo', 'ecosia' => 'Ecosia', 'yandex' => 'Yandex', 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'fb.' => 'Facebook', 'whatsapp' => 'WhatsApp', 'wa.me' => 'WhatsApp',
        'youtube' => 'YouTube', 'youtu.be' => 'YouTube', 't.co' => 'X (Twitter)', 'twitter' => 'X (Twitter)', 'linkedin' => 'LinkedIn', 'pinterest' => 'Pinterest', 'chatgpt' => 'ChatGPT', 'openai' => 'ChatGPT', 'perplexity' => 'Perplexity', 'gemini' => 'Gemini',
        'tripadvisor' => 'Tripadvisor', 'makemytrip' => 'MakeMyTrip', 'goibibo' => 'Goibibo', 'booking.com' => 'Booking.com', 'mail.' => 'Email', 'gmail' => 'Email', 'outlook' => 'Email', 'newsletter' => 'Email'];
    foreach ($map as $k => $v) if (strpos($x, $k) !== false) return $v;
    return $utm !== '' ? ucfirst(substr(preg_replace('/[^a-z0-9 _.-]/', '', $x), 0, 24)) : 'Other sites';
}
function track_clean_path(string $p): string {
    $q = '';
    if (($i = strpos($p, '?')) !== false) { parse_str(substr($p, $i + 1), $qs); $p = substr($p, 0, $i); foreach (['slug', 'type'] as $k) if (!empty($qs[$k]) && is_string($qs[$k]) && preg_match('/^[a-z0-9-]{1,60}$/i', $qs[$k])) $q = '?' . $k . '=' . $qs[$k]; }
    $p = '/' . ltrim(preg_replace('/[^A-Za-z0-9\/_.\-%]/', '', $p), '/');
    $p = preg_replace('#\.php$#', '', $p); if ($p === '/index') $p = '/';
    return substr(rtrim($p, '/') === '' ? '/' : rtrim($p, '/'), 0, 150) . $q;
}
function track_insert(array $r): void {
    try { q('INSERT INTO jm_hits (ts,d,h,sid,sn,ev,ent,path,ref,src,dev,br,os,cc,lg,sw) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
        [time(), date('Y-m-d'), (int)date('G'), $r['sid'] ?? '', $r['sn'] ?? '', $r['ev'] ?? 'pv', (int)($r['ent'] ?? 0), $r['path'] ?? '/', $r['ref'] ?? '', $r['src'] ?? '', $r['dev'] ?? '', $r['br'] ?? '', $r['os'] ?? '', $r['cc'] ?? '', $r['lg'] ?? '', (int)($r['sw'] ?? 0)]);
        if (random_int(1, 400) === 1) q('DELETE FROM jm_hits WHERE ts < ?', [time() - 400 * 86400]);   // keep about 13 months
    } catch (Throwable $e) { /* statistics must never break the website */ }
}
/** Called while a public page renders: logs search-engine crawlers only (people are counted by hit.php). */
function jm_track_crawler(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') return;
    $ua = track_ua(); if ($ua === '' || !preg_match('/bot|spider|crawl|facebookexternalhit|WhatsApp|Google-Inspection|Applebot|ChatGPT|Preview/i', $ua)) return;
    $name = track_crawler_name($ua); if ($name === '') return;
    track_insert(['sid' => track_sid(), 'ev' => 'bot', 'path' => track_clean_path((string)($_SERVER['REQUEST_URI'] ?? '/')), 'br' => $name, 'dev' => 'bot']);
}
