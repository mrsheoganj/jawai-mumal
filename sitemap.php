<?php
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/xml; charset=utf-8');
$base = rtrim(setting('site_url'), '/');
$urls = ['index.php', 'rooms.php', 'restaurant.php', 'safari.php', 'jawai.php', 'gallery.php', 'contact.php', 'book.php', 'policies.php'];
foreach (rows("SELECT slug FROM jm_rooms WHERE status='active'") as $r) $urls[] = 'room.php?slug=' . $r['slug'];
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) echo '  <url><loc>' . htmlspecialchars($base . '/' . ($u === 'index.php' ? '' : $u), ENT_XML1) . '</loc></url>' . "\n";
echo '</urlset>';
