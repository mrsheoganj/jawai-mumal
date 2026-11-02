<?php
/**
 * SEO engine: titles, descriptions, canonical, Open Graph, Twitter, hreflang, geo tags, JSON-LD (Hotel, Restaurant,
 * TouristTrip, HotelRoom, FAQPage, Article, Breadcrumb, WebSite), analytics/verification tags, robots and sitemap helpers.
 * Everything has a researched default and can be overridden in Admin > SEO.
 */

/** Pages that have their own SEO block in the admin: key => [label, path] */
function seo_pages(): array {
    return [
        'home'       => ['Home', ''],
        'rooms'      => ['Rooms', 'rooms'],
        'restaurant' => ['Restaurant', 'restaurant'],
        'safari'     => ['Safari', 'safari'],
        'jawai'      => ['Explore Jawai', 'jawai'],
        'gallery'    => ['Gallery', 'gallery'],
        'contact'    => ['Contact & directions', 'contact'],
        'book'       => ['Booking / enquiry', 'book'],
        'policies'   => ['Policies', 'policies'],
        'blog'       => ['Travel journal (blog index)', 'blog'],
    ];
}

/** Researched defaults – shown pre-filled in Admin > SEO. */
function seo_defaults(): array {
    return [
        'home' => [
            'title' => 'Jawai Mumal | Hotel, Restaurant & Leopard Safari in Bera',
            'desc'  => 'Stay at Jawai Mumal in Bera, Pali: AC rooms, a pure-vegetarian Rajasthani restaurant and private leopard safaris in the Jawai hills. Book on WhatsApp.',
            'keywords' => 'Jawai hotel, hotel in Bera Rajasthan, Jawai leopard safari, Jawai Bandh stay, leopard safari Rajasthan, Jawai Mumal, Jawai resort, pure veg restaurant Bera, Jawai leopard hills hotel',
        ],
        'rooms' => [
            'title' => 'Rooms in Bera, Jawai | Jawai Mumal',
            'desc'  => 'Comfortable air-conditioned rooms in Bera village, minutes from the Jawai leopard hills. Hot water, Wi-Fi and a vegetarian kitchen on site.',
            'keywords' => 'Jawai hotel rooms, hotel rooms Bera Rajasthan, stay near Jawai leopard hills, Jawai Bandh accommodation, hotel near Jawai dam, Bera hotel booking',
        ],
        'restaurant' => [
            'title' => 'Pure Veg Rajasthani Restaurant in Bera, Jawai',
            'desc'  => 'Pure-vegetarian Rajasthani village food in Bera: dal baati churma, ker sangri, gatte ki sabzi, bajra roti with ghee, tandoor specials and kulhad chai.',
            'keywords' => 'pure veg restaurant Bera, Rajasthani food Jawai, dal baati churma Bera, vegetarian restaurant near Jawai, ker sangri, bajra roti, Rajasthani thali Pali',
        ],
        'safari' => [
            'title' => 'Jawai Leopard Safari Packages & Booking | Jawai Mumal',
            'desc'  => 'Dawn and dusk Jawai leopard safaris in open 4x4 jeeps with local trackers, plus dam birding, photography drives and village visits. Book on WhatsApp.',
            'keywords' => 'Jawai leopard safari, Jawai safari booking, Jawai safari price, leopard safari Rajasthan, Jawai Bandh safari, wildlife photography Jawai, Rabari village visit, Jawai jeep safari',
        ],
        'jawai' => [
            'title' => 'Explore Jawai: Leopards, Dam, Villages & How to Reach',
            'desc'  => 'Guide to Jawai, Pali: leopard hills, Jawai Dam, wildlife, Rabari culture, best time to visit and real distances from Udaipur, Jodhpur and stations.',
            'keywords' => 'Jawai Rajasthan, Jawai leopard hills, Jawai Bandh, Jawai dam birds, how to reach Jawai, best time to visit Jawai, Jawai Pali tourism, things to do in Jawai',
        ],
        'gallery' => [
            'title' => 'Photo Gallery: Jawai Hotel, Food & Leopard Country',
            'desc'  => 'Photos of Jawai Mumal in Bera: the hotel, rooms, Rajasthani vegetarian food and the granite hills, dam and wildlife of Jawai.',
            'keywords' => 'Jawai photos, Jawai leopard photos, Jawai Mumal gallery, Bera hotel photos, Jawai Bandh pictures',
        ],
        'contact' => [
            'title' => 'Contact & Directions to Jawai Mumal, Bera, Pali',
            'desc'  => 'Message Jawai Mumal on WhatsApp or find us near Up-Tehsil Office, Bera village, Bera–Bali road, Pali, Rajasthan. Real driving distances included.',
            'keywords' => 'Jawai Mumal contact, Jawai Mumal address, Bera Pali hotel location, how to reach Jawai Mumal, Jawai hotel WhatsApp',
        ],
        'book' => [
            'title' => 'Book a Stay, Table or Safari | Jawai Mumal',
            'desc'  => 'Request a room, table, leopard safari or party booking at Jawai Mumal, Bera. No online payment; our team confirms availability and the final rate on WhatsApp.',
            'keywords' => 'book Jawai hotel, Jawai safari booking, Jawai Mumal booking, Bera hotel enquiry',
        ],
        'policies' => [
            'title' => 'Policies: Booking, Cancellation & Safari Rules | Jawai Mumal',
            'desc'  => 'Booking, check-in, cancellation, safari-conduct and privacy policies for Jawai Mumal, Bera, Jawai.',
            'keywords' => 'Jawai Mumal policies, cancellation policy, safari rules Jawai',
        ],
        'blog' => [
            'title' => 'Jawai Travel Journal: Leopard Safari, Stay & Food Guides',
            'desc'  => 'Practical Jawai guides: leopard safari tips, best time to visit, how to reach, Rabari culture, birdwatching, Rajasthani vegetarian food and day trips.',
            'keywords' => 'Jawai travel guide, Jawai blog, Jawai leopard safari guide, Jawai travel tips, things to do in Jawai',
        ],
    ];
}

/** One raw (admin-saved) value, trimmed. */
function seo_raw(string $k): string { return trim((string)get_setting_raw($k)); }

/** Global SEO defaults (also pre-filled in the admin form). */
function seo_global_defaults(): array {
    return [
        'seo_image'         => 'assets/images/hotel-front.jpg',
        'seo_keywords'      => 'Jawai, Jawai leopard safari, Bera Rajasthan, Pali Rajasthan, Jawai Bandh, Rajasthan wildlife, Jawai hotel',
        'seo_locale'        => 'en-IN',
        'seo_index'         => '1',
        'seo_business_type' => 'Hotel',
        'seo_price_range'   => '₹₹',
        'seo_same_as'       => '',
        'seo_area_served'   => "Jawai\nBera\nPali district\nRajasthan",
        'seo_amenities'     => "Air conditioning\nFree Wi-Fi\nHot water\nPure-vegetarian restaurant\nLeopard safari arrangements\nParking\nHill views",
        'seo_cuisine'       => "Rajasthani\nIndian\nVegetarian",
        'seo_ga4'           => '',
        'seo_gtm'           => '',
        'seo_gsc'           => '',
        'seo_bing'          => '',
        'seo_robots_extra'  => '',
        'blog_in_menu'      => '0',
        'blog_in_footer'    => '0',
    ];
}
function seo_cfg(string $k): string {
    $v = seo_raw($k);
    if ($v !== '') return $v;
    $d = seo_global_defaults();
    return $d[$k] ?? '';
}

/** Title / description / keywords for a page key, admin override first, then the researched default. */
function seo_field(string $page, string $f): string {
    $v = seo_raw("seo_{$page}_{$f}");
    if ($v !== '') return $v;
    $d = seo_defaults();
    return $d[$page][$f] ?? '';
}

function seo_base(): string { return rtrim(setting('site_url'), '/'); }
function seo_abs(string $path = ''): string { return seo_base() . '/' . ltrim($path, '/'); }
function seo_img_abs(?string $p): string {
    $p = trim((string)$p);
    if ($p === '') $p = seo_cfg('seo_image');
    return preg_match('#^https?://#', $p) ? $p : seo_abs($p);
}
function seo_lines(string $s): array { return array_values(array_filter(array_map('trim', preg_split('/\R/', $s)), 'strlen')); }
function seo_clip(string $s, int $n): string {
    $s = trim(preg_replace('/\s+/', ' ', strip_tags($s)));
    if (mb_strlen($s) <= $n) return $s;
    $c = mb_substr($s, 0, $n - 1);
    $sp = mb_strrpos($c, ' ');
    return rtrim(($sp !== false && $sp > $n * .6 ? mb_substr($c, 0, $sp) : $c), " ,;:–-") . '…';
}

/** Queue a JSON-LD block to be printed in the footer. */
function seo_ld(array $d): void { $GLOBALS['jm_ld'][] = $d; }

function seo_hotel_ld(): array {
    $type = seo_cfg('seo_business_type');
    $addr = seo_lines(setting('address'));
    $sameAs = seo_lines(seo_cfg('seo_same_as'));
    foreach (['instagram', 'facebook'] as $s) if (setting($s) !== '') $sameAs[] = setting($s);
    $d = [
        '@context' => 'https://schema.org', '@type' => in_array($type, ['Hotel', 'LodgingBusiness', 'Resort', 'BedAndBreakfast', 'Hostel', 'Motel'], true) ? $type : 'Hotel',
        '@id' => seo_abs('#hotel'), 'name' => setting('site_name'), 'alternateName' => setting('site_name') . ' Bera Jawai',
        'url' => seo_abs(), 'logo' => seo_img_abs(setting('logo_emblem')),
        'image' => array_values(array_unique([seo_img_abs(seo_cfg('seo_image')), seo_img_abs('assets/images/hotel-day.jpg')])),
        'description' => seo_field('home', 'desc'),
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => implode(', ', array_slice($addr, 0, 2)), 'addressLocality' => 'Bera', 'addressRegion' => 'Rajasthan', 'addressCountry' => 'IN'],
        'priceRange' => seo_cfg('seo_price_range'),
        'checkinTime' => setting('checkin_time'), 'checkoutTime' => setting('checkout_time'),
        'areaServed' => array_map(fn($a) => ['@type' => 'Place', 'name' => $a], seo_lines(seo_cfg('seo_area_served'))),
        'amenityFeature' => array_map(fn($a) => ['@type' => 'LocationFeatureSpecification', 'name' => $a, 'value' => true], seo_lines(seo_cfg('seo_amenities'))),
        'hasMap' => maps_open_url(),
        'knowsAbout' => ['Jawai leopard safari', 'Rajasthani vegetarian cuisine', 'Rabari culture', 'Jawai Dam birdwatching'],
    ];
    if (setting('owner1_phone') !== '') $d['telephone'] = preg_replace('/[^\d+]/', '', setting('owner1_phone'));
    if (setting('email') !== '') $d['email'] = setting('email');
    if (has_coords(setting('map_lat'), setting('map_lng'))) $d['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => (float)setting('map_lat'), 'longitude' => (float)setting('map_lng')];
    if ($sameAs) $d['sameAs'] = array_values(array_unique($sameAs));
    return $d;
}
function seo_restaurant_ld(): array {
    return [
        '@context' => 'https://schema.org', '@type' => 'Restaurant', '@id' => seo_abs('#restaurant'), 'name' => setting('site_name') . ' Restaurant',
        'url' => seo_abs('restaurant'), 'image' => seo_img_abs('assets/images/food-thali-rajasthan.jpg'), 'description' => seo_field('restaurant', 'desc'),
        'servesCuisine' => seo_lines(seo_cfg('seo_cuisine')), 'priceRange' => seo_cfg('seo_price_range'),
        'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Bera', 'addressRegion' => 'Rajasthan', 'addressCountry' => 'IN'],
        'openingHours' => 'Mo-Su 07:00-23:00', 'parentOrganization' => ['@id' => seo_abs('#hotel')],
        'suitableForDiet' => 'https://schema.org/VegetarianDiet',
    ] + (has_coords(setting('map_lat'), setting('map_lng')) ? ['geo' => ['@type' => 'GeoCoordinates', 'latitude' => (float)setting('map_lat'), 'longitude' => (float)setting('map_lng')]] : []);
}
function seo_website_ld(): array {
    return ['@context' => 'https://schema.org', '@type' => 'WebSite', '@id' => seo_abs('#website'), 'name' => setting('site_name'), 'url' => seo_abs(), 'inLanguage' => seo_cfg('seo_locale'), 'publisher' => ['@id' => seo_abs('#hotel')]];
}
function seo_breadcrumb_ld(array $crumbs): array {   // [[name, path], ...] (Home is added)
    $items = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => seo_abs()]];
    foreach ($crumbs as $i => $c) $items[] = ['@type' => 'ListItem', 'position' => $i + 2, 'name' => $c[0], 'item' => seo_abs($c[1])];
    return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
}
function seo_faq_ld(array $faqs): ?array {
    if (!$faqs) return null;
    return ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(fn($f) => ['@type' => 'Question', 'name' => $f['question'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($f['answer'])]], $faqs)];
}

/**
 * Work out everything for the current page.
 * A page may set $seo = ['title','desc','image','type','canonical','noindex','keywords','published','modified'] for dynamic content.
 */
function seo_resolve(array $ctx): array {
    $map = ['home' => 'home', 'stay' => 'rooms', 'dining' => 'restaurant', 'safari' => 'safari', 'jawai' => 'jawai', 'gallery' => 'gallery', 'contact' => 'contact'];
    $key = $ctx['seoKey'] ?? ($map[$ctx['active'] ?? ''] ?? null);
    $s = $ctx['seo'] ?? [];
    $title = $s['title'] ?? ($key ? seo_field($key, 'title') : ($ctx['pageTitle'] ?? ''));
    if ($title === '') $title = setting('site_name') . ' — ' . setting('site_tagline');
    if (!isset($s['title']) && $key === null && !empty($ctx['pageTitle'])) $title = $ctx['pageTitle'] . ' | ' . setting('site_name');
    $desc = $s['desc'] ?? ($key ? seo_field($key, 'desc') : ($ctx['pageDesc'] ?? setting('meta_description')));
    $kw = $s['keywords'] ?? ($key ? seo_field($key, 'keywords') : '');
    $kwAll = trim(implode(', ', array_filter([$kw, seo_cfg('seo_keywords')])), ', ');
    $image = $s['image'] ?? ($key ? (seo_raw("seo_{$key}_image") ?: ($ctx['ogImage'] ?? '')) : ($ctx['ogImage'] ?? ''));
    $path = trim((string)(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? ''), '/');
    $bp = trim(base_path(), '/'); if ($bp !== '' && strpos($path, $bp) === 0) $path = ltrim(substr($path, strlen($bp)), '/');
    $path = preg_replace('~(^|/)index(\.php)?$~', '', $path); $path = preg_replace('~\.php$~', '', $path);
    $canon = $s['canonical'] ?? seo_abs($path);
    $noindex = !empty($s['noindex']) || seo_cfg('seo_index') !== '1' || !empty($GLOBALS['jm_preview']) || http_response_code() >= 400;
    return ['key' => $key, 'title' => $title, 'desc' => seo_clip($desc, 200), 'keywords' => $kwAll, 'image' => seo_img_abs($image), 'type' => $s['type'] ?? 'website',
            'canonical' => $canon, 'noindex' => $noindex, 'published' => $s['published'] ?? '', 'modified' => $s['modified'] ?? '', 'alt' => $s['alt'] ?? setting('site_name')];
}

/** Print everything that belongs inside <head> for search & social. */
function seo_head(array $r): void {
    $e = 'e';
    $loc = str_replace('-', '_', seo_cfg('seo_locale'));
    echo '<title>' . $e($r['title']) . "</title>\n";
    echo '<meta name="description" content="' . $e($r['desc']) . "\">\n";
    if ($r['keywords'] !== '') echo '<meta name="keywords" content="' . $e($r['keywords']) . "\">\n";
    echo '<meta name="robots" content="' . ($r['noindex'] ? 'noindex,follow' : 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1') . "\">\n";
    echo '<link rel="canonical" href="' . $e($r['canonical']) . "\">\n";
    echo '<link rel="alternate" hreflang="' . $e(seo_cfg('seo_locale')) . '" href="' . $e($r['canonical']) . "\">\n";
    echo '<link rel="alternate" hreflang="x-default" href="' . $e($r['canonical']) . "\">\n";
    echo '<meta name="author" content="' . $e(setting('site_name')) . "\">\n";
    echo '<meta property="og:site_name" content="' . $e(setting('site_name')) . "\">\n";
    echo '<meta property="og:locale" content="' . $e($loc) . "\">\n";
    echo '<meta property="og:type" content="' . $e($r['type']) . "\">\n";
    echo '<meta property="og:url" content="' . $e($r['canonical']) . "\">\n";
    echo '<meta property="og:title" content="' . $e($r['title']) . "\">\n";
    echo '<meta property="og:description" content="' . $e($r['desc']) . "\">\n";
    echo '<meta property="og:image" content="' . $e($r['image']) . "\">\n";
    echo '<meta property="og:image:alt" content="' . $e($r['alt']) . "\">\n";
    if ($r['type'] === 'article') {
        if ($r['published']) echo '<meta property="article:published_time" content="' . $e(date('c', strtotime($r['published']))) . "\">\n";
        if ($r['modified'])  echo '<meta property="article:modified_time" content="' . $e(date('c', strtotime($r['modified']))) . "\">\n";
    }
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . $e($r['title']) . "\">\n";
    echo '<meta name="twitter:description" content="' . $e($r['desc']) . "\">\n";
    echo '<meta name="twitter:image" content="' . $e($r['image']) . "\">\n";
    echo '<meta name="geo.region" content="IN-RJ">' . "\n" . '<meta name="geo.placename" content="Bera, Pali, Rajasthan">' . "\n";
    if (has_coords(setting('map_lat'), setting('map_lng'))) {
        $la = trim(setting('map_lat')); $lo = trim(setting('map_lng'));
        echo '<meta name="geo.position" content="' . $e("$la;$lo") . '">' . "\n" . '<meta name="ICBM" content="' . $e("$la, $lo") . "\">\n";
    }
    if (seo_cfg('seo_gsc') !== '')  echo '<meta name="google-site-verification" content="' . $e(seo_cfg('seo_gsc')) . "\">\n";
    if (seo_cfg('seo_bing') !== '') echo '<meta name="msvalidate.01" content="' . $e(seo_cfg('seo_bing')) . "\">\n";
    $gtm = seo_cfg('seo_gtm'); $ga = seo_cfg('seo_ga4');
    if (!$r['noindex'] && $gtm !== '' && preg_match('/^GTM-[A-Z0-9]+$/', $gtm)) {
        echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer'," . json_encode($gtm) . ");</script>\n";
    } elseif (!$r['noindex'] && $ga !== '' && preg_match('/^G-[A-Z0-9]+$/', $ga)) {
        echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $e($ga) . "\"></script>\n<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config'," . json_encode($ga) . ");</script>\n";
    }
}

/** JSON-LD for the current page, printed once before </body>. */
function seo_ld_print(array $r): void {
    $blocks = [];
    $blocks[] = seo_hotel_ld(); $blocks[] = seo_website_ld();
    foreach ($GLOBALS['jm_ld'] ?? [] as $b) $blocks[] = $b;
    foreach ($blocks as $b) echo '<script type="application/ld+json">' . json_encode($b, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</script>\n";
}

// ---------------------------------------------------------------- Blog helpers
function blog_url(string $slug = ''): string { return $slug === '' ? url('blog') : url('blog/' . $slug); }
/** Replace {{page}} tokens in post bodies with real internal links. */
function blog_body(string $html): string {
    return preg_replace_callback('/\{\{([a-z0-9_\-\/\.?=&]+)\}\}/i', fn($m) => e(url($m[1])), $html);
}
/** Adds id anchors to <h2> headings and returns [html, toc[[id,text],...]] for a table of contents. */
function blog_toc(string $html): array {
    $toc = [];
    $html = preg_replace_callback('~<h2>(.*?)</h2>~s', function ($m) use (&$toc) {
        $id = slugify(strip_tags($m[1])); if ($id === '') $id = 'section-' . (count($toc) + 1);
        $toc[] = [$id, trim(strip_tags($m[1]))]; return '<h2 id="' . e($id) . '">' . $m[1] . '</h2>';
    }, $html);
    return [$html, $toc];
}
function blog_published(): array {
    return rows("SELECT * FROM jm_blog WHERE status = 'published' AND published_at <= ? ORDER BY published_at DESC, id DESC", [date('Y-m-d H:i:s')]);
}
function blog_reading_minutes(string $html): int { return max(1, (int)ceil(str_word_count(strip_tags($html)) / 200)); }

// ---------------------------------------------------------------- Health check (Admin > SEO)
function seo_health(): array {
    $out = []; $add = function ($ok, $label, $hint = '') use (&$out) { $out[] = [$ok, $label, $hint]; };
    $add(site_mode() === 'live', 'Website is live (not Coming soon / Maintenance)', 'Search engines are shown a temporary page until the site goes live. This switches on its own at launch time.');
    $add(seo_cfg('seo_index') === '1', 'Search engines are allowed to index the site', 'Turn on “Allow search engines” under Indexing.');
    $add(strpos(setting('site_url'), 'https://') === 0 && strpos(setting('site_url'), 'localhost') === false, 'Website address uses https://', 'Set Website address in Contact, logo & rules.');
    foreach (seo_pages() as $k => $p) {
        $t = seo_field($k, 'title'); $d = seo_field($k, 'desc'); $tl = mb_strlen($t); $dl = mb_strlen($d);
        $add($tl >= 25 && $tl <= 62, $p[0] . ': title length ' . $tl . ' (aim 30–60)');
        $add($dl >= 90 && $dl <= 165, $p[0] . ': description length ' . $dl . ' (aim 110–160)');
    }
    $add(has_coords(setting('map_lat'), setting('map_lng')), 'Exact hotel coordinates set (local SEO)', '');
    $add(seo_cfg('seo_ga4') !== '' || seo_cfg('seo_gtm') !== '', 'Analytics connected (GA4 or GTM)', 'Optional but recommended to measure rankings and visits.');
    $add(seo_cfg('seo_gsc') !== '', 'Google Search Console verified', 'Paste the verification code, then submit the sitemap in Search Console.');
    $n = (int)val("SELECT COUNT(*) FROM jm_blog WHERE status = 'published'");
    $add($n >= 15, 'Journal articles published: ' . $n . ' (15+ recommended)');
    $add(setting('instagram') !== '' || setting('facebook') !== '' || seo_cfg('seo_same_as') !== '', 'Social / Google Business profile links added', 'Add them under Business info for Google.');
    return $out;
}
