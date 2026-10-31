<?php
require_once __DIR__ . '/includes/db.php';
$pageTitle = 'Restaurant & Café'; $pageDesc = 'Rajasthani classics, tandoor grills, breads, desserts and café drinks at Jawai Mumal, Bera. Veg and non-veg. Party bookings available.';
$active = 'dining'; $bookTab = 'dine';
$items = rows("SELECT * FROM jm_menu WHERE available=1 ORDER BY sort_order, id");
$cats = array_values(array_unique(array_column($items, 'category')));
include __DIR__ . '/includes/header.php';
page_head('Dining', 'Restaurant & Café', 'Good food, comfort and happy moments — from slow-cooked classics to your morning chai.', 'assets/images/dining-fine.jpg');
?>
<section class="sec">
  <div class="wrap">
    <div class="split" style="margin-bottom:4rem">
      <div class="rv"><span class="eyebrow eyebrow--dark">The kitchen</span><h2>Rajasthani at heart, easy for everyone</h2>
        <p>Our kitchen cooks the dishes this region is known for — dal baati churma, laal maas, ker sangri — alongside tandoor grills, North Indian favourites and an all-day café.</p>
        <ul class="checks" style="color:var(--ink-2)"><li><?= icon('check', 16) ?>Vegetarian &amp; non-vegetarian</li><li><?= icon('check', 16) ?>Jain options on request</li><li><?= icon('check', 16) ?>Café, tea &amp; cold drinks</li><li><?= icon('check', 16) ?>Group, party &amp; catering menus</li></ul>
        <div class="hero__cta"><a class="btn btn--gold" href="#book">Reserve a table</a><a class="btn btn--line" href="<?= url('book.php?type=event') ?>">Plan a party</a></div></div>
      <div class="duo rv d1"><figure class="frame"><img src="<?= e(img('assets/images/dining-hall.jpg')) ?>" alt="Restaurant interior" loading="lazy"></figure><figure class="frame"><img src="<?= e(img('assets/images/food-thali.jpg')) ?>" alt="Rajasthani thali" loading="lazy"></figure></div>
    </div>
    <div class="sechead"><span class="eyebrow eyebrow--dark">Menu</span><h2>What we cook</h2><p><span class="vd" style="vertical-align:-2px"></span> Vegetarian &nbsp; <span class="vd vd--nv" style="vertical-align:-2px"></span> Non-vegetarian · Prices in rupees, taxes extra.</p></div>
    <div class="chips" data-filter-group=".dish[data-cat]">
      <button class="chip" data-filter="all" aria-pressed="true">All</button>
      <?php foreach ($cats as $c): ?><button class="chip" data-filter="<?= e($c) ?>" aria-pressed="false"><?= e($c) ?></button><?php endforeach; ?>
    </div>
    <div class="menu">
      <?php foreach ($items as $m): ?>
        <div class="dish" data-cat="<?= e($m['category']) ?>">
          <h3><span class="vd <?= $m['veg'] ? '' : 'vd--nv' ?>" title="<?= $m['veg'] ? 'Vegetarian' : 'Non-vegetarian' ?>"></span><?= e($m['name']) ?><?php if ($m['tag']): ?> <span class="pill"><?= e($m['tag']) ?></span><?php endif; ?></h3>
          <span class="price"><?= money($m['price']) ?></span>
          <p><?= e($m['description']) ?><?= $m['spice'] ? ' · <em>' . e($m['spice']) . '</em>' : '' ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<section class="sec sec--sand" id="book" style="padding-top:3rem"><div class="wrap"><div class="sechead" style="margin-bottom:1.5rem"><h2>Reserve a table</h2></div><?php include __DIR__ . '/includes/booking_panel.php'; ?></div></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
