<?php
require __DIR__ . '/_init.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $a = $_POST['action'] ?? 'save';
    if ($a === 'regen') { set_setting('preview_key', bin2hex(random_bytes(8))); flash('ok', 'New secret link created. The old link and any saved previews no longer work.'); redirect('mode.php'); }
    $mode = in_array($_POST['site_mode'] ?? '', ['live', 'coming_soon', 'maintenance'], true) ? $_POST['site_mode'] : 'live';
    $launch = trim($_POST['launch_at'] ?? ''); $until = trim($_POST['maint_until'] ?? '');
    $valid = fn($v) => $v === '' || ((bool)date_create($v) && strtotime($v) < strtotime('+5 years') && strtotime($v) > strtotime('2020-01-01'));
    if (!$valid($launch) || !$valid($until)) { flash('err', 'Please enter a valid date and time.'); redirect('mode.php'); }
    if ($mode === 'coming_soon' && $launch === '') { flash('err', 'Please choose the launch date and time for Coming soon mode.'); redirect('mode.php'); }
    set_setting('site_mode', $mode); set_setting('launch_at', $launch); set_setting('maint_until', $until);
    set_setting('auto_launch', !empty($_POST['auto_launch']) ? '1' : '0');
    preview_key();
    flash('ok', $mode === 'live' ? 'Website is LIVE for everyone.' : ($mode === 'coming_soon' ? 'Coming soon mode is ON. Visitors see the countdown; use the secret link to show the site to clients.' : 'Maintenance mode is ON. Visitors see the maintenance notice.'));
    redirect('mode.php');
}
$mode = mode_cfg('site_mode', 'live') ?: 'live'; $eff = site_mode();
$launch = mode_cfg('launch_at'); $until = mode_cfg('maint_until'); $auto = get_setting_raw('auto_launch') !== '0';
$pv = preview_url();
admin_head('Site mode', 'mode');
?>
<div class="alert <?= $eff === 'live' ? 'alert--ok' : 'alert--warn' ?>"><strong>Right now:</strong> <?= ['live' => 'the website is LIVE for everyone.', 'coming_soon' => 'visitors see the Coming soon page with a countdown.', 'maintenance' => 'visitors see the Maintenance page.'][$eff] ?>
<?php if ($mode === 'coming_soon' && $eff === 'live'): ?> (The launch time has passed, so the site went live automatically.)<?php endif; ?></div>
<form method="post" class="card"><?= csrf_field() ?><input type="hidden" name="action" value="save">
  <h2>Choose the mode</h2>
  <div class="modes">
    <label class="mode"><input type="radio" name="site_mode" value="live" <?= $mode === 'live' ? 'checked' : '' ?>><b><?= icon('globe', 20) ?> Live</b><span>Everyone can see and use the website and book.</span></label>
    <label class="mode"><input type="radio" name="site_mode" value="coming_soon" <?= $mode === 'coming_soon' ? 'checked' : '' ?>><b><?= icon('clock', 20) ?> Coming soon</b><span>Visitors see a countdown page. Only people with the secret link (and you, when signed in) see the real site.</span></label>
    <label class="mode"><input type="radio" name="site_mode" value="maintenance" <?= $mode === 'maintenance' ? 'checked' : '' ?>><b><?= icon('sliders', 20) ?> Maintenance</b><span>Visitors see a “back soon” page while you make changes. The secret link still works for you.</span></label>
  </div>
  <div class="grid" style="margin-top:1.2rem">
    <div class="c6"><label for="la">Launch date &amp; time (India time) — for Coming soon</label><input id="la" type="datetime-local" name="launch_at" value="<?= e($launch) ?>"><p class="help">The countdown on the public page counts down to this moment.</p></div>
    <div class="c6"><label for="mu">Expected back (optional) — for Maintenance</label><input id="mu" type="datetime-local" name="maint_until" value="<?= e($until) ?>"><p class="help">If set, the maintenance page shows a countdown. Leave empty to hide it.</p></div>
    <div class="c12"><label style="display:flex;gap:.6rem;align-items:center;min-height:44px"><input type="checkbox" name="auto_launch" value="1" <?= $auto ? 'checked' : '' ?>> Go live automatically when the launch time arrives</label></div>
  </div>
  <p style="margin:1rem 0 0"><button class="btn btn--gold"><?= icon('check', 18) ?> Save mode</button></p>
</form>
<div class="card"><h2>Secret preview link (show the site to a client)</h2>
  <p class="muted" style="margin-top:0">While the site is in Coming soon or Maintenance mode, anyone who opens this link can browse the complete website (the browser remembers it for 30 days). Everyone else still sees the countdown. Share it only with people you trust.</p>
  <div class="copyrow"><input id="pvl" readonly value="<?= e($pv) ?>" aria-label="Secret preview link"><button type="button" class="btn btn--line" id="copypv"><?= icon('check', 16) ?> Copy link</button><a class="btn btn--line" href="<?= e($pv) ?>" target="_blank" rel="noopener"><?= icon('external', 16) ?> Open</a></div>
  <form method="post" style="margin-top:1rem"><?= csrf_field() ?><input type="hidden" name="action" value="regen"><button class="btn btn--danger btn--sm" data-confirm="Make a new link? The current link will stop working."><?= icon('refresh', 16) ?> Create a new secret link</button></form>
</div>
<script>document.getElementById('copypv').addEventListener('click',function(){var i=document.getElementById('pvl');i.select();try{navigator.clipboard.writeText(i.value)}catch(e){document.execCommand('copy')}this.textContent='Copied ✓'});</script>
<?php admin_foot(); ?>
