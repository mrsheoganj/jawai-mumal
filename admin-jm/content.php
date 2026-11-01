<?php
require __DIR__ . '/_init.php';
$schema = settings_schema();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errs = []; $toSave = [];
    foreach ($schema as $k => $s) {
        $type = $s[2]; $label = $s[1];
        if ($type === 'image') {
            $err = null; $up = save_upload('f_' . $k, $err, 2400);
            if ($up === null) { $errs[] = $label . ': ' . $err; continue; }
            if ($up !== '') { $toSave[$k] = $up; continue; }
        }
        if (!isset($_POST['v_' . $k])) continue;
        $v = v_clean((string)$_POST['v_' . $k]);
        $e = null;
        if ($type === 'image') $e = v_media($v, $label);
        elseif ($type === 'url') $e = v_url($v, false, $label);
        elseif ($type === 'number') $e = $v === '' ? null : v_int($v, 0, 100000000, $label);
        elseif (strpos($type, 'select:') === 0) { if ($v !== '' && !in_array($v, explode('|', substr($type, 7)), true)) $e = "Please choose a valid option for $label."; }
        elseif ($type === 'textarea') $e = v_len($v, 0, 5000, $label);
        else $e = v_len($v, 0, 500, $label);
        // field-specific rules
        if (!$e) switch ($k) {
            case 'email': case 'notify_email': $e = v_email_list($v, $label); break;
            case 'whatsapp': if ($v !== '' && !preg_match('/^[1-9]\d{9,14}$/', $v)) $e = $label . ' must be digits only, with the country code (for example 919876543210).'; break;
            case 'owner1_phone': case 'owner2_phone': if ($v !== '') { [, $e] = v_phone($v); } break;
            case 'owner1_name': case 'owner2_name': if ($v !== '') $e = v_name($v, $label); break;
            case 'map_lat': $e = v_coord($v, true, $label); break;
            case 'map_lng': $e = v_coord($v, false, $label); break;
            case 'tax_percent': case 'tax_percent_high': if ($v !== '' && ($x = v_int($v, 0, 50, $label))) $e = $x; break;
            case 'tax_threshold': if ($v !== '' && ($x = v_int($v, 0, 1000000, $label))) $e = $x; break;
            case 'max_guests': if ($v !== '' && ($x = v_int($v, 1, 50, $label))) $e = $x; break;
            case 'site_url': if ($v !== '' && !preg_match('#^https://#i', $v)) $e = $label . ' must start with https://'; elseif ($v !== '') $e = v_url($v, true, $label); break;
        }
        if ($e) $errs[] = $e; else $toSave[$k] = $v;
    }
    if (!$errs) foreach ($toSave as $k => $v) set_setting($k, $v);
    foreach ($errs as $er) flash('err', $er);
    if (!$errs) flash('ok', 'Website content saved.'); else flash('err', 'Nothing was saved. Please fix the points above and try again.');
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
        <label for="v_<?= $k ?>"><?= e($s[1]) ?></label><textarea id="v_<?= $k ?>" name="v_<?= $k ?>" rows="<?= $k === 'facilities' ? 8 : 3 ?>" maxlength="5000"><?= e($v) ?></textarea>
      <?php elseif (strpos($type, 'select:') === 0): ?>
        <label for="v_<?= $k ?>"><?= e($s[1]) ?></label><select id="v_<?= $k ?>" name="v_<?= $k ?>"><?php foreach (explode('|', substr($type, 7)) as $o): ?><option value="<?= e($o) ?>" <?= $v === $o ? 'selected' : '' ?>><?= $k === 'show_open_badge' ? ($o === '1' ? 'Yes – show badge' : 'No – hide badge') : ($k === 'font_pair' ? e(font_presets()[$o]['label']) : e($o)) ?></option><?php endforeach; ?></select>
      <?php else: ?>
        <label for="v_<?= $k ?>"><?= e($s[1]) ?></label><input id="v_<?= $k ?>" name="v_<?= $k ?>" type="<?= $type === 'number' ? 'number' : ($type === 'url' ? 'url' : (in_array($k, ['email', 'notify_email'], true) ? 'email' : 'text')) ?>" value="<?= e($v) ?>"<?= $type === 'number' ? ' step="1" min="0"' : ' maxlength="500"' ?><?= in_array($k, ['owner1_phone', 'owner2_phone'], true) ? ' inputmode="tel" pattern="\\+?[0-9][0-9 ()\\-]{6,24}"' : '' ?><?= $k === 'whatsapp' ? ' inputmode="numeric" pattern="[1-9][0-9]{9,14}" title="Digits only with country code, e.g. 919876543210"' : '' ?><?= in_array($k, ['map_lat', 'map_lng'], true) ? ' inputmode="decimal" pattern="-?[0-9]{1,3}(\\.[0-9]+)?"' : '' ?>>
      <?php endif; ?>
      <?php if (!empty($s[4])): ?><p class="help"><?= e($s[4]) ?></p><?php endif; ?>
    </div>
  <?php endforeach; ?>
  </div></div>
<?php endforeach; ?>
<div class="actions" style="position:sticky;bottom:0;background:#fafaf8;padding:1rem 0"><button class="btn btn--gold" type="submit"><?= icon('check', 18) ?> Save all changes</button><a class="btn btn--line" href="<?= url('index.php') ?>" target="_blank" rel="noopener">Preview website</a></div>
</form>
<?php admin_foot(); ?>
