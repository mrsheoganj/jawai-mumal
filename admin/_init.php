<?php
require_once __DIR__ . '/../includes/db.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_ok()) {
    http_response_code(400);
    exit('Security check failed. Go back, refresh the page and try again.');
}
function admin_nav(): array {
    return [
        ['index.php', 'dashboard', 'Dashboard', 'dashboard'],
        ['bookings.php', 'bookings', 'Bookings & enquiries', 'calendar-check'],
        ['rooms.php', 'rooms', 'Rooms', 'bed'],
        ['menu.php', 'menu', 'Restaurant menu', 'utensils'],
        ['safaris.php', 'safaris', 'Safari packages', 'car'],
        ['attractions.php', 'attractions', 'Explore Jawai', 'compass'],
        ['routes.php', 'routes', 'Distances & travel', 'navigation'],
        ['gallery.php', 'gallery', 'Gallery', 'image'],
        ['testimonials.php', 'testimonials', 'Guest reviews', 'star'],
        ['faqs.php', 'faqs', 'FAQs', 'help'],
        ['slides.php', 'slides', 'Home banner slides', 'image'],
        ['texts.php', 'texts', 'Page text & images', 'pencil'],
        ['content.php', 'content', 'Contact, logo & rules', 'sliders'],
        ['mode.php', 'mode', 'Site mode (launch / maintenance)', 'bell'],
        ['update.php', 'update', 'Update from GitHub', 'refresh'],
        ['account.php', 'account', 'My account', 'lock'],
    ];
}
function admin_head(string $title, string $active = ''): void {
    $new = (int)val("SELECT COUNT(*) FROM jm_bookings WHERE status='New'");
    ?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> · Admin · <?= e(setting('site_name')) ?></title><meta name="robots" content="noindex,nofollow">
<link rel="icon" href="<?= url('assets/brand/favicon.ico') ?>">
<?= font_head() ?>
<link rel="stylesheet" href="<?= url('assets/css/admin.css') ?>?v=1"></head>
<body><div class="shell">
<aside class="side" id="side">
  <a class="side__brand" href="index.php"><img src="<?= e(img(setting('logo_light'))) ?>" alt="<?= e(setting('site_name')) ?>"><small>Admin panel</small></a>
  <nav><?php foreach (admin_nav() as $n): ?>
    <a href="<?= $n[0] ?>" class="<?= $active === $n[1] ? 'is-on' : '' ?>"><?= icon($n[3], 20) ?><span><?= e($n[2]) ?></span><?php if ($n[1] === 'bookings' && $new): ?><b class="count"><?= $new ?></b><?php endif; ?></a>
  <?php endforeach; ?></nav>
  <div class="side__foot"><a href="<?= url('index.php') ?>" target="_blank" rel="noopener"><?= icon('external', 18) ?><span>View website</span></a><a href="logout.php"><?= icon('log-out', 18) ?><span>Sign out</span></a></div>
</aside>
<div class="main">
  <header class="top"><button class="burger" id="burger" aria-label="Menu"><?= icon('menu', 24) ?></button><h1><?= e($title) ?></h1><?php $sm = site_mode(); if ($sm !== 'live'): ?><a class="modepill modepill--<?= $sm ?>" href="mode.php"><?= $sm === 'coming_soon' ? 'Coming soon mode' : 'Maintenance mode' ?></a><?php endif; ?><span class="who"><?= icon('user', 18) ?><?= e($_SESSION['admin_name'] ?? 'Admin') ?></span></header>
  <div class="content">
    <?php foreach (flashes() as $f): ?><div class="alert alert--<?= $f[0] === 'ok' ? 'ok' : 'err' ?>" role="status"><?= e($f[1]) ?></div><?php endforeach;
}
function admin_foot(): void { ?>
  </div></div></div>
<script>document.getElementById('burger').addEventListener('click',function(){document.getElementById('side').classList.toggle('open')});
document.querySelectorAll('[data-confirm]').forEach(function(b){b.addEventListener('click',function(e){if(!confirm(b.dataset.confirm))e.preventDefault()})});
document.querySelectorAll('input[type=file][data-preview]').forEach(function(i){i.addEventListener('change',function(){var t=document.getElementById(i.dataset.preview);if(t&&i.files[0]){t.src=URL.createObjectURL(i.files[0]);t.hidden=false}})});</script>
</body></html><?php }
