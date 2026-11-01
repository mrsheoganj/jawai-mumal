<?php
/**
 * Generic list / add / edit / delete screen for a content table.
 * Config: table, active, title, singular, fields[col => [label, type, help?, options?, width?]], list[[label, col|callable]], order, slug (source col or null), thumb (image col or null)
 * Field types: text, textarea, number, image, gallery, select, check
 */
function crud_multi_upload(string $field, array &$errs): array {
    $out = [];
    if (empty($_FILES[$field]['name']) || !is_array($_FILES[$field]['name'])) return $out;
    foreach ($_FILES[$field]['name'] as $i => $n) {
        if ($_FILES[$field]['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
        $_FILES['_one'] = ['name' => $n, 'type' => $_FILES[$field]['type'][$i], 'tmp_name' => $_FILES[$field]['tmp_name'][$i], 'error' => $_FILES[$field]['error'][$i], 'size' => $_FILES[$field]['size'][$i]];
        $err = null; $p = save_upload('_one', $err);
        if ($p) $out[] = $p; elseif ($err) $errs[] = $n . ': ' . $err;
    }
    return $out;
}

function admin_crud(array $c): void {
    $t = $c['table']; $self = basename($_SERVER['SCRIPT_NAME']);
    $hasSlug = !empty($c['slug']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
        if (($_POST['action'] ?? '') === 'delete' && $id) {
            q("DELETE FROM $t WHERE id = ?", [$id]);
            flash('ok', ucfirst($c['singular']) . ' deleted.'); redirect($self);
        }
        if (($_POST['action'] ?? '') === 'save') {
            $vals = []; $errs = [];
            foreach ($c['fields'] as $col => $f) {
                $type = $f[1];
                if ($type === 'image') {
                    $err = null; $up = save_upload('f_' . $col, $err);
                    if ($up === null) { $errs[] = $f[0] . ': ' . $err; $up = ''; }
                    $vals[$col] = $up !== '' ? $up : trim($_POST['v_' . $col] ?? '');
                } elseif ($type === 'gallery') {
                    $list = lines($_POST['v_' . $col] ?? '');
                    $list = array_merge($list, crud_multi_upload('g_' . $col, $errs));
                    $vals[$col] = implode("\n", $list);
                } elseif ($type === 'check') {
                    $vals[$col] = !empty($_POST['v_' . $col]) ? 1 : 0;
                } elseif ($type === 'number') {
                    $vals[$col] = (int)($_POST['v_' . $col] ?? 0);
                } else {
                    $vals[$col] = trim($_POST['v_' . $col] ?? '');
                }
            }
            if (!empty($c['before_save'])) $vals = ($c['before_save'])($vals, $id);
            $req = $c['required'] ?? array_key_first($c['fields']);
            if (($vals[$req] ?? '') === '' || $vals[$req] === 0 && $c['fields'][$req][1] !== 'number') $errs[] = $c['fields'][$req][0] . ' is required.';
            if ($errs) {
                foreach ($errs as $er) flash('err', $er);
                $_SESSION['crud_old'] = $vals; redirect($self . ($id ? '?edit=' . $id : '?new=1'));
            }
            if ($hasSlug) {
                $base = slugify($vals[$c['slug']]); $slug = $base; $n = 2;
                while (val("SELECT COUNT(*) FROM $t WHERE slug = ? AND id <> ?", [$slug, $id])) { $slug = $base . '-' . $n++; }
                if ($id) { $existing = val("SELECT slug FROM $t WHERE id = ?", [$id]); if ($existing) $slug = $existing; }   // never change a live URL when a title is edited
                $vals['slug'] = $slug;
            }
            if ($id) {
                $set = implode(',', array_map(fn($k) => "$k = ?", array_keys($vals)));
                q("UPDATE $t SET $set WHERE id = ?", array_merge(array_values($vals), [$id]));
                flash('ok', ucfirst($c['singular']) . ' saved.');
            } else {
                $cols = implode(',', array_keys($vals)); $ph = implode(',', array_fill(0, count($vals), '?'));
                q("INSERT INTO $t ($cols) VALUES ($ph)", array_values($vals));
                flash('ok', ucfirst($c['singular']) . ' added.');
            }
            redirect($self);
        }
    }

    admin_head($c['title'], $c['active']);
    if (!empty($c['top']) && !isset($_GET['edit']) && !isset($_GET['new'])) { ($c['top'])(); }

    // ---------- Form
    if (isset($_GET['edit']) || isset($_GET['new'])) {
        $id = (int)($_GET['edit'] ?? 0);
        $r = $id ? row("SELECT * FROM $t WHERE id = ?", [$id]) : null;
        if ($id && !$r) { echo '<div class="alert alert--err">Not found.</div>'; admin_foot(); return; }
        $r = $r ?: ($c['defaults'] ?? []);
        if (!empty($_SESSION['crud_old'])) { $r = array_merge($r, $_SESSION['crud_old']); unset($_SESSION['crud_old']); }
        ?>
        <form class="card" method="post" enctype="multipart/form-data">
          <?= csrf_field() ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= $id ?>">
          <h2><?= $id ? 'Edit ' : 'Add ' ?><?= e($c['singular']) ?></h2>
          <div class="grid">
          <?php foreach ($c['fields'] as $col => $f): $type = $f[1]; $v = $r[$col] ?? ''; $w = $f[4] ?? ($type === 'textarea' || $type === 'gallery' || $type === 'image' ? 12 : 6); ?>
            <div class="c<?= $w ?>">
              <?php if ($type === 'check'): ?>
                <label style="display:flex;gap:.6rem;align-items:center;min-height:44px"><input type="checkbox" name="v_<?= $col ?>" value="1" <?= $v ? 'checked' : '' ?>> <?= e($f[0]) ?></label>
              <?php elseif ($type === 'image'): ?>
                <label><?= e($f[0]) ?></label>
                <div class="imgfield"><img id="pv_<?= $col ?>" src="<?= e(img((string)$v)) ?>" alt="" <?= $v ? '' : 'hidden' ?>>
                  <div><input type="file" name="f_<?= $col ?>" accept="image/jpeg,image/png,image/webp,image/gif" data-preview="pv_<?= $col ?>">
                  <p class="help">Upload a new photo (JPG, PNG or WEBP, up to 8 MB), or paste an image link below.</p>
                  <input name="v_<?= $col ?>" value="<?= e($v) ?>" placeholder="assets/uploads/… or https://…"></div></div>
              <?php elseif ($type === 'gallery'): ?>
                <label><?= e($f[0]) ?></label>
                <div class="imgfield"><div style="flex:1 1 100%"><input type="file" name="g_<?= $col ?>[]" multiple accept="image/jpeg,image/png,image/webp,image/gif"><p class="help">Add more photos. Existing photo paths (one per line) can be edited or removed here.</p>
                <textarea name="v_<?= $col ?>" rows="3"><?= e($v) ?></textarea></div></div>
              <?php elseif ($type === 'textarea'): ?>
                <label for="v_<?= $col ?>"><?= e($f[0]) ?></label><textarea id="v_<?= $col ?>" name="v_<?= $col ?>"><?= e($v) ?></textarea>
              <?php elseif ($type === 'select'): ?>
                <label for="v_<?= $col ?>"><?= e($f[0]) ?></label><select id="v_<?= $col ?>" name="v_<?= $col ?>"><?php foreach ($f[3] as $ov => $ol): ?><option value="<?= e($ov) ?>" <?= (string)$v === (string)$ov ? 'selected' : '' ?>><?= e($ol) ?></option><?php endforeach; ?></select>
              <?php else: ?>
                <label for="v_<?= $col ?>"><?= e($f[0]) ?></label><input id="v_<?= $col ?>" name="v_<?= $col ?>" type="<?= $type === 'number' ? 'number' : 'text' ?>" value="<?= e($v) ?>">
              <?php endif; ?>
              <?php if (!empty($f[2])): ?><p class="help"><?= e($f[2]) ?></p><?php endif; ?>
            </div>
          <?php endforeach; ?>
          </div>
          <div class="actions" style="margin-top:1.4rem"><button class="btn btn--gold" type="submit"><?= icon('check', 18) ?> Save</button><a class="btn btn--line" href="<?= $self ?>">Cancel</a></div>
        </form>
        <?php
        admin_foot(); return;
    }

    // ---------- List
    $rows = rows("SELECT * FROM $t ORDER BY " . ($c['order'] ?? 'id'));
    ?>
    <div class="toolbar"><p class="muted" style="margin:0"><?= count($rows) ?> <?= e($c['plural'] ?? $c['singular'] . 's') ?><?= !empty($c['intro']) ? ' · ' . e($c['intro']) : '' ?></p>
      <a class="btn btn--gold" href="<?= $self ?>?new=1"><?= icon('plus', 18) ?> Add <?= e($c['singular']) ?></a></div>
    <div class="tablewrap"><table>
      <thead><tr><?php if (!empty($c['thumb'])): ?><th></th><?php endif; ?><?php foreach ($c['list'] as $l): ?><th><?= e($l[0]) ?></th><?php endforeach; ?><th style="text-align:right">Actions</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <?php if (!empty($c['thumb'])): ?><td><img class="thumb" src="<?= e(img($r[$c['thumb']] ?? '')) ?>" alt="" loading="lazy"></td><?php endif; ?>
          <?php foreach ($c['list'] as $l): $x = is_callable($l[1]) ? $l[1]($r) : e($r[$l[1]] ?? ''); ?><td><?= $x ?></td><?php endforeach; ?>
          <td><div class="actions" style="justify-content:flex-end">
            <?= !empty($c['row_actions']) ? ($c['row_actions'])($r) : '' ?>
            <a class="btn btn--line btn--sm" href="<?= $self ?>?edit=<?= (int)$r['id'] ?>"><?= icon('pencil', 16) ?> Edit</a>
            <form method="post" style="display:inline"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn btn--danger btn--sm" data-confirm="Delete this <?= e($c['singular']) ?>? This cannot be undone." aria-label="Delete"><?= icon('trash', 16) ?></button></form>
          </div></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="9" class="muted" style="text-align:center;padding:2rem">Nothing here yet. Click “Add <?= e($c['singular']) ?>”.</td></tr><?php endif; ?>
      </tbody></table></div>
    <?php
    admin_foot();
}
function pill_status($v): string { return '<span class="pill pill--' . e($v) . '">' . e($v) . '</span>'; }
