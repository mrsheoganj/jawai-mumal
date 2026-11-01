<?php
require_once __DIR__ . '/includes/db.php';
$pageTitle = 'Rooms & Suites'; $pageDesc = 'Comfortable air-conditioned rooms and suites at Jawai Mumal, Bera — a base for leopard safaris in Jawai.';
$active = 'stay';
$rooms = rows("SELECT * FROM jm_rooms WHERE status='active' ORDER BY sort_order, id");
$cats = array_values(array_unique(array_column($rooms, 'category')));
seo_ld(seo_breadcrumb_ld([['Rooms & suites', 'rooms']]));
include __DIR__ . '/includes/header.php';
page_head(t('rooms.head.eyebrow', 'Stay'), t('rooms.head.title', 'Rooms & Suites'), t('rooms.head.sub', 'Quiet, clean and comfortable — a proper base for your Jawai trip.'), im('rooms.head.img', 'assets/images/hotel-front.jpg'));
?>
<section class="sec">
  <div class="wrap">
    <div class="chips" data-filter-group=".card[data-cat]">
      <button class="chip" data-filter="all" aria-pressed="true"><?= et('rooms.all', 'All rooms') ?></button>
      <?php foreach ($cats as $c): ?><button class="chip" data-filter="<?= e($c) ?>" aria-pressed="false"><?= e($c) ?></button><?php endforeach; ?>
    </div>
    <div class="grid">
      <?php foreach ($rooms as $i => $r): ?>
        <article class="card rv d<?= $i % 3 ?>" data-cat="<?= e($r['category']) ?>">
          <a class="card__img" href="<?= url('room.php?slug=' . urlencode($r['slug'])) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e(img($r['image'])) ?>" alt="" loading="lazy"><span class="tag"><?= e($r['category']) ?></span></a>
          <div class="card__body">
            <h3><a href="<?= url('room.php?slug=' . urlencode($r['slug'])) ?>" style="text-decoration:none"><?= e($r['name']) ?></a></h3>
            <p><?= e($r['tagline']) ?></p>
            <ul class="meta"><li><?= icon('users', 16) ?><?= e($r['capacity']) ?></li><li><?= icon('bed', 16) ?><?= e($r['beds']) ?></li><li><?= icon('building', 16) ?><?= e($r['size']) ?></li><li><?= icon('mountain', 16) ?><?= e($r['view_type']) ?></li></ul>
            <div class="card__foot"><div class="price"><small><?= et('rooms.from', 'from') ?></small><?= money($r['price']) ?> <span>/ <?= et('rooms.night', 'night') ?></span></div>
              <div style="display:flex;gap:.5rem"><a class="btn btn--line btn--sm" href="<?= url('room.php?slug=' . urlencode($r['slug'])) ?>"><?= et('rooms.details', 'Details') ?></a><a class="btn btn--gold btn--sm" href="<?= url('book.php?type=stay&room=' . (int)$r['id']) ?>"><?= et('rooms.book', 'Book') ?></a></div></div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    <p class="demo-note" style="margin-top:1.5rem"><?= icon('shield-check', 16) ?> <?= et('rooms.note', 'Rates are per room per night, before GST. Our team confirms the final rate for your dates.') ?> GST: <?= e(setting('tax_percent')) ?>% up to ₹<?= number_format((int)setting('tax_threshold')) ?>, <?= e(setting('tax_percent_high')) ?>% above.</p>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
