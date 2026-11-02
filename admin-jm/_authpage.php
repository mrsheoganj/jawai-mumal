<?php
/** Shared look for the sign-in helper pages (forgot / reset password). */
function auth_page(string $title, string $heading, string $intro, string $bodyHtml): void { ?><!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> · <?= e(setting('site_name')) ?> admin</title><meta name="robots" content="noindex,nofollow">
<link rel="icon" href="<?= url('assets/brand/favicon.ico') ?>">
<?= font_head() ?>
<link rel="stylesheet" href="<?= asset('assets/css/admin.css') ?>"></head>
<body class="login">
<div class="login__art" aria-hidden="true"><img src="<?= e(img('assets/images/hotel-front.jpg')) ?>" alt=""><div class="login__mark"><img src="<?= e(img(setting('logo_emblem'))) ?>" alt="" width="230" height="230"></div></div>
<main class="login__panel">
  <div class="login__card">
    <img class="login__logo" src="<?= e(img(setting('logo_dark'))) ?>" alt="<?= e(setting('site_name')) ?>">
    <h1><?= e($heading) ?></h1>
    <p class="muted"><?= e($intro) ?></p>
    <?= $bodyHtml ?>
    <a class="back" href="login.php">← Back to sign in</a>
  </div>
</main>
</body></html>
<?php }
