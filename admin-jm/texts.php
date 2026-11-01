<?php
require __DIR__ . '/_init.php';
$reg = content_registry();
$names = ['nav' => 'Menu & header', 'home' => 'Home page', 'rooms' => 'Rooms page', 'room' => 'Room detail page', 'restaurant' => 'Restaurant page', 'safari' => 'Safari page', 'jawai' => 'Explore Jawai page',
          'gallery' => 'Gallery page', 'contact' => 'Contact page', 'book' => 'Booking forms', 'bookpage' => 'Booking page', 'footer' => 'Footer', 'gate' => 'Coming soon / Maintenance page', 'wa' => 'WhatsApp button labels', 'done' => 'Booking confirmation', 'routes' => 'How to reach (map)', 'scene' => 'Animated Jawai scene', 'mnav' => 'Mobile bottom bar', 'policies' => 'Policies page', 'e404' => '404 page', 'sdial' => 'WhatsApp messages'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errs = []; $n = 0;
    foreach ($reg as $r) {
        $k = $r['key']; $sk = $r['kind'] . '.' . $k;
        if ($r['kind'] === 'txt') {
            if (!isset($_POST['t'][$k])) continue;
            $v = v_clean((string)$_POST['t'][$k]);
            if (mb_strlen($v) > 3000) { $errs[] = 'A text is too long (maximum 3000 characters): ' . mb_substr($v, 0, 30) . '…'; continue; }
            if ($v === '' || $v === $r['default']) q('DELETE FROM jm_settings WHERE skey = ?', [$sk]); else set_setting($sk, $v);
        } else {
            $up = '';
            if (!empty($_FILES['fi']['name'][$k]) && ($_FILES['fi']['error'][$k] ?? 4) !== UPLOAD_ERR_NO_FILE) {
                $_FILES['_one'] = ['name' => $_FILES['fi']['name'][$k], 'type' => $_FILES['fi']['type'][$k], 'tmp_name' => $_FILES['fi']['tmp_name'][$k], 'error' => $_FILES['fi']['error'][$k], 'size' => $_FILES['fi']['size'][$k]];
                $err = null; $up = save_upload('_one', $err, 2400);
                if ($up === null) { $errs[] = $k . ': ' . $err; $up = ''; }
            }
            $v = $up !== '' ? $up : trim((string)($_POST['ip'][$k] ?? ''));
            if ($up === '' && ($ie = v_media($v, 'Image'))) { $errs[] = $ie . ' (' . $k . ')'; continue; }
            if ($v === '' || $v === $r['default']) q('DELETE FROM jm_settings WHERE skey = ?', [$sk]); else set_setting($sk, $v);
        }
        $n++;
    }
    settings_all(true);
    foreach ($errs as $er) flash('err', $er);
    if (!$errs) flash('ok', 'Page text and images saved.');
    redirect('texts.php' . (!empty($_POST['go']) ? '#' . preg_replace('/[^a-z0-9]/', '', $_POST['go']) : ''));
}
$groups = [];
foreach ($reg as $r) { $groups[explode('.', $r['key'])[0]][] = $r; }
uksort($groups, fn($a, $b) => array_search($a, array_keys($names)) <=> array_search($b, array_keys($names)));
admin_head('Page text & images', 'texts');
?>
<form method="post" enctype="multipart/form-data">
<?= csrf_field() ?>
<p class="muted">Every heading, paragraph and picture on the public pages is listed here. Change it and press save. Clear a box to go back to the original wording. For prices, rooms, menu, safaris and photos use the dedicated menus on the left.</p>
<div class="actions" style="margin-bottom:1rem"><?php foreach ($groups as $g => $items): ?><a class="btn btn--line btn--sm" href="#<?= e($g) ?>"><?= e($names[$g] ?? ucfirst($g)) ?></a><?php endforeach; ?></div>
<?php foreach ($groups as $g => $items): ?>
  <h2 class="grp" id="<?= e($g) ?>"><?= e($names[$g] ?? ucfirst($g)) ?></h2>
  <div class="card"><div class="grid">
  <?php foreach ($items as $r): $k = $r['key']; $label = ucwords(str_replace(['.', '_'], [' › ', ' '], substr($k, strlen($g) + 1))); ?>
    <?php if ($r['kind'] === 'img'): $cur = im($k, $r['default']); ?>
      <div class="c12"><label><?= e($label) ?> <span class="muted">(photo)</span></label>
        <div class="imgfield"><img id="pv_<?= e(md5($k)) ?>" src="<?= e(img($cur)) ?>" alt="">
          <div><input type="file" name="fi[<?= e($k) ?>]" accept="image/jpeg,image/png,image/webp,image/gif" data-preview="pv_<?= e(md5($k)) ?>"><input name="ip[<?= e($k) ?>]" value="<?= e($cur) ?>" style="margin-top:.5rem" aria-label="Image path"></div></div></div>
    <?php else: $cur = t($k, $r['default']); $long = strlen($r['default']) > 80 || strpos($r['default'], "\n") !== false; ?>
      <div class="<?= $long ? 'c12' : 'c6' ?>"><label for="t_<?= e(md5($k)) ?>"><?= e($label) ?></label>
        <?php if ($long): ?><textarea id="t_<?= e(md5($k)) ?>" name="t[<?= e($k) ?>]" rows="<?= min(8, max(2, (int)ceil(strlen($r['default']) / 110))) ?>"><?= e($cur) ?></textarea>
        <?php else: ?><input id="t_<?= e(md5($k)) ?>" name="t[<?= e($k) ?>]" value="<?= e($cur) ?>"><?php endif; ?></div>
    <?php endif; ?>
  <?php endforeach; ?>
  </div></div>
<?php endforeach; ?>
<div class="actions" style="position:sticky;bottom:0;background:#fafaf8;padding:1rem 0"><button class="btn btn--gold" type="submit"><?= icon('check', 18) ?> Save all changes</button><a class="btn btn--line" href="<?= url('index.php') ?>" target="_blank" rel="noopener">Preview website</a></div>
</form>
<?php admin_foot(); ?>
