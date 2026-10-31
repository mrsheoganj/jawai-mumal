-- ==============================================================
-- Jawai Mumal - MySQL Database Dump for Hostinger (phpMyAdmin)
-- Hostinger Compatibility: MySQL 5.7+ / MySQL 8.0+ / MariaDB 10.3+
-- Default Admin Login:
--   Username: admin
--   Password: admin123
-- ==============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `admin_users`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL UNIQUE,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(150) DEFAULT 'General Manager',
  `role` varchar(50) DEFAULT 'Super Admin',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `admin_users` (admin / admin123)
INSERT INTO `admin_users` (`id`, `username`, `password_hash`, `full_name`, `role`) VALUES
(1, 'admin', '$2y$10$eEsk1H6qGk4WzO3Jg3rGteK3y8n5w7h7i4B9sW6vGk.n4hC0x1m.y', 'Thakur Bhanupratap Singh', 'Super Admin');

-- --------------------------------------------------------
-- Table structure for table `rooms`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL UNIQUE,
  `name` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `size` varchar(100) DEFAULT NULL,
  `capacity` varchar(100) DEFAULT NULL,
  `view_type` varchar(255) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'available',
  `inventory` int(11) DEFAULT 4,
  `featured` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `rooms`
INSERT INTO `rooms` (`id`, `slug`, `name`, `type`, `price`, `size`, `capacity`, `view_type`, `image`, `description`, `amenities`, `status`, `inventory`, `featured`) VALUES
(1, 'royal-tent-suite', 'The Royal Mumal Tented Suite', 'Luxury Glamping Tent', 34000, '950 sq. ft.', '2 Adults + 1 Child', 'Granite Hills & Private Campfire', 'assets/images/room_tent_suite.jpg', 'An ultra-luxurious handcrafted canvas tent suite perched on a teak deck. Features handcrafted vintage furniture, an en-suite granite bathroom with a standalone copper bathtub, and a private stargazing verandah overlooking the ancient boulders.', 'Private Heated Plunge Pool, Clawfoot Hand-Beaten Copper Tub, Dedicated Butler Service 24/7, Panoramic Granite Hillside Deck, Kashmir Down King Bedding, High-Speed Starlink Wi-Fi, Evening Campfire Setup', 'available', 5, 1),
(2, 'granite-cliff-villa', 'Bespoke Granite Cliff Villa', 'Heritage Villa', 48000, '1,450 sq. ft.', '4 Adults', 'Sunset Leopard Ridge View', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80', 'Carved into the natural granite slope, this stone villa combines raw desert architecture with regal Rajasthani opulence. Features two master bedrooms, private heated infinity pool, and an outdoor telescope deck.', 'Private Infinity Edge Pool, Celestial Telescope on Private Terrace, Double Master Suites with Fireplaces, Outdoor Rain Shower & Jacuzzi, Personal Wildlife Concierge, Inclusive Sunset High-Tea Service', 'available', 3, 1),
(3, 'wilderness-pavilion', 'Wilderness Luxury Pavilion', 'Safari Pavilion', 26000, '720 sq. ft.', '2 Adults', 'Savannah & Acacia Forest', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80', 'A private safari lodge enveloped by golden grasses and acacia trees. Designed for avid wildlife photographers and romantic escapes, offering direct views of birdlife and visiting desert wildlife.', 'Wrap-Around Observation Balcony, Custom Bushcraft Leather Bar, Artisanal Bath Essentials, Photography Gear Charging Station, Breakfast on the Rock Platform', 'available', 6, 0),
(4, 'maharaja-royal-suite', 'The Maharaja Heritage Enclave', 'Presidential Compound', 68000, '2,200 sq. ft.', '6 Adults', '360° Jawai Valley & Lake View', 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1200&q=80', 'The crown jewel of Jawai Mumal. An expansive private compound comprising two royal tents, private dining pavilion, dedicated security, and an infinity pool mirroring the Jawai sky.', 'Private Chauffeur & Dedicated 4x4 Gypsy, Private Chef for All Meals, Private Heated Lap Pool, Complimentary Spa Treatments, Exclusive Sunrise Leopard Tracking', 'available', 2, 1);

-- --------------------------------------------------------
-- Table structure for table `menu_items`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `is_veg` tinyint(1) DEFAULT 1,
  `spice_level` varchar(50) DEFAULT 'Medium',
  `description` text DEFAULT NULL,
  `tag` varchar(100) DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `menu_items`
INSERT INTO `menu_items` (`id`, `name`, `category`, `price`, `is_veg`, `spice_level`, `description`, `tag`, `available`) VALUES
(1, 'Royal Mewari Laal Maas', 'Royal Mewari', 1450, 0, 'High', 'Slow-cooked organic lamb simmered in fiery Mathania chillies, garlic, and whole spices, pot-smoked with charred cloves and pure desi ghee.', 'Chef Signature', 1),
(2, 'Dal Baati Churma Mahathali', 'Royal Mewari', 1250, 1, 'Medium', 'The definitive Rajasthani feast: 5-lentil panchmel dal, clay-baked baatis drenched in village ghee, trio of churmas (Rose, Besan, Gond), ker sangri, and gatte ki sabzi.', 'Royal Heritage', 1),
(3, 'Granite Smoked Charcoal Paneer Tikka', 'Bush & Bonfire Grill', 890, 1, 'Mild', 'Fresh country paneer stuffed with anardana & mint chutney, marinated in wild mustard yogurt, barbecued over riverbed granite rocks.', 'Bestseller', 1),
(4, 'Jawai Wilderness Hunter Chicken Kebab', 'Bush & Bonfire Grill', 1150, 0, 'Medium', 'Tender farm chicken drumsticks rubbed with roasted coriander, black pepper, and jungle wild herbs, flame-grilled on skewers.', 'Woodfire Grilled', 1),
(5, 'Desert Ker Sangri Caviar', 'Royal Mewari', 950, 1, 'Medium', 'Rare dried Khejri tree beans and desert wild berries tempered with roasted cumin, amchur, and cold-pressed mustard oil. A Thar delicacy.', 'Authentic Folk', 1),
(6, 'Wood-Fired Truffle & Wild Mushroom Kulcha', 'All-Day Gourmet', 720, 1, 'Mild', 'Crispy tandoori kulcha bread stuffed with morel mushrooms, aged goat cheese, and infused with black truffle butter.', 'Fusion Delicacy', 1),
(7, 'Mumal Royal Rose & Pistachio Kulfi', 'Signature Desserts', 490, 1, 'Sweet', 'Slow-reduced milk infused with organic Pushkar rose petals, cardamom, and toasted Iranian pistachios, frozen in earthen terracotta cones.', 'Signature', 1),
(8, 'Maharaja Saffron & Cardamom Ghevar', 'Signature Desserts', 550, 1, 'Sweet', 'Honeycomb Rajasthani disc steeped in saffron rabri syrup, topped with 24k edible gold leaf and almond slivers.', 'Festive Royalty', 1),
(9, 'Wild Hibiscus & Cumin Sunset Elixir', 'Artisanal Drinks', 390, 1, 'Refreshing', 'Chilled infusion of dried crimson desert hibiscus, roasted cumin, black salt, fresh lime, and sparkling Himalayan spring water.', 'Mocktail', 1),
(10, 'Smoked Leopard Hill Single Malt Old Fashioned', 'Artisanal Drinks', 850, 1, 'Cocktail', 'Aged single malt stirred with aromatic bitters, wild desert honey syrup, smoked with rosemary and acacia bark in front of your table.', 'Cocktail', 1);

-- --------------------------------------------------------
-- Table structure for table `safaris`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `safaris` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL UNIQUE,
  `title` varchar(255) NOT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `price_per_person` int(11) NOT NULL,
  `private_gypsy_price` int(11) NOT NULL,
  `vehicle` varchar(150) DEFAULT NULL,
  `difficulty` varchar(100) DEFAULT 'Moderate',
  `image` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `highlights` text DEFAULT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `slots` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `safaris`
INSERT INTO `safaris` (`id`, `slug`, `title`, `duration`, `price_per_person`, `private_gypsy_price`, `vehicle`, `difficulty`, `image`, `description`, `highlights`, `badge`, `slots`, `status`) VALUES
(1, 'safari-dawn-leopard', 'Dawn Leopard Tracker Expedition', '3.5 Hours (05:30 AM – 09:00 AM)', 5500, 22000, 'Custom 4x4 Open Safari Gypsy', 'Moderate', 'assets/images/safari_leopard.jpg', 'As the first light strikes the billion-year-old volcanic granite kopjes, board our specially configured 4x4 open-top safari vehicles. Our trackers read morning pugmarks and alarm calls of langurs and peafowls to pinpoint leopards greeting the morning sun.', 'High probability leopard sightings on sunrise granite rocks, Professional Senior Naturalist & Local Rabari Tracker guide, Spotting scopes Leica binoculars & fleece blankets provided, Hot Darjeeling tea & fresh pastries served on rock summit', 'Most Popular', '05:30 AM, 06:00 AM', 'active'),
(2, 'safari-dusk-leopard', 'Dusk Granite Kopjes & Sunset Leopard Safari', '3.5 Hours (04:00 PM – 07:30 PM)', 5500, 22000, 'Custom 4x4 Open Safari Gypsy', 'Moderate', 'https://images.unsplash.com/photo-1549366021-9f761d450615?auto=format&fit=crop&w=1200&q=80', 'Golden hour in Jawai is pure poetry. Travel deep into boulder canyons where majestic big cats bask in the warm twilight. The safari culminates atop a secret granite hill with sundowner drinks as the sky turns fiery crimson.', 'Witness leopards stretching and descending rocks for nocturnal hunts, Dramatic sunset golden hour over the Aravalli hills, Champagne or spiced artisanal drinks at Jawai scenic vista, Spotting hyenas owls and desert wildcats as darkness falls', 'Golden Hour', '04:00 PM, 04:30 PM', 'active'),
(3, 'safari-dam-croc-birds', 'Jawai Dam Flamingo & Crocodile Wetland Expedition', '3 Hours (07:30 AM – 10:30 AM)', 4200, 17000, '4x4 Safari Jeep / Waterfront Boat Track', 'Easy', 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1200&q=80', 'Jawai Dam is a pristine wetland sanctuary. Cruise through the shoreline wetlands witnessing hundreds of giant marsh crocodiles warming on sandbars and vast flocks of pink migratory flamingos creating a surreal contrast against desert hills.', 'Thousands of migratory flamingos demoiselle cranes & bar-headed geese, Basking giant Mugger crocodiles along the tranquil reservoir banks, High-power telephoto spotting scopes provided, Lakeside breakfast basket with fresh pressed juices', 'Bird Sanctuary', '07:30 AM, 08:00 AM', 'active'),
(4, 'safari-rabari-culture', 'Rabari Pastoral Tribe Heritage & Village Walk', '2.5 Hours (09:00 AM – 11:30 AM)', 2800, 11000, 'Walking Tour + Return 4x4', 'Easy Walking', 'https://images.unsplash.com/photo-1598899134739-24c46f58b8c0?auto=format&fit=crop&w=1200&q=80', 'The soul of Jawai lies in the unique coexistence between the indigenous Rabari tribes and wild leopards. Walk through pastoral settlements, sip village chai, and hear firsthand accounts of peaceful coexistence that has existed for centuries.', 'Interact with the iconic red-turbaned Rabari shepherd families, Learn the sacred bond between leopards and local shepherds, Traditional masala chai and bajra roti inside a mud-plastered dhani, Witness centuries-old camel breeding & silver jewelry craft', 'Cultural Heritage', '09:00 AM, 03:30 PM', 'active');

-- --------------------------------------------------------
-- Table structure for table `stay_bookings`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `stay_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_code` varchar(50) NOT NULL UNIQUE,
  `guest_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `room_name` varchar(255) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `nights` int(11) NOT NULL,
  `guests` int(11) NOT NULL,
  `total_price` int(11) NOT NULL,
  `payment_status` varchar(50) DEFAULT 'Paid',
  `status` varchar(50) DEFAULT 'Confirmed',
  `special_requests` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `stay_bookings`
INSERT INTO `stay_bookings` (`id`, `booking_code`, `guest_name`, `email`, `phone`, `room_name`, `check_in`, `check_out`, `nights`, `guests`, `total_price`, `payment_status`, `status`, `special_requests`) VALUES
(1, 'JM-STAY-101', 'Vikramaditya Rathore', 'vikram.rathore@heritage.in', '+91 98290 14552', 'The Royal Mumal Tented Suite', '2026-11-04', '2026-11-07', 3, 2, 102000, 'Paid', 'Confirmed', 'Anniversary surprise setup with red roses and private campfire'),
(2, 'JM-STAY-102', 'Lady Charlotte Cavendish', 'c.cavendish@londonwildlife.uk', '+44 7911 123456', 'The Maharaja Heritage Enclave', '2026-11-10', '2026-11-15', 5, 4, 340000, 'Paid', 'Confirmed', 'Dedicated wildlife naturalist and dietary: gluten-free'),
(3, 'JM-STAY-103', 'Arjun Singhal', 'arjun.singhal@techventures.co', '+91 98100 89211', 'Bespoke Granite Cliff Villa', '2026-10-30', '2026-11-02', 3, 3, 144000, 'Paid', 'Checked In', 'High-angle photography vantage points assistance');

-- --------------------------------------------------------
-- Table structure for table `dining_bookings`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `dining_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_code` varchar(50) NOT NULL UNIQUE,
  `guest_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `dining_date` date NOT NULL,
  `time_slot` varchar(50) NOT NULL,
  `party_size` int(11) NOT NULL,
  `dining_type` varchar(150) NOT NULL,
  `table_preference` varchar(150) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Confirmed',
  `special_requests` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `dining_bookings`
INSERT INTO `dining_bookings` (`id`, `booking_code`, `guest_name`, `email`, `phone`, `dining_date`, `time_slot`, `party_size`, `dining_type`, `table_preference`, `status`, `special_requests`) VALUES
(1, 'JM-DINE-201', 'Ananya Deshmukh', 'ananya.d@luxuryjourneys.com', '+91 98450 77123', '2026-11-02', '08:00 PM', 2, 'Private Bush Dinner Under The Stars', 'Rock Summit Candlelit Table', 'Confirmed', 'Anniversary celebration, mild Rajasthani spice'),
(2, 'JM-DINE-202', 'Devraj Kothari', 'devraj.kothari@mewartextiles.com', '+91 99280 44556', '2026-11-01', '01:30 PM', 6, 'The Royal Mumal Rasoi', 'Courtyard Fountain Pavilion', 'Confirmed', 'Dal Baati Mahathali preparation for family'),
(3, 'JM-DINE-203', 'Sarah & Mark Jenkins', 'mjenkins@baynature.org', '+1 415 555 2671', '2026-10-31', '07:30 PM', 2, 'Granite Live Barbecue Terrace', 'Boutique Firepit Edge', 'Seated', 'Signature cocktails recommendation');

-- --------------------------------------------------------
-- Table structure for table `safari_bookings`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `safari_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_code` varchar(50) NOT NULL UNIQUE,
  `guest_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `safari_title` varchar(255) NOT NULL,
  `safari_date` date NOT NULL,
  `time_slot` varchar(50) NOT NULL,
  `guests` int(11) NOT NULL,
  `vehicle_type` varchar(100) NOT NULL,
  `assigned_gypsy` varchar(100) DEFAULT 'Gypsy #1 (Royal Green)',
  `assigned_guide` varchar(100) DEFAULT 'Kumbha Ram Rabari (Tracker)',
  `total_price` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Confirmed',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `safari_bookings`
INSERT INTO `safari_bookings` (`id`, `booking_code`, `guest_name`, `email`, `phone`, `safari_title`, `safari_date`, `time_slot`, `guests`, `vehicle_type`, `assigned_gypsy`, `assigned_guide`, `total_price`, `status`) VALUES
(1, 'JM-SAF-301', 'Marcus Sterling', 'm.sterling@natgeophoto.com', '+44 7700 900142', 'Dawn Leopard Tracker Expedition', '2026-11-03', '05:30 AM', 2, 'Private 4x4 Gypsy', 'Gypsy #4 (Forest Green)', 'Kumbha Ram Rabari (Master Tracker)', 22000, 'Confirmed'),
(2, 'JM-SAF-302', 'Sunaina Bajaj', 'sunaina.bajaj@mumbaicapital.com', '+91 98201 33445', 'Dusk Granite Kopjes & Sunset Leopard Safari', '2026-11-01', '04:00 PM', 4, 'Shared 4x4 Gypsy', 'Gypsy #2 (Sandy Khaki)', 'Narendra Singh (Senior Naturalist)', 22000, 'Confirmed');

-- --------------------------------------------------------
-- Table structure for table `inquiries`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `inquiries`
INSERT INTO `inquiries` (`id`, `name`, `email`, `phone`, `subject`, `message`, `status`) VALUES
(1, 'Maharawal Digvijay Tours', 'concierge@luxuryrajasthan.co', '+91 98290 88771', 'Exclusive 3-Day Resort Buyout for Royal Wedding', 'We are organizing a private royal celebration for 40 guests in December. We would like to inquire about full resort buyout, private bush dinner arrangements, and dedicated safari gypsies for 3 nights.', 'Pending'),
(2, 'Dr. Elena Rostova', 'elena.rostova@wildlifefoundation.ch', '+41 22 555 8912', 'Wildlife Photography Workshop & Drone Permit', 'Planning a 5-day photography group tour focused on Jawai leopards and Rabari culture. Please advise on custom private open Gypsy timings and camera permits.', 'Contacted');

-- --------------------------------------------------------
-- Table structure for table `settings`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `setting_key` varchar(100) NOT NULL UNIQUE,
  `setting_value` text NOT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `settings`
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('resort_name', 'Jawai Mumal'),
('tagline', 'Luxury Wilderness Camp, Royal Dining & Leopard Safari'),
('phone', '+91 98290 55555'),
('whatsapp', '919829055555'),
('email', 'reservations@jawaimumal.com'),
('address', 'Sena Granite Hills, Near Jawai Dam, Sumerpur, District Pali, Rajasthan 306126, India'),
('currency', '₹'),
('tax_rate', '12'),
('seasonal_discount', '10');

COMMIT;
