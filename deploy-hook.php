<?php
/** GitHub webhook: push to the live branch -> website updates itself within seconds. Needs DEPLOY_SECRET in config.local.php. */
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/json');
if (!defined('DEPLOY_SECRET') || DEPLOY_SECRET === '') { http_response_code(404); exit('{"ok":false}'); }
$body = file_get_contents('php://input');
$sig = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
if (!hash_equals('sha256=' . hash_hmac('sha256', $body, DEPLOY_SECRET), $sig)) { http_response_code(403); exit('{"ok":false,"error":"bad signature"}'); }
$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
if ($event === 'ping') { echo '{"ok":true,"pong":true}'; exit; }
$p = json_decode($body, true);
if ($event !== 'push' || ($p['ref'] ?? '') !== 'refs/heads/' . upd_branch()) { echo '{"ok":true,"ignored":true}'; exit; }
http_response_code(202); echo '{"ok":true,"updating":true}';
if (function_exists('fastcgi_finish_request')) { @fastcgi_finish_request(); } elseif (function_exists('litespeed_finish_request')) { @litespeed_finish_request(); }
upd_run();
