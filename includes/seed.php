<?php
/** Demo content created on first run. Everything here can be edited or deleted from the admin panel. */
function jm_seed(PDO $pdo): void {
    $I = 'assets/images/';

    // ---------- Rooms (DEMO – replace names, prices and photos from Admin > Rooms)
    $st = $pdo->prepare('INSERT INTO jm_rooms (slug,name,category,tagline,price,size,capacity,beds,view_type,image,gallery,description,amenities,inventory,featured,status,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $am = "Air conditioning\nFree Wi-Fi\nHot water 24 hours\nTea & coffee maker\nWork desk\nDaily housekeeping";
    $rooms = [
        ['premium-room', 'Premium Room', 'Premium', 'Calm, bright and made for easy stays', 2500, '240 sq ft', '2 adults + 1 child', '1 King bed', 'Hill side', $I.'room-premium.jpg', '', 'A comfortable air-conditioned room with a king bed, soft lighting and a quiet work corner. Our starting rate; rates vary with the season and we confirm the exact rate for your dates on WhatsApp.', $am, 8, 1, 1],
        ['deluxe-room', 'Deluxe Room', 'Deluxe', 'A little more space to spread out', 3000, '280 sq ft', '2 adults + 1 child', '1 King bed', 'Garden side', $I.'room-deluxe.jpg', '', 'A roomier air-conditioned room with a king bed, sitting corner and good natural light, a relaxed base for a few days around Jawai.', $am, 6, 1, 2],
        ['super-deluxe-room', 'Super Deluxe Room', 'Super Deluxe', 'Our most spacious double room', 3500, '320 sq ft', '2 adults + 2 children', '1 King bed + sofa', 'Hill side', $I.'room-suite.jpg', '', 'Our most spacious double room, with a king bed, sofa seating and a large window, good for longer stays and small families.', $am, 4, 1, 3],
    ];
    foreach ($rooms as $r) {
        $st->execute([$r[0], $r[1], $r[2], $r[3], $r[4], $r[5], $r[6], $r[7], $r[8], $r[9], $r[10], $r[11], $r[12], $r[13], $r[14], 'active', $r[15]]);
    }

    // ---------- Menu (100% vegetarian; prices are demo – edit in admin)
    $st = $pdo->prepare('INSERT INTO jm_menu (name,category,price,veg,spice,description,image,tag,available,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?)');
    $menu = [
        ['Jawai Village Thali', 'Village Rajasthani', 450, 1, 'Medium', 'The full village-style meal: dal, seasonal sabzi, kadhi, bajra roti with ghee, rice, papad, chutney, salad and a sweet.', $I.'food-thali-rajasthan.jpg', 'House special'],
        ['Dal Baati Churma', 'Village Rajasthani', 380, 1, 'Medium', 'Wheat baati baked and dunked in ghee, five-lentil panchmel dal, garlic chutney and sweet churma.', $I.'food-dal-baati-churma.jpg', 'Signature'],
        ['Ker Sangri', 'Village Rajasthani', 320, 1, 'Medium', 'Desert beans and berries tempered with cumin, dry mango and mustard oil, a classic of the Marwar villages.', '', 'Local'],
        ['Gatte ki Sabzi', 'Village Rajasthani', 290, 1, 'Medium', 'Gram-flour dumplings simmered in a spiced yoghurt gravy.', '', ''],
        ['Rajasthani Kadhi', 'Village Rajasthani', 220, 1, 'Mild', 'Tangy buttermilk and gram-flour curry with whole spices.', '', ''],
        ['Bajra Roti with Ghee & Lasun Chutney', 'Village Rajasthani', 90, 1, 'Medium', 'Pearl-millet flatbread cooked on a griddle, served with white butter, ghee and red garlic chutney.', '', 'Village favourite'],
        ['Raab (winter special)', 'Village Rajasthani', 160, 1, '', 'Warm millet porridge cooked in buttermilk with jaggery and ghee, a winter morning comfort. Available in season.', '', 'Seasonal'],
        ['Mirchi Vada', 'Snacks & Chaat', 120, 1, 'Hot', 'Large green chilli stuffed with spiced potato, dipped in gram-flour batter and fried.', '', 'Popular'],
        ['Pyaaz Kachori', 'Snacks & Chaat', 90, 1, 'Medium', 'Flaky kachori filled with spiced onion, served with tamarind and mint chutneys.', '', ''],
        ['Dahi Vada', 'Snacks & Chaat', 130, 1, 'Mild', 'Soft lentil dumplings in whisked curd with sweet and tangy chutneys.', '', ''],
        ['Paneer Tikka', 'Tandoor', 340, 1, 'Mild', 'Cottage cheese marinated in hung curd and spices, grilled in the tandoor.', $I.'food-thali-rajasthan.jpg', 'Popular'],
        ['Hariyali Kebab', 'Tandoor', 280, 1, 'Mild', 'Spinach, peas and herb kebabs, crisp outside and soft within.', '', ''],
        ['Tandoori Aloo', 'Tandoor', 240, 1, 'Medium', 'Baby potatoes marinated in yoghurt and spices, roasted till charred.', '', ''],
        ['Paneer Butter Masala', 'Mains', 360, 1, 'Mild', 'Soft paneer in a rich tomato-cashew gravy.', '', ''],
        ['Kadai Paneer', 'Mains', 350, 1, 'Medium', 'Paneer and peppers tossed in freshly ground kadai masala.', '', ''],
        ['Dal Tadka', 'Mains', 240, 1, 'Mild', 'Yellow lentils tempered with ghee, garlic and cumin.', '', ''],
        ['Dal Makhani', 'Mains', 280, 1, 'Mild', 'Black lentils simmered overnight with butter and cream.', '', ''],
        ['Mix Vegetable', 'Mains', 260, 1, 'Medium', 'Seasonal vegetables in an onion-tomato masala.', '', ''],
        ['Butter Naan', 'Breads & Rice', 70, 1, '', 'Soft tandoor-baked bread brushed with butter.', '', ''],
        ['Tandoori Roti', 'Breads & Rice', 35, 1, '', 'Whole-wheat bread from the clay oven.', '', ''],
        ['Jeera Rice', 'Breads & Rice', 180, 1, '', 'Basmati rice tempered with cumin.', '', ''],
        ['Vegetable Pulao', 'Breads & Rice', 220, 1, 'Mild', 'Fragrant basmati cooked with vegetables and whole spices.', '', ''],
        ['Moong Dal Halwa', 'Desserts', 180, 1, '', 'Slow-roasted lentil halwa with ghee, cardamom and almonds.', '', 'Winter favourite'],
        ['Malpua with Rabdi', 'Desserts', 200, 1, '', 'Crisp-edged pancakes soaked in syrup, topped with thickened milk.', '', ''],
        ['Churma Ladoo', 'Desserts', 120, 1, '', 'Crumbled wheat and ghee rolled with jaggery.', '', 'Local'],
        ['Gulab Jamun', 'Desserts', 150, 1, '', 'Warm milk dumplings in cardamom syrup.', '', ''],
        ['Kulhad Masala Chai', 'Café & Drinks', 60, 1, '', 'Strong milk tea with ginger and cardamom, served in a clay kulhad.', '', ''],
        ['Masala Chaas', 'Café & Drinks', 70, 1, '', 'Spiced buttermilk with roasted cumin and mint, the village summer drink.', '', ''],
        ['Sweet Lassi', 'Café & Drinks', 110, 1, '', 'Thick chilled curd drink, sweet or salted.', '', ''],
        ['Cold Coffee', 'Café & Drinks', 140, 1, '', 'Chilled coffee with ice cream.', '', ''],
        ['Fresh Lime Soda', 'Café & Drinks', 90, 1, '', 'Sweet, salted or mixed.', '', ''],
    ];
    foreach ($menu as $i => $m) {
        $st->execute([$m[0], $m[1], $m[2], $m[3], $m[4], $m[5], $m[6], $m[7], 1, $i]);
    }

    // ---------- Safari packages (DEMO prices – edit in admin)
    $st = $pdo->prepare('INSERT INTO jm_safaris (slug,title,duration,timing,price_pp,private_price,vehicle,difficulty,image,description,highlights,badge,status,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $safaris = [
        ['classic-leopard-tracking-safari', 'Classic Leopard Tracking Safari', 'About 3 hours', 'Morning 6:00–9:00 AM · Evening 4:00–7:00 PM', 3500, 12000, 'Private open 4x4 jeep, up to 6 guests', 'Easy', $I.'jawai-leopard-walk.jpg', 'Follow alarm calls, pugmarks and the warmth of sun-struck granite with a local tracker as the hills\' resident leopards come out onto the rocks at first and last light. Jawai\'s leopards live on open granite domes, not in dense forest, so the land itself shows you where to look.', "Open 4x4 jeep with driver and local tracker\nTracking by langur and peafowl alarm calls\nEngine off during sightings, at a respectful distance\nBinoculars on board, hot tea in the field", 'Most popular', 'active', 1],
        ['jawai-dam-wetland-birding-drive', 'Jawai Dam Wetland & Birding Drive', 'About 3 hours', 'Morning 7:00–10:00 AM', 2500, 9000, 'Private open 4x4 jeep, up to 6 guests', 'Easy', '', 'A slower, scope-led drive along the Jawai Dam shoreline. In winter look for flamingos, demoiselle cranes and bar-headed geese; raptors circle the hills and mugger crocodiles bask on the banks. More than 300 bird species have been recorded around Jawai.', "Shoreline birdwatching with a guide\nWinter flocks of flamingos and cranes\nMugger crocodiles on the sandbanks\nGentle pace, ideal for families", 'Birds & wildlife', 'active', 2],
        ['private-4x4-expedition', 'Private 4x4 Expedition', 'Half day or full day', 'Flexible timings', 6000, 20000, 'Private open 4x4 jeep', 'Easy–Moderate', $I.'jawai-leopard-walk.jpg', 'Your own jeep and tracker, with the day shaped around you: longer sightings, unhurried breaks, a boulder-top sundowner or a breakfast picnic on the rocks. Good for families with children, couples marking an occasion and serious wildlife watchers.', "Whole jeep for your group only\nFlexible start time and unhurried sightings\nOptional sundowner on the granite hills\nCombine the leopard hills and the dam in one day", 'Private', 'active', 3],
        ['wildlife-photography-safari', 'Wildlife Photography Safari', 'Multi-drive programmes', 'Dawn and dusk drives', 7000, 22000, 'Private open 4x4 jeep, max 3 photographers', 'Easy', $I.'jawai-leopard-walk.jpg', 'Planned around the light: low-angle positioning, camera beanbags and a tracker who understands composition. Small groups of up to three photographers per jeep so everyone gets a clear line of sight.', "Maximum 3 photographers per jeep\nBeanbag camera mounts on request\nPlanned around golden-hour light\nPatient, no-chase approach", 'Photography', 'active', 4],
        ['rabari-village-culture-visit', 'Rabari Village & Culture Visit', '2 to 2.5 hours', 'Morning 9:00–11:30 AM', 1800, 6500, 'Jeep + walking', 'Easy', '', 'Meet the Rabari herding families who have shared these hills with leopards for generations. Walk through a village, sip chai in a courtyard and hear how people and big cats live side by side.', "Village walk with a local guide\nMasala chai with a family\nStories of living alongside leopards\nRespectful, small groups", 'Culture', 'active', 5],
    ];
    foreach ($safaris as $s) { $st->execute([$s[0], $s[1], $s[2], $s[3], $s[4], $s[5], $s[6], $s[7], $s[8], $s[9], $s[10], $s[11], $s[12], $s[13]]); }

    // ---------- Explore Jawai (distances are approximate – edit in admin)
    $st = $pdo->prepare('INSERT INTO jm_attractions (name,category,distance,description,image,status,sort_order) VALUES (?,?,?,?,?,?,?)');
    $att = [
        ['Jawai Leopard Hills', 'Wildlife', 'Nearby', 'Granite domes, caves and boulder fields where leopards rest by day. A short walk from villages, farms and hill temples. Estimates put the wider Jawai area at roughly 50–70 leopards; there is no official count.', $I.'jawai-leopard-walk.jpg'],
        ['Jawai Bandh Leopard Conservation Reserve', 'Wildlife', 'Nearby', 'A notified conservation reserve around the Jawai hills that helps protect leopards and their habitat while local herding communities continue to graze their animals nearby.', $I.'jawai-leopard-padma.jpg'],
        ['Jawai Dam (Jawai Bandh)', 'Nature', '15 km · about 30 min', 'Completed in 1957 on the Jawai river, one of western Rajasthan\'s largest reservoirs. Mugger crocodiles, winter flamingos, cranes and raptors, with granite hills all around.', $I.'jawai-hills-view.jpg'],
        ['Rabari Villages', 'Culture', 'Nearby', 'Meet the Rabari herders, known for their red turbans and silver jewellery, whose long tradition of living beside leopards is the heart of Jawai\'s story.', $I.'jawai-rabari.jpg'],
        ['Village Food & Chai Stops', 'Experience', 'In and around Bera', 'Bajra roti with ghee, buttermilk, ker sangri and kulhad chai, ask us to arrange a village-style meal or a chai halt on your drive.', $I.'food-thali-rajasthan.jpg'],
        ['Granite Hill Sunrise & Sunset Points', 'Nature', 'Nearby', 'Quiet viewpoints over the boulder-strewn hills, perfect for golden-hour photographs and a flask of tea.', $I.'jawai-bandh.jpg'],
        ['Stargazing & Campfire', 'Experience', 'At the hotel', 'Clear desert skies away from city light. Ask the team to arrange an evening campfire.', $I.'hotel-front.jpg'],
        ['Ranakpur Jain Temples', 'Heritage', '48 km · about 1 hr', 'A marble temple complex with 1,444 carved pillars set in a forested valley.', ''],
        ['Kumbhalgarh Fort', 'Heritage', '81 km · about 1 hr 40 min', 'A 15th-century hill fort with one of the longest continuous walls in the world.', ''],
        ['Mount Abu', 'Nature', '99 km · about 2 hrs', 'Rajasthan\'s only hill station, with Nakki Lake, the Dilwara temples and cool air.', ''],
        ['Udaipur, City of Lakes', 'Heritage', '114 km · about 2 hrs', 'City Palace, Lake Pichola and royal-era havelis.', $I.'hotel-front.jpg'],
        ['Jodhpur, Mehrangarh Fort', 'Heritage', '175 km · about 3 hrs', 'The Blue City and its giant fortress above the old town.', ''],
    ];
    foreach ($att as $i => $a) { $st->execute([$a[0], $a[1], $a[2], $a[3], $a[4], 'active', $i]); }

    // ---------- Gallery
    $st = $pdo->prepare('INSERT INTO jm_gallery (title,category,image,sort_order) VALUES (?,?,?,?)');
    $gal = [
        ['Jawai Mumal, Bera', 'Hotel', $I.'hotel-day.jpg'], ['Jawai Mumal at dusk', 'Hotel', $I.'hotel-front.jpg'], ['Leopard on the rocks', 'Safari', $I.'jawai-leopard-walk.jpg'], ['Premium Room', 'Rooms', $I.'hotel-day.jpg'],
        ['Jawai leopard country', 'Safari', $I.'jawai-leopard-walk.jpg'], ['Village-style curry', 'Dining', $I.'food-thali-rajasthan.jpg'], ['Premium Room', 'Rooms', $I.'hotel-front.jpg'],
        ['Rajasthani bowls', 'Dining', $I.'food-dal-baati-churma.jpg'], ['Premium Room', 'Rooms', $I.'hotel-day.jpg'],
        ['Premium Room', 'Rooms', $I.'hotel-day.jpg'], ['Evening lights', 'Hotel', $I.'hotel-front.jpg'], ['Paneer tikka', 'Dining', $I.'food-thali-rajasthan.jpg'],
    ];
    foreach ($gal as $i => $g) { $st->execute([$g[0], $g[1], $g[2], $i]); }

    // ---------- Testimonials – left hidden on purpose until real guest reviews are added
    $st = $pdo->prepare('INSERT INTO jm_testimonials (name,origin,rating,quote,status,sort_order) VALUES (?,?,?,?,?,?)');
    $st->execute(['Sample guest', 'City', 5, 'Replace this sample with a real guest review, then set it to Visible.', 'hidden', 1]);

    // ---------- FAQs
    $st = $pdo->prepare('INSERT INTO jm_faqs (question,answer,sort_order) VALUES (?,?,?)');
    $faq = [
        ['Where is Jawai Mumal?', 'In Bera village on the Bera–Bali road, near the Up-Tehsil office, in Pali district, Rajasthan, in the middle of Jawai leopard country.'],
        ['Are leopard sightings guaranteed?', 'No honest operator can guarantee a wild animal sighting. Jawai\'s leopards live on open granite hills rather than in thick forest, so the chances over a stay of two or three days are very good, and our trackers read the signs to help you find them.'],
        ['What is the best time to visit Jawai?', 'November to February is the most comfortable: cool, clear mornings (often 8–12°C at dawn), basking leopards and migratory birds on the dam. March to June is hot but rewarding for sightings. July to September is green and dramatic, with fewer visitors.'],
        ['How many safari drives should we book?', 'Three or four drives (morning and evening on two days) give a fair chance of good sightings. Photographers often book more.'],
        ['How is Jawai different from a national park?', 'Jawai is an open, lived-in landscape of granite hills, villages and farms rather than a fenced park. Drives are in private open jeeps on village tracks, with no crowded convoys or fixed zones.'],
        ['Is the restaurant vegetarian?', 'Yes, our kitchen is 100% vegetarian, with Rajasthani village food, tandoor dishes and a café. Jain options are available on request.'],
        ['Can I book party or personal functions?', 'Yes. Birthdays, family functions, small weddings, corporate meets and personal celebrations are all welcome. Use the Party & Events tab or message us on WhatsApp with your date and guest count.'],
        ['What are check-in and check-out times?', 'Check-in is from 12:00 PM and check-out is by 11:00 AM. Early check-in or late check-out can be arranged on request.'],
        ['Is it suitable for children and older travellers?', 'Very much. Private jeeps let us set a gentle pace, shorten drives and plan comfortable breaks.'],
        ['What should I wear on safari?', 'Neutral colours such as khaki and olive, warm layers for winter mornings, a hat, sunscreen and closed shoes. Modest clothing is appreciated on village visits.'],
        ['How do I reach Bera?', 'By road, Udaipur airport is about 129 km (roughly 2 hr 15 min), Jodhpur airport about 173 km (around 2 hr 50 min), Sumerpur Jawai Bandh railway station about 18 km and Falna station about 37 km. See the live map on the Contact page, or message us on WhatsApp for pick-up options.'],
        ['How do I confirm a booking?', 'Send a request on the website or WhatsApp us. Our team confirms availability and the final rate personally, usually within a few hours.'],
    ];
    foreach ($faq as $i => $f) { $st->execute([$f[0], $f[1], $i]); }
}

function jm_seed_slides(PDO $pdo): void {
    $I = 'assets/images/';
    $st = $pdo->prepare('INSERT INTO jm_slides (image,eyebrow,title,text,cta_label,cta_url,cta2_label,cta2_url,status,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?)');
    $rows = [
        [$I.'hotel-front.jpg', 'Bera · Jawai · Rajasthan', 'Stay in the heart of Jawai leopard country', 'A comfortable hotel, a pure-vegetarian Rajasthani kitchen and guided leopard safaris, all in Bera village, Pali.', 'Book your stay', '#book', 'Jawai safari', 'safari.php', 'active', 1],
        [$I.'jawai-leopard-walk.jpg', 'Jawai Safari', 'Leopards on the granite hills', 'Sunrise and sunset drives in open 4x4 jeeps with local trackers who read every alarm call and pugmark.', 'See safaris', 'safari.php', 'Request a safari', 'book.php?type=safari', 'active', 2],
        [$I.'food-thali-rajasthan.jpg', 'Village flavours', 'Rajasthani food, the village way', 'Dal baati churma, ker sangri, bajra roti with ghee and more, a 100% vegetarian kitchen.', 'View the menu', 'restaurant.php', 'Reserve a table', 'book.php?type=dine', 'active', 3],
    ];
    foreach ($rows as $r) { $st->execute($r); }
}

function jm_seed_routes(PDO $pdo): void {
    // Real road distances/routes from Bera village (OpenStreetMap + OSRM), generated once; recalculate any time in Admin > Distances & travel.
    $data = json_decode((string)@file_get_contents(__DIR__ . '/seed_routes.json'), true) ?: [];
    $st = $pdo->prepare('INSERT INTO jm_routes (name,kind,code,km,travel_time,note,lat,lng,geom,status,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
    foreach ($data as $r) {
        $m = (int)round($r['minutes'] * 1.2);
        $h = intdiv($m, 60); $mm = $m % 60;
        $time = $h === 0 ? 'about ' . $m . ' min' : 'about ' . $h . ' hr' . ($mm >= 8 ? ' ' . (int)(round($mm / 5) * 5) . ' min' : '');
        $st->execute([$r['name'], $r['kind'], $r['code'], $r['km'], $time, $r['note'], $r['lat'], $r['lng'], json_encode($r['geom']), 'active', $r['sort']]);
    }
}
