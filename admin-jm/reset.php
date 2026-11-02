<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/_authpage.php';
if (admin_logged_in()) redirect('index.php');
foreach ($_POST as $k => $v) if (is_array($v)) unset($_POST[$k]);
$t = (string)($_GET['t'] ?? $_POST['t'] ?? '');
$valid = preg_match('/^[a-f0-9]{48}$/', $t) && (int)get_setting_raw('pwreset.exp') > time() && hash_equals((string)get_setting_raw('pwreset.hash'), hash('sha256', $t));
$err = '';
if ($valid && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = (string)($_POST['new'] ?? ''); $new2 = (string)($_POST['new2'] ?? '');
    if (!csrf_ok()) $err = 'Session expired. Please reload the page and try again.';
    elseif (strlen($new) < 8 || strlen($new) > 100 || !preg_match('/[A-Za-z]/', $new) || !preg_match('/\d/', $new)) $err = 'Use 8 to 100 characters with at least one letter and one number.';
    elseif ($new !== $new2) $err = 'The two passwords do not match.';
    else {
        q('UPDATE jm_admins SET password_hash = ? WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), (int)get_setting_raw('pwreset.admin')]);
        set_setting('pwreset.hash', ''); set_setting('pwreset.exp', '0'); set_setting('pwreset.admin', '');
        flash('ok', 'Password changed. Please sign in.');
        redirect('login.php');
    }
}
if (!$valid) auth_page('Link expired', 'This link has expired', 'Reset links work once and for 1 hour. Please ask for a new one.', '<a class="btn" href="forgot.php"><span>Get a new link</span></a>');
else auth_page('Choose a new password', 'Choose a new password', 'Pick something only you know: 8 or more characters with a letter and a number.',
  ($err ? '<div class="alert alert--err" role="alert">' . e($err) . '</div>' : '') . '<form method="post" novalidate>' . csrf_field() . '<input type="hidden" name="t" value="' . e($t) . '">'
  . '<label for="n1">New password</label><div class="inp">' . icon('lock', 20) . '<input id="n1" name="new" type="password" autocomplete="new-password" required></div>'
  . '<label for="n2">Repeat new password</label><div class="inp">' . icon('lock', 20) . '<input id="n2" name="new2" type="password" autocomplete="new-password" required></div>'
  . '<button class="btn" type="submit"><span>Save new password</span>' . icon('arrow-right', 18) . '</button></form>');
