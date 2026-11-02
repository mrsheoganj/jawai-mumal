<?php
require_once __DIR__ . '/includes/db.php';
$active = 'home';
$bookTab = 'stay';
$slides    = rows("SELECT * FROM jm_slides WHERE status='active' ORDER BY sort_order, id");
if (!$slides) $slides = [['image' => 'assets/images/hotel-front.jpg', 'eyebrow' => 'Bera · Jawai · Rajasthan', 'title' => setting('site_name'), 'text' => setting('site_tagline'), 'cta_label' => 'Book your stay', 'cta_url' => '#book', 'cta2_label' => '', 'cta2_url' => '', 'device' => 'both', 'id' => 0]];
// Separate banner sets: computers / big screens and phones. A set with no slides falls back to the other one.
$forD = array_values(array_filter($slides, fn($s) => in_array($s['device'] ?? 'both', ['both', 'desktop'], true)));
$forM = array_values(array_filter($slides, fn($s) => in_array($s['device'] ?? 'both', ['both', 'mobile'], true)));
if (!$forD) $forD = $forM; if (!$forM) $forM = $forD;
$firstD = $forD[0] ?? null; $firstM = $forM[0] ?? null;
$inD = array_flip(array_column($forD, 'id')); $inM = array_flip(array_column($forM, 'id'));
$preloads = [];
$imgD = $firstD ? $firstD['image'] : null; $imgM = $firstM ? (trim((string)($firstM['image_mobile'] ?? '')) ?: $firstM['image']) : null;
if ($imgD && $imgM && $imgD === $imgM) $preloads[] = [$imgD, ''];
else { if ($imgD) $preloads[] = [$imgD, '(min-width: 701px)']; if ($imgM) $preloads[] = [$imgM, '(max-width: 700px)']; }
include __DIR__ . '/includes/header.php';

$featRooms = rows("SELECT * FROM jm_rooms WHERE status='active' ORDER BY featured DESC, sort_order, id LIMIT 3");
$safaris   = rows("SELECT * FROM jm_safaris WHERE status='active' ORDER BY sort_order, id LIMIT 3");
$attrs     = rows("SELECT * FROM jm_attractions WHERE status='active' ORDER BY sort_order, id LIMIT 6");
$gallery   = rows("SELECT * FROM jm_gallery ORDER BY sort_order, id LIMIT 6");
$quotes    = rows("SELECT * FROM jm_testimonials WHERE status='visible' ORDER BY sort_order, id LIMIT 3");
$faqs      = rows("SELECT * FROM jm_faqs ORDER BY sort_order, id LIMIT 6");
$fac = array_map(fn($l) => array_pad(explode('|', $l, 3), 3, ''), lines(setting('facilities')));
$lowest = (int)val("SELECT MIN(price) FROM jm_rooms WHERE status='active'");
$link = fn($u) => ($u === '' ? '#' : (preg_match('#^(https?:|\#|mailto:|tel:)#', $u) ? $u : url($u)));
$slides = array_values(array_filter($slides, fn($s) => isset($inD[$s['id']]) || isset($inM[$s['id']])));
$n = count($slides);
?>

<!-- ============ HERO (slides are managed in Admin > Home banner slides) ============ -->
<section class="hero" id="hero" aria-roledescription="carousel" aria-label="Featured">
  <?php foreach ($slides as $i => $s):
    $d = isset($inD[$s['id']]); $m = isset($inM[$s['id']]);
    $first = ($firstD && $s['id'] === $firstD['id']) || ($firstM && $s['id'] === $firstM['id']);
    $cls = 'hslide' . ($d ? ' dv-d' : '') . ($m ? ' dv-m' : '') . ($first ? ' is-active' : '');
    $isH1 = $firstD && $s['id'] === $firstD['id']; ?>
  <div class="<?= $cls ?>" role="group" aria-roledescription="slide" aria-label="Slide <?= $i + 1 ?>"<?= $first ? '' : ' aria-hidden="true"' ?>>
    <picture>
      <?php if (trim((string)($s['image_mobile'] ?? '')) !== ''): ?><source media="(max-width: 700px)" srcset="<?= e(img($s['image_mobile'])) ?>"><?php endif; ?>
      <img class="hslide__bg" src="<?= e(img($s['image'])) ?>" alt="<?= e(trim(strip_tags((string)($s['title'] ?? ''))) ?: setting('site_name') . ' – Bera, Jawai') ?>"<?= $first ? ' fetchpriority="high"' : ' loading="lazy"' ?>>
    </picture>
    <div class="wrap">
      <div class="hero__in">
        <?php if ($s['eyebrow']): ?><span class="eyebrow"><?= e($s['eyebrow']) ?></span><?php endif; ?>
        <<?= $isH1 ? 'h1' : 'h2 class="h1"' ?>><?= e($s['title']) ?></<?= $isH1 ? 'h1' : 'h2' ?>>
        <?php if ($s['text']): ?><p><?= e($s['text']) ?></p><?php endif; ?>
        <div class="hero__cta">
          <?php if ($s['cta_label']): ?><a class="btn btn--gold" href="<?= e($link($s['cta_url'])) ?>"><?= e($s['cta_label']) ?> <?= icon('arrow-right', 18) ?></a><?php endif; ?>
          <?php if ($s['cta2_label']): ?><a class="btn btn--ghost" href="<?= e($link($s['cta2_url'])) ?>"><?= e($s['cta2_label']) ?></a><?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <div class="hero__ctl" hidden>
    <button type="button" class="hctl" data-hero="prev" aria-label="Previous slide"><?= icon('chevron-left', 20) ?></button>
    <div class="hdots" role="tablist" aria-label="Choose slide"></div>
    <button type="button" class="hctl" data-hero="next" aria-label="Next slide"><?= icon('chevron-right', 20) ?></button>
    <button type="button" class="hctl" data-hero="pause" aria-label="Pause slideshow"><?= icon('minus', 18) ?></button>
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
        <span class="eyebrow eyebrow--dark"><?= et('home.about.eyebrow', 'Welcome') ?></span>
        <h2><?= et('home.about.title', 'A hotel and kitchen in the heart of Jawai') ?></h2>
        <p><?= et('home.about.p1', 'Jawai Mumal is a family-run hotel and restaurant in Bera village on the Bera–Bali road, in Pali district of Rajasthan. Granite hills, village lanes, Rabari herders and wild leopards all share this landscape, and we are right in the middle of it.') ?></p>
        <p><?= et('home.about.p2', 'Stay in a comfortable room, eat a pure-vegetarian Rajasthani meal cooked the village way, and head out for a sunrise or sunset leopard safari with local trackers. Good food, comfortable stay, happy moments, a place where every guest feels special.') ?></p>
        <p style="color:var(--gold-d);font-weight:500"><?= et('home.about.founders', 'Founded by Devendra Singh & Nilesh Singh Chouhan') ?></p>
        <div class="hero__cta"><a class="link" href="<?= url('contact.php') ?>"><?= et('home.about.link', 'Meet the team & find us') ?> <?= icon('arrow-right', 18) ?></a></div>
      </div>
      <div class="duo duo--one rv d1">
        <figure class="frame"><img src="<?= imgu('home.about.img1', 'assets/images/hotel-front.jpg') ?>" alt="<?= et('home.about.alt1', 'Jawai Mumal hotel in Bera village') ?>" loading="lazy"></figure>
      </div>
    </div>
  </div>
</section>


<!-- ============ THREE PILLARS ============ -->
<section class="sec sec--sand">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark"><?= et('home.pillars.eyebrow', 'One address, three experiences') ?></span><h2><?= et('home.pillars.title', 'Stay. Dine. Explore.') ?></h2><p><?= et('home.pillars.sub', 'Everything for a Jawai trip, looked after by one local team.') ?></p></div>
    <div class="cards3 snap-m">
      <a class="pillar rv" href="<?= url('rooms.php') ?>"><img src="<?= imgu('home.pillar1.img', 'assets/images/hotel-day.jpg') ?>" alt="Jawai Mumal hotel in Bera by day" loading="lazy"><div class="pillar__in"><div class="pillar__ic"><?= icon('bed', 24) ?></div><h3><?= et('home.pillar1.title', 'Hotel') ?></h3><p><?= et('home.pillar1.text', 'Comfortable air-conditioned rooms in Bera village.') ?><?= $lowest ? ' ' . et('home.pillar1.from', 'From') . ' ' . money($lowest) . ' ' . et('home.pillar1.night', 'a night.') : '' ?></p><span class="link"><?= et('home.pillar1.link', 'View rooms') ?> <?= icon('arrow-right', 16) ?></span></div></a>
      <a class="pillar rv d1" href="<?= url('restaurant.php') ?>"><img src="<?= imgu('home.pillar2.img', 'assets/images/food-thali-rajasthan.jpg') ?>" alt="Pure vegetarian Rajasthani thali at Jawai Mumal" loading="lazy"><div class="pillar__in"><div class="pillar__ic"><?= icon('utensils', 24) ?></div><h3><?= et('home.pillar2.title', 'Restaurant') ?></h3><p><?= et('home.pillar2.text', '100% vegetarian Rajasthani village food, tandoor and a relaxed café.') ?></p><span class="link"><?= et('home.pillar2.link', 'See the menu') ?> <?= icon('arrow-right', 16) ?></span></div></a>
      <a class="pillar rv d2" href="<?= url('safari.php') ?>"><img src="<?= imgu('home.pillar3.img', 'assets/images/jawai-leopard-portrait.jpg') ?>" alt="Leopard resting on a log in the Jawai hills" loading="lazy"><div class="pillar__in"><div class="pillar__ic"><?= icon('car', 24) ?></div><h3><?= et('home.pillar3.title', 'Jawai Safari') ?></h3><p><?= et('home.pillar3.text', 'Dawn and dusk leopard drives in open 4x4 jeeps with local trackers.') ?></p><span class="link"><?= et('home.pillar3.link', 'Explore safaris') ?> <?= icon('arrow-right', 16) ?></span></div></a>
    </div>
  </div>
</section>

<!-- ============ ROOMS ============ -->
<section class="sec">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark"><?= et('home.rooms.eyebrow', 'Rooms') ?></span><h2><?= et('home.rooms.title', 'A comfortable base in Bera') ?></h2><p><?= et('home.rooms.sub', 'Clean, quiet and well-equipped, minutes from the Jawai hills. Rates start from the price shown and vary with the season; our team confirms the exact rate for your dates.') ?></p></div>
    <div class="grid snap-m">
      <?php foreach ($featRooms as $i => $r): ?>
        <article class="card rv d<?= $i ?>">
          <a class="card__img" href="<?= url('room.php?slug=' . urlencode($r['slug'])) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e(img($r['image'])) ?>" alt="" loading="lazy"><span class="tag"><?= e($r['category']) ?></span></a>
          <div class="card__body">
            <h3><a href="<?= url('room.php?slug=' . urlencode($r['slug'])) ?>" style="text-decoration:none"><?= e($r['name']) ?></a></h3>
            <p><?= e($r['tagline']) ?></p>
            <ul class="meta"><li><?= icon('users', 16) ?><?= e($r['capacity']) ?></li><li><?= icon('bed', 16) ?><?= e($r['beds']) ?></li><li><?= icon('mountain', 16) ?><?= e($r['view_type']) ?></li></ul>
            <div class="card__foot"><div class="price"><small><?= et('home.rooms.from', 'from') ?></small><?= money($r['price']) ?> <span>/ <?= et('home.rooms.night', 'night') ?></span></div><a class="btn btn--dark btn--sm" href="<?= url('room.php?slug=' . urlencode($r['slug'])) ?>"><?= et('home.rooms.details', 'Details') ?></a></div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    <p style="text-align:center;margin-top:2.2rem"><a class="btn btn--line" href="<?= url('rooms.php') ?>"><?= et('home.rooms.all', 'See all rooms') ?> <?= icon('arrow-right', 18) ?></a></p>
  </div>
</section>

<!-- ============ FACILITIES ============ -->
<section class="sec sec--sand">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark"><?= et('home.fac.eyebrow', 'Good to know') ?></span><h2><?= et('home.fac.title', 'Comfort, without the fuss') ?></h2></div>
    <div class="fac">
      <?php foreach ($fac as $i => $f): ?>
        <div class="fac__item rv d<?= $i % 3 ?>"><span class="fac__ic"><?= icon(trim($f[0]), 24) ?></span><div><h3><?= e($f[1]) ?></h3><p><?= e($f[2]) ?></p></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ RESTAURANT / VILLAGE FOOD ============ -->
<section class="sec">
  <div class="wrap split split--rev">
    <div class="duo rv">
      <figure class="frame"><img src="<?= imgu('home.food.img1', 'assets/images/food-gatte.jpg') ?>" alt="Gatte ki sabzi, a Rajasthani vegetarian classic" loading="lazy"></figure>
      <figure class="frame"><img src="<?= imgu('home.food.img2', 'assets/images/food-ghevar.jpg') ?>" alt="Ghevar, a Rajasthani sweet" loading="lazy"></figure>
    </div>
    <div class="rv d1">
      <span class="eyebrow eyebrow--dark"><?= et('home.food.eyebrow', 'Restaurant & café · 100% vegetarian') ?></span>
      <h2><?= et('home.food.title', 'Rajasthani food, the Jawai village way') ?></h2>
      <p><?= et('home.food.p1', 'Dal baati churma dunked in ghee, ker sangri from the desert, gatte ki sabzi, kadhi and bajra roti hot off the griddle with garlic chutney, the food of the villages around Jawai, cooked fresh in our kitchen.') ?></p>
      <p><?= et('home.food.p2', 'Add tandoor classics, mirchi vada and pyaaz kachori, kulhad chai and cold chaas, and finish with moong dal halwa or malpua. Jain options on request.') ?></p>
      <ul class="checks" style="color:var(--ink-2)">
        <li><?= icon('check', 16) ?><?= et('home.food.c1', 'Pure vegetarian kitchen') ?></li><li><?= icon('check', 16) ?><?= et('home.food.c2', 'Village-style thali') ?></li><li><?= icon('check', 16) ?><?= et('home.food.c3', 'Café, chai & fresh drinks') ?></li><li><?= icon('check', 16) ?><?= et('home.food.c4', 'Group & party menus') ?></li>
      </ul>
      <div class="hero__cta"><a class="btn btn--dark" href="<?= url('restaurant.php') ?>"><?= et('home.food.b1', 'View the menu') ?></a><a class="btn btn--line" href="<?= url('book.php?type=dine') ?>"><?= et('home.food.b2', 'Reserve a table') ?></a></div>
    </div>
  </div>
</section>

<!-- ============ SAFARI ============ -->
<section class="sec sec--dark">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow"><?= et('home.safari.eyebrow', 'Jawai Safari') ?></span><h2><?= et('home.safari.title', 'Leopards, granite hills & open skies') ?></h2><p><?= et('home.safari.sub', 'Jawai is one of the few places where leopards are regularly seen resting on open granite hills, beside villages and farms. Our drives use open 4x4 jeeps, a driver and a local tracker, at dawn and at dusk.') ?></p></div>
    <?php foreach ($safaris as $s): ?>
      <article class="row rv">
        <div class="row__img"><img src="<?= e(img($s['image'])) ?>" alt="<?= e($s['title']) ?>" loading="lazy"></div>
        <div>
          <h3><?= e($s['title']) ?></h3>
          <ul class="meta"><li><?= icon('clock', 16) ?><?= e($s['duration']) ?></li><li><?= icon('sun', 16) ?><?= e($s['timing']) ?></li><li><?= icon('car', 16) ?><?= e($s['vehicle']) ?></li></ul>
          <p><?= e($s['description']) ?></p>
          <div class="row__foot"><span class="muted" style="font-size:.95rem"><?= et('safari.enquire', 'Price shared on enquiry') ?></span>
            <a class="btn btn--gold btn--sm" href="<?= url('book.php?type=safari&safari=' . (int)$s['id']) ?>"><?= et('home.safari.btn', 'Request this safari') ?></a></div>
        </div>
      </article>
    <?php endforeach; ?>
    <p style="text-align:center;margin-top:2rem"><a class="btn btn--line" href="<?= url('safari.php') ?>"><?= et('home.safari.all', 'All safari experiences') ?> <?= icon('arrow-right', 18) ?></a></p>
  </div>
</section>

<!-- ============ EXPLORE JAWAI ============ -->
<section class="sec sec--sand">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark"><?= et('home.explore.eyebrow', 'Explore Jawai') ?></span><h2><?= et('home.explore.title', 'Wild hills, village life, ancient forts') ?></h2><p><?= et('home.explore.sub', 'Base yourself in Bera and reach the leopard hills, a reservoir full of birds, Rabari villages and some of Rajasthan\'s finest heritage.') ?></p></div>
    <div class="grid snap-m">
      <?php foreach ($attrs as $i => $a): ?>
        <article class="card attr rv d<?= $i % 3 ?>">
          <div class="card__img"><img src="<?= e(img($a['image'])) ?>" alt="<?= e($a['name']) ?>" loading="lazy"><span class="tag"><?= e($a['category']) ?></span></div>
          <div class="card__body"><span class="dist"><?= icon('map-pin', 14) ?><?= e($a['distance']) ?></span><h3><?= e($a['name']) ?></h3><p><?= e($a['description']) ?></p></div>
        </article>
      <?php endforeach; ?>
    </div>
    <p style="text-align:center;margin-top:2.2rem"><a class="btn btn--line" href="<?= url('jawai.php') ?>"><?= et('home.explore.all', 'The complete Jawai guide') ?> <?= icon('arrow-right', 18) ?></a></p>
  </div>
</section>

<!-- ============ HOW TO REACH ============ -->
<?php include __DIR__ . '/includes/routes.php'; ?>

<!-- ============ EVENTS ============ -->
<section class="sec">
  <div class="wrap">
    <div class="cta rv">
      <img src="<?= imgu('home.events.img', 'assets/images/jawai-village-celebration.jpg') ?>" alt="Wedding guests in Jawai Bera, Rajasthan" loading="lazy">
      <span class="eyebrow"><?= et('home.events.eyebrow', 'Party & personal bookings') ?></span>
      <h2><?= et('home.events.title', 'Parties, celebrations & private functions') ?></h2>
      <p><?= et('home.events.text', 'Birthdays, family get-togethers, small weddings, corporate meets or a quiet personal celebration, party and personal bookings are both available. Tell us your date, guest count and menu ideas and we will plan the rest.') ?></p>
      <div class="hero__cta"><a class="btn btn--gold" href="<?= url('book.php?type=event') ?>"><?= et('home.events.btn', 'Plan an event') ?> <?= icon('arrow-right', 18) ?></a><a class="wa-ico wa-ico--light" target="_blank" rel="noopener" href="<?= e(wa_link(t('home.events.wamsg', 'Hello Jawai Mumal, I would like to plan a party or function.'))) ?>" aria-label="<?= e(t('wa.aria', 'Chat on WhatsApp')) ?>" title="<?= e(t('wa.aria', 'Chat on WhatsApp')) ?>"><?= icon('whatsapp', 26) ?></a></div>
    </div>
  </div>
</section>

<!-- ============ GALLERY ============ -->
<?php if ($gallery): ?>
<section class="sec sec--sand">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark"><?= et('home.gallery.eyebrow', 'Gallery') ?></span><h2><?= et('home.gallery.title', 'A look around') ?></h2></div>
    <div class="mason rv">
      <?php foreach ($gallery as $g): ?><a href="<?= e(img($g['image'])) ?>" data-title="<?= e($g['title']) ?>"><img src="<?= e(img($g['image'])) ?>" alt="<?= e($g['title']) ?>" loading="lazy"><span><?= e($g['title']) ?></span></a><?php endforeach; ?>
    </div>
    <p style="text-align:center;margin-top:2rem"><a class="btn btn--line" href="<?= url('gallery.php') ?>"><?= et('home.gallery.btn', 'Open the gallery') ?></a></p>
  </div>
</section>
<div class="lb" id="lb" role="dialog" aria-modal="true" aria-label="Image viewer"><button class="lb__x" aria-label="Close"><?= icon('x', 22) ?></button><button class="lb__p" aria-label="Previous"><?= icon('chevron-left', 22) ?></button><img alt=""><button class="lb__n" aria-label="Next"><?= icon('chevron-right', 22) ?></button><p></p></div>
<?php endif; ?>

<!-- ============ TESTIMONIALS (only real, approved reviews) ============ -->
<?php if ($quotes): ?>
<section class="sec">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark"><?= et('home.reviews.eyebrow', 'Guest words') ?></span><h2><?= et('home.reviews.title', 'What guests say') ?></h2></div>
    <div class="grid">
      <?php foreach ($quotes as $i => $t): ?>
        <figure class="quote rv d<?= $i ?>"><div class="stars" aria-label="<?= (int)$t['rating'] ?> out of 5"><?php for ($s = 0; $s < (int)$t['rating']; $s++) echo icon('star', 16); ?></div><blockquote style="margin:0 0 1rem"><?= e($t['quote']) ?></blockquote><figcaption><strong><?= e($t['name']) ?></strong><br><small style="color:var(--muted)"><?= e($t['origin']) ?></small></figcaption></figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ FAQ ============ -->
<section class="sec <?= $quotes ? 'sec--sand' : '' ?>">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark"><?= et('home.faq.eyebrow', 'Questions') ?></span><h2><?= et('home.faq.title', 'Before you book') ?></h2></div>
    <div class="faq rv">
      <?php foreach ($faqs as $f): ?><details><summary><?= e($f['question']) ?><?= icon('chevron-down', 22) ?></summary><p><?= e($f['answer']) ?></p></details><?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ FIND US ============ -->
<section class="sec sec--dark">
  <div class="wrap split">
    <div class="rv">
      <span class="eyebrow"><?= et('home.find.eyebrow', 'Find us') ?></span>
      <h2><?= et('home.find.title', 'Bera, on the road to Jawai') ?></h2>
      <p style="white-space:pre-line"><?= e(setting('address')) ?></p>
      <ul class="footer__contact" style="list-style:none;padding:0;display:grid;gap:1rem;margin:1.5rem 0">
        <?php foreach (['owner1', 'owner2'] as $o): ?>
          <li style="display:flex;gap:.9rem;align-items:center"><span style="flex:1"><strong style="font:600 1.15rem var(--serif)"><?= e(setting($o . '_name')) ?></strong><br><small style="color:var(--muted)"><?= e(setting($o . '_role')) ?> · <?= e(setting($o . '_phone')) ?></small></span><a class="call-ico" href="tel:<?= e(preg_replace('/[^\d+]/', '', setting($o . '_phone'))) ?>" aria-label="<?= e(t('call.aria', 'Call')) ?> <?= e(setting($o . '_name')) ?>" title="<?= e(t('call.aria', 'Call')) ?> <?= e(setting($o . '_name')) ?>"><?= icon('phone-call', 24) ?></a></li>
        <?php endforeach; ?>
      </ul>
      <div class="hero__cta"><a class="btn btn--line" href="<?= e(maps_open_url()) ?>" target="_blank" rel="noopener"><?= icon('navigation', 18) ?> <?= et('home.find.btn', 'Get directions') ?></a></div>
    </div>
    <iframe class="map rv d1" title="Map to <?= e(setting('site_name')) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="<?= e(map_embed_src()) ?>"></iframe>
  </div>
</section>

<?php if ($fq = seo_faq_ld($faqs)) seo_ld($fq); ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
