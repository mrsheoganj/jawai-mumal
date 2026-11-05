<?php
/** sitemap.xml: every public page, room and journal article, with real last-changed dates and cover images. */
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=3600');
$mt = fn(string $f) => date('Y-m-d', (int)@filemtime(__DIR__ . '/' . $f) ?: time());
$posts = blog_published();
$lastPost = $posts ? max(array_map(fn($p) => strtotime($p['published_at']), $posts)) : 0;
$urls = [
    ['', 1.0, 'weekly', max($mt('index.php'), date('Y-m-d', $lastPost ?: 0))],
    ['rooms', .9, 'weekly', $mt('rooms.php')], ['safari', .9, 'weekly', $mt('safari.php')], ['restaurant', .8, 'weekly', $mt('restaurant.php')],
    ['jawai', .8, 'monthly', $mt('jawai.php')], ['blog', .8, 'daily', $lastPost ? date('Y-m-d', $lastPost) : $mt('blog.php')],
    ['contact', .7, 'monthly', $mt('contact.php')], ['book', .6, 'monthly', $mt('book.php')], ['gallery', .5, 'monthly', $mt('gallery.php')],
    ['policies', .3, 'yearly', $mt('policies.php')], ['credits', .2, 'yearly', $mt('credits.php')],
];
foreach (rows("SELECT slug, image FROM jm_rooms WHERE status='active' ORDER BY sort_order") as $r) $urls[] = ['room?slug=' . $r['slug'], .8, 'weekly', $mt('room.php'), $r['image']];
foreach ($posts as $p) $urls[] = ['blog/' . $p['slug'], .6, 'monthly', date('Y-m-d', strtotime($p['published_at'])), $p['image']];
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
foreach ($urls as $u) {
    echo '  <url><loc>' . htmlspecialchars(seo_abs($u[0]), ENT_XML1) . '</loc><lastmod>' . $u[3] . '</lastmod><changefreq>' . $u[2] . '</changefreq><priority>' . number_format($u[1], 1) . '</priority>';
    if (!empty($u[4])) echo '<image:image><image:loc>' . htmlspecialchars(seo_img_abs($u[4]), ENT_XML1) . '</image:loc></image:image>';
    echo "</url>\n";
}
echo '</urlset>';
