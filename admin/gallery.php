<?php
require __DIR__ . '/_init.php'; require_once __DIR__ . '/_crud.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $a = $_POST['action'] ?? '';
    if ($a === 'upload') {
        $errs = []; $paths = crud_multi_upload('files', $errs); $cat = trim($_POST['category'] ?? 'General') ?: 'General';
        $next = (int)val('SELECT COALESCE(MAX(sort_order),0) FROM jm_gallery') + 1;
        foreach ($paths as $p) { q('INSERT INTO jm_gallery (title, category, image, sort_order) VALUES (?,?,?,?)', [ucfirst(trim(trim($_POST['title'] ?? '') ?: 'Jawai Mumal')), $cat, $p, $next++]); }
        if ($paths) flash('ok', count($paths) . ' photo(s) uploaded.');
        foreach ($errs as $er) flash('err', $er);
        if (!$paths && !$errs) flash('err', 'Choose at least one image.');
    } elseif ($a === 'save') {
        q('UPDATE jm_gallery SET title=?, category=?, sort_order=? WHERE id=?', [trim($_POST['title'] ?? ''), trim($_POST['category'] ?? ''), (int)($_POST['sort_order'] ?? 0), (int)$_POST['id']]); flash('ok', 'Photo updated.');
    } elseif ($a === 'delete') {
        q('DELETE FROM jm_gallery WHERE id = ?', [(int)$_POST['id']]); flash('ok', 'Photo removed.');
    }
    redirect('gallery.php');
}
$g = rows('SELECT * FROM jm_gallery ORDER BY sort_order, id');
admin_head('Gallery', 'gallery');
?>
<form class="card" method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="upload"><h2>Upload photos</h2>
  <div class="grid"><div class="c6"><label for="files">Photos (you can select many)</label><input id="files" type="file" name="files[]" multiple accept="image/jpeg,image/png,image/webp,image/gif" required></div>
  <div class="c3"><label for="cat">Category</label><input id="cat" name="category" list="cats" value="Hotel"><datalist id="cats"><?php foreach (array_unique(array_column($g, 'category')) as $c): ?><option value="<?= e($c) ?>"><?php endforeach; ?></datalist></div>
  <div class="c3"><label for="ttl">Caption</label><input id="ttl" name="title" placeholder="Jawai Mumal"></div></div>
  <p style="margin:1rem 0 0"><button class="btn btn--gold"><?= icon('upload', 18) ?> Upload</button></p></form>
<div class="gal">
<?php foreach ($g as $x): ?>
  <figure><img src="<?= e(img($x['image'])) ?>" alt="<?= e($x['title']) ?>" loading="lazy">
    <figcaption><form method="post" style="display:grid;gap:.4rem"><?= csrf_field() ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)$x['id'] ?>">
      <input name="title" value="<?= e($x['title']) ?>" aria-label="Caption"><div style="display:flex;gap:.4rem"><input name="category" value="<?= e($x['category']) ?>" aria-label="Category"><input name="sort_order" type="number" value="<?= (int)$x['sort_order'] ?>" style="width:80px" aria-label="Order"></div>
      <div class="actions"><button class="btn btn--line btn--sm">Save</button></form>
      <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$x['id'] ?>"><button class="btn btn--danger btn--sm" data-confirm="Remove this photo from the gallery?" aria-label="Delete"><?= icon('trash', 16) ?></button></form></div>
    </figcaption></figure>
<?php endforeach; ?>
</div>
<?php admin_foot(); ?>
