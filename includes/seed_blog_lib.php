<?php
/**
 * Builds journal articles from a compact spec so large batches stay consistent.
 * Spec: [slug, title, category, image-name, excerpt, meta_title, meta_description, keywords, intro, [[heading, body], ...], [[question, answer], ...], cta-type]
 * Body markup: parts separated by " || "; a part starting with "- " is a bullet (neighbouring bullets form one list); [text](page) becomes an internal link
 * (page = safari, rooms, contact, restaurant, book?type=safari, blog/<slug>, ...).
 */
function jm_blog_md(string $s): string {
    $h = htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    return preg_replace_callback('/\[([^\]]+)\]\(([^)]+)\)/', fn($m) => '<a href="{{' . html_entity_decode($m[2], ENT_QUOTES) . '}}">' . $m[1] . '</a>', $h);
}
function jm_blog_body(string $body): string {
    $out = ''; $list = false;
    foreach (array_map('trim', explode(' || ', $body)) as $part) {
        if ($part === '') continue;
        if (strpos($part, '- ') === 0) { if (!$list) { $out .= "<ul>\n"; $list = true; } $out .= '<li>' . jm_blog_md(substr($part, 2)) . "</li>\n"; continue; }
        if ($list) { $out .= "</ul>\n"; $list = false; }
        $out .= '<p>' . jm_blog_md($part) . "</p>\n";
    }
    return $out . ($list ? "</ul>\n" : '');
}
function jm_blog_cta(string $type, int $i): string {
    $v = $i % 2;
    $c = [
        'safari' => ["Ready to see Jawai’s leopard country? [Request a safari](book?type=safari) or message the team on [WhatsApp](contact), and read our [complete safari guide](blog/jawai-leopard-safari-complete-guide).", "Want to plan your own drives? See [our safaris](safari), then [send a request](book?type=safari) and we will suggest the best slots."],
        'stay' => ["Our family-run hotel in Bera is a short drive from the hills. See the [rooms](rooms) or [request a stay](book?type=stay), and ask us anything on [WhatsApp](contact).", "Looking for a base in Bera? Browse our [rooms](rooms) and [request your dates](book?type=stay). We reply personally, usually within a few hours."],
        'food' => ["Hungry yet? Our kitchen is 100% vegetarian and cooks Rajasthani village food. See the [restaurant](restaurant) or [request a table](book?type=dine).", "Come and taste it. Visit the [restaurant page](restaurant) or [book a table](book?type=dine), and tell us your dietary needs on [WhatsApp](contact)."],
        'plan' => ["Need help shaping your trip? Message us on [WhatsApp](contact) with your dates and we will suggest a realistic plan, or [send an enquiry](book?type=enquiry).", "Tell us your dates, group size and interests via [our contact page](contact), and we will help you plan a trip that fits."],
        'culture' => ["Bera is a good place to meet this culture respectfully. Read our [Bera village guide](blog/bera-rajasthan-village-guide) and [get in touch](contact) to plan your visit.", "Curious to see it for yourself? [Message us](contact) and we will help you arrange a respectful village visit alongside your safari."],
    ];
    return '<p>' . jm_blog_md($c[$type][$v] ?? $c['plan'][$v]) . "</p>\n";
}

/** Extra sections appended to the thinnest articles (slug => [heading, body]). */
function jm_blog_extras(): array {
    return [
    'golden-jackals-and-small-predators-of-jawai' => ['Why they matter to the hills', 'Jackals and other small hunters keep rodent numbers down and clear carrion, which helps the whole food chain. A jackal sighting at dawn also tells your tracker something about what else is moving, because jackals often react to a leopard nearby. || Jackals are social. You may see a pair or a family, and cubs appear in the cooler months. Keep your jeep still and quiet, and let them go about their morning.'],
    'second-visit-to-jawai-how-to-do-it-differently' => ['A simple plan for your return', '- Night one: an evening drive somewhere new, in a different direction from last time. || - Day two: a long morning drive, then a slow afternoon. || - Night two: stargazing, then another dawn drive. || Share what you saw before with the team, and we will aim for different rocks, waterholes and villages. Many returning guests say the second trip is when they finally notice the small things: birds, tracks and the way the light changes the hills.'],
    'rajasthani-handicrafts-to-buy-near-jawai' => ['How to pack and carry crafts', 'Textiles roll up small and travel well, while leather mojari and glass items need careful wrapping. Ask the shop to pack fragile items, keep receipts for larger purchases and check airline limits if you are flying out. || Look for natural dyes, even stitching and tidy edges. Real hand-block prints show tiny variations; perfectly identical patterns are usually machine-made. Buying from the artisans or cooperatives gives the maker a fairer share.'],
    'udaipur-sightseeing-after-jawai-safari' => ['Where to eat and how to move around', 'Udaipur’s old city has rooftop cafés with lake views and simple thali places for lunch. Auto-rickshaws are easy for short hops, and a car with a driver is best for a day that includes Saheliyon-ki-Bari or the hilltop viewpoints. Book palace tickets and any boat ride in advance during winter weekends. || If you are driving on to Bera afterwards, leave by early afternoon so you arrive before the evening drive.'],
    'rajasthani-breakfast-kachori-poha-mirchi-vada' => ['What goes with it', 'Most breakfasts come with sweet and sour tamarind chutney, green chilli chutney and sometimes a little curd. Pair a crisp kachori with a cup of tea, and balance a spicy mirchi vada with lassi. If you have a sensitive stomach, start with poha or a plain paratha and add the fried items slowly. || Street stalls are busiest early; choose places that are busy and cook everything fresh, and ask for hot food only.'],
    'jodhpur-mehrangarh-fort-and-blue-city-from-jawai' => ['Timing your day', 'Start at Mehrangarh by 9:00 AM when it opens, to beat the heat and the tour buses, then walk down through the old city lanes for lunch. Visit the clock tower market in the afternoon, and finish with sunset from a rooftop café facing the fort. || If you are driving back to Bera, leave Jodhpur by early afternoon. Otherwise stay the night in the old city and continue the next morning.'],
    'mobile-phone-wildlife-photography-on-safari' => ['Editing and sharing', 'A little editing goes a long way. Crop gently, lift shadows, reduce highlights and add a touch of clarity, but avoid heavy filters that hide fur or feather detail. Keep the originals, and only share locations if the sighting is not sensitive. Remember to take in the animal with your own eyes too, because the best moment is rarely the one on the screen.'],
    'photographing-granite-landscapes-and-golden-light-in-jawai' => ['A simple shot list', '- A wide dam view with a boulder in the foreground. || - A silhouette of a tree or herder against the sunset. || - Rock texture close-ups in side light. || - A sweeping hill scene with long shadows at dawn. || - A village lane in warm evening light. || Pick two or three per outing so you work each one properly instead of rushing.'],
    ];
}
/** Turns specs into the rows jm_seed_* functions insert. $first = index of the first article (for dates and image rotation). */
function jm_blog_build(array $specs, int $first, array $imgPool): array {
    $P = [];
    foreach ($specs as $k => $s) {
        [$slug, $title, $cat, $img, $excerpt, $mt, $md, $kw, $intro, $secs, $faq, $cta] = $s;
        $i = $first + $k;
        $body = '<p>' . jm_blog_md($intro) . "</p>\n";
        foreach ($secs as $sec) $body .= '<h2>' . htmlspecialchars($sec[0], ENT_QUOTES, 'UTF-8') . "</h2>\n" . jm_blog_body($sec[1]);
        $ex = jm_blog_extras(); if (isset($ex[$slug])) $body .= '<h2>' . htmlspecialchars($ex[$slug][0], ENT_QUOTES, 'UTF-8') . "</h2>\n" . jm_blog_body($ex[$slug][1]);
        $body .= jm_blog_cta($cta, $i);
        $body .= "<h2>Frequently asked questions</h2>\n";
        foreach ($faq as $qa) $body .= '<h3>' . htmlspecialchars($qa[0], ENT_QUOTES, 'UTF-8') . "</h3>\n<p>" . jm_blog_md($qa[1]) . "</p>\n";
        $image = $img !== '' ? 'assets/images/' . $img . '.jpg' : 'assets/images/' . $imgPool[$i % count($imgPool)] . '.jpg';
        $date = date('Y-m-d', strtotime('2026-09-03') + (int)floor($i * 1.12) * 86400) . ($i % 2 ? ' 15:00:00' : ' 09:00:00');
        $P[] = [$slug, $title, $cat, $image, $date, $excerpt, $mt, $md, $kw, $body];
    }
    return $P;
}
/** Adds articles by slug (never overwrites edits made in Admin). */
function jm_blog_add(array $posts): void {
    $have = array_column(rows('SELECT slug FROM jm_blog'), 'slug');
    $max = (int)val('SELECT COALESCE(MAX(sort_order), 0) FROM jm_blog');
    $st = db()->prepare('INSERT INTO jm_blog (slug,title,category,image,published_at,excerpt,meta_title,meta_description,keywords,body,status,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
    foreach ($posts as $p) { if (in_array($p[0], $have, true)) continue; $st->execute([$p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8], $p[9], 'published', ++$max]); }
}

/** Adds the 54 articles of batches 3-5 once (by slug; Admin edits are never overwritten). */
function jm_seed_blog_batches(): void {
    if (get_setting_raw('blog_batches_v1') === '1') return;
    try {
        require_once __DIR__ . '/seed_blog3.php'; require_once __DIR__ . '/seed_blog4.php'; require_once __DIR__ . '/seed_blog5.php';
        $pool = ['jawai-leopard-walk', 'jawai-leopard-padma', 'jawai-leopard-rock', 'jawai-leopard-watch', 'jawai-leopard-stride', 'jawai-leopard-boulder', 'jawai-bandh', 'jawai-hills-view', 'jawai-granite-hill', 'jawai-granite-plain', 'jawai-boulders',
                 'jawai-lake-birds', 'jawai-rabari', 'jawai-village-hut', 'jawai-bullock-cart', 'jawai-flamingo', 'jawai-peafowl', 'jawai-jackal-run', 'jawai-elder', 'jawai-leopard-sunset'];
        jm_blog_add(array_merge(jm_blog_build(jm_blog_specs_3(), 0, $pool), jm_blog_build(jm_blog_specs_4(), 18, $pool), jm_blog_build(jm_blog_specs_5(), 36, $pool)));
        set_setting('blog_batches_v1', '1');
    } catch (Throwable $e) { /* table not ready yet: try again on the next request */ }
}
