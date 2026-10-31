<?php
require_once __DIR__ . '/includes/db.php';
$pageTitle = 'Explore Jawai'; $pageDesc = 'A guide to Jawai: leopard hills, Jawai Dam, Rabari culture, best time to visit and nearby heritage — from Bera, Pali, Rajasthan.';
$active = 'jawai';
$attrs = rows("SELECT * FROM jm_attractions WHERE status='active' ORDER BY sort_order, id");
$groups = []; foreach ($attrs as $a) { $groups[$a['category']][] = $a; }
include __DIR__ . '/includes/header.php';
page_head('Explore Jawai', 'Explore Jawai', 'Leopard hills, a bird-filled reservoir, Rabari culture and the heritage of Mewar and Marwar within reach.', 'assets/images/hills-green.jpg');
?>
<section class="sec">
  <div class="wrap split">
    <div class="rv"><span class="eyebrow eyebrow--dark">About Jawai</span><h2>Leopard country in the Aravallis</h2>
      <p>Jawai, in Pali district of Rajasthan, is known for its granite hillocks, caves and the leopards that live on them. Unlike a fenced national park, the hills here are shared with Rabari herding families, whose centuries-old culture of living alongside big cats draws wildlife lovers and photographers from around the world.</p>
      <p>The Jawai Dam, built on the Jawai river, is one of western Rajasthan's largest reservoirs. Its shores attract marsh crocodiles and many migratory and resident birds, and its water supports the villages and farmland around it.</p>
      <p>Jawai Mumal sits in Bera village, on the Bera–Bali road — a practical base for sunrise and sunset drives, village visits and day trips to Ranakpur, Kumbhalgarh and Mount Abu.</p></div>
    <div class="duo rv d1"><figure class="frame"><img src="<?= e(img('assets/images/leopard-log.jpg')) ?>" alt="Leopard resting" loading="lazy"></figure><figure class="frame"><img src="<?= e(img('assets/images/hills-terrace.jpg')) ?>" alt="Hills at dusk" loading="lazy"></figure></div>
  </div>
</section>

<section class="sec sec--dark">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow">Best time to visit</span><h2>Jawai through the seasons</h2><p>Leopards live here all year round — each season simply offers a different Jawai.</p></div>
    <div class="season">
      <div class="rv"><?= icon('sun', 28) ?><h3>Summer</h3><small>Mar – Jun</small><p>Hot days, but dry hills and thirsty wildlife can mean very good sightings. Go early and late.</p></div>
      <div class="rv d1"><?= icon('droplets', 28) ?><h3>Monsoon</h3><small>Jul – Sep</small><p>The hills turn green and the dam fills up. Fewer visitors, dramatic skies.</p></div>
      <div class="rv d2"><?= icon('leaf', 28) ?><h3>Post-monsoon</h3><small>Oct – Nov</small><p>Fresh air, clear light and comfortable days. Migratory birds begin to arrive.</p></div>
      <div class="rv d3"><?= icon('moon', 28) ?><h3>Winter</h3><small>Dec – Feb</small><p>The most popular season — cool mornings, pleasant afternoons, best for families and long drives.</p></div>
    </div>
  </div>
</section>

<?php foreach ($groups as $cat => $list): ?>
<section class="sec <?= array_search($cat, array_keys($groups)) % 2 ? '' : 'sec--sand' ?>">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark"><?= e($cat) ?></span><h2><?= e(['Wildlife' => 'Wildlife', 'Nature' => 'Nature & landscapes', 'Culture' => 'People & culture', 'Heritage' => 'Heritage day trips', 'Experience' => 'Experiences'][$cat] ?? $cat) ?></h2></div>
    <div class="grid">
      <?php foreach ($list as $i => $a): ?>
        <article class="card attr rv d<?= $i % 3 ?>"><div class="card__img"><img src="<?= e(img($a['image'])) ?>" alt="" loading="lazy"></div>
          <div class="card__body"><span class="dist"><?= icon('map-pin', 14) ?><?= e($a['distance']) ?></span><h3><?= e($a['name']) ?></h3><p><?= e($a['description']) ?></p></div></article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endforeach; ?>

<section class="sec">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark">Getting here</span><h2>How to reach Bera</h2><p>Distances and times are approximate and depend on traffic and route.</p></div>
    <div class="ccards">
      <div class="ccard rv"><div class="pillar__ic"><?= icon('navigation', 24) ?></div><h3>By road</h3><p>Bera lies on the Bera–Bali road in Pali district. Self-drive or taxi from Udaipur, Jodhpur or Mount Abu is the easiest route.</p></div>
      <div class="ccard rv d1"><div class="pillar__ic"><?= icon('landmark', 24) ?></div><h3>By train</h3><p>Falna and Marwar Junction are the nearest major railheads on the Delhi–Ahmedabad line. We can help arrange pick-ups.</p></div>
      <div class="ccard rv d2"><div class="pillar__ic"><?= icon('globe', 24) ?></div><h3>By air</h3><p>Udaipur and Jodhpur airports are the closest, each a few hours' drive away.</p></div>
    </div>
    <p style="text-align:center;margin-top:2rem"><a class="btn btn--dark" href="<?= url('contact.php') ?>">Get directions &amp; contact <?= icon('arrow-right', 18) ?></a></p>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
