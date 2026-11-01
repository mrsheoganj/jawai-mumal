<?php
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/xml; charset=utf-8');
$now = date('Y-m-d');
$urls = [['', 1.0, 'weekly', $now], ['rooms', .9, 'weekly', $now], ['restaurant', .8, 'weekly', $now], ['safari', .9, 'weekly', $now], ['jawai', .8, 'monthly', $now],
         ['gallery', .5, 'monthly', $now], ['contact', .7, 'monthly', $now], ['policies', .3, 'yearly', $now], ['blog', .7, 'weekly', $now]];
foreach (rows("SELECT slug FROM jm_rooms WHERE status='active' ORDER BY sort_order") as $r) $urls[] = ['room?slug=' . $r['slug'], .8, 'weekly', $now];
foreach (blog_published() as $p) $urls[] = ['blog/' . $p['slug'], .6, 'monthly', date('Y-m-d', strtotime($p['published_at'])), $p['image']];
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
foreach ($urls as $u) {
    echo '  <url><loc>' . htmlspecialchars(seo_abs($u[0]), ENT_XML1) . '</loc><lastmod>' . $u[3] . '</lastmod><changefreq>' . $u[2] . '</changefreq><priority>' . number_format($u[1], 1) . '</priority>';
    if (!empty($u[4])) echo '<image:image><image:loc>' . htmlspecialchars(seo_img_abs($u[4]), ENT_XML1) . '</image:loc></image:image>';
    echo "</url>\n";
}
echo '</urlset>';
