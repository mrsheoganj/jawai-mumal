<?php
require_once __DIR__ . '/includes/db.php';
$pageTitle = 'Gallery'; $pageDesc = 'Photos of Jawai Mumal hotel, rooms, restaurant, safari and the Jawai landscape.';
$active = 'gallery';
$g = rows('SELECT * FROM jm_gallery ORDER BY sort_order, id');
$cats = array_values(array_unique(array_column($g, 'category')));
seo_ld(seo_breadcrumb_ld([['Gallery', 'gallery']]));
include __DIR__ . '/includes/header.php';
page_head(t('gallery.head.eyebrow', 'Gallery'), t('gallery.head.title', 'Gallery'), t('gallery.head.sub', 'The hotel, the food and the wild country around us.'), im('gallery.head.img', 'assets/images/jawai-lake-birds.jpg'));
?>
<section class="sec">
  <div class="wrap">
    <div class="chips" data-filter-group=".mason a">
      <button class="chip" data-filter="all" aria-pressed="true"><?= et('gallery.all', 'All') ?></button>
      <?php foreach ($cats as $c): ?><button class="chip" data-filter="<?= e($c) ?>" aria-pressed="false"><?= e($c) ?></button><?php endforeach; ?>
    </div>
    <div class="mason">
      <?php foreach ($g as $x): ?><a href="<?= e(img($x['image'])) ?>" data-cat="<?= e($x['category']) ?>" data-title="<?= e($x['title']) ?>"><img src="<?= e(img($x['image'])) ?>" alt="<?= e($x['title']) ?>" loading="lazy"><span><?= e($x['title']) ?></span></a><?php endforeach; ?>
    </div>
  </div>
</section>
<div class="lb" id="lb" role="dialog" aria-modal="true" aria-label="Image viewer"><button class="lb__x" aria-label="Close"><?= icon('x', 22) ?></button><button class="lb__p" aria-label="Previous"><?= icon('chevron-left', 22) ?></button><img alt=""><button class="lb__n" aria-label="Next"><?= icon('chevron-right', 22) ?></button><p></p></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
