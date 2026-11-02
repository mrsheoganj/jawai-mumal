<?php
require_once __DIR__ . '/includes/db.php';
$pageTitle = 'Policies & Terms'; $pageDesc = 'Booking, check-in, cancellation, safari and privacy policies for ' . setting('site_name') . ', Bera.';
$active = '';
$seoKey = 'policies';
seo_ld(seo_breadcrumb_ld([['Policies', 'policies']]));
include __DIR__ . '/includes/header.php';
page_head(t('policies.head.eyebrow', 'Good to know'), t('policies.head.title', 'Policies & Terms'), t('policies.head.sub', 'Simple, fair rules so everyone enjoys their stay.'), im('policies.head.img', 'assets/images/jawai-bullock-cart.jpg'));
$blocks = [
  ['calendar-check', t('policies.b1.title', 'Booking & confirmation'), t('policies.b1.text', 'A request on the website or WhatsApp is not a confirmed booking until our team confirms availability and the final rate by call or WhatsApp.
No payment is taken online. Advance payment, if needed, is arranged directly with the team.')],
  ['clock', t('policies.b2.title', 'Check-in & check-out'), t('policies.b2.text', 'Check-in from 12:00 PM and check-out by 11:00 AM. Early check-in or late check-out is subject to availability, please ask in advance.
All adult guests must show a valid photo ID at check-in. Foreign guests need their passport and visa details.')],
  ['refresh', t('policies.b3.title', 'Changes & cancellation'), t('policies.b3.text', 'Please tell us as early as possible if your plans change; we will try to move your dates free of charge, subject to availability.
Cancellation terms and any advance refund are agreed with the team when the booking is confirmed.')],
  ['bed', t('policies.b4.title', 'In the hotel'), t('policies.b4.text', 'Children are welcome. Please keep noise low after 10:00 PM and respect other guests.
Please do not smoke inside rooms. Damage to hotel property may be charged at cost.
Outside food and drinks are not permitted in the restaurant.')],
  ['car', t('policies.b5.title', 'Safaris'), t('policies.b5.text', 'Safari timings depend on weather, road conditions and availability. A wildlife sighting is never guaranteed.
Follow your tracker and driver, stay seated, keep voices low and never feed or call animals.')],
  ['shield-check', t('policies.b6.title', 'Privacy'), t('policies.b6.text', 'We use your name, phone number and email only to handle your request and contact you about it. We do not sell or share your details.
You can ask us to delete your request at any time.')],
];
?>
<section class="sec">
  <div class="wrap" style="max-width:900px">
    <div class="note"><?= icon('bell', 20) ?><span><?= et('policies.note', 'These are our standard house rules. Specific terms for your booking are confirmed by our team when we reply.') ?></span></div>
    <?php foreach ($blocks as $i => $b): ?>
      <div class="fac__item rv" style="margin:0 0 2.2rem"><span class="fac__ic"><?= icon($b[0], 24) ?></span><div><h3><?= e($b[1]) ?></h3><?php foreach (lines($b[2]) as $para): ?><p><?= e($para) ?></p><?php endforeach; ?></div></div>
    <?php endforeach; ?>
    <p class="muted"><?= et('policies.contact', 'Questions? Message us on WhatsApp') ?> <a class="wa-ico wa-ico--sm" href="<?= e(wa_link('Hello Jawai Mumal')) ?>" target="_blank" rel="noopener" aria-label="<?= e(t('wa.aria', 'Chat on WhatsApp')) ?>" title="<?= e(t('wa.aria', 'Chat on WhatsApp')) ?>"><?= icon('whatsapp', 20) ?></a></p>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
