<?php
require_once __DIR__ . '/includes/db.php';
$pageTitle = 'Restaurant & Café, Vegetarian Rajasthani Food'; $pageDesc = 'Pure vegetarian Rajasthani village food at Jawai Mumal, Bera: dal baati churma, ker sangri, bajra roti, tandoor dishes, desserts and café. Party bookings available.';
$active = 'dining'; $bookTab = 'dine';
$items = rows("SELECT * FROM jm_menu WHERE available=1 ORDER BY sort_order, id");
$cats = array_values(array_unique(array_column($items, 'category')));
seo_ld(seo_breadcrumb_ld([['Restaurant', 'restaurant']]));
seo_ld(seo_restaurant_ld());
include __DIR__ . '/includes/header.php';
page_head(t('restaurant.head.eyebrow', 'Dining'), t('restaurant.head.title', 'Restaurant & Café'), t('restaurant.head.sub', '100% vegetarian. Village Rajasthani flavours, tandoor classics and an all-day café.'), im('restaurant.head.img', 'assets/images/food-thali-rajasthan.jpg'));
?>
<section class="sec">
  <div class="wrap">
    <div class="split" style="margin-bottom:4.5rem">
      <div class="rv"><span class="eyebrow eyebrow--dark"><?= et('restaurant.intro.eyebrow', 'Our kitchen') ?></span><h2><?= et('restaurant.intro.title', 'The food of the Jawai villages') ?></h2>
        <p><?= et('restaurant.intro.p1', 'In the villages around Jawai a meal is simple and generous: bajra roti pressed on the griddle and served with ghee and red garlic chutney, ker sangri cooked with desert beans and berries, dal and baati, kadhi, and a glass of cold chaas. We cook these dishes the way they are made at home in Rajasthan.') ?></p>
        <p><?= et('restaurant.intro.p2', 'Round it out with tandoor favourites, North Indian gravies, fresh breads and a proper kulhad chai. The whole kitchen is vegetarian, and Jain preparations are available on request.') ?></p>
        <ul class="checks" style="color:var(--ink-2)"><li><?= icon('check', 16) ?><?= et('restaurant.intro.c1', '100% pure vegetarian') ?></li><li><?= icon('check', 16) ?><?= et('restaurant.intro.c2', 'Jain options on request') ?></li><li><?= icon('check', 16) ?><?= et('restaurant.intro.c3', 'Café, chai & cold drinks') ?></li><li><?= icon('check', 16) ?><?= et('restaurant.intro.c4', 'Group, party & catering menus') ?></li></ul>
        <div class="hero__cta"><a class="btn btn--gold" href="#book"><?= et('restaurant.intro.b1', 'Reserve a table') ?></a><a class="btn btn--line" href="<?= url('book.php?type=event') ?>"><?= et('restaurant.intro.b2', 'Plan a party') ?></a></div></div>
      <div class="duo rv d1"><figure class="frame"><img src="<?= imgu('restaurant.intro.img1', 'assets/images/food-dal-baati-churma.jpg') ?>" alt="Rajasthani vegetarian bowls at Jawai Mumal restaurant, Bera" loading="lazy"></figure><figure class="frame"><img src="<?= imgu('restaurant.intro.img2', 'assets/images/food-thali-rajasthan.jpg') ?>" alt="" loading="lazy"></figure></div>
    </div>

    <div class="sechead"><span class="eyebrow eyebrow--dark"><?= et('restaurant.village.eyebrow', 'From the village kitchen') ?></span><h2><?= et('restaurant.village.title', 'Four things to try') ?></h2></div>
    <div class="fac" style="margin-bottom:4.5rem">
      <div class="fac__item rv"><span class="fac__ic"><?= icon('flame', 24) ?></span><div><h3><?= et('restaurant.v1.title', 'Bajra roti with ghee') ?></h3><p><?= et('restaurant.v1.text', 'Pearl-millet flatbread, the everyday bread of Rajasthan, with white butter, ghee and garlic chutney.') ?></p></div></div>
      <div class="fac__item rv d1"><span class="fac__ic"><?= icon('leaf', 24) ?></span><div><h3><?= et('restaurant.v2.title', 'Ker sangri') ?></h3><p><?= et('restaurant.v2.text', 'A desert classic of wild beans and berries, tangy and spiced, best with bajra roti.') ?></p></div></div>
      <div class="fac__item rv d2"><span class="fac__ic"><?= icon('utensils', 24) ?></span><div><h3><?= et('restaurant.v3.title', 'Dal baati churma') ?></h3><p><?= et('restaurant.v3.text', 'Baked wheat baatis dunked in ghee with five-lentil dal and sweet churma, the dish Rajasthan is known for.') ?></p></div></div>
      <div class="fac__item rv"><span class="fac__ic"><?= icon('coffee', 24) ?></span><div><h3><?= et('restaurant.v4.title', 'Kulhad chai & masala chaas') ?></h3><p><?= et('restaurant.v4.text', 'Ginger-cardamom chai in a clay cup, and spiced buttermilk for hot afternoons.') ?></p></div></div>
    </div>

    <div class="sechead"><span class="eyebrow eyebrow--dark"><?= et('restaurant.menu.eyebrow', 'Menu') ?></span><h2><?= et('restaurant.menu.title', 'What we cook') ?></h2><p><span class="vd" style="vertical-align:-2px"></span> <?= et('restaurant.menu.legend', 'Pure vegetarian kitchen · Prices in rupees, taxes extra') ?></p></div>
    <div class="chips" data-filter-group=".dish[data-cat]">
      <button class="chip" data-filter="all" aria-pressed="true"><?= et('restaurant.menu.all', 'All') ?></button>
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
<section class="sec sec--sand" id="book" style="padding-top:3rem"><div class="wrap"><div class="sechead" style="margin-bottom:1.5rem"><h2><?= et('restaurant.book.title', 'Reserve a table') ?></h2></div><?php include __DIR__ . '/includes/booking_panel.php'; ?></div></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
