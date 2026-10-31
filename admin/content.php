<?php
require __DIR__ . '/_init.php';
$schema = settings_schema();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errs = [];
    foreach ($schema as $k => $s) {
        if ($s[2] === 'image') {
            $err = null; $up = save_upload('f_' . $k, $err, 2400);
            if ($up === null) { $errs[] = $s[1] . ': ' . $err; continue; }
            if ($up !== '') { set_setting($k, $up); continue; }
        }
        if (isset($_POST['v_' . $k])) set_setting($k, trim((string)$_POST['v_' . $k]));
    }
    foreach ($errs as $er) flash('err', $er);
    if (!$errs) flash('ok', 'Website content saved.');
    redirect('content.php');
}
$groups = []; foreach ($schema as $k => $s) $groups[$s[0]][$k] = $s;
admin_head('Site content & logo', 'content');
?>
<form method="post" enctype="multipart/form-data">
<?= csrf_field() ?>
<p class="muted">Everything visitors read on the website can be changed here: the logo, banner photo, headings, phone numbers, address, prices rules and more.</p>
<?php foreach ($groups as $g => $items): ?>
  <h2 class="grp"><?= e($g) ?></h2>
  <div class="card"><div class="grid">
  <?php foreach ($items as $k => $s): $type = $s[2]; $v = setting($k); ?>
    <div class="<?= in_array($type, ['textarea', 'image'], true) ? 'c12' : 'c6' ?>">
      <?php if ($type === 'image'): ?>
        <label><?= e($s[1]) ?></label>
        <div class="imgfield"><img id="pv_<?= $k ?>" src="<?= e(img($v)) ?>" alt="" style="<?= strpos($k, 'logo') !== false ? 'object-fit:contain;background:' . (strpos($k, 'light') !== false ? '#2b180e' : '#faf6ec') : '' ?>">
          <div><input type="file" name="f_<?= $k ?>" accept="image/jpeg,image/png,image/webp,image/gif" data-preview="pv_<?= $k ?>"><input name="v_<?= $k ?>" value="<?= e($v) ?>" style="margin-top:.5rem" aria-label="Image path"></div></div>
      <?php elseif ($type === 'textarea'): ?>
        <label for="v_<?= $k ?>"><?= e($s[1]) ?></label><textarea id="v_<?= $k ?>" name="v_<?= $k ?>" rows="<?= $k === 'facilities' ? 8 : 3 ?>"><?= e($v) ?></textarea>
      <?php elseif (strpos($type, 'select:') === 0): ?>
        <label for="v_<?= $k ?>"><?= e($s[1]) ?></label><select id="v_<?= $k ?>" name="v_<?= $k ?>"><?php foreach (explode('|', substr($type, 7)) as $o): ?><option value="<?= e($o) ?>" <?= $v === $o ? 'selected' : '' ?>><?= $k === 'opening_soon' ? ($o === '1' ? 'Yes – Opening Soon mode' : 'No – hotel is open') : e($o) ?></option><?php endforeach; ?></select>
      <?php else: ?>
        <label for="v_<?= $k ?>"><?= e($s[1]) ?></label><input id="v_<?= $k ?>" name="v_<?= $k ?>" type="<?= $type === 'number' ? 'number' : 'text' ?>" value="<?= e($v) ?>">
      <?php endif; ?>
      <?php if (!empty($s[4])): ?><p class="help"><?= e($s[4]) ?></p><?php endif; ?>
    </div>
  <?php endforeach; ?>
  </div></div>
<?php endforeach; ?>
<div class="actions" style="position:sticky;bottom:0;background:#f4efe4;padding:1rem 0"><button class="btn btn--gold" type="submit"><?= icon('check', 18) ?> Save all changes</button><a class="btn btn--line" href="<?= url('index.php') ?>" target="_blank" rel="noopener">Preview website</a></div>
</form>
<?php admin_foot(); ?>
