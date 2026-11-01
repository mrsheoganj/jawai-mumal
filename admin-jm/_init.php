<?php
require_once __DIR__ . '/../includes/db.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_ok()) {
    http_response_code(400);
    exit('Security check failed. Go back, refresh the page and try again.');
}
/** Sidebar structure: top-level links and expandable groups by use case. Items: [file, key, label, icon]. */
function admin_nav(): array {
    return [
        ['link', ['index.php', 'dashboard', 'Dashboard', 'dashboard']],
        ['link', ['bookings.php', 'bookings', 'Bookings & enquiries', 'calendar-check']],
        ['group', 'stay', 'Stay & dining', 'bed', [
            ['rooms.php', 'rooms', 'Rooms & rates', 'bed'],
            ['menu.php', 'menu', 'Restaurant menu', 'utensils'],
        ]],
        ['group', 'safari', 'Safari & places', 'car', [
            ['safaris.php', 'safaris', 'Safari packages', 'car'],
            ['attractions.php', 'attractions', 'Explore Jawai', 'compass'],
            ['routes.php', 'routes', 'Distances & travel', 'navigation'],
        ]],
        ['group', 'content', 'Website content', 'pencil', [
            ['slides.php', 'slides', 'Home banner slides', 'image'],
            ['texts.php', 'texts', 'Page text & images', 'pencil'],
            ['gallery.php', 'gallery', 'Gallery', 'image'],
            ['testimonials.php', 'testimonials', 'Guest reviews', 'star'],
            ['faqs.php', 'faqs', 'FAQs', 'help'],
            ['content.php', 'content', 'Contact, logo & rules', 'sliders'],
        ]],
        ['group', 'seo', 'Search & journal', 'search', [
            ['seo.php', 'seo', 'SEO & search ranking', 'search'],
            ['blog.php', 'blog', 'Journal articles', 'list'],
        ]],
        ['group', 'settings', 'Settings', 'sliders', [
            ['email.php', 'email', 'Email (SMTP)', 'mail'],
            ['mode.php', 'mode', 'Launch & maintenance', 'bell'],
            ['update.php', 'update', 'Update from GitHub', 'refresh'],
            ['cleanup.php', 'cleanup', 'Go-live cleanup', 'trash'],
            ['account.php', 'account', 'My account', 'lock'],
        ]],
    ];
}
function admin_head(string $title, string $active = ''): void {
    $new = (int)val("SELECT COUNT(*) FROM jm_bookings WHERE status='New'");
    ?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> · Admin · <?= e(setting('site_name')) ?></title><meta name="robots" content="noindex,nofollow">
<link rel="icon" href="<?= url('assets/brand/favicon.ico') ?>">
<?= font_head() ?>
<link rel="stylesheet" href="<?= asset('assets/css/admin.css') ?>"><noscript><style>.ngrp__b{grid-template-rows:1fr!important}</style></noscript></head>
<body><div class="shell">
<aside class="side" id="side">
  <a class="side__brand" href="index.php"><img src="<?= e(img(setting('logo_light'))) ?>" alt="<?= e(setting('site_name')) ?>"><small>Admin panel</small></a>
  <nav aria-label="Admin menu"><?php foreach (admin_nav() as $n):
    if ($n[0] === 'link'): $i = $n[1]; ?>
    <a href="<?= $i[0] ?>" class="<?= $active === $i[1] ? 'is-on' : '' ?>"<?= $active === $i[1] ? ' aria-current="page"' : '' ?>><?= icon($i[3], 20) ?><span><?= e($i[2]) ?></span><?php if ($i[1] === 'bookings' && $new): ?><b class="count"><?= $new ?></b><?php endif; ?></a>
  <?php else: [, $gid, $glabel, $gicon, $items] = $n; $isAct = in_array($active, array_column($items, 1), true); ?>
    <div class="ngrp<?= $isAct ? ' is-open has-active' : '' ?>" data-grp="<?= $gid ?>">
      <button type="button" class="ngrp__h" aria-expanded="<?= $isAct ? 'true' : 'false' ?>" aria-controls="ng-<?= $gid ?>"><?= icon($gicon, 20) ?><span><?= e($glabel) ?></span><?= icon('chevron-down', 18, 'ngrp__chev') ?></button>
      <div class="ngrp__b" id="ng-<?= $gid ?>"><div class="ngrp__in">
        <?php foreach ($items as $i): ?><a href="<?= $i[0] ?>" class="<?= $active === $i[1] ? 'is-on' : '' ?>"<?= $active === $i[1] ? ' aria-current="page"' : '' ?>><?= icon($i[3], 18) ?><span><?= e($i[2]) ?></span></a><?php endforeach; ?>
      </div></div>
    </div>
  <?php endif; endforeach; ?></nav>
  <div class="side__foot"><a href="<?= url('index.php') ?>" target="_blank" rel="noopener"><?= icon('external', 18) ?><span>View website</span></a><a href="logout.php"><?= icon('log-out', 18) ?><span>Sign out</span></a></div>
</aside>
<div class="main">
  <header class="top"><button class="burger" id="burger" aria-label="Menu"><?= icon('menu', 24) ?></button><h1><?= e($title) ?></h1><?php $sm = site_mode(); if ($sm !== 'live'): ?><a class="modepill modepill--<?= $sm ?>" href="mode.php"><?= $sm === 'coming_soon' ? 'Coming soon mode' : 'Maintenance mode' ?></a><?php endif; ?><span class="who"><?= icon('user', 18) ?><?= e($_SESSION['admin_name'] ?? 'Admin') ?></span></header>
  <div class="content">
    <?php foreach (flashes() as $f): ?><div class="alert alert--<?= $f[0] === 'ok' ? 'ok' : 'err' ?>" role="status"><?= e($f[1]) ?></div><?php endforeach;
}
function admin_foot(): void { ?>
  </div></div></div>
<script>
(function(){var key='jmAdminNav',st=[];try{st=JSON.parse(localStorage.getItem(key)||'[]')}catch(e){}
document.querySelectorAll('.ngrp').forEach(function(g){
  var h=g.querySelector('.ngrp__h'),b=g.querySelector('.ngrp__b');
  function set(open,save){g.classList.toggle('is-open',open);h.setAttribute('aria-expanded',open?'true':'false');if('inert' in b)b.inert=!open;
    if(save){try{var o=[];document.querySelectorAll('.ngrp.is-open').forEach(function(x){o.push(x.dataset.grp)});localStorage.setItem(key,JSON.stringify(o))}catch(e){}}}
  set(g.classList.contains('has-active')||st.indexOf(g.dataset.grp)>-1,false);
  h.addEventListener('click',function(){set(!g.classList.contains('is-open'),true)});
});})();
</script>
<script>document.getElementById('burger').addEventListener('click',function(){document.getElementById('side').classList.toggle('open')});
document.querySelectorAll('[data-confirm]').forEach(function(b){b.addEventListener('click',function(e){if(!confirm(b.dataset.confirm))e.preventDefault()})});
document.querySelectorAll('input[type=file][data-preview]').forEach(function(i){i.addEventListener('change',function(){var t=document.getElementById(i.dataset.preview);if(t&&i.files[0]){t.src=URL.createObjectURL(i.files[0]);t.hidden=false}})});</script>
</body></html><?php }
