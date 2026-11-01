<?php
/** /llms.txt – a plain-text summary for AI search assistants. Generated from your site settings and journal. */
require_once __DIR__ . '/includes/db.php';
header('Content-Type: text/plain; charset=utf-8');
if (seo_cfg('seo_index') !== '1') { echo "# " . setting('site_name') . "\n"; exit; }
$n = setting('site_name');
echo "# $n\n\n> " . seo_field('home', 'desc') . "\n\n";
echo "$n is a hotel, pure-vegetarian Rajasthani restaurant and leopard-safari operator in Bera village, Pali district, Rajasthan, India, at the edge of the Jawai leopard hills. Bookings are handled on WhatsApp.\n\n";
echo "## Main pages\n";
foreach (['rooms' => 'Rooms & suites', 'restaurant' => 'Restaurant', 'safari' => 'Jawai leopard safari', 'jawai' => 'Explore Jawai & distances', 'contact' => 'Contact & directions', 'policies' => 'Policies'] as $k => $l) echo "- [$l](" . seo_abs($k) . "): " . seo_field($k, 'desc') . "\n";
echo "\n## Journal\n";
foreach (blog_published() as $p) echo "- [" . $p['title'] . "](" . seo_abs('blog/' . $p['slug']) . "): " . $p['excerpt'] . "\n";
