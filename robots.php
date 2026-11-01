<?php
/** Dynamic robots.txt – controlled in Admin > SEO. */
require_once __DIR__ . '/includes/db.php';
header('Content-Type: text/plain; charset=utf-8');
if (seo_cfg('seo_index') !== '1' || site_mode() !== 'live') {
    echo "User-agent: *\nDisallow: /\n";
    exit;
}
echo "User-agent: *\nAllow: /\nDisallow: /" . ADMIN_DIR . "/\nDisallow: /data/\nDisallow: /includes/\nDisallow: /deploy/\nDisallow: /book?done=\n";
if (seo_cfg('seo_robots_extra') !== '') echo seo_cfg('seo_robots_extra') . "\n";
echo "\nSitemap: " . seo_abs('sitemap.xml') . "\n";
