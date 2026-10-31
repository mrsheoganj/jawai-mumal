<?php
require_once __DIR__ . '/db.php';
$siteName = setting('site_name');
$pageTitle = isset($pageTitle) ? $pageTitle . ' | ' . $siteName : $siteName . ' — ' . setting('site_tagline') . ' | Bera, Jawai';
$pageDesc  = $pageDesc ?? setting('meta_description');
$active    = $active ?? '';
$ogImage   = img($ogImage ?? 'assets/images/hotel-day.jpg');
$nav = [
    'home' => [t('nav.home', 'Home'), 'index.php'], 'stay' => [t('nav.stay', 'Stay'), 'rooms.php'], 'dining' => [t('nav.dining', 'Dining'), 'restaurant.php'],
    'safari' => [t('nav.safari', 'Safari'), 'safari.php'], 'jawai' => [t('nav.jawai', 'Explore Jawai'), 'jawai.php'], 'gallery' => [t('nav.gallery', 'Gallery'), 'gallery.php'], 'contact' => [t('nav.contact', 'Contact'), 'contact.php'],
];
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDesc) ?>">
<meta name="theme-color" content="#2b180e">
<link rel="icon" href="<?= url('assets/brand/favicon.ico') ?>" sizes="any">
<link rel="icon" type="image/png" sizes="32x32" href="<?= url('assets/brand/icon-32.png') ?>">
<link rel="icon" type="image/png" sizes="192x192" href="<?= url('assets/brand/icon-192.png') ?>">
<link rel="apple-touch-icon" href="<?= url('assets/brand/icon-180.png') ?>">
<meta property="og:type" content="website">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e($pageDesc) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= url('assets/css/site.css') ?>?v=1">
<script>try{if(sessionStorage.getItem('jmPre'))document.documentElement.classList.add('no-pre')}catch(e){}</script>
</head>
<body class="<?= e($bodyClass ?? '') ?>" data-tax="<?= e(setting('tax_percent')) ?>">
<a class="skip" href="#main">Skip to content</a>

<div class="preloader" id="preloader" aria-hidden="true">
  <div class="preloader__mark">
    <svg class="preloader__ring" viewBox="0 0 120 120"><circle cx="60" cy="60" r="56" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
    <img src="<?= e(img(setting('logo_emblem'))) ?>" alt="" width="150" height="150">
  </div>
  <p class="preloader__text"><?= e($siteName) ?></p>
</div>

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
    </nav>
    <button class="nav__toggle" id="navtoggle" aria-label="Open menu" aria-expanded="false" aria-controls="navlinks"><?= icon('menu', 24) ?></button>
  </div>
</header>
<main id="main">
