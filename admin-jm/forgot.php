<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/_authpage.php';
if (admin_logged_in()) redirect('index.php');
foreach ($_POST as $k => $v) if (is_array($v)) unset($_POST[$k]);
$ip = substr($_SERVER['REMOTE_ADDR'] ?? '0', 0, 60);
$msg = ''; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    q('DELETE FROM jm_attempts WHERE created_at < ?', [time() - 900]);
    $tag = 'fp:' . $ip;
    $recent = (int)val('SELECT COUNT(*) FROM jm_attempts WHERE ip = ? AND created_at > ?', [$tag, time() - 900]);
    if (!csrf_ok()) $err = 'Session expired. Please reload the page and try again.';
    elseif ($recent >= 3) $err = 'Too many requests. Please wait 15 minutes and try again.';
    else {
        q('INSERT INTO jm_attempts (ip, created_at) VALUES (?, ?)', [$tag, time()]);
        $id = trim((string)($_POST['who'] ?? ''));
        $a = $id !== '' && strlen($id) <= 190 ? row('SELECT * FROM jm_admins WHERE username = ? OR LOWER(email) = LOWER(?)', [$id, $id]) : null;
        if ($a && filter_var((string)$a['email'], FILTER_VALIDATE_EMAIL)) {
            $token = bin2hex(random_bytes(24));
            set_setting('pwreset.hash', hash('sha256', $token)); set_setting('pwreset.exp', (string)(time() + 3600)); set_setting('pwreset.admin', (string)$a['id']);
            $link = rtrim(setting('site_url'), '/') . '/' . ADMIN_DIR . '/reset.php?t=' . $token;
            $text = "Hello,\n\nSomeone asked to reset the admin password for " . setting('site_name') . ".\nOpen this link within 1 hour to choose a new password:\n$link\n\nIf you did not ask for this, ignore this email. Your password stays the same.";
            $html = mail_html('Reset your admin password', 'Someone asked to reset the admin password. This link works for 1 hour.', ['Reset link' => $link], 'If you did not ask for this, ignore this email. Your password stays the same.');
            jm_mail((string)$a['email'], 'Reset your ' . setting('site_name') . ' admin password', $text, $html, 'password-reset');
        }
        $msg = 'If that account has an email address on file, a reset link is on its way. It is valid for 1 hour.';
    }
}
$body = ($err ? '<div class="alert alert--err" role="alert">' . e($err) . '</div>' : '') . ($msg ? '<div class="alert alert--ok">' . e($msg) . '</div>' : '')
  . '<form method="post" novalidate>' . csrf_field() . '<label for="who">Username or admin email</label><div class="inp">' . icon('user', 20) . '<input id="who" name="who" autocomplete="username" required autofocus maxlength="190"></div>'
  . '<button class="btn" type="submit"><span>Email me a reset link</span>' . icon('arrow-right', 18) . '</button></form>';
auth_page('Forgot password', 'Forgot your password?', 'Enter your username or admin email and we will email you a link to choose a new one.', $body);
