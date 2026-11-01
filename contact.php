<?php
require_once __DIR__ . '/includes/db.php';
$pageTitle = 'Contact & Directions'; $pageDesc = 'Message or visit Jawai Mumal in Bera village, Bera–Bali road, Pali, Rajasthan.';
$active = 'contact'; $bookTab = 'enquiry';
include __DIR__ . '/includes/header.php';
page_head(t('contact.head.eyebrow', 'Contact'), t('contact.head.title', 'Contact & Directions'), t('contact.head.sub', 'Message us directly — we are happy to help plan your stay, safari or celebration.'), im('contact.head.img', 'assets/images/hotel-front.jpg'));
$owners = [['owner1_name', 'owner1_role', 'owner1_phone'], ['owner2_name', 'owner2_role', 'owner2_phone']];
?>
<section class="sec">
  <div class="wrap">
    <div class="ccards">
      <?php foreach ($owners as $i => $o): ?>
        <div class="ccard rv d<?= $i ?>"><div class="pillar__ic"><?= icon('user', 24) ?></div><h3><?= e(setting($o[0])) ?></h3><p><?= e(setting($o[1])) ?></p><span class="big"><?= e(setting($o[2])) ?></span>
          <div class="cbtns"><a class="wa-ico" target="_blank" rel="noopener" href="<?= e(wa_to(setting($o[2]), t('contact.chatmsg', 'Hello, I would like to know more about Jawai Mumal.'))) ?>" aria-label="<?= e(t('wa.aria', 'Chat on WhatsApp')) ?> — <?= e(setting($o[0])) ?>" title="WhatsApp <?= e(setting($o[0])) ?>"><?= icon('whatsapp', 26) ?></a></div></div>
      <?php endforeach; ?>
      <div class="ccard rv d2"><div class="pillar__ic" style="background:#1f8f4e;color:#fff"><?= icon('whatsapp', 24) ?></div><h3><?= et('contact.wa.title', 'WhatsApp') ?></h3><p><?= et('contact.wa.text', 'Fastest way to share dates and get rates.') ?></p><span class="demo-note"><?= e(setting('reception_hours')) ?></span>
        <div class="cbtns"><a class="wa-ico" target="_blank" rel="noopener" href="<?= e(wa_link(t('contact.wa.msg2', 'Hello Jawai Mumal, I would like to book. Please share availability and rates.'))) ?>" aria-label="<?= et('wa.aria', 'Chat on WhatsApp') ?>" title="<?= et('wa.aria', 'Chat on WhatsApp') ?>"><?= icon('whatsapp', 26) ?></a></div></div>
    </div>
    <div class="split" style="margin-top:3.5rem;align-items:stretch">
      <div class="rv">
        <span class="eyebrow eyebrow--dark"><?= et('contact.visit.eyebrow', 'Visit us') ?></span><h2><?= et('contact.visit.title', 'Find Jawai Mumal') ?></h2>
        <p style="white-space:pre-line;font-size:1.15rem"><?= e(setting('address')) ?></p>
        <ul class="checks" style="grid-template-columns:1fr;color:var(--ink-2)">
          <li><?= icon('clock', 16) ?><?= et('contact.v0', 'Check-in') ?> <?= e(setting('checkin_time')) ?> · <?= et('contact.v0b', 'Check-out') ?> <?= e(setting('checkout_time')) ?></li>
          <li><?= icon('car-park', 16) ?><?= et('contact.v1', 'Parking available') ?></li><li><?= icon('wifi', 16) ?><?= et('contact.v2', 'Free Wi-Fi') ?></li>
          <?php if (setting('email')): ?><li><?= icon('mail', 16) ?><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></li><?php endif; ?>
        </ul>
        <div class="hero__cta"><a class="btn btn--dark" target="_blank" rel="noopener" href="<?= e(maps_open_url()) ?>"><?= icon('navigation', 18) ?> <?= et('contact.maps', 'Open in Google Maps') ?></a></div>
        <figure class="frame" style="margin-top:2rem;aspect-ratio:4/3"><img src="<?= imgu('contact.img', 'assets/images/hotel-day.jpg') ?>" alt="Jawai Mumal hotel building" loading="lazy" style="object-position:center 30%"></figure>
      </div>
      <iframe class="map rv d1" title="Map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="<?= e(map_embed_src()) ?>"></iframe>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/routes.php'; ?>
<section class="sec sec--sand" id="book" style="padding-top:3rem"><div class="wrap"><div class="sechead" style="margin-bottom:1.5rem"><h2><?= et('contact.msg.title', 'Send us a message') ?></h2><p><?= et('contact.msg.sub', 'Stay, table, safari, party or a simple question — pick a tab.') ?></p></div><?php include __DIR__ . '/includes/booking_panel.php'; ?></div></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
