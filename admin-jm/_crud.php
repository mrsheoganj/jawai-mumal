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

/** Validate one submitted record against the field definitions. Returns a list of error messages. */
function crud_validate(array $vals, array $c, array $raw): array {
    $errs = [];
    $numLimits = ['price' => [0, 1000000], 'price_pp' => [0, 1000000], 'private_price' => [0, 1000000], 'sort_order' => [0, 9999], 'inventory' => [0, 1000], 'featured' => [0, 1]];
    foreach ($c['fields'] as $col => $f) {
        $type = $f[1]; $label = $f[0]; $o = is_array($f[5] ?? null) ? $f[5] : []; $v = $vals[$col] ?? '';
        if ($type === 'text') {
            $max = $o['max'] ?? db_col_max($c['table'], $col) ?? 250;
            if (mb_strlen((string)$v) > $max) $errs[] = "$label must be $max characters or fewer.";
        } elseif ($type === 'textarea') {
            $max = $o['max'] ?? 20000;
            if (mb_strlen((string)$v) > $max) $errs[] = "$label is too long (maximum $max characters).";
        } elseif ($type === 'number') {
            $rawv = trim((string)($raw['v_' . $col] ?? ''));
            [$mn, $mx] = [$o['min'] ?? ($numLimits[$col][0] ?? 0), $o['max'] ?? ($numLimits[$col][1] ?? 10000000)];
            if (!empty($o['decimal'])) { if ($rawv !== '' && (!preg_match('/^\d+(\.\d+)?$/', $rawv) || (float)$rawv < $mn || (float)$rawv > $mx)) $errs[] = "$label must be a number between $mn and $mx."; }
            elseif ($rawv !== '' && ($e = v_int($rawv, $mn, $mx, $label))) $errs[] = $e;
        } elseif ($type === 'image') {
            if ($e = v_media((string)$v, $label)) $errs[] = $e;
        } elseif ($type === 'gallery') {
            foreach (lines((string)$v) as $ln) if ($e = v_media($ln, $label)) { $errs[] = $e; break; }
        } elseif ($type === 'select') {
            if (!array_key_exists((string)$v, (array)($f[3] ?? []))) $errs[] = "Please choose a valid option for $label.";
        }
        if (!empty($o['pattern']) && $v !== '' && !preg_match($o['pattern'], (string)$v)) $errs[] = $o['msg'] ?? "$label is not in the right format.";
    }
    if (!empty($c['rules'])) foreach (($c['rules'])($vals) as $e) $errs[] = $e;
    return array_values(array_unique($errs));
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
                    $vals[$col] = !empty($f[5]['decimal']) ? round((float)($_POST['v_' . $col] ?? 0), 1) : (int)($_POST['v_' . $col] ?? 0);
                } else {
                    $vals[$col] = trim($_POST['v_' . $col] ?? '');
                }
            }
            if (!empty($c['before_save'])) $vals = ($c['before_save'])($vals, $id);
            $errs = array_merge($errs, crud_validate($vals, $c, $_POST));
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

    // ---------- Export (Excel / PDF / CSV of everything in this list, built from the field definitions)
    if (isset($_GET['export'])) {
        require_once __DIR__ . '/../includes/export.php';
        $fmt = in_array($_GET['export'], ['xlsx', 'pdf', 'csv'], true) ? $_GET['export'] : 'xlsx';
        [$ws, $wp] = !empty($c['where']) ? ($c['where'])() : ['', []];
        $all = rows("SELECT * FROM $t" . ($ws ? " WHERE $ws" : '') . " ORDER BY " . ($c['order'] ?? 'id'), $wp);
        $ex = [];   // [label, col, type, options, width]
        foreach ($c['fields'] as $col => $f) {
            $ty = $f[1] ?? 'text'; if (in_array($ty, ['image', 'gallery'], true)) continue;
            $money = in_array($col, ['price', 'amount', 'price_from', 'price_up_to'], true) || (strpos($col, 'price') !== false && $ty === 'number');
            $ex[] = [$f[0], $col, $ty, $f[3] ?? null, $ty === 'textarea' ? 44 : ($money ? 12 : ($ty === 'number' || $ty === 'check' || $ty === 'select' ? 11 : 24)), $money ? 'm' : ($ty === 'number' ? 'n' : ($ty === 'check' || $ty === 'select' ? 'c' : 's'))];
        }
        $val = function ($r, $x) { $v = $r[$x[1]] ?? ''; if ($x[2] === 'check') return $v ? 'Yes' : 'No'; if ($x[2] === 'select' && is_array($x[3])) return $x[3][$v] ?? $v; return $v; };
        $title = $c['title']; $sub = $title . ' · ' . count($all) . ' record' . (count($all) === 1 ? '' : 's') . ' · generated ' . date('d M Y, H:i');
        $base = 'jawai-mumal-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($title));
        if ($fmt === 'csv') {
            $safe = fn($v) => is_string($v) && $v !== '' && strpos("=+-@\t\r", $v[0]) !== false ? "'" . $v : $v;
            header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="' . export_filename($base, 'csv') . '"');
            $o = fopen('php://output', 'w'); fwrite($o, "\xEF\xBB\xBF"); fputcsv($o, array_map(fn($x) => $x[0], $ex), ',', '"', '\\');
            foreach ($all as $r) fputcsv($o, array_map(fn($x) => $safe($val($r, $x)), $ex), ',', '"', '\\'); exit;
        }
        $use = $fmt === 'pdf' ? array_values(array_filter($ex, fn($x) => $x[2] !== 'textarea')) : $ex;
        $cols = array_map(fn($x) => [$x[0], $x[4], $x[5]], $use);
        $data = array_map(fn($r) => array_map(fn($x) => $val($r, $x), $use), $all);
        if ($fmt === 'xlsx') export_xlsx(export_filename($base, 'xlsx'), $title, setting('site_name') . ' – ' . $title, $sub, $cols, $data);
        export_pdf(export_filename($base, 'pdf'), $title, $sub, $cols, $data);
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
                <label for="v_<?= $col ?>"><?= e($f[0]) ?></label><textarea id="v_<?= $col ?>" name="v_<?= $col ?>" maxlength="<?= (int)((is_array($f[5] ?? null) ? ($f[5]['max'] ?? 0) : 0) ?: 20000) ?>"><?= e($v) ?></textarea>
              <?php elseif ($type === 'select'): ?>
                <label for="v_<?= $col ?>"><?= e($f[0]) ?></label><select id="v_<?= $col ?>" name="v_<?= $col ?>"><?php foreach ($f[3] as $ov => $ol): ?><option value="<?= e($ov) ?>" <?= (string)$v === (string)$ov ? 'selected' : '' ?>><?= e($ol) ?></option><?php endforeach; ?></select>
              <?php else: ?>
                <label for="v_<?= $col ?>"><?= e($f[0]) ?></label><?php $o = is_array($f[5] ?? null) ? $f[5] : []; $nl = ['price' => [0, 1000000], 'price_pp' => [0, 1000000], 'private_price' => [0, 1000000], 'sort_order' => [0, 9999], 'inventory' => [0, 1000]][$col] ?? [0, 10000000]; ?><input id="v_<?= $col ?>" name="v_<?= $col ?>" type="<?= $type === 'number' ? 'number' : 'text' ?>" value="<?= e($v) ?>"<?= $type === 'number' ? ' step="' . (!empty($o['decimal']) ? '0.1' : '1') . '" min="' . ($o['min'] ?? $nl[0]) . '" max="' . ($o['max'] ?? $nl[1]) . '"' : ' maxlength="' . ($o['max'] ?? db_col_max($c['table'], $col) ?? 250) . '"' ?><?= !empty($o['pattern']) ? ' title="' . e($o['msg'] ?? '') . '"' : '' ?>>
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
    [$wsql, $wpar] = !empty($c['where']) ? ($c['where'])() : ['', []];
    $rows = rows("SELECT * FROM $t" . ($wsql ? " WHERE $wsql" : '') . " ORDER BY " . ($c['order'] ?? 'id'), $wpar);
    ?>
    <div class="toolbar"><p class="muted" style="margin:0"><?= count($rows) ?> <?= e($c['plural'] ?? $c['singular'] . 's') ?><?= !empty($c['intro']) ? ' · ' . e($c['intro']) : '' ?></p>
      <span class="actions"><a class="btn btn--line btn--sm" href="<?= $self ?>?export=xlsx"><?= icon('download', 16) ?> Excel</a><a class="btn btn--line btn--sm" href="<?= $self ?>?export=pdf"><?= icon('download', 16) ?> PDF</a>
      <a class="btn btn--gold" href="<?= $self ?>?new=1<?= e($c['add_query'] ?? '') ?>"><?= icon('plus', 18) ?> Add <?= e($c['singular']) ?></a></span></div>
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
