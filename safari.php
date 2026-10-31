<?php
require_once __DIR__ . '/includes/db.php';
$pageTitle = 'Jawai Leopard Safari'; $pageDesc = 'Sunrise and sunset leopard safaris, Jawai Dam birding and Rabari village visits, arranged by Jawai Mumal in Bera.';
$active = 'safari'; $bookTab = 'safari';
$safaris = rows("SELECT * FROM jm_safaris WHERE status='active' ORDER BY sort_order, id");
include __DIR__ . '/includes/header.php';
page_head('Safari', 'Jawai Leopard Safari', 'Granite hills, open jeeps and local trackers who know every rock.', 'assets/images/safari-sunset.jpg');
?>
<section class="sec">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark">How it works</span><h2>Choose a safari, we handle the rest</h2><p>Tell us the date and guests — we arrange the jeep, guide and timing. Safari slots depend on weather and availability, and we confirm everything personally.</p></div>
    <?php foreach ($safaris as $s): ?>
      <article class="row rv" id="s<?= (int)$s['id'] ?>">
        <div class="row__img"><img src="<?= e(img($s['image'])) ?>" alt="<?= e($s['title']) ?>" loading="lazy"></div>
        <div>
          <?php if ($s['badge']): ?><span class="pill" style="display:inline-block;margin-bottom:.6rem"><?= e($s['badge']) ?></span><?php endif; ?>
          <h3><?= e($s['title']) ?></h3>
          <ul class="meta"><li><?= icon('clock', 16) ?><?= e($s['duration']) ?></li><li><?= icon('sun', 16) ?><?= e($s['timing']) ?></li><li><?= icon('car', 16) ?><?= e($s['vehicle']) ?></li><li><?= icon('compass', 16) ?><?= e($s['difficulty']) ?></li></ul>
          <p><?= e($s['description']) ?></p>
          <ul class="checks"><?php foreach (lines($s['highlights']) as $h): ?><li><?= icon('check', 16) ?><?= e($h) ?></li><?php endforeach; ?></ul>
          <div class="row__foot">
            <div style="display:flex;gap:2rem;flex-wrap:wrap"><div class="price"><small>per person</small><?= money($s['price_pp']) ?></div><div class="price"><small>private jeep</small><?= money($s['private_price']) ?></div></div>
            <a class="btn btn--gold" href="#book" data-open-tab="safari" data-safari="<?= (int)$s['id'] ?>">Request this safari <?= icon('arrow-right', 16) ?></a>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<section class="sec sec--sand">
  <div class="wrap">
    <div class="sechead"><span class="eyebrow eyebrow--dark">Safari tips</span><h2>Make the most of it</h2></div>
    <div class="fac">
      <div class="fac__item"><span class="fac__ic"><?= icon('sunrise', 24) ?></span><div><h3>Go early or late</h3><p>Leopards are most active at dawn and dusk, and the light is best for photos.</p></div></div>
      <div class="fac__item"><span class="fac__ic"><?= icon('leaf', 24) ?></span><div><h3>Wear earthy colours</h3><p>Browns, khaki and olive keep you comfortable and blend in. Carry a light jacket for mornings.</p></div></div>
      <div class="fac__item"><span class="fac__ic"><?= icon('camera', 24) ?></span><div><h3>Bring a zoom lens</h3><p>Sightings are at a respectful distance. Binoculars help too — ask us if you need a pair.</p></div></div>
      <div class="fac__item"><span class="fac__ic"><?= icon('shield-check', 24) ?></span><div><h3>Respect the wild</h3><p>Stay seated, keep voices low and follow your guide. Sightings are never guaranteed.</p></div></div>
      <div class="fac__item"><span class="fac__ic"><?= icon('droplets', 24) ?></span><div><h3>Stay hydrated</h3><p>Carry water and a hat, especially in summer months.</p></div></div>
      <div class="fac__item"><span class="fac__ic"><?= icon('calendar-check', 24) ?></span><div><h3>Book ahead</h3><p>Weekends and winter holidays fill quickly — send a request early.</p></div></div>
    </div>
  </div>
</section>
<section class="sec" id="book" style="padding-top:3rem"><div class="wrap"><div class="sechead" style="margin-bottom:1.5rem"><h2>Request a safari</h2></div><?php include __DIR__ . '/includes/booking_panel.php'; ?></div></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
