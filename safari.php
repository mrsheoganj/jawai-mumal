<?php
require_once __DIR__ . '/includes/db.php';
$pageTitle = 'Jawai Leopard Safari'; $pageDesc = 'Dawn and dusk leopard safaris in open 4x4 jeeps with local trackers, Jawai Dam birding, photography drives and Rabari village visits — from Jawai Mumal, Bera.';
$active = 'safari'; $bookTab = 'safari';
$safaris = rows("SELECT * FROM jm_safaris WHERE status='active' ORDER BY sort_order, id");
include __DIR__ . '/includes/header.php';
page_head(t('safari.head.eyebrow', 'Safari'), t('safari.head.title', 'Jawai Leopard Safari'), t('safari.head.sub', 'Granite hills, open jeeps and local trackers who know every rock.'), im('safari.head.img', 'assets/images/safari-sunset.jpg'));
?>
<section class="sec">
  <div class="wrap">
    <div class="split" style="margin-bottom:4rem">
      <div class="rv"><span class="eyebrow eyebrow--dark"><?= et('safari.intro.eyebrow', 'Our safaris') ?></span><h2><?= et('safari.intro.title', 'Tracking leopards the patient way') ?></h2>
        <p><?= et('safari.intro.p1', 'Jawai\'s leopards do not hide in dense forest. They rest on and inside great granite domes, and the land tells you where they are: a langur barking from a ridge, a peacock\'s sharp alarm call, fresh pugmarks in a sandy riverbed. Our trackers read each sign, position the jeep quietly, switch off the engine and wait.') ?></p>
        <p><?= et('safari.intro.p2', 'Drives run twice a day, in the morning from about 6 to 9 AM and in the evening from about 4 to 7 PM, when leopards are most often out on the rocks. A sighting is never guaranteed — but over a two or three day stay the chances are very good.') ?></p></div>
      <div class="duo rv d1"><figure class="frame"><img src="<?= imgu('safari.intro.img1', 'assets/images/leopard-log.jpg') ?>" alt="" loading="lazy"></figure><figure class="frame"><img src="<?= imgu('safari.intro.img2', 'assets/images/hills-terrace.jpg') ?>" alt="" loading="lazy"></figure></div>
    </div>
    <div class="fac" style="margin-bottom:4.5rem">
      <div class="fac__item rv"><span class="fac__ic"><?= icon('car', 24) ?></span><div><h3><?= et('safari.f1.title', 'Open 4x4 jeeps') ?></h3><p><?= et('safari.f1.text', 'Light, raised-seat jeeps that take the village tracks and granite slopes, with up to six guests per vehicle.') ?></p></div></div>
      <div class="fac__item rv d1"><span class="fac__ic"><?= icon('compass', 24) ?></span><div><h3><?= et('safari.f2.title', 'Driver + local tracker') ?></h3><p><?= et('safari.f2.text', 'Every drive has a driver and a tracker from the villages who knows these hills.') ?></p></div></div>
      <div class="fac__item rv d2"><span class="fac__ic"><?= icon('eye', 24) ?></span><div><h3><?= et('safari.f3.title', 'Binoculars on board') ?></h3><p><?= et('safari.f3.text', 'Binoculars in the jeep, camera beanbags on request, and tea or coffee in the field.') ?></p></div></div>
    </div>

    <div class="sechead rv"><span class="eyebrow eyebrow--dark"><?= et('safari.list.eyebrow', 'Choose your drive') ?></span><h2><?= et('safari.list.title', 'Safari experiences') ?></h2><p><?= et('safari.list.sub', 'Tell us the date and guests — we arrange the jeep, tracker and timing and confirm everything personally. Prices shown are per person in a shared jeep, or for a private jeep.') ?></p></div>
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
            <div style="display:flex;gap:2rem;flex-wrap:wrap"><div class="price"><small><?= et('safari.pp', 'per person') ?></small><?= money($s['price_pp']) ?></div><div class="price"><small><?= et('safari.private', 'private jeep') ?></small><?= money($s['private_price']) ?></div></div>
            <a class="btn btn--gold" href="#book" data-open-tab="safari" data-safari="<?= (int)$s['id'] ?>"><?= et('safari.request', 'Request this safari') ?> <?= icon('arrow-right', 16) ?></a>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<section class="sec sec--dark">
  <div class="wrap split">
    <div class="rv"><span class="eyebrow"><?= et('safari.ethics.eyebrow', 'Respect for the wild') ?></span><h2><?= et('safari.ethics.title', 'How we watch wildlife') ?></h2>
      <p><?= et('safari.ethics.p', 'Jawai works because people and leopards have shared these hills for generations. We keep it that way.') ?></p>
      <ul class="checks"><?php foreach (lines(t('safari.ethics.list', "We never block an animal's path or crowd a sighting\nNo calls, baiting or chasing — ever\nEngines off while we watch, from a respectful distance\nSmall groups and no convoys\nWe leave before an animal is disturbed\nLocal trackers and village families benefit directly")) as $li): ?><li><?= icon('check', 16) ?><?= e($li) ?></li><?php endforeach; ?></ul></div>
    <div class="frame rv d1" style="aspect-ratio:4/3"><img src="<?= imgu('safari.ethics.img', 'assets/images/safari-sunset.jpg') ?>" alt="" loading="lazy"></div>
  </div>
</section>

<section class="sec sec--sand">
  <div class="wrap">
    <div class="sechead"><span class="eyebrow eyebrow--dark"><?= et('safari.tips.eyebrow', 'Safari tips') ?></span><h2><?= et('safari.tips.title', 'Make the most of it') ?></h2></div>
    <div class="fac">
      <div class="fac__item rv"><span class="fac__ic"><?= icon('sunrise', 24) ?></span><div><h3><?= et('safari.t1.title', 'Go early or late') ?></h3><p><?= et('safari.t1.text', 'Leopards are most active at dawn and dusk, and the light is best for photos.') ?></p></div></div>
      <div class="fac__item rv d1"><span class="fac__ic"><?= icon('leaf', 24) ?></span><div><h3><?= et('safari.t2.title', 'Wear earthy colours') ?></h3><p><?= et('safari.t2.text', 'Khaki, olive and stone shades, with warm layers for winter mornings (dawn can be 8–12°C), a hat and closed shoes.') ?></p></div></div>
      <div class="fac__item rv d2"><span class="fac__ic"><?= icon('camera', 24) ?></span><div><h3><?= et('safari.t3.title', 'Bring a zoom lens') ?></h3><p><?= et('safari.t3.text', 'A 70–200 mm or 100–400 mm lens suits most sightings. We carry beanbags.') ?></p></div></div>
      <div class="fac__item rv"><span class="fac__ic"><?= icon('shield-check', 24) ?></span><div><h3><?= et('safari.t4.title', 'Stay seated, keep quiet') ?></h3><p><?= et('safari.t4.text', 'Follow your tracker, keep voices low and never leave the jeep. Sightings are never guaranteed.') ?></p></div></div>
      <div class="fac__item rv d1"><span class="fac__ic"><?= icon('droplets', 24) ?></span><div><h3><?= et('safari.t5.title', 'Carry water & sunscreen') ?></h3><p><?= et('safari.t5.text', 'Dusty tracks and strong sun — bring water, a scarf and sunscreen.') ?></p></div></div>
      <div class="fac__item rv d2"><span class="fac__ic"><?= icon('calendar-check', 24) ?></span><div><h3><?= et('safari.t6.title', 'Book 3–4 drives') ?></h3><p><?= et('safari.t6.text', 'Morning and evening over two days gives a fair chance of excellent sightings. Winter weekends fill quickly.') ?></p></div></div>
    </div>
  </div>
</section>
<section class="sec" id="book" style="padding-top:3rem"><div class="wrap"><div class="sechead" style="margin-bottom:1.5rem"><h2><?= et('safari.book.title', 'Request a safari') ?></h2></div><?php include __DIR__ . '/includes/booking_panel.php'; ?></div></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
