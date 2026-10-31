<?php
/** CLI only: writes config.local.php on the server the first time (used by the GitHub deploy). Env: JM_PASS, JM_BRANCH, JM_LAUNCH. */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$target = dirname(__DIR__) . '/config.local.php';
if (is_file($target)) { echo "config.local.php already exists - left untouched\n"; exit(0); }
$q = fn($s) => var_export((string)$s, true);
$pass = getenv('JM_PASS'); if ($pass === false || strlen($pass) < 8) { fwrite(STDERR, "ADMIN_PASSWORD must be at least 8 characters\n"); exit(1); }
$cfg = "<?php\n// Server-only settings (created by the GitHub deploy; never committed)\n"
     . "define('DEFAULT_ADMIN_PASS', " . $q($pass) . ");\n"
     . "define('DEFAULT_SITE_MODE', 'coming_soon');       // coming_soon | live | maintenance\n"
     . "define('DEFAULT_LAUNCH_AT', " . $q(getenv('JM_LAUNCH') ?: '2026-11-06T00:00') . ");   // India time; goes live automatically\n"
     . "define('DB_DRIVER', 'sqlite');\n"
     . "define('UPDATE_REPO', 'mrsheoganj/jawai-mumal');\n"
     . "define('UPDATE_BRANCH', " . $q(getenv('JM_BRANCH') ?: 'main') . ");\n"
     . "define('DEFAULT_AUTO_UPDATE', false);               // GitHub Actions deploys instead\n"
     . "define('DEPLOY_SECRET', " . $q(bin2hex(random_bytes(16))) . ");\n";
file_put_contents($target, $cfg); chmod($target, 0600);
echo "Created config.local.php\n";
