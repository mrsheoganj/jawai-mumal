<?php
/**
 * Live sync with GitHub without SSH/git: downloads the branch as a zip over HTTPS and copies the files in.
 * Never touches: data/ (database), assets/uploads/ (photos), config.local.php (server secrets), .git, deploy/.
 */
function upd_repo(): string   { return defined('UPDATE_REPO') ? UPDATE_REPO : 'mrsheoganj/jawai-mumal'; }
function upd_branch(): string { $b = trim((string)get_setting_raw('update_branch')); return $b !== '' ? $b : (defined('UPDATE_BRANCH') ? UPDATE_BRANCH : 'main'); }
function upd_auto(): bool     { $v = get_setting_raw('auto_update'); return $v !== null ? $v === '1' : (defined('DEFAULT_AUTO_UPDATE') ? (bool)DEFAULT_AUTO_UPDATE : false); }
function upd_dir(): string    { $d = __DIR__ . '/../data'; if (!is_dir($d)) @mkdir($d, 0755, true); return $d; }
function upd_local_sha(): string { return trim((string)@file_get_contents(upd_dir() . '/.version')); }
function upd_log(string $m): void { @file_put_contents(upd_dir() . '/update.log', date('Y-m-d H:i:s') . ' ' . $m . "\n", FILE_APPEND); }

function upd_get(string $url, ?string $saveTo = null, array $headers = []): ?string {
    $ua = 'JawaiMumalUpdater/1.0';
    if (function_exists('curl_init')) {
        $c = curl_init($url); $fh = $saveTo ? fopen($saveTo, 'wb') : null;
        $o = [CURLOPT_TIMEOUT => 90, CURLOPT_FOLLOWLOCATION => true, CURLOPT_USERAGENT => $ua, CURLOPT_HTTPHEADER => $headers];
        if (($ca = getenv('CURL_CA_BUNDLE')) && is_file($ca)) $o[CURLOPT_CAINFO] = $ca;
        if ($fh) $o[CURLOPT_FILE] = $fh; else $o[CURLOPT_RETURNTRANSFER] = true;
        curl_setopt_array($c, $o); $r = curl_exec($c); $code = (int)curl_getinfo($c, CURLINFO_HTTP_CODE); if (PHP_VERSION_ID < 80000) curl_close($c); if ($fh) fclose($fh);
        if ($r === false || $code >= 400) return null;
        return $saveTo ? 'ok' : (string)$r;
    }
    $ctx = stream_context_create(['http' => ['timeout' => 90, 'header' => "User-Agent: $ua\r\n" . implode("\r\n", $headers), 'follow_location' => 1]]);
    $r = @file_get_contents($url, false, $ctx); if ($r === false) return null;
    if ($saveTo) { file_put_contents($saveTo, $r); return 'ok'; } return $r;
}
/** Latest commit sha of the branch on GitHub, or null. */
function upd_remote_sha(): ?string {
    $j = upd_get('https://api.github.com/repos/' . upd_repo() . '/commits/' . rawurlencode(upd_branch()), null, ['Accept: application/vnd.github.sha']);
    $j = $j ? trim($j) : ''; return preg_match('/^[0-9a-f]{40}$/', $j) ? $j : null;
}
function upd_rrmdir(string $d): void { if (!is_dir($d)) return; foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $f) { $f->isDir() ? @rmdir($f->getPathname()) : @unlink($f->getPathname()); } @rmdir($d); }

/** Downloads and installs the newest code. Returns ['ok'=>bool,'msg'=>string,'sha'=>string]. */
function upd_run(): array {
    if (!class_exists('ZipArchive')) return ['ok' => false, 'msg' => 'The PHP zip extension is not available on this server.', 'sha' => ''];
    @set_time_limit(180); @ignore_user_abort(true);
    $dir = upd_dir(); $lock = fopen($dir . '/.update.lock', 'c');
    if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) return ['ok' => false, 'msg' => 'Another update is already running.', 'sha' => ''];
    $sha = upd_remote_sha() ?: ''; $zip = $dir . '/_update.zip'; $tmp = $dir . '/_update';
    try {
        upd_rrmdir($tmp); @unlink($zip);
        if (!upd_get('https://codeload.github.com/' . upd_repo() . '/zip/' . ($sha ?: 'refs/heads/' . upd_branch()), $zip) || !is_file($zip) || filesize($zip) < 20000) throw new RuntimeException('Could not download the update from GitHub.');
        $z = new ZipArchive(); if ($z->open($zip) !== true) throw new RuntimeException('The downloaded update is not a valid zip.');
        $z->extractTo($tmp); $z->close();
        $roots = glob($tmp . '/*', GLOB_ONLYDIR); $src = $roots[0] ?? '';
        if (!$src || !is_file($src . '/index.php') || !is_file($src . '/includes/db.php')) throw new RuntimeException('The update does not look like the Jawai Mumal website; nothing was changed.');
        $skip = ['data', 'assets/uploads', 'config.local.php', '.git', '.github', '.claude', 'deploy', 'dist'];
        $n = 0; $root = realpath(__DIR__ . '/..');
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($it as $f) {
            $rel = str_replace('\\', '/', substr($f->getPathname(), strlen($src) + 1));
            foreach ($skip as $s) if ($rel === $s || strpos($rel, $s . '/') === 0) continue 2;
            $dest = $root . '/' . $rel;
            if ($f->isDir()) { if (!is_dir($dest)) @mkdir($dest, 0755, true); continue; }
            if (!is_dir(dirname($dest))) @mkdir(dirname($dest), 0755, true);
            if (is_file($dest) && md5_file($dest) === md5_file($f->getPathname())) continue;   // unchanged
            $t = $dest . '.jmnew'; if (!@copy($f->getPathname(), $t) || !@rename($t, $dest)) { @unlink($t); throw new RuntimeException('Could not write ' . $rel . ' (check folder permissions).'); }
            $n++;
        }
        if ($sha) file_put_contents($dir . '/.version', $sha);
        upd_log('updated to ' . substr($sha, 0, 7) . ' from ' . upd_branch() . ' (' . $n . ' files)');
        $res = ['ok' => true, 'msg' => $n ? "Updated {$n} file(s) to " . substr($sha, 0, 7) . '.' : 'Already up to date (no file changes).', 'sha' => $sha];
    } catch (Throwable $e) { upd_log('FAILED: ' . $e->getMessage()); $res = ['ok' => false, 'msg' => $e->getMessage(), 'sha' => $sha]; }
    upd_rrmdir($tmp); @unlink($zip); flock($lock, LOCK_UN); fclose($lock);
    if (function_exists('opcache_reset')) @opcache_reset();
    return $res;
}
/** Cheap "live sync": at most every few minutes, after the page has been sent, look for a newer commit and install it. */
function upd_maybe_auto(): void {
    if (PHP_SAPI === 'cli' || !upd_auto()) return;
    $stamp = upd_dir() . '/.last_check';
    if (is_file($stamp) && time() - filemtime($stamp) < 300) return;
    @touch($stamp);
    register_shutdown_function(function () {
        if (function_exists('fastcgi_finish_request')) @fastcgi_finish_request(); elseif (function_exists('litespeed_finish_request')) @litespeed_finish_request();
        $r = upd_remote_sha(); if ($r && $r !== upd_local_sha()) upd_run();
    });
}
