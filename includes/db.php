<?php
/**
 * Database Handler & Auto-Initializer for Jawai Mumal
 * Automatically supports MySQL (Hostinger) and SQLite (Local / Fallback)
 */

require_once __DIR__ . '/../config.php';

$pdo = null;
$dbType = 'mysql';

// Ensure data directory exists for sqlite or local assets
$dataDir = __DIR__ . '/../data';
if (!is_dir($dataDir)) {
    @mkdir($dataDir, 0755, true);
}

try {
    // Attempt MySQL connection
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 2
    ]);
    $dbType = 'mysql';
} catch (PDOException $e) {
    // If MySQL connection fails (e.g. database not created yet in Hostinger / local preview),
    // smoothly fallback to SQLite so the website and admin panel run immediately without failing!
    try {
        $sqlitePath = $dataDir . '/jawai_mumal.sqlite';
        $pdo = new PDO("sqlite:" . $sqlitePath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        $dbType = 'sqlite';
    } catch (Exception $sqle) {
        die("Database Initialization Error: " . htmlspecialchars($sqle->getMessage()));
    }
}

// ------------------------------------------------------------------
// Auto-schema initialization if tables don't exist
// ------------------------------------------------------------------
function initDatabaseTables($pdo, $dbType) {
    // Check if rooms table exists
    $tableExists = false;
    try {
        if ($dbType === 'mysql') {
            $stmt = $pdo->query("SHOW TABLES LIKE 'rooms'");
            $tableExists = (bool)$stmt->fetch();
        } else {
            $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='rooms'");
            $tableExists = (bool)$stmt->fetch();
        }
    } catch (Exception $e) {
        $tableExists = false;
    }

    if ($tableExists) {
        return; // Already initialized
    }

    $autoInc = ($dbType === 'mysql') ? "INT AUTO_INCREMENT PRIMARY KEY" : "INTEGER PRIMARY KEY AUTOINCREMENT";

    // 1. Rooms Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS rooms (
        id $autoInc,
        slug VARCHAR(100) UNIQUE,
        name VARCHAR(255) NOT NULL,
        type VARCHAR(100) NOT NULL,
        price INT NOT NULL,
        size VARCHAR(100),
        capacity VARCHAR(100),
        view_type VARCHAR(255),
        image VARCHAR(500),
        description TEXT,
        amenities TEXT,
        status VARCHAR(50) DEFAULT 'available',
        inventory INT DEFAULT 4,
        featured INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Menu Items Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS menu_items (
        id $autoInc,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL,
        price INT NOT NULL,
        is_veg INT DEFAULT 1,
        spice_level VARCHAR(50) DEFAULT 'Medium',
        description TEXT,
        tag VARCHAR(100),
        available INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. Safari Packages Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS safaris (
        id $autoInc,
        slug VARCHAR(100) UNIQUE,
        title VARCHAR(255) NOT NULL,
        duration VARCHAR(100),
        price_per_person INT NOT NULL,
        private_gypsy_price INT NOT NULL,
        vehicle VARCHAR(150),
        difficulty VARCHAR(100) DEFAULT 'Moderate',
        image VARCHAR(500),
        description TEXT,
        highlights TEXT,
        badge VARCHAR(100),
        slots TEXT,
        status VARCHAR(50) DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 4. Stay Bookings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS stay_bookings (
        id $autoInc,
        booking_code VARCHAR(50) UNIQUE,
        guest_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        room_name VARCHAR(255) NOT NULL,
        check_in DATE NOT NULL,
        check_out DATE NOT NULL,
        nights INT NOT NULL,
        guests INT NOT NULL,
        total_price INT NOT NULL,
        payment_status VARCHAR(50) DEFAULT 'Paid',
        status VARCHAR(50) DEFAULT 'Confirmed',
        special_requests TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 5. Dining Bookings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS dining_bookings (
        id $autoInc,
        booking_code VARCHAR(50) UNIQUE,
        guest_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        dining_date DATE NOT NULL,
        time_slot VARCHAR(50) NOT NULL,
        party_size INT NOT NULL,
        dining_type VARCHAR(150) NOT NULL,
        table_preference VARCHAR(150),
        status VARCHAR(50) DEFAULT 'Confirmed',
        special_requests TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 6. Safari Bookings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS safari_bookings (
        id $autoInc,
        booking_code VARCHAR(50) UNIQUE,
        guest_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        safari_title VARCHAR(255) NOT NULL,
        safari_date DATE NOT NULL,
        time_slot VARCHAR(50) NOT NULL,
        guests INT NOT NULL,
        vehicle_type VARCHAR(100) NOT NULL,
        assigned_gypsy VARCHAR(100) DEFAULT 'Gypsy #1 (Royal Green)',
        assigned_guide VARCHAR(100) DEFAULT 'Kumbha Ram Rabari (Tracker)',
        total_price INT NOT NULL,
        status VARCHAR(50) DEFAULT 'Confirmed',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 7. Inquiries Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS inquiries (
        id $autoInc,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50),
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        status VARCHAR(50) DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 8. Admin Users Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
        id $autoInc,
        username VARCHAR(100) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role VARCHAR(50) DEFAULT 'admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // ----------------------------------------------------------------
    // Seed Default Admin Account (admin / admin123)
    // ----------------------------------------------------------------
    $defaultPassHash = password_hash(DEFAULT_ADMIN_PASS, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash, role) VALUES (?, ?, ?)");
    $stmt->execute([DEFAULT_ADMIN_USER, $defaultPassHash, 'Super Admin']);

    // ----------------------------------------------------------------
    // Seed Initial Rooms
    // ----------------------------------------------------------------
    $roomStmt = $pdo->prepare("INSERT INTO rooms (slug, name, type, price, size, capacity, view_type, image, description, amenities, status, inventory, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $roomStmt->execute([
        'royal-tent-suite',
        'The Royal Mumal Tented Suite',
        'Luxury Glamping Tent',
        34000,
        '950 sq. ft.',
        '2 Adults + 1 Child',
        'Granite Hills & Private Campfire',
        'assets/images/room_tent_suite.jpg',
        'An ultra-luxurious handcrafted canvas tent suite perched on a teak deck. Features handcrafted vintage furniture, an en-suite granite bathroom with a standalone copper bathtub, and a private stargazing verandah overlooking the ancient boulders.',
        'Private Heated Plunge Pool, Clawfoot Hand-Beaten Copper Tub, Dedicated Butler Service 24/7, Panoramic Granite Hillside Deck, Kashmir Down King Bedding, High-Speed Starlink Wi-Fi, Evening Campfire Setup',
        'available',
        5,
        1
    ]);

    $roomStmt->execute([
        'granite-cliff-villa',
        'Bespoke Granite Cliff Villa',
        'Heritage Villa',
        48000,
        '1,450 sq. ft.',
        '4 Adults',
        'Sunset Leopard Ridge View',
        'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80',
        'Carved into the natural granite slope, this stone villa combines raw desert architecture with regal Rajasthani opulence. Features two master bedrooms, private heated infinity pool, and an outdoor telescope deck.',
        'Private Infinity Edge Pool, Celestial Telescope on Private Terrace, Double Master Suites with Fireplaces, Outdoor Rain Shower & Jacuzzi, Personal Wildlife Concierge, Inclusive Sunset High-Tea Service',
        'available',
        3,
        1
    ]);

    $roomStmt->execute([
        'wilderness-pavilion',
        'Wilderness Luxury Pavilion',
        'Safari Pavilion',
        26000,
        '720 sq. ft.',
        '2 Adults',
        'Savannah & Acacia Forest',
        'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
        'A private safari lodge enveloped by golden grasses and acacia trees. Designed for avid wildlife photographers and romantic escapes, offering direct views of birdlife and visiting desert wildlife.',
        'Wrap-Around Observation Balcony, Custom Bushcraft Leather Bar, Artisanal Bath Essentials, Photography Gear Charging Station, Breakfast on the Rock Platform',
        'available',
        6,
        0
    ]);

    $roomStmt->execute([
        'maharaja-royal-suite',
        'The Maharaja Heritage Enclave',
        'Presidential Compound',
        68000,
        '2,200 sq. ft.',
        '6 Adults',
        '360° Jawai Valley & Lake View',
        'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1200&q=80',
        'The crown jewel of Jawai Mumal. An expansive private compound comprising two royal tents, private dining pavilion, dedicated security, and an infinity pool mirroring the Jawai sky.',
        'Private Chauffeur & Dedicated 4x4 Gypsy, Private Chef for All Meals, Private Heated Lap Pool, Complimentary Spa Treatments, Exclusive Sunrise Leopard Tracking',
        'available',
        2,
        1
    ]);

    // ----------------------------------------------------------------
    // Seed Initial Menu Items
    // ----------------------------------------------------------------
    $menuStmt = $pdo->prepare("INSERT INTO menu_items (name, category, price, is_veg, spice_level, description, tag, available) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $menuItems = [
        ['Royal Mewari Laal Maas', 'Royal Mewari', 1450, 0, 'High', 'Slow-cooked organic lamb simmered in fiery Mathania chillies, garlic, and whole spices, pot-smoked with charred cloves and pure desi ghee.', 'Chef Signature', 1],
        ['Dal Baati Churma Mahathali', 'Royal Mewari', 1250, 1, 'Medium', 'The definitive Rajasthani feast: 5-lentil panchmel dal, clay-baked baatis drenched in village ghee, trio of churmas (Rose, Besan, Gond), ker sangri, and gatte ki sabzi.', 'Royal Heritage', 1],
        ['Granite Smoked Charcoal Paneer Tikka', 'Bush & Bonfire Grill', 890, 1, 'Mild', 'Fresh country paneer stuffed with anardana & mint chutney, marinated in wild mustard yogurt, barbecued over riverbed granite rocks.', 'Bestseller', 1],
        ['Jawai Wilderness Hunter Chicken Kebab', 'Bush & Bonfire Grill', 1150, 0, 'Medium', 'Tender farm chicken drumsticks rubbed with roasted coriander, black pepper, and jungle wild herbs, flame-grilled on skewers.', 'Woodfire Grilled', 1],
        ['Desert Ker Sangri Caviar', 'Royal Mewari', 950, 1, 'Medium', 'Rare dried Khejri tree beans and desert wild berries tempered with roasted cumin, amchur, and cold-pressed mustard oil. A Thar delicacy.', 'Authentic Folk', 1],
        ['Wood-Fired Truffle & Wild Mushroom Kulcha', 'All-Day Gourmet', 720, 1, 'Mild', 'Crispy tandoori kulcha bread stuffed with morel mushrooms, aged goat cheese, and infused with black truffle butter.', 'Fusion Delicacy', 1],
        ['Mumal Royal Rose & Pistachio Kulfi', 'Signature Desserts', 490, 1, 'Sweet', 'Slow-reduced milk infused with organic Pushkar rose petals, cardamom, and toasted Iranian pistachios, frozen in earthen terracotta cones.', 'Signature', 1],
        ['Maharaja Saffron & Cardamom Ghevar', 'Signature Desserts', 550, 1, 'Sweet', 'Honeycomb Rajasthani disc steeped in saffron rabri syrup, topped with 24k edible gold leaf and almond slivers.', 'Festive Royalty', 1],
        ['Wild Hibiscus & Cumin Sunset Elixir', 'Artisanal Drinks', 390, 1, 'Refreshing', 'Chilled infusion of dried crimson desert hibiscus, roasted cumin, black salt, fresh lime, and sparkling Himalayan spring water.', 'Mocktail', 1],
        ['Smoked Leopard Hill Single Malt Old Fashioned', 'Artisanal Drinks', 850, 1, 'Cocktail', 'Aged single malt stirred with aromatic bitters, wild desert honey syrup, smoked with rosemary and acacia bark in front of your table.', 'Cocktail', 1]
    ];
    foreach ($menuItems as $item) {
        $menuStmt->execute($item);
    }

    // ----------------------------------------------------------------
    // Seed Initial Safari Packages
    // ----------------------------------------------------------------
    $safariStmt = $pdo->prepare("INSERT INTO safaris (slug, title, duration, price_per_person, private_gypsy_price, vehicle, difficulty, image, description, highlights, badge, slots, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $safaris = [
        [
            'safari-dawn-leopard',
            'Dawn Leopard Tracker Expedition',
            '3.5 Hours (05:30 AM – 09:00 AM)',
            5500,
            22000,
            'Custom 4x4 Open Safari Gypsy',
            'Moderate',
            'assets/images/safari_leopard.jpg',
            'As the first light strikes the billion-year-old volcanic granite kopjes, board our specially configured 4x4 open-top safari vehicles. Our trackers read morning pugmarks and alarm calls of langurs and peafowls to pinpoint leopards greeting the morning sun.',
            'High probability leopard sightings on sunrise granite rocks, Professional Senior Naturalist & Local Rabari Tracker guide, Spotting scopes Leica binoculars & fleece blankets provided, Hot Darjeeling tea & fresh pastries served on rock summit',
            'Most Popular',
            '05:30 AM, 06:00 AM',
            'active'
        ],
        [
            'safari-dusk-leopard',
            'Dusk Granite Kopjes & Sunset Leopard Safari',
            '3.5 Hours (04:00 PM – 07:30 PM)',
            5500,
            22000,
            'Custom 4x4 Open Safari Gypsy',
            'Moderate',
            'https://images.unsplash.com/photo-1549366021-9f761d450615?auto=format&fit=crop&w=1200&q=80',
            'Golden hour in Jawai is pure poetry. Travel deep into boulder canyons where majestic big cats bask in the warm twilight. The safari culminates atop a secret granite hill with sundowner drinks as the sky turns fiery crimson.',
            'Witness leopards stretching and descending rocks for nocturnal hunts, Dramatic sunset golden hour over the Aravalli hills, Champagne or spiced artisanal drinks at Jawai scenic vista, Spotting hyenas owls and desert wildcats as darkness falls',
            'Golden Hour',
            '04:00 PM, 04:30 PM',
            'active'
        ],
        [
            'safari-dam-croc-birds',
            'Jawai Dam Flamingo & Crocodile Wetland Expedition',
            '3 Hours (07:30 AM – 10:30 AM)',
            4200,
            17000,
            '4x4 Safari Jeep / Waterfront Boat Track',
            'Easy',
            'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1200&q=80',
            'Jawai Dam is a pristine wetland sanctuary. Cruise through the shoreline wetlands witnessing hundreds of giant marsh crocodiles warming on sandbars and vast flocks of pink migratory flamingos creating a surreal contrast against desert hills.',
            'Thousands of migratory flamingos demoiselle cranes & bar-headed geese, Basking giant Mugger crocodiles along the tranquil reservoir banks, High-power telephoto spotting scopes provided, Lakeside breakfast basket with fresh pressed juices',
            'Bird Sanctuary',
            '07:30 AM, 08:00 AM',
            'active'
        ],
        [
            'safari-rabari-culture',
            'Rabari Pastoral Tribe Heritage & Village Walk',
            '2.5 Hours (09:00 AM – 11:30 AM)',
            2800,
            11000,
            'Walking Tour + Return 4x4',
            'Easy Walking',
            'https://images.unsplash.com/photo-1598899134739-24c46f58b8c0?auto=format&fit=crop&w=1200&q=80',
            'The soul of Jawai lies in the unique coexistence between the indigenous Rabari tribes and wild leopards. Walk through pastoral settlements, sip village chai, and hear firsthand accounts of peaceful coexistence that has existed for centuries.',
            'Interact with the iconic red-turbaned Rabari shepherd families, Learn the sacred bond between leopards and local shepherds, Traditional masala chai and bajra roti inside a mud-plastered dhani, Witness centuries-old camel breeding & silver jewelry craft',
            'Cultural Heritage',
            '09:00 AM, 03:30 PM',
            'active'
        ]
    ];
    foreach ($safaris as $saf) {
        $safariStmt->execute($saf);
    }

    // ----------------------------------------------------------------
    // Seed Sample Bookings (Stays, Dining, Safari)
    // ----------------------------------------------------------------
    $stayBookStmt = $pdo->prepare("INSERT INTO stay_bookings (booking_code, guest_name, email, phone, room_name, check_in, check_out, nights, guests, total_price, payment_status, status, special_requests) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stayBookStmt->execute(['JM-STAY-101', 'Vikramaditya Rathore', 'vikram.rathore@heritage.in', '+91 98290 14552', 'The Royal Mumal Tented Suite', '2026-11-04', '2026-11-07', 3, 2, 102000, 'Paid', 'Confirmed', 'Anniversary surprise setup with red roses and private campfire']);
    $stayBookStmt->execute(['JM-STAY-102', 'Lady Charlotte Cavendish', 'c.cavendish@londonwildlife.uk', '+44 7911 123456', 'The Maharaja Heritage Enclave', '2026-11-10', '2026-11-15', 5, 4, 340000, 'Paid', 'Confirmed', 'Dedicated wildlife naturalist and dietary: gluten-free']);
    $stayBookStmt->execute(['JM-STAY-103', 'Arjun Singhal', 'arjun.singhal@techventures.co', '+91 98100 89211', 'Bespoke Granite Cliff Villa', '2026-10-30', '2026-11-02', 3, 3, 144000, 'Paid', 'Checked In', 'High-angle photography vantage points assistance']);

    $dineBookStmt = $pdo->prepare("INSERT INTO dining_bookings (booking_code, guest_name, email, phone, dining_date, time_slot, party_size, dining_type, table_preference, status, special_requests) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $dineBookStmt->execute(['JM-DINE-201', 'Ananya Deshmukh', 'ananya.d@luxuryjourneys.com', '+91 98450 77123', '2026-11-02', '08:00 PM', 2, 'Private Bush Dinner Under The Stars', 'Rock Summit Candlelit Table', 'Confirmed', 'Anniversary celebration, mild Rajasthani spice']);
    $dineBookStmt->execute(['JM-DINE-202', 'Devraj Kothari', 'devraj.kothari@mewartextiles.com', '+91 99280 44556', '2026-11-01', '01:30 PM', 6, 'The Royal Mumal Rasoi', 'Courtyard Fountain Pavilion', 'Confirmed', 'Dal Baati Mahathali preparation for family']);
    $dineBookStmt->execute(['JM-DINE-203', 'Sarah & Mark Jenkins', 'mjenkins@baynature.org', '+1 415 555 2671', '2026-10-31', '07:30 PM', 2, 'Granite Live Barbecue Terrace', 'Boutique Firepit Edge', 'Seated', 'Signature cocktails recommendation']);

    $safariBookStmt = $pdo->prepare("INSERT INTO safari_bookings (booking_code, guest_name, email, phone, safari_title, safari_date, time_slot, guests, vehicle_type, assigned_gypsy, assigned_guide, total_price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $safariBookStmt->execute(['JM-SAF-301', 'Marcus Sterling', 'm.sterling@natgeophoto.com', '+44 7700 900142', 'Dawn Leopard Tracker Expedition', '2026-11-03', '05:30 AM', 2, 'Private 4x4 Gypsy', 'Gypsy #4 (Forest Green)', 'Kumbha Ram Rabari (Master Tracker)', 22000, 'Confirmed']);
    $safariBookStmt->execute(['JM-SAF-302', 'Sunaina Bajaj', 'sunaina.bajaj@mumbaicapital.com', '+91 98201 33445', 'Dusk Granite Kopjes & Sunset Leopard Safari', '2026-11-01', '04:00 PM', 4, 'Shared 4x4 Gypsy', 'Gypsy #2 (Sandy Khaki)', 'Narendra Singh (Senior Naturalist)', 22000, 'Confirmed']);

    $inqStmt = $pdo->prepare("INSERT INTO inquiries (name, email, phone, subject, message, status) VALUES (?, ?, ?, ?, ?, ?)");
    $inqStmt->execute(['Maharawal Digvijay Tours', 'concierge@luxuryrajasthan.co', '+91 98290 88771', 'Exclusive 3-Day Resort Buyout for Royal Wedding', 'We are organizing a private royal celebration for 40 guests in December. We would like to inquire about full resort buyout, private bush dinner arrangements, and dedicated safari gypsies for 3 nights.', 'Pending']);
    $inqStmt->execute(['Dr. Elena Rostova', 'elena.rostova@wildlifefoundation.ch', '+41 22 555 8912', 'Wildlife Photography Workshop & Drone Permit', 'Planning a 5-day photography group tour focused on Jawai leopards and Rabari culture. Please advise on custom private open Gypsy timings and camera permits.', 'Contacted']);
}

// Automatically ensure tables exist
initDatabaseTables($pdo, $dbType);
