<?php
require_once __DIR__ . '/includes/db.php';
$active = 'home';
$bookTab = 'stay';
include __DIR__ . '/includes/header.php';

$featRooms = rows("SELECT * FROM jm_rooms WHERE status='active' ORDER BY featured DESC, sort_order, id LIMIT 3");
$safaris   = rows("SELECT * FROM jm_safaris WHERE status='active' ORDER BY sort_order, id LIMIT 3");
$dishes    = rows("SELECT * FROM jm_menu WHERE available=1 AND image<>'' ORDER BY sort_order, id LIMIT 3");
$attrs     = rows("SELECT * FROM jm_attractions WHERE status='active' ORDER BY sort_order, id LIMIT 6");
$gallery   = rows("SELECT * FROM jm_gallery ORDER BY sort_order, id LIMIT 6");
$quotes    = rows("SELECT * FROM jm_testimonials WHERE status='visible' ORDER BY sort_order, id LIMIT 3");
$faqs      = rows("SELECT * FROM jm_faqs ORDER BY sort_order, id LIMIT 6");
$soon = opening_soon();
$fac = array_map(fn($l) => array_pad(explode('|', $l, 3), 3, ''), lines(setting('facilities')));
$lowest = (int)val("SELECT MIN(price) FROM jm_rooms WHERE status='active'");
?>

<!-- ============ HERO ============ -->
<section class="hero">
  <img class="hero__bg" src="<?= e(img(setting('hero_image'))) ?>" alt="" fetchpriority="high">
  <div class="wrap">
    <div class="hero__in">
      <?php if ($soon): ?><div class="badge-soon"><i></i><?= e(setting('opening_label')) ?></div><?php endif; ?>
      <span class="eyebrow"><?= e(setting('hero_eyebrow')) ?></span>
      <h1><?= e(setting('hero_title')) ?></h1>
      <p><?= e(setting('hero_text')) ?></p>
      <div class="hero__cta">
        <a class="btn btn--gold" href="#book"><?= $soon ? 'Pre-book your stay' : 'Book your stay' ?> <?= icon('arrow-right', 18) ?></a>
        <a class="btn btn--ghost" href="<?= url('safari.php') ?>"><?= icon('compass', 18) ?> Jawai safari</a>
      </div>
    </div>
  </div>
</section>

<!-- ============ BOOKING / ENQUIRY ============ -->
<section class="book" id="book" aria-label="Booking and enquiry">
  <div class="wrap"><?php include __DIR__ . '/includes/booking_panel.php'; ?></div>
</section>

<!-- ============ ABOUT ============ -->
<section class="sec" id="about">
  <div class="wrap">
    <div class="split">
      <div class="rv">
        <span class="eyebrow eyebrow--dark">Welcome</span>
        <h2><?= e(setting('about_title')) ?></h2>
        <p><?= e(setting('about_text')) ?></p>
        <p><?= e(setting('about_text2')) ?></p>
        <p style="color:var(--gold-d);font-weight:500"><?= e(setting('founders_line')) ?></p>
        <div class="hero__cta"><a class="link" href="<?= url('contact.php') ?>">Meet the team &amp; find us <?= icon('arrow-right', 18) ?></a></div>
      </div>
      <div class="duo rv d1">
        <figure class="frame"><img src="<?= e(img(setting('property_image'))) ?>" alt="The Jawai Mumal building in Bera village" loading="lazy"><figcaption>Jawai Mumal, Bera</figcaption></figure>
        <figure class="frame"><img src="<?= e(img(setting('about_image'))) ?>" alt="Jawai Mumal hotel entrance at dusk" loading="lazy"><figcaption>Evening view</figcaption></figure>
      </div>
    </div>
    <div class="stats rv">
      <?php for ($i = 1; $i <= 4; $i++): ?><div class="stat"><b><?= e(setting("stat{$i}_value")) ?></b><span><?= e(setting("stat{$i}_label")) ?></span></div><?php endfor; ?>
    </div>
  </div>
</section>

<!-- ============ THREE PILLARS ============ -->
<section class="sec sec--sand">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark">One address, three experiences</span><h2>Stay. Dine. Explore.</h2><p>Everything for a Jawai trip, looked after by one local team.</p></div>
    <div class="cards3">
      <a class="pillar rv" href="<?= url('rooms.php') ?>"><img src="<?= e(img('assets/images/room-heritage.jpg')) ?>" alt="" loading="lazy"><div class="pillar__in"><div class="pillar__ic"><?= icon('bed', 24) ?></div><h3>Hotel</h3><p>Comfortable air-conditioned rooms and suites<?= $lowest ? ' from ' . money($lowest) . ' a night' : '' ?>.</p><span class="link">View rooms <?= icon('arrow-right', 16) ?></span></div></a>
      <a class="pillar rv d1" href="<?= url('restaurant.php') ?>"><img src="<?= e(img('assets/images/food-thali.jpg')) ?>" alt="" loading="lazy"><div class="pillar__in"><div class="pillar__ic"><?= icon('utensils', 24) ?></div><h3>Restaurant</h3><p>Rajasthani classics, tandoor grills and a relaxed all-day café.</p><span class="link">See the menu <?= icon('arrow-right', 16) ?></span></div></a>
      <a class="pillar rv d2" href="<?= url('safari.php') ?>"><img src="<?= e(img('assets/images/leopard-log.jpg')) ?>" alt="" loading="lazy"><div class="pillar__in"><div class="pillar__ic"><?= icon('car', 24) ?></div><h3>Jawai Safari</h3><p>Sunrise and sunset leopard drives with local trackers.</p><span class="link">Explore safaris <?= icon('arrow-right', 16) ?></span></div></a>
    </div>
  </div>
</section>

<!-- ============ ROOMS ============ -->
<section class="sec">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark">Rooms &amp; suites</span><h2>A comfortable base in Bera</h2><p>Clean, quiet and well-equipped. Rates shown are indicative and confirmed by our team.</p></div>
    <div class="grid">
      <?php foreach ($featRooms as $i => $r): ?>
        <article class="card rv d<?= $i ?>">
          <a class="card__img" href="<?= url('room.php?slug=' . urlencode($r['slug'])) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e(img($r['image'])) ?>" alt="" loading="lazy"><span class="tag"><?= e($r['category']) ?></span></a>
          <div class="card__body">
            <h3><a href="<?= url('room.php?slug=' . urlencode($r['slug'])) ?>" style="text-decoration:none"><?= e($r['name']) ?></a></h3>
            <p><?= e($r['tagline']) ?></p>
            <ul class="meta"><li><?= icon('users', 16) ?><?= e($r['capacity']) ?></li><li><?= icon('bed', 16) ?><?= e($r['beds']) ?></li><li><?= icon('mountain', 16) ?><?= e($r['view_type']) ?></li></ul>
            <div class="card__foot"><div class="price"><small>from</small><?= money($r['price']) ?> <span>/ night</span></div><a class="btn btn--dark btn--sm" href="<?= url('room.php?slug=' . urlencode($r['slug'])) ?>">Details</a></div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    <p style="text-align:center;margin-top:2.2rem"><a class="btn btn--line" href="<?= url('rooms.php') ?>">See all rooms <?= icon('arrow-right', 18) ?></a></p>
  </div>
</section>

<!-- ============ FACILITIES ============ -->
<section class="sec sec--sand">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark">Good to know</span><h2>Comfort, without the fuss</h2></div>
    <div class="fac">
      <?php foreach ($fac as $i => $f): ?>
        <div class="fac__item rv d<?= $i % 3 ?>"><span class="fac__ic"><?= icon(trim($f[0]), 24) ?></span><div><h3><?= e($f[1]) ?></h3><p><?= e($f[2]) ?></p></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ SAFARI ============ -->
<section class="sec sec--dark">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow">Jawai Safari</span><h2>Leopards, granite hills &amp; open skies</h2><p>Jawai is one of the few places where leopards can regularly be seen living among rocky hills and village life. We arrange guided drives with local trackers.</p></div>
    <?php foreach ($safaris as $s): ?>
      <article class="row rv">
        <div class="row__img"><img src="<?= e(img($s['image'])) ?>" alt="<?= e($s['title']) ?>" loading="lazy"></div>
        <div>
          <h3><?= e($s['title']) ?></h3>
          <ul class="meta"><li><?= icon('clock', 16) ?><?= e($s['duration']) ?></li><li><?= icon('sun', 16) ?><?= e($s['timing']) ?></li><li><?= icon('car', 16) ?><?= e($s['vehicle']) ?></li></ul>
          <p><?= e($s['description']) ?></p>
          <div class="row__foot"><div class="price"><small>from</small><?= money($s['price_pp']) ?> <span>/ person</span></div>
            <a class="btn btn--gold btn--sm" href="<?= url('book.php?type=safari&safari=' . (int)$s['id']) ?>">Request this safari</a></div>
        </div>
      </article>
    <?php endforeach; ?>
    <p style="text-align:center;margin-top:2rem"><a class="btn btn--ghost" href="<?= url('safari.php') ?>">All safari experiences <?= icon('arrow-right', 18) ?></a></p>
  </div>
</section>

<!-- ============ RESTAURANT ============ -->
<section class="sec">
  <div class="wrap split split--rev">
    <div class="duo rv">
      <?php foreach (array_slice($dishes, 0, 2) as $d): ?><figure class="frame"><img src="<?= e(img($d['image'])) ?>" alt="<?= e($d['name']) ?>" loading="lazy"></figure><?php endforeach; ?>
    </div>
    <div class="rv d1">
      <span class="eyebrow eyebrow--dark">Restaurant &amp; café</span>
      <h2>Honest Rajasthani food, cooked fresh</h2>
      <p>From slow-cooked laal maas and dal baati churma to tandoor grills, breads and desserts — plus an easy all-day café for chai, coffee and snacks. Vegetarian and non-vegetarian menus, with Jain options on request.</p>
      <ul class="checks" style="color:var(--ink-2)">
        <li><?= icon('check', 16) ?>Veg &amp; non-veg menus</li><li><?= icon('check', 16) ?>Café &amp; fresh drinks</li><li><?= icon('check', 16) ?>Group &amp; party menus</li><li><?= icon('check', 16) ?>Table reservations</li>
      </ul>
      <div class="hero__cta"><a class="btn btn--dark" href="<?= url('restaurant.php') ?>">View the menu</a><a class="btn btn--line" href="<?= url('book.php?type=dine') ?>">Reserve a table</a></div>
    </div>
  </div>
</section>

<!-- ============ EXPLORE JAWAI ============ -->
<section class="sec sec--sand">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark">Explore Jawai</span><h2>Wild hills, ancient forts, living culture</h2><p>Base yourself in Bera and reach leopard hills, a reservoir full of birds and some of Rajasthan's finest heritage.</p></div>
    <div class="grid">
      <?php foreach ($attrs as $i => $a): ?>
        <article class="card attr rv d<?= $i % 3 ?>">
          <div class="card__img"><img src="<?= e(img($a['image'])) ?>" alt="" loading="lazy"><span class="tag"><?= e($a['category']) ?></span></div>
          <div class="card__body"><span class="dist"><?= icon('map-pin', 14) ?><?= e($a['distance']) ?></span><h3><?= e($a['name']) ?></h3><p><?= e($a['description']) ?></p></div>
        </article>
      <?php endforeach; ?>
    </div>
    <p style="text-align:center;margin-top:2.2rem"><a class="btn btn--line" href="<?= url('jawai.php') ?>">The complete Jawai guide <?= icon('arrow-right', 18) ?></a></p>
  </div>
</section>

<!-- ============ EVENTS ============ -->
<section class="sec">
  <div class="wrap">
    <div class="cta rv">
      <img src="<?= e(img(setting('events_image'))) ?>" alt="" loading="lazy">
      <span class="eyebrow">Party &amp; personal bookings</span>
      <h2><?= e(setting('events_title')) ?></h2>
      <p><?= e(setting('events_text')) ?></p>
      <div class="hero__cta"><a class="btn btn--gold" href="<?= url('book.php?type=event') ?>">Plan an event <?= icon('arrow-right', 18) ?></a><a class="btn btn--ghost" href="<?= e(tel(setting('owner1_phone'))) ?>"><?= icon('phone', 18) ?> <?= e(setting('owner1_phone')) ?></a></div>
    </div>
  </div>
</section>

<!-- ============ GALLERY ============ -->
<?php if ($gallery): ?>
<section class="sec sec--sand">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark">Gallery</span><h2>A look around</h2></div>
    <div class="mason rv">
      <?php foreach ($gallery as $g): ?><a href="<?= e(img($g['image'])) ?>" data-title="<?= e($g['title']) ?>"><img src="<?= e(img($g['image'])) ?>" alt="<?= e($g['title']) ?>" loading="lazy"><span><?= e($g['title']) ?></span></a><?php endforeach; ?>
    </div>
    <p style="text-align:center;margin-top:2rem"><a class="btn btn--line" href="<?= url('gallery.php') ?>">Open the gallery</a></p>
  </div>
</section>
<div class="lb" id="lb" role="dialog" aria-modal="true" aria-label="Image viewer"><button class="lb__x" aria-label="Close"><?= icon('x', 22) ?></button><button class="lb__p" aria-label="Previous"><?= icon('chevron-left', 22) ?></button><img alt=""><button class="lb__n" aria-label="Next"><?= icon('chevron-right', 22) ?></button><p></p></div>
<?php endif; ?>

<!-- ============ TESTIMONIALS (only real, approved reviews) ============ -->
<?php if ($quotes): ?>
<section class="sec">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark">Guest words</span><h2>What guests say</h2></div>
    <div class="grid">
      <?php foreach ($quotes as $i => $t): ?>
        <figure class="quote rv d<?= $i ?>" style="margin:0"><div class="stars" aria-label="<?= (int)$t['rating'] ?> out of 5"><?php for ($s = 0; $s < (int)$t['rating']; $s++) echo icon('star', 16); ?></div><blockquote style="margin:0 0 1rem"><?= e($t['quote']) ?></blockquote><figcaption><strong><?= e($t['name']) ?></strong><br><small style="color:var(--muted)"><?= e($t['origin']) ?></small></figcaption></figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ FAQ ============ -->
<section class="sec <?= $quotes ? 'sec--sand' : '' ?>">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark">Questions</span><h2>Before you book</h2></div>
    <div class="faq rv">
      <?php foreach ($faqs as $f): ?><details><summary><?= e($f['question']) ?><?= icon('chevron-down', 22) ?></summary><p><?= e($f['answer']) ?></p></details><?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ FIND US ============ -->
<section class="sec sec--dark">
  <div class="wrap split">
    <div class="rv">
      <span class="eyebrow">Find us</span>
      <h2>Bera, on the road to Jawai</h2>
      <p style="white-space:pre-line"><?= e(setting('address')) ?></p>
      <ul class="footer__contact" style="list-style:none;padding:0;display:grid;gap:1rem;margin:1.5rem 0">
        <?php foreach ([['owner1', 'phone'], ['owner2', 'phone']] as $o): ?>
          <li style="display:flex;gap:.8rem"><?= icon('phone', 20) ?><span><a style="font:600 1.4rem var(--serif);text-decoration:none" href="<?= e(tel(setting($o[0] . '_phone'))) ?>"><?= e(setting($o[0] . '_phone')) ?></a><br><small style="opacity:.7"><?= e(setting($o[0] . '_name')) ?> · <?= e(setting($o[0] . '_role')) ?></small></span></li>
        <?php endforeach; ?>
      </ul>
      <div class="hero__cta"><a class="btn btn--gold" href="<?= e(wa_link('Hello Jawai Mumal')) ?>" target="_blank" rel="noopener"><?= icon('whatsapp', 18) ?> WhatsApp</a><a class="btn btn--ghost" href="<?= url('contact.php') ?>">Directions &amp; contact</a></div>
    </div>
    <iframe class="map rv d1" title="Map to <?= e(setting('site_name')) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q=<?= urlencode(setting('map_query')) ?>&output=embed"></iframe>
  </div>
</section>

<script type="application/ld+json"><?= json_encode([
  '@context' => 'https://schema.org', '@type' => 'Hotel', 'name' => setting('site_name'), 'url' => setting('site_url'),
  'image' => img(setting('hero_image')), 'telephone' => setting('owner1_phone'), 'description' => setting('meta_description'),
  'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Bera', 'addressRegion' => 'Rajasthan', 'addressCountry' => 'IN'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php include __DIR__ . '/includes/footer.php'; ?>
