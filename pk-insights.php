<?php
/** Private insights dashboard: live visitors, traffic, sources, devices, funnel, bookings. Password = INSIGHTS_PASS in config.local.php. */
require_once __DIR__ . '/includes/db.php';
header('X-Robots-Tag: noindex, nofollow'); header('Cache-Control: no-store, private');
$self = url('pk-insights');
$pass = defined('INSIGHTS_PASS') ? trim((string)INSIGHTS_PASS) : '';
$ok = !empty($_SESSION['pk_ins']) && (int)$_SESSION['pk_ins'] > time() - 12 * 3600;
$err = '';

if (($_POST['action'] ?? '') === 'logout' && csrf_ok()) { unset($_SESSION['pk_ins']); redirect($self); }
if (!$ok && ($_POST['action'] ?? '') === 'login') {
    $tag = 'ins:' . substr(track_ip(), 0, 60);
    q('DELETE FROM jm_attempts WHERE created_at < ?', [time() - 900]);
    $fails = (int)val('SELECT COUNT(*) FROM jm_attempts WHERE ip = ? AND created_at > ?', [$tag, time() - 600]);
    if (!csrf_ok()) $err = 'Session expired. Please try again.';
    elseif ($pass === '') $err = 'The insights password is not set on this server.';
    elseif ($fails >= 6) $err = 'Too many attempts. Please wait 10 minutes.';
    elseif (hash_equals($pass, trim((string)($_POST['password'] ?? '')))) { session_regenerate_id(true); $_SESSION['pk_ins'] = time(); q('DELETE FROM jm_attempts WHERE ip = ?', [$tag]); redirect($self); }
    else { q('INSERT INTO jm_attempts (ip, created_at) VALUES (?, ?)', [$tag, time()]); usleep(700000); $err = 'Wrong password.'; }
}
if (!$ok) {
    if (isset($_GET['api'])) { http_response_code(401); header('Content-Type: application/json'); exit('{"error":"auth"}'); }
    ?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow">
<title>Insights</title><link rel="icon" href="<?= url('assets/brand/favicon.ico') ?>"><?= font_head() ?><link rel="stylesheet" href="<?= asset('assets/css/pk.css') ?>"></head>
<body class="login"><form class="pk-card" method="post" novalidate><?= csrf_field() ?><input type="hidden" name="action" value="login">
<img src="<?= e(img(setting('logo_dark'))) ?>" alt="<?= e(setting('site_name')) ?>"><h1>Insights</h1><p class="muted" style="margin:0">Enter the developer number to continue.</p>
<?php if ($err): ?><div class="alert alert--err" role="alert" style="margin-top:1rem"><?= e($err) ?></div><?php endif; ?>
<label for="p">Password</label><input id="p" name="password" type="password" inputmode="numeric" autocomplete="off" required autofocus><button class="btn btn--gold">Open insights</button></form></body></html><?php
    exit;
}

// ======================================================================= data
function ins_label(string $path, array $blog): string {
    if ($path === '/') return 'Home';
    $map = ['/rooms' => 'Rooms', '/restaurant' => 'Restaurant', '/safari' => 'Safari', '/jawai' => 'Explore Jawai', '/gallery' => 'Gallery', '/contact' => 'Contact', '/book' => 'Book / enquire', '/policies' => 'Policies', '/credits' => 'Image credits', '/blog' => 'Journal (index)'];
    if (isset($map[$path])) return $map[$path];
    if (strpos($path, '/room?slug=') === 0) return 'Room: ' . ucwords(str_replace('-', ' ', substr($path, 11)));
    if (strpos($path, '/book?type=') === 0) return 'Book: ' . substr($path, 11);
    if (strpos($path, '/blog/') === 0) { $s = substr($path, 6); return 'Journal: ' . ($blog[$s] ?? ucwords(str_replace('-', ' ', $s))); }
    return $path;
}
function ins_totals(int $s, int $e): array {
    $W = 'ts >= ? AND ts < ?'; $p = [$s, $e];
    $sd = date('Y-m-d H:i:s', $s); $ed = date('Y-m-d H:i:s', $e);
    $pv = (int)val("SELECT COUNT(*) FROM jm_hits WHERE ev = 'pv' AND $W", $p);
    $vis = (int)val("SELECT COUNT(*) FROM (SELECT DISTINCT d, sid FROM jm_hits WHERE ev = 'pv' AND $W) x", $p);
    $ses = (int)val("SELECT COUNT(*) FROM (SELECT DISTINCT sn FROM jm_hits WHERE ev = 'pv' AND sn <> '' AND $W) x", $p);
    $bnc = (int)val("SELECT COUNT(*) FROM (SELECT sn FROM jm_hits WHERE ev = 'pv' AND sn <> '' AND $W GROUP BY sn HAVING COUNT(*) = 1) x", $p);
    $ev = ['wa' => 0, 'call' => 0, 'mail' => 0]; foreach (rows("SELECT ev, COUNT(*) c FROM jm_hits WHERE ev IN ('wa','call','mail') AND $W GROUP BY ev", $p) as $r) $ev[$r['ev']] = (int)$r['c'];
    $req = (int)val('SELECT COUNT(*) FROM jm_bookings WHERE created_at >= ? AND created_at < ?', [$sd, $ed]);
    return ['visitors' => $vis, 'pageviews' => $pv, 'sessions' => $ses, 'bounce' => $bnc, 'wa' => $ev['wa'], 'call' => $ev['call'], 'mail' => $ev['mail'], 'requests' => $req];
}
function ins_data(int $days): array {
    $now = time(); $start = strtotime('today') - ($days - 1) * 86400; $end = $now + 1; $W = 'ts >= ? AND ts < ?'; $P = [$start, $end];
    $blog = []; try { foreach (rows('SELECT slug, title FROM jm_blog') as $r) $blog[$r['slug']] = $r['title']; } catch (Throwable $e) {}
    $cur = ins_totals($start, $end); $prev = ins_totals($start - $days * 86400, $start);
    // time series: hourly for "today", daily otherwise
    $series = [];
    if ($days === 1) { $m = []; foreach (rows("SELECT h, COUNT(*) pv, COUNT(DISTINCT sid) v FROM jm_hits WHERE ev = 'pv' AND $W GROUP BY h", $P) as $r) $m[(int)$r['h']] = $r; for ($h = 0; $h <= (int)date('G'); $h++) $series[] = ['l' => sprintf('%02d:00', $h), 'pv' => (int)($m[$h]['pv'] ?? 0), 'v' => (int)($m[$h]['v'] ?? 0)]; }
    else { $m = []; foreach (rows("SELECT d, COUNT(*) pv, COUNT(DISTINCT sid) v FROM jm_hits WHERE ev = 'pv' AND $W GROUP BY d", $P) as $r) $m[$r['d']] = $r; for ($i = 0; $i < $days; $i++) { $dd = date('Y-m-d', $start + $i * 86400); $series[] = ['l' => date('D d M', $start + $i * 86400), 'd' => $dd, 'pv' => (int)($m[$dd]['pv'] ?? 0), 'v' => (int)($m[$dd]['v'] ?? 0)]; } }
    $bk = []; foreach (rows('SELECT SUBSTR(created_at,1,10) d, COUNT(*) c FROM jm_bookings WHERE created_at >= ? AND created_at < ? GROUP BY SUBSTR(created_at,1,10)', [date('Y-m-d H:i:s', $start), date('Y-m-d H:i:s', $end)]) as $r) $bk[$r['d']] = (int)$r['c'];
    foreach ($series as &$s) { $s['b'] = $days === 1 ? null : ($bk[$s['d']] ?? 0); } unset($s);
    $hours = array_fill(0, 24, 0); foreach (rows("SELECT h, COUNT(*) c FROM jm_hits WHERE ev = 'pv' AND $W GROUP BY h", $P) as $r) $hours[(int)$r['h']] = (int)$r['c'];
    $wd = array_fill(1, 7, 0); if ($days > 1) foreach ($series as $s) $wd[(int)date('N', strtotime($s['d']))] += $s['v'];
    $top = fn($sql, $p = []) => rows($sql, $p);
    $pages = array_map(fn($r) => ['l' => ins_label($r['path'], $blog), 'path' => $r['path'], 'pv' => (int)$r['pv'], 'v' => (int)$r['v']], $top("SELECT path, COUNT(*) pv, COUNT(DISTINCT sid) v FROM jm_hits WHERE ev = 'pv' AND $W GROUP BY path ORDER BY pv DESC LIMIT 12", $P));
    $entry = array_map(fn($r) => ['l' => ins_label($r['path'], $blog), 'n' => (int)$r['c']], $top("SELECT path, COUNT(*) c FROM jm_hits WHERE ev = 'pv' AND ent = 1 AND $W GROUP BY path ORDER BY c DESC LIMIT 8", $P));
    $grp = fn($col, $lim = 8) => array_map(fn($r) => ['l' => (string)$r[$col] !== '' ? (string)$r[$col] : 'Unknown', 'n' => (int)$r['c']], $top("SELECT $col, COUNT(*) c FROM jm_hits WHERE ev = 'pv' AND ent = 1 AND $W GROUP BY $col ORDER BY c DESC LIMIT $lim", $P));
    $cn = ['IN' => 'India', 'US' => 'United States', 'GB' => 'United Kingdom', 'AE' => 'UAE', 'SG' => 'Singapore', 'AU' => 'Australia', 'CA' => 'Canada', 'DE' => 'Germany', 'FR' => 'France', 'NP' => 'Nepal', 'BD' => 'Bangladesh', 'LK' => 'Sri Lanka', 'NL' => 'Netherlands', 'IT' => 'Italy', 'ES' => 'Spain', 'JP' => 'Japan', 'RU' => 'Russia', 'CH' => 'Switzerland', 'SA' => 'Saudi Arabia', 'QA' => 'Qatar', 'KW' => 'Kuwait', 'OM' => 'Oman', 'ZA' => 'South Africa', 'NZ' => 'New Zealand', 'IE' => 'Ireland', 'MY' => 'Malaysia', 'TH' => 'Thailand', 'BR' => 'Brazil'];
    $countries = array_map(function ($r) use ($cn) { $c = (string)$r['cc']; return ['l' => $c === '' ? 'Unknown' : ($cn[$c] ?? $c), 'n' => (int)$r['c']]; }, $top("SELECT cc, COUNT(*) c FROM jm_hits WHERE ev = 'pv' AND ent = 1 AND $W GROUP BY cc ORDER BY c DESC LIMIT 8", $P));
    $refs = array_map(fn($r) => ['l' => $r['ref'], 'n' => (int)$r['c']], $top("SELECT ref, COUNT(*) c FROM jm_hits WHERE ev = 'pv' AND ent = 1 AND ref <> '' AND $W GROUP BY ref ORDER BY c DESC LIMIT 8", $P));
    $ses = (int)$cur['sessions'];
    $stay = (int)val("SELECT COUNT(DISTINCT sn) FROM jm_hits WHERE ev = 'pv' AND sn <> '' AND $W AND (path LIKE '/rooms%' OR path LIKE '/room?%' OR path LIKE '/safari%' OR path LIKE '/restaurant%' OR path LIKE '/jawai%')", $P);
    $form = (int)val("SELECT COUNT(DISTINCT sn) FROM jm_hits WHERE ev = 'pv' AND sn <> '' AND $W AND path LIKE '/book%'", $P);
    $types = booking_types(); $byType = []; foreach (rows('SELECT type, COUNT(*) c FROM jm_bookings WHERE created_at >= ? AND created_at < ? GROUP BY type ORDER BY c DESC', [date('Y-m-d H:i:s', $start), date('Y-m-d H:i:s', $end)]) as $r) $byType[] = ['l' => $types[$r['type']] ?? $r['type'], 'n' => (int)$r['c']];
    $byStatus = []; foreach (rows('SELECT status, COUNT(*) c FROM jm_bookings WHERE created_at >= ? AND created_at < ? GROUP BY status', [date('Y-m-d H:i:s', $start), date('Y-m-d H:i:s', $end)]) as $r) $byStatus[$r['status']] = (int)$r['c'];
    $latest = array_map(fn($r) => ['code' => $r['code'], 'name' => $r['guest_name'], 'type' => $types[$r['type']] ?? $r['type'], 'item' => $r['item_name'], 'status' => $r['status'], 'at' => $r['created_at']], rows('SELECT code, guest_name, type, item_name, status, created_at FROM jm_bookings ORDER BY id DESC LIMIT 6'));
    $blogTop = array_map(fn($r) => ['l' => ins_label($r['path'], $blog), 'n' => (int)$r['c']], $top("SELECT path, COUNT(*) c FROM jm_hits WHERE ev = 'pv' AND path LIKE '/blog/%' AND $W GROUP BY path ORDER BY c DESC LIMIT 8", $P));
    $bots = array_map(fn($r) => ['l' => $r['br'], 'n' => (int)$r['c'], 'last' => date('d M, g:i a', (int)$r['last'])], $top("SELECT br, COUNT(*) c, MAX(ts) last FROM jm_hits WHERE ev = 'bot' AND $W GROUP BY br ORDER BY c DESC LIMIT 10", $P));
    $botFeed = array_map(fn($r) => ['bot' => $r['br'], 'l' => ins_label($r['path'], $blog), 'at' => date('d M, g:i a', (int)$r['ts'])], $top("SELECT br, path, ts FROM jm_hits WHERE ev = 'bot' ORDER BY id DESC LIMIT 8"));
    $google = (int)val("SELECT COUNT(*) FROM jm_hits WHERE ev = 'pv' AND ent = 1 AND src = 'Google' AND $W", $P);
    $first = (int)val('SELECT MIN(ts) FROM jm_hits');
    return ['days' => $days, 'from' => date('d M Y', $start), 'to' => date('d M Y'), 'cur' => $cur, 'prev' => $prev, 'series' => $series, 'hours' => array_values($hours), 'weekdays' => array_values($wd), 'pages' => $pages, 'entry' => $entry,
        'sources' => $grp('src'), 'refs' => $refs, 'devices' => $grp('dev', 4), 'browsers' => $grp('br', 6), 'os' => $grp('os', 6), 'countries' => $countries,
        'funnel' => [['l' => 'Visits', 'n' => $ses], ['l' => 'Looked at rooms, safari or food', 'n' => $stay], ['l' => 'Opened the booking form', 'n' => $form], ['l' => 'Sent a request', 'n' => $cur['requests']]],
        'byType' => $byType, 'byStatus' => $byStatus, 'latest' => $latest, 'blog' => $blogTop, 'bots' => $bots, 'botFeed' => $botFeed, 'google' => $google, 'since' => $first ? date('d M Y, g:i a', $first) : '', 'server' => date('d M Y, g:i:s a')];
}
function ins_live(): array {
    $now = time(); $blog = []; try { foreach (rows('SELECT slug, title FROM jm_blog') as $r) $blog[$r['slug']] = $r['title']; } catch (Throwable $e) {}
    $online = (int)val("SELECT COUNT(DISTINCT sn) FROM jm_hits WHERE ev IN ('pv','wa','call','mail') AND sn <> '' AND ts >= ?", [$now - 300]);
    $min = array_fill(0, 30, 0); foreach (rows("SELECT ts FROM jm_hits WHERE ev = 'pv' AND ts >= ?", [$now - 1800]) as $r) { $i = 29 - (int)floor(($now - (int)$r['ts']) / 60); if ($i >= 0 && $i < 30) $min[$i]++; }
    $pages = array_map(fn($r) => ['l' => ins_label($r['path'], $blog), 'n' => (int)$r['c']], rows("SELECT path, COUNT(DISTINCT sn) c FROM jm_hits WHERE ev = 'pv' AND sn <> '' AND ts >= ? GROUP BY path ORDER BY c DESC LIMIT 6", [$now - 300]));
    $evl = ['pv' => 'viewed', 'wa' => 'tapped WhatsApp on', 'call' => 'tapped Call on', 'mail' => 'tapped Email on'];
    $feed = array_map(fn($r) => ['t' => (int)$r['ts'], 'l' => ins_label($r['path'], $blog), 'a' => $evl[$r['ev']] ?? 'viewed', 'src' => $r['src'], 'dev' => $r['dev'], 'cc' => $r['cc']], rows("SELECT ts, ev, path, src, dev, cc FROM jm_hits WHERE ev IN ('pv','wa','call','mail') ORDER BY id DESC LIMIT 14"));
    $today = ins_totals(strtotime('today'), $now + 1);
    return ['now' => $now, 'online' => $online, 'perMinute' => $min, 'pages' => $pages, 'feed' => $feed, 'today' => $today];
}
if (isset($_GET['api'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($_GET['api'] === 'live' ? ins_live() : ins_data(in_array((int)($_GET['range'] ?? 7), [1, 7, 30, 90], true) ? (int)$_GET['range'] : 7), JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
$range = in_array((int)($_GET['range'] ?? 7), [1, 7, 30, 90], true) ? (int)$_GET['range'] : 7;
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow">
<title>Insights · <?= e(setting('site_name')) ?></title><link rel="icon" href="<?= url('assets/brand/favicon.ico') ?>"><?= font_head() ?><link rel="stylesheet" href="<?= asset('assets/css/pk.css') ?>"></head>
<body>
<header class="pk-top"><div class="pk-top__in"><img src="<?= e(img(setting('logo_light'))) ?>" alt="<?= e(setting('site_name')) ?>"><h1>Insights</h1>
  <nav><span class="pk-live"><i class="pk-dot"></i><span id="liveTxt">Live</span></span><a href="<?= url('pk-crm') ?>">CRM</a><a href="<?= url('index.php') ?>" target="_blank" rel="noopener">Website</a>
  <form method="post" style="display:inline"><?= csrf_field() ?><input type="hidden" name="action" value="logout"><button>Lock</button></form></nav></div></header>
<main class="pk-wrap" id="app" data-range="<?= $range ?>" data-api="<?= e($self) ?>"><p class="muted">Loading insights…</p></main>
<script src="<?= asset('assets/js/pk-insights.js') ?>"></script>
</body></html>
