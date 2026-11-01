<?php
require_once __DIR__ . '/../includes/db.php';
if (admin_logged_in()) redirect('index.php');

foreach ($_POST as $k => $v) if (is_array($v)) unset($_POST[$k]);
$ip = substr($_SERVER['REMOTE_ADDR'] ?? '0', 0, 64);
$error = '';
$wantsJson = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    q('DELETE FROM jm_attempts WHERE created_at < ?', [time() - 900]);
    $fails = (int)val('SELECT COUNT(*) FROM jm_attempts WHERE ip = ? AND created_at > ?', [$ip, time() - 600]);
    $ok = false;
    if (!csrf_ok()) {
        $error = 'Session expired. Please reload the page and try again.';
    } elseif ($fails >= 6) {
        $error = 'Too many attempts. Please wait 10 minutes and try again.';
    } else {
        $u = trim($_POST['username'] ?? '');
        $user = row('SELECT * FROM jm_admins WHERE username = ?', [$u]);
        if ($user && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
            session_regenerate_id(true);
            if (!empty($_POST['remember'])) {   // keep this device signed in for 30 days (persistent cookie)
                setcookie(session_name(), session_id(), ['expires' => time() + 2592000, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS'])]);
            }
            $_SESSION['admin_id'] = (int)$user['id'];
            $_SESSION['admin_user'] = $user['username'];
            $_SESSION['admin_name'] = $user['full_name'] ?: $user['username'];
            q('DELETE FROM jm_attempts WHERE ip = ?', [$ip]);
            $ok = true;
        } else {
            q('INSERT INTO jm_attempts (ip, created_at) VALUES (?, ?)', [$ip, time()]);
            usleep(600000);
            $error = 'Incorrect username or password.';
        }
    }
    if ($wantsJson) { header('Content-Type: application/json'); echo json_encode(['ok' => $ok, 'error' => $error]); exit; }
    if ($ok) redirect('index.php');
}
?><!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sign in · <?= e(setting('site_name')) ?> admin</title><meta name="robots" content="noindex,nofollow">
<link rel="icon" href="<?= url('assets/brand/favicon.ico') ?>">
<?= font_head() ?>
<link rel="stylesheet" href="<?= url('assets/css/admin.css') ?>?v=1"></head>
<body class="login" id="loginbody">
<div class="login__art" aria-hidden="true">
  <img src="<?= e(img('assets/images/hills-green.jpg')) ?>" alt="">
  <div class="login__mark">
    <svg viewBox="0 0 120 120" class="login__ring"><circle cx="60" cy="60" r="57" fill="none" stroke="currentColor" stroke-width="1.2"/></svg>
    <svg viewBox="0 0 120 120" class="login__ring login__ring--2"><circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" stroke-width=".6" stroke-dasharray="2 5"/></svg>
    <img src="<?= e(img(setting('logo_emblem'))) ?>" alt="" width="230" height="230">
  </div>
  <p class="login__quote">Good food · Comfort stay · Happy moments</p>
</div>
<main class="login__panel">
  <form class="login__card" id="loginform" method="post" novalidate>
    <?= csrf_field() ?>
    <img class="login__logo" src="<?= e(img(setting('logo_dark'))) ?>" alt="<?= e(setting('site_name')) ?>">
    <h1>Welcome back</h1>
    <p class="muted">Sign in to manage rooms, menu, safaris, bookings and the whole website.</p>
    <?php if (isset($_GET['bye'])): ?><div class="alert alert--ok">You have been signed out.</div><?php endif; ?>
    <div class="alert alert--err" id="lerr" role="alert" <?= $error ? '' : 'hidden' ?>><?= e($error) ?></div>
    <label for="u">Username</label>
    <div class="inp"><?= icon('user', 20) ?><input id="u" name="username" autocomplete="username" required autofocus value="<?= e($_POST['username'] ?? '') ?>"></div>
    <label for="p">Password</label>
    <div class="inp"><?= icon('lock', 20) ?><input id="p" name="password" type="password" autocomplete="current-password" required><button type="button" class="eye" id="eye" aria-label="Show password"><?= icon('eye', 20) ?></button></div>
    <label class="remember"><input type="checkbox" name="remember" value="1" checked> <span>Keep me signed in on this device (30 days)</span></label>
    <button class="btn" id="lbtn" type="submit"><span>Sign in</span><?= icon('arrow-right', 18) ?></button>
    <a class="back" href="<?= url('index.php') ?>">← Back to website</a>
  </form>
</main>
<div class="curtain" id="curtain" aria-live="polite"><img src="<?= e(img(setting('logo_emblem'))) ?>" alt="" width="120" height="120"><p>Welcome back</p></div>
<script>
(function(){
  var f=document.getElementById('loginform'),err=document.getElementById('lerr'),btn=document.getElementById('lbtn');
  document.getElementById('eye').addEventListener('click',function(){var p=document.getElementById('p');p.type=p.type==='password'?'text':'password'});
  f.addEventListener('submit',function(e){
    e.preventDefault();
    if(!f.username.value.trim()||!f.password.value){show('Please enter your username and password.');return}
    btn.disabled=true;btn.classList.add('is-busy');
    fetch('login.php',{method:'POST',body:new FormData(f),headers:{'X-Requested-With':'fetch'},credentials:'same-origin'})
      .then(function(r){return r.json()}).then(function(d){
        if(d.ok){document.getElementById('curtain').classList.add('is-on');document.getElementById('loginbody').classList.add('is-leaving');setTimeout(function(){location.href='index.php'},1400)}
        else{show(d.error||'Could not sign in.');btn.disabled=false;btn.classList.remove('is-busy')}
      }).catch(function(){f.submit()});
  });
  function show(m){err.textContent=m;err.hidden=false;f.classList.remove('shake');void f.offsetWidth;f.classList.add('shake')}
})();
</script></body></html>
