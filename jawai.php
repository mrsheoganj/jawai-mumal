<?php
require_once __DIR__ . '/includes/db.php';
$pageTitle = 'Explore Jawai — Leopards, Dam, Villages & How to Reach'; $pageDesc = 'A guide to Jawai in Pali, Rajasthan: leopard hills, Jawai Dam, wildlife, Rabari culture, best time to visit and how to reach Bera from Udaipur and Jodhpur.';
$active = 'jawai';
$attrs = rows("SELECT * FROM jm_attractions WHERE status='active' ORDER BY sort_order, id");
$groups = []; foreach ($attrs as $a) { $groups[$a['category']][] = $a; }
include __DIR__ . '/includes/header.php';
page_head(t('jawai.head.eyebrow', 'Explore Jawai'), t('jawai.head.title', 'Explore Jawai'), t('jawai.head.sub', 'Leopard hills, a bird-filled reservoir, village life and the heritage of Mewar and Marwar within reach.'), im('jawai.head.img', 'assets/images/hills-green.jpg'));
?>
<section class="sec">
  <div class="wrap split">
    <div class="rv"><span class="eyebrow eyebrow--dark"><?= et('jawai.about.eyebrow', 'About Jawai') ?></span><h2><?= et('jawai.about.title', 'Leopards, granite hills and a working countryside') ?></h2>
      <p><?= et('jawai.about.p1', 'Jawai is a stretch of granite hills around the Jawai Dam in Pali district, roughly halfway between Udaipur and Jodhpur. Leopards den in the caves and rest on the rocks here, a short walk from villages, farms and hill temples. The Rabari herders who graze their animals on these hills have lived alongside them for generations.') ?></p>
      <p><?= et('jawai.about.p2', 'It is not a fenced national park — it is a lived-in landscape. That is why the leopards are so visible, and why how we behave around them matters. Jawai Mumal sits in Bera village on the Bera–Bali road, in the middle of it all.') ?></p></div>
    <div class="duo rv d1"><figure class="frame"><img src="<?= imgu('jawai.about.img1', 'assets/images/leopard-log.jpg') ?>" alt="" loading="lazy"></figure><figure class="frame"><img src="<?= imgu('jawai.about.img2', 'assets/images/hills-terrace.jpg') ?>" alt="" loading="lazy"></figure></div>
  </div>
  <div class="wrap"><div class="stats rv">
    <?php for ($i = 1; $i <= 4; $i++): ?><div class="stat"><b><?= e(setting("stat{$i}_value")) ?></b><span><?= e(setting("stat{$i}_label")) ?></span></div><?php endfor; ?>
  </div></div>
</section>

<section class="sec sec--sand">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark"><?= et('jawai.wild.eyebrow', 'Wildlife') ?></span><h2><?= et('jawai.wild.title', 'More than leopards') ?></h2><p><?= et('jawai.wild.sub', 'Leopards are the headline, but the hills and the dam are alive all day.') ?></p></div>
    <div class="fac">
      <div class="fac__item rv"><span class="fac__ic"><?= icon('mountain', 24) ?></span><div><h3><?= et('jawai.w1.title', 'Leopards') ?></h3><p><?= et('jawai.w1.text', 'Resident leopards rest on granite ledges and cave mouths, especially at first and last light. Estimates put the wider area at roughly 50–70 animals; there is no official count.') ?></p></div></div>
      <div class="fac__item rv d1"><span class="fac__ic"><?= icon('eye', 24) ?></span><div><h3><?= et('jawai.w2.title', 'Hills & scrub') ?></h3><p><?= et('jawai.w2.text', 'Striped hyena, nilgai, chinkara, jungle cat, Indian grey wolf and desert fox are all possible, along with langurs and peafowl.') ?></p></div></div>
      <div class="fac__item rv d2"><span class="fac__ic"><?= icon('bird', 24) ?></span><div><h3><?= et('jawai.w3.title', 'Birds') ?></h3><p><?= et('jawai.w3.text', 'More than 300 bird species have been recorded around Jawai, from raptors over the hills to winter flamingos, demoiselle cranes and bar-headed geese on the dam.') ?></p></div></div>
      <div class="fac__item rv"><span class="fac__ic"><?= icon('waves', 24) ?></span><div><h3><?= et('jawai.w4.title', 'Jawai Dam') ?></h3><p><?= et('jawai.w4.text', 'Completed in 1957, the reservoir fills after the monsoon. Mugger crocodiles bask on its sandbanks and the shoreline is a fine place for slow birdwatching.') ?></p></div></div>
      <div class="fac__item rv d1"><span class="fac__ic"><?= icon('users', 24) ?></span><div><h3><?= et('jawai.w5.title', 'Rabari culture') ?></h3><p><?= et('jawai.w5.text', 'Rabari herding families, known for red turbans and silver jewellery, graze their animals on these hills and have long lived with leopards.') ?></p></div></div>
      <div class="fac__item rv d2"><span class="fac__ic"><?= icon('moon', 24) ?></span><div><h3><?= et('jawai.w6.title', 'Night skies') ?></h3><p><?= et('jawai.w6.text', 'With little light pollution, the skies over Jawai are superb for stargazing.') ?></p></div></div>
    </div>
  </div>
</section>

<section class="sec sec--dark">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow"><?= et('jawai.season.eyebrow', 'Best time to visit') ?></span><h2><?= et('jawai.season.title', 'Jawai through the seasons') ?></h2><p><?= et('jawai.season.sub', 'Leopards live here all year — each season simply offers a different Jawai. October to March is the main safari season.') ?></p></div>
    <div class="season" style="grid-template-columns:repeat(3,1fr)">
      <div class="rv"><?= icon('moon', 28) ?><h3><?= et('jawai.s1.title', 'Winter') ?></h3><small><?= et('jawai.s1.when', 'November – February') ?></small><p><?= et('jawai.s1.text', 'Cool, clear mornings, often 8–12°C at dawn. The reservoir is full and migratory birds arrive in thousands; leopards bask on the warm rock. The best season for safaris and the busiest.') ?></p></div>
      <div class="rv d1"><?= icon('sun', 28) ?><h3><?= et('jawai.s2.title', 'Summer') ?></h3><small><?= et('jawai.s2.when', 'March – June') ?></small><p><?= et('jawai.s2.text', 'Hot and dry, often above 40°C by May. Water levels drop, crocodiles bask on sandbanks and wildlife gathers near what water is left. Drives run very early and very late.') ?></p></div>
      <div class="rv d2"><?= icon('droplets', 28) ?><h3><?= et('jawai.s3.title', 'Monsoon') ?></h3><small><?= et('jawai.s3.when', 'July – September') ?></small><p><?= et('jawai.s3.text', 'Rain turns the granite hills green and refills the dam. Dramatic skies and very few visitors; some tracks get muddy, so plans can change at short notice.') ?></p></div>
    </div>
  </div>
</section>

<?php foreach ($groups as $cat => $list): ?>
<section class="sec <?= array_search($cat, array_keys($groups)) % 2 ? '' : 'sec--sand' ?>">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark"><?= e($cat) ?></span><h2><?= e(['Wildlife' => 'Wildlife & reserve', 'Nature' => 'Nature & landscapes', 'Culture' => 'People & culture', 'Heritage' => 'Heritage day trips', 'Experience' => 'Experiences'][$cat] ?? $cat) ?></h2></div>
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
    <div class="sechead rv"><span class="eyebrow eyebrow--dark"><?= et('jawai.reach.eyebrow', 'Getting here') ?></span><h2><?= et('jawai.reach.title', 'How far is Bera?') ?></h2><p><?= et('jawai.reach.sub', 'Approximate road distances to Bera. Times depend on traffic and road works.') ?></p></div>
    <div class="ccards" style="grid-template-columns:repeat(auto-fit,minmax(210px,1fr))">
      <div class="ccard rv"><div class="pillar__ic"><?= icon('landmark', 22) ?></div><h3><?= et('jawai.r1.title', 'Sumerpur Jawai Bandh station') ?></h3><p><?= et('jawai.r1.text', 'About 18 km · 30 min') ?></p></div>
      <div class="ccard rv d1"><div class="pillar__ic"><?= icon('landmark', 22) ?></div><h3><?= et('jawai.r2.title', 'Falna station') ?></h3><p><?= et('jawai.r2.text', 'About 37 km · 50 min') ?></p></div>
      <div class="ccard rv d2"><div class="pillar__ic"><?= icon('building', 22) ?></div><h3><?= et('jawai.r3.title', 'Udaipur city') ?></h3><p><?= et('jawai.r3.text', 'About 115 km · 2.5 hrs') ?></p></div>
      <div class="ccard rv"><div class="pillar__ic"><?= icon('globe', 22) ?></div><h3><?= et('jawai.r4.title', 'Udaipur airport') ?></h3><p><?= et('jawai.r4.text', 'About 130 km · 2.5–3 hrs') ?></p></div>
      <div class="ccard rv d1"><div class="pillar__ic"><?= icon('globe', 22) ?></div><h3><?= et('jawai.r5.title', 'Jodhpur airport') ?></h3><p><?= et('jawai.r5.text', 'About 175 km · 3.5 hrs') ?></p></div>
    </div>
    <p style="text-align:center;margin-top:2rem"><a class="btn btn--dark" href="<?= url('contact.php') ?>"><?= et('jawai.reach.btn', 'Get directions & contact') ?> <?= icon('arrow-right', 18) ?></a></p>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
