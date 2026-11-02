<?php
/** Receives the visit beacon from assets/js/site.js (people only; no cookies, no IP stored). */
require_once __DIR__ . '/includes/db.php';
header('Cache-Control: no-store'); header('X-Robots-Tag: noindex');
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { http_response_code(405); exit; }
if (($_SERVER['HTTP_DNT'] ?? '') === '1' || admin_logged_in()) { http_response_code(204); exit; }   // respect Do Not Track; never count the hotel team
$ua = track_ua();
if ($ua === '' || track_crawler_name($ua) !== '' || preg_match('/headless|python|curl|wget|java\/|libwww|httpclient|scrapy|monitor|uptime|pingdom|lighthouse/i', $ua)) { http_response_code(204); exit; }
$origin = parse_url((string)($_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? ''), PHP_URL_HOST); $self = preg_replace('/^www\./', '', (string)($_SERVER['HTTP_HOST'] ?? ''));
if ($origin && preg_replace('/^www\./', '', $origin) !== $self) { http_response_code(204); exit; }
$in = $_POST; if (!$in) { $j = json_decode((string)file_get_contents('php://input'), true); if (is_array($j)) $in = $j; }
$ev = in_array($in['ev'] ?? 'pv', ['pv', 'wa', 'call', 'mail', 'book'], true) ? $in['ev'] : 'pv';
$path = track_clean_path((string)($in['p'] ?? '/')); if (preg_match('#^/(admin-jm|pk-crm|pk-insights|hit)#', $path)) { http_response_code(204); exit; }
$sid = track_sid();
if ((int)val('SELECT COUNT(*) FROM jm_hits WHERE sid = ? AND d = ?', [$sid, date('Y-m-d')]) > 300) { http_response_code(204); exit; }   // runaway-protection
$ref = ''; $rh = parse_url((string)($in['r'] ?? ''), PHP_URL_HOST);
if ($rh && preg_replace('/^www\./', '', strtolower($rh)) !== strtolower($self)) $ref = substr(strtolower(preg_replace('/^www\./', '', $rh)), 0, 100);
$utm = strtolower(trim((string)($in['u'] ?? ''))); $utm = preg_match('/^[a-z0-9 _.\-]{1,30}$/', $utm) ? $utm : '';
$ent = !empty($in['e']) ? 1 : 0;
[$dev, $br, $os] = track_parse_ua($ua, (int)($in['w'] ?? 0));
$cc = ''; $tz = (string)($in['z'] ?? '');
if ($tz !== '' && preg_match('#^[A-Za-z_]+(/[A-Za-z_+\-0-9]+){0,2}$#', $tz)) { try { $loc = (new DateTimeZone($tz))->getLocation(); $cc = strtoupper((string)($loc['country_code'] ?? '')); if ($cc === '??') $cc = ''; } catch (Throwable $e) {} }
$lg = strtolower(substr(preg_replace('/[^A-Za-z\-]/', '', (string)($in['l'] ?? '')), 0, 10));
track_insert(['sid' => $sid, 'sn' => substr(preg_replace('/[^a-z0-9]/i', '', (string)($in['s'] ?? '')), 0, 12), 'ev' => $ev, 'ent' => $ent, 'path' => $path, 'ref' => $ref, 'src' => $ent ? track_source($ref, $utm) : '', 'dev' => $dev, 'br' => $br, 'os' => $os, 'cc' => $cc, 'lg' => $lg, 'sw' => (int)($in['w'] ?? 0)]);
http_response_code(204);
