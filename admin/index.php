<?php
require __DIR__ . '/_init.php'; require_once __DIR__ . '/_crud.php';
$me = row('SELECT * FROM jm_admins WHERE id = ?', [$_SESSION['admin_id']]);
$defaultPw = false;
if (is_file(__DIR__ . '/../data/first-login.txt')) $defaultPw = true;   // random first-run password file still on the server
$c = fn($sql) => (int)val($sql);
$latest = rows('SELECT * FROM jm_bookings ORDER BY id DESC LIMIT 8');
admin_head('Dashboard', 'dashboard');
?>
<?php $sm = site_mode(); if ($sm !== 'live'): ?><div class="alert alert--warn"><strong><?= $sm === 'coming_soon' ? 'Coming soon mode is on.' : 'Maintenance mode is on.' ?></strong> Visitors cannot see the website yet. <a href="mode.php">Manage / go live →</a></div><?php endif; ?>
<?php if ($defaultPw): ?><div class="alert alert--warn"><strong>Finish securing your login.</strong> Change your password, then delete <code>data/first-login.txt</code> from the server (hPanel → File Manager). <a href="account.php">Change password →</a></div><?php endif; ?>
<div class="stats">
  <a class="stat" href="bookings.php?status=New"><span class="ic"><?= icon('bell', 24) ?></span><div><b><?= $c("SELECT COUNT(*) FROM jm_bookings WHERE status='New'") ?></b><span>New requests</span></div></a>
  <a class="stat" href="bookings.php?type=stay"><span class="ic"><?= icon('bed', 24) ?></span><div><b><?= $c("SELECT COUNT(*) FROM jm_bookings WHERE type='stay'") ?></b><span>Stay requests</span></div></a>
  <a class="stat" href="bookings.php?type=safari"><span class="ic"><?= icon('car', 24) ?></span><div><b><?= $c("SELECT COUNT(*) FROM jm_bookings WHERE type='safari'") ?></b><span>Safari requests</span></div></a>
  <a class="stat" href="bookings.php?type=event"><span class="ic"><?= icon('party', 24) ?></span><div><b><?= $c("SELECT COUNT(*) FROM jm_bookings WHERE type='event'") ?></b><span>Party / event</span></div></a>
  <a class="stat" href="rooms.php"><span class="ic"><?= icon('building', 24) ?></span><div><b><?= $c("SELECT COUNT(*) FROM jm_rooms WHERE status='active'") ?></b><span>Rooms live</span></div></a>
  <a class="stat" href="menu.php"><span class="ic"><?= icon('utensils', 24) ?></span><div><b><?= $c("SELECT COUNT(*) FROM jm_menu WHERE available=1") ?></b><span>Dishes on menu</span></div></a>
</div>
<div class="grid">
  <div class="c8"><div class="card"><h2>Latest requests</h2>
    <?php if ($latest): ?><div class="tablewrap" style="border:0"><table><thead><tr><th>Ref</th><th>Guest</th><th>For</th><th>Status</th></tr></thead><tbody>
      <?php foreach ($latest as $b): ?><tr><td><a href="bookings.php?q=<?= urlencode($b['code']) ?>"><?= e($b['code']) ?></a></td><td><strong><?= e($b['guest_name']) ?></strong><br><span class="muted"><?= e($b['phone']) ?></span></td><td><?= e(booking_types()[$b['type']] ?? $b['type']) ?><br><span class="muted"><?= e($b['item_name']) ?></span></td><td><?= pill_status($b['status']) ?></td></tr><?php endforeach; ?>
    </tbody></table></div><?php else: ?><p class="muted">No requests yet. They will appear here as soon as someone submits the booking form.</p><?php endif; ?>
    <p style="margin:1rem 0 0"><a class="btn btn--line btn--sm" href="bookings.php">All bookings</a></p></div></div>
  <div class="c4">
    <div class="card"><h2>Quick links</h2><div class="actions" style="flex-direction:column;align-items:stretch">
      <a class="btn btn--line btn--sm" href="rooms.php?new=1"><?= icon('plus', 16) ?> Add a room</a>
      <a class="btn btn--line btn--sm" href="menu.php?new=1"><?= icon('plus', 16) ?> Add a dish</a>
      <a class="btn btn--line btn--sm" href="gallery.php"><?= icon('upload', 16) ?> Upload photos</a>
      <a class="btn btn--line btn--sm" href="texts.php"><?= icon('pencil', 16) ?> Edit page text &amp; images</a>
      <a class="btn btn--line btn--sm" href="slides.php"><?= icon('image', 16) ?> Home banner slides</a>
      <a class="btn btn--line btn--sm" href="content.php"><?= icon('sliders', 16) ?> Edit website text &amp; logo</a>
      <a class="btn btn--line btn--sm" href="<?= url('index.php') ?>" target="_blank" rel="noopener"><?= icon('external', 16) ?> View website</a></div></div>
  </div>
</div>
<?php admin_foot(); ?>
