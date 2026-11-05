<?php
/** Dynamic robots.txt, controlled in Admin > SEO. Private pages are protected by sign-in and noindex headers, so they are deliberately not listed here. */
require_once __DIR__ . '/includes/db.php';
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: public, max-age=3600');
if (seo_cfg('seo_index') !== '1' || site_mode() !== 'live') {
    echo "User-agent: *\nDisallow: /\n";
    exit;
}
echo "User-agent: *\nAllow: /\nAllow: /assets/\nDisallow: /" . ADMIN_DIR . "/\nDisallow: /data/\nDisallow: /includes/\nDisallow: /deploy/\nDisallow: /hit\nDisallow: /book?done=\nDisallow: /*?preview=\nDisallow: /*?done=\n";
if (seo_cfg('seo_robots_extra') !== '') echo seo_cfg('seo_robots_extra') . "\n";
echo "\nSitemap: " . seo_abs('sitemap.xml') . "\n";
