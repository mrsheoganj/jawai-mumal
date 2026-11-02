<?php
require_once __DIR__ . '/db.php';
$siteName = setting('site_name');
$active    = $active ?? '';
$GLOBALS['jm_seo'] = $seoR = seo_resolve(['seoKey' => $seoKey ?? null, 'seo' => $seo ?? [], 'active' => $active, 'pageTitle' => $pageTitle ?? '', 'pageDesc' => $pageDesc ?? null, 'ogImage' => $ogImage ?? null]);
$nav = [
    'home' => [t('nav.home', 'Home'), 'index.php'], 'stay' => [t('nav.stay', 'Stay'), 'rooms.php'], 'dining' => [t('nav.dining', 'Dining'), 'restaurant.php'],
    'safari' => [t('nav.safari', 'Safari'), 'safari.php'], 'jawai' => [t('nav.jawai', 'Explore Jawai'), 'jawai.php'], 'gallery' => [t('nav.gallery', 'Gallery'), 'gallery.php'], 'contact' => [t('nav.contact', 'Contact'), 'contact.php'],
];
if (seo_cfg('blog_in_menu') === '1') $nav['blog'] = [t('nav.blog', 'Journal'), 'blog'];
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php seo_head($seoR); ?>
<?php foreach ((array)($preloads ?? []) as $pl): ?><link rel="preload" as="image" href="<?= e(img($pl[0])) ?>"<?= !empty($pl[1]) ? ' media="' . e($pl[1]) . '"' : '' ?> fetchpriority="high">
<?php endforeach; ?>
<meta name="theme-color" content="#ffffff">
<meta name="format-detection" content="telephone=no">
<link rel="icon" href="<?= url('assets/brand/favicon.ico') ?>" sizes="any">
<link rel="icon" type="image/png" sizes="32x32" href="<?= url('assets/brand/icon-32.png') ?>">
<link rel="icon" type="image/png" sizes="192x192" href="<?= url('assets/brand/icon-192.png') ?>">
<link rel="manifest" href="<?= url('site.webmanifest') ?>">
<link rel="apple-touch-icon" href="<?= url('assets/brand/icon-180.png') ?>">
<?= font_head() ?>
<link rel="stylesheet" href="<?= asset('assets/css/site.css') ?>">
<noscript><style>.rv{opacity:1!important;transform:none!important}.preloader{display:none!important}</style></noscript>
</head>
<body class="<?= e($bodyClass ?? '') ?>" data-tax="<?= e(setting('tax_percent')) ?>">
<a class="skip" href="#main">Skip to content</a>


<header class="nav" id="nav">
  <div class="wrap nav__in">
    <a class="nav__logo" href="<?= url('index.php') ?>" aria-label="<?= e($siteName) ?> home">
      <img class="logo-light" src="<?= e(img(setting('logo_light'))) ?>" alt="<?= e($siteName) ?>" height="44">
      <img class="logo-dark" src="<?= e(img(setting('logo_dark'))) ?>" alt="" height="44">
    </a>
    <nav class="nav__links" id="navlinks" aria-label="Main">
      <?php foreach ($nav as $k => $n): ?>
        <a href="<?= url($n[1]) ?>" <?= $active === $k ? 'aria-current="page"' : '' ?>><?= e($n[0]) ?></a>
      <?php endforeach; ?>
      <a class="btn btn--gold nav__cta" href="<?= url('book.php') ?>"><?= et('nav.cta', 'Book now') ?></a>
      <div class="nav__extra">
        <a class="wa-ico" href="<?= e(wa_link(t('sdial.msg', 'Hello Jawai Mumal, I would like to know more.'))) ?>" target="_blank" rel="noopener" aria-label="<?= et('wa.aria', 'Chat on WhatsApp') ?>" title="<?= et('wa.aria', 'Chat on WhatsApp') ?>"><?= icon('whatsapp', 26) ?></a>
        <a href="<?= e(maps_open_url()) ?>" target="_blank" rel="noopener"><?= icon('map-pin', 20) ?> <?= et('nav.directions', 'Get directions') ?></a>
      </div>
    </nav>
    <button class="nav__toggle" id="navtoggle" aria-label="Open menu" aria-expanded="false" aria-controls="navlinks"><?= icon('menu', 24) ?></button>
  </div>
  <div class="nav__bd" id="navbd"></div>
</header>
<main id="main">
