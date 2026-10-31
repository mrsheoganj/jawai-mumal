<?php
/** Demo content created on first run. Everything here can be edited or deleted from the admin panel. */
function jm_seed(PDO $pdo): void {
    $I = 'assets/images/';

    // ---------- Rooms (DEMO – replace names, prices and photos from Admin > Rooms)
    $st = $pdo->prepare('INSERT INTO jm_rooms (slug,name,category,tagline,price,size,capacity,beds,view_type,image,gallery,description,amenities,inventory,featured,status,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $am = "Air conditioning\nFree Wi-Fi\nHot water 24 hours\nTea & coffee maker\nWork desk\nDaily housekeeping";
    $rooms = [
        ['deluxe-room', 'Deluxe Room', 'Deluxe', 'Calm, bright and made for easy stays', 3500, '260 sq ft', '2 adults + 1 child', '1 King bed', 'Courtyard', $I.'room-modern.jpg', $I.'room-bed.jpg', 'A comfortable air-conditioned room with a king bed, soft lighting and a quiet work corner. A good base for travellers and couples exploring Jawai.', $am, 6, 1, 1],
        ['heritage-suite', 'Heritage Suite', 'Suite', 'Warm woods, deep sofas, room to spread out', 6500, '420 sq ft', '2 adults + 2 children', '1 King bed + sofa', 'Garden', $I.'room-heritage.jpg', $I.'room-desert-suite.jpg', 'A larger suite with a separate seating area and Rajasthani-inspired décor. Ideal for families and longer stays.', $am . "\nSeating lounge\nMini fridge", 3, 1, 2],
        ['granite-hill-room', 'Granite Hill Room', 'Premium', 'Wake up facing the hills', 5000, '320 sq ft', '2 adults + 1 child', '1 King bed', 'Hill view', $I.'room-desert-suite.jpg', $I.'room-modern.jpg', 'A premium room with a large window framing the granite hills, a lounge corner and premium bedding.', $am . "\nHill-facing window\nLounge corner", 4, 1, 3],
        ['family-room', 'Family Room', 'Family', 'Space for everyone, close together', 5500, '380 sq ft', '4 adults', '2 Queen beds', 'Courtyard', $I.'room-bed.jpg', $I.'room-heritage.jpg', 'Two queen beds and extra floor space so families and small groups can stay together comfortably.', $am . "\nExtra seating", 4, 0, 4],
        ['terrace-suite', 'Terrace Suite', 'Suite', 'Private terrace for sunsets and chai', 8500, '480 sq ft', '2 adults + 2 children', '1 King bed + sofa', 'Terrace & hills', $I.'villa-pool.jpg', $I.'resort-deck.jpg', 'Our top-floor suite with a private terrace, outdoor seating and the best sunset views at Jawai Mumal.', $am . "\nPrivate terrace\nOutdoor seating", 2, 1, 5],
        ['standard-room', 'Standard Room', 'Standard', 'Simple, clean and great value', 2500, '200 sq ft', '2 adults', '1 Queen bed', 'Street side', $I.'room-bed.jpg', $I.'room-modern.jpg', 'A neat, well-kept room with everything you need for a short stay at a friendly price.', "Air conditioning\nFree Wi-Fi\nHot water 24 hours\nDaily housekeeping", 8, 0, 6],
    ];
    foreach ($rooms as $r) {
        $st->execute([$r[0], $r[1], $r[2], $r[3], $r[4], $r[5], $r[6], $r[7], $r[8], $r[9], $r[10], $r[11], $r[12], $r[13], $r[14], 'active', $r[15]]);
    }

    // ---------- Menu
    $st = $pdo->prepare('INSERT INTO jm_menu (name,category,price,veg,spice,description,image,tag,available,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?)');
    $menu = [
        ['Dal Baati Churma', 'Rajasthani Classics', 380, 1, 'Medium', 'Wheat baati baked and dunked in ghee, five-lentil dal and sweet churma.', $I.'food-thali.jpg', 'House special'],
        ['Laal Maas', 'Rajasthani Classics', 520, 0, 'Hot', 'Mutton slow-cooked with Mathania red chillies, garlic and whole spices.', $I.'food-curry.jpg', 'Signature'],
        ['Ker Sangri', 'Rajasthani Classics', 320, 1, 'Medium', 'Desert beans and berries tempered with cumin, amchur and mustard oil.', '', 'Local'],
        ['Gatte ki Sabzi', 'Rajasthani Classics', 290, 1, 'Medium', 'Gram-flour dumplings in a spiced yoghurt gravy.', '', ''],
        ['Paneer Tikka', 'Grill & Tandoor', 340, 1, 'Mild', 'Cottage cheese marinated in hung curd and spices, grilled in the tandoor.', $I.'food-paneer.jpg', 'Popular'],
        ['Chicken Tikka', 'Grill & Tandoor', 420, 0, 'Medium', 'Juicy chicken pieces marinated overnight, charred on skewers.', '', ''],
        ['Butter Chicken', 'Mains', 450, 0, 'Mild', 'Tandoori chicken in a silky tomato-butter gravy.', $I.'food-bowls.jpg', ''],
        ['Dal Tadka', 'Mains', 240, 1, 'Mild', 'Yellow lentils tempered with ghee, garlic and cumin.', '', ''],
        ['Paneer Butter Masala', 'Mains', 360, 1, 'Mild', 'Soft paneer in a rich tomato-cashew gravy.', '', ''],
        ['Butter Naan', 'Breads & Rice', 70, 1, '', 'Soft tandoor-baked bread brushed with butter.', '', ''],
        ['Bajra Roti', 'Breads & Rice', 60, 1, '', 'Pearl-millet flatbread with white butter and jaggery.', '', 'Local'],
        ['Jeera Rice', 'Breads & Rice', 180, 1, '', 'Basmati rice tempered with cumin.', '', ''],
        ['Gulab Jamun', 'Desserts', 150, 1, '', 'Warm milk dumplings in cardamom syrup.', '', ''],
        ['Rabdi Ghevar', 'Desserts', 220, 1, '', 'Honeycomb sweet topped with thickened milk and saffron.', '', 'Seasonal'],
        ['Masala Chai', 'Café & Drinks', 60, 1, '', 'Strong milk tea with ginger and cardamom.', '', ''],
        ['Cold Coffee', 'Café & Drinks', 140, 1, '', 'Chilled coffee with ice cream.', '', ''],
        ['Fresh Lime Soda', 'Café & Drinks', 90, 1, '', 'Sweet, salted or mixed.', '', ''],
    ];
    foreach ($menu as $i => $m) {
        $st->execute([$m[0], $m[1], $m[2], $m[3], $m[4], $m[5], $m[6], $m[7], 1, $i]);
    }

    // ---------- Safari packages (DEMO prices)
    $st = $pdo->prepare('INSERT INTO jm_safaris (slug,title,duration,timing,price_pp,private_price,vehicle,difficulty,image,description,highlights,badge,status,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $safaris = [
        ['sunrise-leopard-safari', 'Sunrise Leopard Safari', '3 hours', '5:30 AM – 8:30 AM', 3500, 12000, 'Open 4x4 jeep', 'Easy', $I.'leopard-log.jpg', 'Head out with a local tracker while the granite hills glow. Early morning is when leopards are most active on the rocks.', "Local naturalist guide\nBest light for photography\nTea & snacks on the hill\nBinoculars on request", 'Most popular', 1],
        ['sunset-leopard-safari', 'Sunset Leopard Safari', '3 hours', '4:00 PM – 7:00 PM', 3500, 12000, 'Open 4x4 jeep', 'Easy', $I.'safari-sunset.jpg', 'Golden hour across the hills — watch for leopards moving down the rocks as the day cools, and end with a sundowner view.', "Local naturalist guide\nGolden hour photography\nSundowner stop\nReturn before dark", 'Golden hour', 2],
        ['jawai-dam-trail', 'Jawai Dam Birding & Crocodile Trail', '3 hours', '7:30 AM – 10:30 AM', 2500, 9000, 'Jeep + walking', 'Easy', $I.'hills-green.jpg', 'Explore the reservoir edge for migratory birds, marsh crocodiles on the banks, and wide views of the Aravalli hills.', "Birdwatching with guide\nMarsh crocodile spotting\nLakeside breakfast option\nIdeal for families", 'Wildlife', 3],
        ['rabari-village-visit', 'Rabari Village & Culture Visit', '2.5 hours', '9:00 AM – 11:30 AM', 1800, 6500, 'Jeep + walking', 'Easy', $I.'culture-hawamahal.jpg', 'Meet the Rabari herding families whose lives are woven with the hills. Village chai, crafts and stories.', "Village walk\nMasala chai with a family\nLocal craft stops\nRespectful, small groups", 'Culture', 4],
        ['full-day-explorer', 'Full-Day Jawai Explorer', '8 hours', '6:00 AM – 2:00 PM', 7500, 24000, 'Open 4x4 jeep', 'Moderate', $I.'hills-terrace.jpg', 'Sunrise leopard track, a village visit and the dam in one unhurried day, with breakfast and lunch stops.', "Leopard track at sunrise\nVillage & dam stops\nBreakfast and lunch included\nPrivate guide", 'Best value', 5],
    ];
    foreach ($safaris as $s) { $st->execute([$s[0], $s[1], $s[2], $s[3], $s[4], $s[5], $s[6], $s[7], $s[8], $s[9], $s[10], $s[11], 'active', $s[12]]); }

    // ---------- Explore Jawai (distances are approximate – edit in admin)
    $st = $pdo->prepare('INSERT INTO jm_attractions (name,category,distance,description,image,status,sort_order) VALUES (?,?,?,?,?,?,?)');
    $att = [
        ['Jawai Leopard Hills', 'Wildlife', 'Nearby', 'Granite hillocks and caves where leopards live alongside herding communities — one of India\'s best places to see them in the open.', $I.'leopard-log.jpg'],
        ['Jawai Dam', 'Nature', 'Short drive', 'A large reservoir on the Jawai river with marsh crocodiles, migratory birds and sunset views over the Aravallis.', $I.'hills-green.jpg'],
        ['Rabari Villages', 'Culture', 'Nearby', 'Meet the Rabari herders, known for red turbans, silver jewellery and a long tradition of living with leopards.', $I.'culture-hawamahal.jpg'],
        ['Granite Hill Sunrise Point', 'Nature', 'Nearby', 'Climb or drive to a viewpoint over the boulder-strewn hills for sunrise with tea.', $I.'hills-terrace.jpg'],
        ['Bera Village & Local Market', 'Culture', 'In Bera', 'Everyday Rajasthani village life — textiles, sweets and local crafts.', $I.'culture-fort.jpg'],
        ['Stargazing & Campfire', 'Experience', 'At the hotel', 'Clear desert skies, away from city light. Ask the team to arrange an evening campfire.', $I.'resort-night.jpg'],
        ['Ranakpur Jain Temples', 'Heritage', 'Approx. 1.5–2 hrs', 'Marble temple complex with 1,444 carved pillars, set in a forest valley.', $I.'culture-hawamahal.jpg'],
        ['Kumbhalgarh Fort', 'Heritage', 'Approx. 2 hrs', 'A 15th-century fort with one of the longest continuous walls in the world.', $I.'culture-fort.jpg'],
        ['Mount Abu', 'Nature', 'Approx. 2.5–3 hrs', 'Rajasthan\'s only hill station, with lakes, Dilwara temples and cool air.', $I.'hills-green.jpg'],
        ['Jodhpur — Mehrangarh Fort', 'Heritage', 'Approx. 3 hrs', 'The Blue City and its giant fortress above the old town.', $I.'culture-fort.jpg'],
        ['Udaipur — City of Lakes', 'Heritage', 'Approx. 3 hrs', 'City Palace, Lake Pichola and royal-era havelis.', $I.'resort-deck.jpg'],
    ];
    foreach ($att as $i => $a) { $st->execute([$a[0], $a[1], $a[2], $a[3], $a[4], 'active', $i]); }

    // ---------- Gallery
    $st = $pdo->prepare('INSERT INTO jm_gallery (title,category,image,sort_order) VALUES (?,?,?,?)');
    $gal = [
        ['Jawai Mumal, Bera', 'Hotel', $I.'hotel-day.jpg'], ['Jawai Mumal at dusk', 'Hotel', $I.'hotel-front.jpg'], ['Leopard on the rocks', 'Safari', $I.'leopard-log.jpg'], ['Heritage suite', 'Rooms', $I.'room-heritage.jpg'],
        ['Safari at sunset', 'Safari', $I.'safari-sunset.jpg'], ['Rajasthani thali', 'Dining', $I.'food-thali.jpg'], ['Granite Hill Room', 'Rooms', $I.'room-desert-suite.jpg'],
        ['Savanna light', 'Landscape', $I.'hero-savanna.jpg'], ['Restaurant hall', 'Dining', $I.'dining-hall.jpg'], ['Terrace Suite', 'Rooms', $I.'villa-pool.jpg'],
        ['Hills of Jawai', 'Landscape', $I.'hills-green.jpg'], ['Curry & naan', 'Dining', $I.'food-curry.jpg'], ['Hilltop view', 'Landscape', $I.'hills-terrace.jpg'],
        ['Deluxe Room', 'Rooms', $I.'room-modern.jpg'], ['Evening lights', 'Hotel', $I.'resort-night.jpg'], ['Paneer tikka', 'Dining', $I.'food-paneer.jpg'], ['Rajasthan heritage', 'Culture', $I.'culture-hawamahal.jpg'],
    ];
    foreach ($gal as $i => $g) { $st->execute([$g[0], $g[1], $g[2], $i]); }

    // ---------- Testimonials – left hidden on purpose until real guest reviews are added
    $st = $pdo->prepare('INSERT INTO jm_testimonials (name,origin,rating,quote,status,sort_order) VALUES (?,?,?,?,?,?)');
    $st->execute(['Sample guest', 'City', 5, 'Replace this sample with a real guest review, then set it to Visible.', 'hidden', 1]);

    // ---------- FAQs
    $st = $pdo->prepare('INSERT INTO jm_faqs (question,answer,sort_order) VALUES (?,?,?)');
    $faq = [
        ['Where is Jawai Mumal located?', 'In Bera village on the Bera–Bali road, near the Up-Tehsil office, in Pali district, Rajasthan — close to Jawai leopard country.'],
        ['Can I book party or personal functions?', 'Yes. Both party and personal bookings are available. Use the Party / Event form or call us with your date and guest count.'],
        ['What are check-in and check-out times?', 'Check-in is from 12:00 PM and check-out is by 11:00 AM. Early check-in or late check-out can be arranged on request.'],
        ['Do you arrange leopard safaris?', 'Yes. Our team books jeep safaris with local trackers at sunrise and sunset, plus dam and village trips.'],
        ['Is there parking and Wi-Fi?', 'Yes — free Wi-Fi and safe parking are available for guests.'],
        ['What is the best time to visit Jawai?', 'October to March is the most comfortable. Leopards are seen year-round; summer is hot but wildlife sightings can be very good.'],
        ['How do I confirm a booking?', 'Send a request on the site. Our team will call or WhatsApp you to confirm availability and the final rate.'],
    ];
    foreach ($faq as $i => $f) { $st->execute([$f[0], $f[1], $i]); }
}
