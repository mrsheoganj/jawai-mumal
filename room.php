<?php
require_once __DIR__ . '/includes/db.php';
$r = row("SELECT * FROM jm_rooms WHERE slug = ? AND status='active'", [$_GET['slug'] ?? '']);
if (!$r) { http_response_code(404); $pageTitle = 'Room not found'; $active = 'stay'; $bodyClass = 'nav-solid'; include __DIR__ . '/includes/header.php'; echo '<section class="sec" style="padding-top:10rem;text-align:center"><div class="wrap"><h1>Room not found</h1><p><a class="btn btn--dark" href="' . url('rooms.php') . '">See all rooms</a></p></div></section>'; include __DIR__ . '/includes/footer.php'; exit; }
$pageTitle = $r['name']; $pageDesc = $r['tagline'] . ' — ' . mb_substr($r['description'], 0, 140);
$ogImage = $r['image']; $active = 'stay'; $bookRoom = (int)$r['id']; $bookTab = 'stay';
$imgs = array_values(array_filter(array_merge([$r['image']], lines((string)$r['gallery']))));
$others = rows("SELECT * FROM jm_rooms WHERE status='active' AND id<>? ORDER BY sort_order LIMIT 3", [$r['id']]);
include __DIR__ . '/includes/header.php';
page_head(t('room.head.eyebrow', 'Stay'), $r['name'], $r['tagline'], $r['image']);
?>
<section class="sec">
  <div class="wrap split">
    <div class="rv">
      <div class="duo" style="grid-template-columns:1fr 1fr">
        <?php foreach (array_slice($imgs, 0, 2) as $k => $im): ?><figure class="frame" style="aspect-ratio:3/4"><img src="<?= e(img($im)) ?>" alt="<?= e($r['name']) ?> photo <?= $k + 1 ?>"></figure><?php endforeach; ?>
      </div>
    </div>
    <div class="rv d1">
      <span class="eyebrow eyebrow--dark"><?= e($r['category']) ?></span>
      <h2><?= e($r['name']) ?></h2>
      <p><?= e($r['description']) ?></p>
      <ul class="meta" style="font-size:1rem"><li><?= icon('users', 18) ?><?= e($r['capacity']) ?></li><li><?= icon('bed', 18) ?><?= e($r['beds']) ?></li><li><?= icon('building', 18) ?><?= e($r['size']) ?></li><li><?= icon('mountain', 18) ?><?= e($r['view_type']) ?></li></ul>
      <h3 style="margin-top:1.5rem"><?= et('room.inroom', 'In the room') ?></h3>
      <ul class="checks" style="color:var(--ink-2)"><?php foreach (lines($r['amenities']) as $a): ?><li><span style="color:var(--gold-d)"><?= icon(amenity_icon($a), 16) ?></span><?= e($a) ?></li><?php endforeach; ?></ul>
      <div class="price" style="margin:1.2rem 0"><small><?= et('room.from', 'from') ?></small><?= money($r['price']) ?> <span>/ <?= et('room.night', 'night') ?> + <?= e(rtrim(rtrim(number_format(gst_rate((int)$r['price']), 1), '0'), '.')) ?>% GST</span></div>
      <p class="demo-note"><?= icon('clock', 16) ?> Check-in <?= e(setting('checkin_time')) ?> · Check-out <?= e(setting('checkout_time')) ?></p>
    </div>
  </div>
</section>
<section class="sec sec--sand" id="book" style="padding-top:3rem">
  <div class="wrap"><div class="sechead" style="margin-bottom:1.5rem"><h2><?= et('room.request', 'Request this room') ?></h2></div><?php include __DIR__ . '/includes/booking_panel.php'; ?></div>
</section>
<?php if ($others): ?>
<section class="sec"><div class="wrap"><div class="sechead"><h2><?= et('room.others', 'Other rooms') ?></h2></div><div class="grid">
  <?php foreach ($others as $o): ?><article class="card"><a class="card__img" href="<?= url('room.php?slug=' . urlencode($o['slug'])) ?>"><img src="<?= e(img($o['image'])) ?>" alt="<?= e($o['name']) ?>" loading="lazy"></a><div class="card__body"><h3><?= e($o['name']) ?></h3><div class="card__foot"><div class="price"><small><?= et('room.from', 'from') ?></small><?= money($o['price']) ?></div><a class="btn btn--line btn--sm" href="<?= url('room.php?slug=' . urlencode($o['slug'])) ?>">Details</a></div></div></article><?php endforeach; ?>
</div></div></section>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
