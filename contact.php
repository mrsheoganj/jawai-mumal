<?php
require_once __DIR__ . '/includes/db.php';
$pageTitle = 'Contact & Directions'; $pageDesc = 'Call, WhatsApp or visit Jawai Mumal in Bera village, Bera–Bali road, Pali, Rajasthan.';
$active = 'contact'; $bookTab = 'enquiry';
include __DIR__ . '/includes/header.php';
page_head('Contact', 'Contact & Directions', 'Talk to the owners directly — we are happy to help plan your stay.', 'assets/images/hotel-front.jpg');
$owners = [['owner1_name', 'owner1_role', 'owner1_phone'], ['owner2_name', 'owner2_role', 'owner2_phone']];
?>
<section class="sec">
  <div class="wrap">
    <div class="ccards">
      <?php foreach ($owners as $i => $o): ?>
        <div class="ccard rv d<?= $i ?>"><div class="pillar__ic"><?= icon('phone', 24) ?></div><h3><?= e(setting($o[0])) ?></h3><p><?= e(setting($o[1])) ?></p><a class="big" href="<?= e(tel(setting($o[2]))) ?>"><?= e(setting($o[2])) ?></a><a class="link" href="<?= e(tel(setting($o[2]))) ?>">Call now <?= icon('arrow-right', 16) ?></a></div>
      <?php endforeach; ?>
      <div class="ccard rv d2"><div class="pillar__ic" style="background:#1f8f4e;color:#fff"><?= icon('whatsapp', 24) ?></div><h3>WhatsApp</h3><p>Fastest way to share dates and get rates.</p><a class="big" href="<?= e(wa_link('Hello Jawai Mumal')) ?>" target="_blank" rel="noopener">Chat with us</a><span class="demo-note"><?= e(setting('reception_hours')) ?></span></div>
    </div>
    <div class="split" style="margin-top:3.5rem;align-items:stretch">
      <div class="rv">
        <span class="eyebrow eyebrow--dark">Visit us</span><h2>Find Jawai Mumal</h2>
        <p style="white-space:pre-line;font-size:1.15rem"><?= e(setting('address')) ?></p>
        <ul class="checks" style="grid-template-columns:1fr;color:var(--ink-2)">
          <li><?= icon('clock', 16) ?>Check-in <?= e(setting('checkin_time')) ?> · Check-out <?= e(setting('checkout_time')) ?></li>
          <li><?= icon('car-park', 16) ?>Parking available</li><li><?= icon('wifi', 16) ?>Free Wi-Fi</li>
          <?php if (setting('email')): ?><li><?= icon('mail', 16) ?><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></li><?php endif; ?>
        </ul>
        <div class="hero__cta"><a class="btn btn--dark" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query=<?= urlencode(setting('map_query')) ?>"><?= icon('navigation', 18) ?> Open in Google Maps</a></div>
        <figure class="frame" style="margin-top:2rem;aspect-ratio:4/3"><img src="<?= e(img(setting('property_image'))) ?>" alt="Jawai Mumal building" loading="lazy" style="object-position:center 30%"></figure>
      </div>
      <iframe class="map rv d1" title="Map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q=<?= urlencode(setting('map_query')) ?>&output=embed"></iframe>
    </div>
  </div>
</section>
<section class="sec sec--sand" id="book" style="padding-top:3rem"><div class="wrap"><div class="sechead" style="margin-bottom:1.5rem"><h2>Send us a message</h2><p>Stay, table, safari, party or a simple question — pick a tab.</p></div><?php include __DIR__ . '/includes/booking_panel.php'; ?></div></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
