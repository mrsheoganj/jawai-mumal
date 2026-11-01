<?php
/** Jawai Travel Journal. Not linked in the menu by default; published articles are in sitemap.xml and open at /blog and /blog/<slug>. */
require_once __DIR__ . '/includes/db.php';
$slug = trim((string)($_GET['slug'] ?? ''));
$bodyClass = 'nav-solid'; $active = '';
$fmt = fn($d) => date('j F Y', strtotime($d));

if ($slug === '') {
    $posts = blog_published();
    $seoKey = 'blog';
    seo_ld(seo_breadcrumb_ld([['Journal', 'blog']]));
    seo_ld(['@context' => 'https://schema.org', '@type' => 'CollectionPage', 'name' => seo_field('blog', 'title'), 'url' => seo_abs('blog'), 'description' => seo_field('blog', 'desc'),
        'mainEntity' => ['@type' => 'ItemList', 'itemListElement' => array_map(fn($p, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'url' => seo_abs('blog/' . $p['slug']), 'name' => $p['title']], $posts, array_keys($posts))]]);
    include __DIR__ . '/includes/header.php';
    page_head('Journal', 'Jawai Travel Journal', 'Practical guides to leopard safaris, seasons, food, culture and day trips around Bera and Jawai.', 'assets/images/hills-green.jpg');
    ?>
<section class="sec"><div class="wrap">
  <div class="bgrid">
    <?php foreach ($posts as $p): ?>
      <article class="bcard rv">
        <a class="bcard__img" href="<?= e(blog_url($p['slug'])) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e(img($p['image'])) ?>" alt="" loading="lazy"></a>
        <div class="bcard__body">
          <span class="bcard__meta"><?= e($p['category']) ?> · <?= e($fmt($p['published_at'])) ?></span>
          <h2><a href="<?= e(blog_url($p['slug'])) ?>"><?= e($p['title']) ?></a></h2>
          <p><?= e($p['excerpt']) ?></p>
          <a class="link" href="<?= e(blog_url($p['slug'])) ?>">Read the guide <?= icon('arrow-right', 16) ?></a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</div></section>
<?php include __DIR__ . '/includes/footer.php'; exit; }

$p = row("SELECT * FROM jm_blog WHERE slug = ? AND status = 'published' AND published_at <= ?", [$slug, date('Y-m-d H:i:s')]);
if (!$p) {
    http_response_code(404); $pageTitle = 'Article not found';
    include __DIR__ . '/includes/header.php';
    echo '<section class="sec" style="padding-top:9rem"><div class="wrap"><h1>Article not found</h1><p><a class="link" href="' . e(blog_url()) . '">Back to the journal</a></p></div></section>';
    include __DIR__ . '/includes/footer.php'; exit;
}
$title = $p['meta_title'] !== '' && $p['meta_title'] !== null ? $p['meta_title'] : $p['title'] . ' | ' . setting('site_name');
$desc  = $p['meta_description'] !== '' && $p['meta_description'] !== null ? $p['meta_description'] : $p['excerpt'];
$seo = ['title' => $title, 'desc' => $desc, 'keywords' => (string)$p['keywords'], 'image' => $p['image'], 'type' => 'article', 'canonical' => seo_abs('blog/' . $p['slug']),
        'published' => $p['published_at'], 'modified' => $p['published_at'], 'alt' => $p['title']];
$url = seo_abs('blog/' . $p['slug']);
seo_ld(seo_breadcrumb_ld([['Journal', 'blog'], [$p['title'], 'blog/' . $p['slug']]]));
seo_ld(['@context' => 'https://schema.org', '@type' => 'Article', 'headline' => seo_clip($p['title'], 110), 'description' => seo_clip($desc, 200), 'image' => [seo_img_abs($p['image'])],
    'datePublished' => date('c', strtotime($p['published_at'])), 'dateModified' => date('c', strtotime($p['published_at'])), 'inLanguage' => seo_cfg('seo_locale'),
    'author' => ['@type' => 'Organization', 'name' => setting('site_name'), 'url' => seo_abs()],
    'publisher' => ['@type' => 'Organization', 'name' => setting('site_name'), 'logo' => ['@type' => 'ImageObject', 'url' => seo_img_abs(setting('logo_emblem'))]],
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $url], 'keywords' => (string)$p['keywords'], 'articleSection' => $p['category']]);
$related = rows("SELECT * FROM jm_blog WHERE status = 'published' AND published_at <= ? AND id <> ? ORDER BY (category = ?) DESC, published_at DESC LIMIT 3", [date('Y-m-d H:i:s'), $p['id'], $p['category']]);
include __DIR__ . '/includes/header.php';
page_head($p['category'], $p['title'], $p['excerpt'], $p['image']);
?>
<article class="sec"><div class="wrap wrap--narrow">
  <p class="bmeta"><?= e($fmt($p['published_at'])) ?> · <?= blog_reading_minutes($p['body']) ?> min read</p>
  <?php [$bodyHtml, $toc] = blog_toc(blog_body($p['body'])); if (count($toc) >= 3): ?>
  <nav class="toc" aria-label="In this article"><strong>In this article</strong><ol><?php foreach ($toc as $t): ?><li><a href="#<?= e($t[0]) ?>"><?= e($t[1]) ?></a></li><?php endforeach; ?></ol></nav>
  <?php endif; ?>
  <div class="prose"><?= $bodyHtml ?></div>
  <aside class="bcta">
    <h2>Plan your Jawai trip</h2>
    <p>Stay in Bera, eat pure-vegetarian Rajasthani food and set out for the leopard hills with our own trackers.</p>
    <div class="hero__cta"><a class="btn btn--gold" href="<?= e(url('book?type=safari')) ?>">Request a safari <?= icon('arrow-right', 18) ?></a> <a class="btn btn--line" href="<?= e(url('rooms')) ?>">See rooms</a></div>
  </aside>
  <?php if ($related): ?>
  <h2 class="bmore">More from the journal</h2>
  <div class="bgrid bgrid--3">
    <?php foreach ($related as $r): ?>
      <article class="bcard"><a class="bcard__img" href="<?= e(blog_url($r['slug'])) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e(img($r['image'])) ?>" alt="" loading="lazy"></a>
        <div class="bcard__body"><span class="bcard__meta"><?= e($r['category']) ?></span><h3><a href="<?= e(blog_url($r['slug'])) ?>"><?= e($r['title']) ?></a></h3></div></article>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div></article>
<?php include __DIR__ . '/includes/footer.php'; ?>
