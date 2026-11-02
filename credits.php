<?php
require_once __DIR__ . '/includes/db.php';
$pageTitle = 'Image Credits'; $pageDesc = 'Photographers and licences behind the pictures used on the ' . setting('site_name') . ' website.';
$active = '';
$seoKey = 'credits';
seo_ld(seo_breadcrumb_ld([['Image credits', 'credits']]));
include __DIR__ . '/includes/header.php';
page_head(t('credits.head.eyebrow', 'Thank you'), t('credits.head.title', 'Image Credits'), t('credits.head.sub', 'The photographers behind the pictures of Jawai.'), im('credits.head.img', 'assets/images/jawai-hills-view.jpg'));
$credits = is_file(__DIR__ . '/includes/credits.php') ? (require __DIR__ . '/includes/credits.php') : [];
?>
<section class="sec">
  <div class="wrap" style="max-width:1100px">
    <p class="muted" style="max-width:68ch"><?= et('credits.intro', 'The photographs of Jawai, its leopards, people and food on this website are shared by their photographers on Wikimedia Commons under open licences. We are grateful to every one of them. Photos of the hotel are our own.') ?></p>
    <ul class="credit-list">
      <?php foreach ($credits as $c): ?>
      <li class="credit rv">
        <img src="<?= e(img($c['file'])) ?>" alt="<?= e($c['label']) ?>" loading="lazy">
        <div class="credit__body">
          <h3><?= e($c['label']) ?></h3>
          <p>Photo: <b><?= e($c['author']) ?></b></p>
          <p>Licence: <?php if ($c['license_url']): ?><a href="<?= e($c['license_url']) ?>" target="_blank" rel="noopener license"><?= e($c['license']) ?></a><?php else: ?><?= e($c['license']) ?><?php endif; ?></p>
          <?php if ($c['source']): ?><p><a href="<?= e($c['source']) ?>" target="_blank" rel="noopener">View original on Wikimedia Commons</a></p><?php endif; ?>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
