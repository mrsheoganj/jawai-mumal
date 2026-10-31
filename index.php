<?php
/**
 * Jawai Mumal - Hotel, Restro & Jawai Safari
 * Realistic 5-Star Boutique Hospitality Website
 */
$pageTitle = "Hotel, Restro & Jawai Safari | Official Website";
require_once __DIR__ . '/includes/header.php';

// Fetch featured rooms
$rooms = $pdo->query("SELECT * FROM rooms ORDER BY price ASC LIMIT 3")->fetchAll();

// Fetch sample menu items for preview
$featuredMenu = $pdo->query("SELECT * FROM menu_items WHERE available=1 ORDER BY id ASC LIMIT 6")->fetchAll();

// Fetch safaris
$safaris = $pdo->query("SELECT * FROM safaris WHERE status='active' ORDER BY id ASC")->fetchAll();
?>

<!-- 1. Hero Section -->
<section class="hero-section">
  <div class="hero-overlay"></div>
  <div class="container hero-content">
    <div class="hero-badge">
      <span>JAWAI BANDH, RAJASTHAN • LUXURY RESORT & WILDLIFE EXPEDITIONS</span>
    </div>
    <h1 class="hero-title">
      Where Wild Leopards Roam Free & Royal Hospitality Thrives
    </h1>
    <p class="hero-description">
      Discover Jawai Mumal — a boutique luxury camp set against ancient granite rock formations. Offering luxury tented suites, authentic fireside Mewari cuisine, and private open 4x4 leopard tracking safaris.
    </p>
    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
      <button class="btn btn-brass" onclick="openModal('stayBookingModal')">
        <span>Reserve A Suite</span>
      </button>
      <button class="btn btn-outline-white" onclick="openModal('safariBookingModal')">
        <span>Book Jawai Safari</span>
      </button>
      <button class="btn btn-outline-white" onclick="openModal('diningBookingModal')">
        <span>Restro & Table</span>
      </button>
    </div>
  </div>
</section>

<!-- 2. Booking Engine Bar -->
<div class="booking-widget-wrapper">
  <div class="booking-widget-card">
    <div class="booking-tabs">
      <button class="booking-tab-btn active" data-tab="tab-stay">
        <span>🏨 Hotel Stays</span>
      </button>
      <button class="booking-tab-btn" data-tab="tab-dining">
        <span>🍽️ Restro & Dining</span>
      </button>
      <button class="booking-tab-btn" data-tab="tab-safari">
        <span>🐆 Jawai Safari</span>
      </button>
    </div>

    <!-- Stay Tab Form -->
    <div class="booking-tab-content active" id="tab-stay">
      <form action="book.php?type=stay" method="POST" class="booking-form-grid">
        <div class="form-group">
          <label>Select Suite</label>
          <select name="room_name" class="form-control" required>
            <?php foreach ($navRooms as $r): ?>
              <option value="<?php echo htmlspecialchars($r['name']); ?>">
                <?php echo htmlspecialchars($r['name']); ?> (₹<?php echo number_format($r['price']); ?>/nt)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Check-In</label>
          <input type="date" name="check_in" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="form-group">
          <label>Check-Out</label>
          <input type="date" name="check_out" class="form-control" required value="<?php echo date('Y-m-d', strtotime('+2 days')); ?>">
        </div>
        <div class="form-group">
          <label>Guests</label>
          <select name="guests" class="form-control">
            <option value="1">1 Guest</option>
            <option value="2" selected>2 Guests</option>
            <option value="3">3 Guests</option>
            <option value="4">4 Guests</option>
          </select>
        </div>
        <div>
          <button type="button" class="btn btn-forest" style="width: 100%;" onclick="openModal('stayBookingModal')">
            Check Rates
          </button>
        </div>
      </form>
    </div>

    <!-- Dining Tab Form -->
    <div class="booking-tab-content" id="tab-dining">
      <form action="book.php?type=dining" method="POST" class="booking-form-grid">
        <div class="form-group">
          <label>Dining Experience</label>
          <select name="dining_type" class="form-control">
            <option>Private Bush Dinner Under The Stars</option>
            <option>The Royal Mumal Rasoi</option>
            <option>Granite Live Barbecue Terrace</option>
          </select>
        </div>
        <div class="form-group">
          <label>Date</label>
          <input type="date" name="dining_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="form-group">
          <label>Time Slot</label>
          <select name="time_slot" class="form-control">
            <option>Lunch: 01:00 PM</option>
            <option>Sunset Hi-Tea: 05:30 PM</option>
            <option selected>Dinner: 07:30 PM</option>
            <option>Bush Dinner: 08:30 PM</option>
          </select>
        </div>
        <div class="form-group">
          <label>Party Size</label>
          <select name="party_size" class="form-control">
            <option value="2">2 Guests</option>
            <option value="4">4 Guests</option>
            <option value="6">6+ Guests</option>
          </select>
        </div>
        <div>
          <button type="button" class="btn btn-forest" style="width: 100%;" onclick="openModal('diningBookingModal')">
            Book Table
          </button>
        </div>
      </form>
    </div>

    <!-- Safari Tab Form -->
    <div class="booking-tab-content" id="tab-safari">
      <form action="book.php?type=safari" method="POST" class="booking-form-grid">
        <div class="form-group">
          <label>Safari Route</label>
          <select name="safari_title" class="form-control">
            <?php foreach ($navSafaris as $s): ?>
              <option value="<?php echo htmlspecialchars($s['title']); ?>">
                <?php echo htmlspecialchars($s['title']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Safari Date</label>
          <input type="date" name="safari_date" class="form-control" required value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
        </div>
        <div class="form-group">
          <label>Departure Timing</label>
          <select name="time_slot" class="form-control">
            <option>Morning Safari: 05:45 AM</option>
            <option selected>Evening Golden Hour: 04:15 PM</option>
            <option>Dam Wetland Tour: 07:30 AM</option>
          </select>
        </div>
        <div class="form-group">
          <label>Gypsy Allocation</label>
          <select name="vehicle_type" class="form-control">
            <option>Shared 4x4 Gypsy</option>
            <option>Private 4x4 Gypsy</option>
          </select>
        </div>
        <div>
          <button type="button" class="btn btn-forest" style="width: 100%;" onclick="openModal('safariBookingModal')">
            Book Safari
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- 3. The Three Pillars of Jawai Mumal -->
<section class="section" style="padding-top: 1rem;">
  <div class="container">
    <div class="section-title-wrap">
      <span class="section-tag">Welcome to Jawai Mumal</span>
      <h2 class="section-heading">Hotel, Restro & Jawai Safari</h2>
      <p class="section-subtitle">
        Everything you need for an unforgettable wildlife getaway in Rajasthan. Designed for couples, families, and nature enthusiasts seeking authentic hospitality.
      </p>
    </div>

    <div class="pillars-grid">
      <!-- Pillar 1: Hotel Stays -->
      <div class="pillar-card">
        <div class="pillar-card-img-wrap">
          <img src="assets/images/room_tent_suite.jpg" alt="Luxury Tented Suite Bedroom" class="pillar-card-img">
          <span class="pillar-badge">Hotel & Stays</span>
        </div>
        <div class="pillar-card-body">
          <h3 class="pillar-card-title">Luxury Tents & Cliff Villas</h3>
          <p class="pillar-card-text">
            Spacious handcrafted canvas suites with solid teakwood beds, private viewing decks, attached modern bathrooms, running hot water, and 24-hour butler assistance.
          </p>
          <div class="pillar-card-footer">
            <span class="text-forest" style="font-weight: 700;">From ₹26,000 / Night</span>
            <a href="rooms.php" class="btn btn-outline-brass btn-sm">View Suites →</a>
          </div>
        </div>
      </div>

      <!-- Pillar 2: Restro & Dining -->
      <div class="pillar-card">
        <div class="pillar-card-img-wrap">
          <img src="assets/images/dining_thali.jpg" alt="Authentic Rajasthani Thali Dining" class="pillar-card-img">
          <span class="pillar-badge">Restro & Dining</span>
        </div>
        <div class="pillar-card-body">
          <h3 class="pillar-card-title">The Mumal Rasoi & Bush BBQ</h3>
          <p class="pillar-card-text">
            Authentic Mewari dishes including slow-cooked Laal Maas, Ker Sangri, and Dal Baati Churma, alongside charcoal grills and private candlelit dinners on granite rocks.
          </p>
          <div class="pillar-card-footer">
            <span class="text-forest" style="font-weight: 700;">Fresh Local Ingredients</span>
            <a href="restaurant.php" class="btn btn-outline-brass btn-sm">View Menu →</a>
          </div>
        </div>
      </div>

      <!-- Pillar 3: Jawai Safari -->
      <div class="pillar-card">
        <div class="pillar-card-img-wrap">
          <img src="assets/images/safari_leopard.jpg" alt="Wild Indian Leopard on Granite Rock" class="pillar-card-img">
          <span class="pillar-badge">Jawai Safari</span>
        </div>
        <div class="pillar-card-body">
          <h3 class="pillar-card-title">Leopard Tracking Safaris</h3>
          <p class="pillar-card-text">
            Explore the granite hills in customized 4x4 open safari Gypsies. Guided by native Rabari trackers with over 20 years of experience reading tracks and alarm calls.
          </p>
          <div class="pillar-card-footer">
            <span class="text-forest" style="font-weight: 700;">Morning & Evening Safaris</span>
            <a href="safari.php" class="btn btn-outline-brass btn-sm">Safari Details →</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 4. Featured Accommodations -->
<section class="section" style="background-color: #F3EFEA;">
  <div class="container">
    <div class="section-title-wrap">
      <span class="section-tag">Accommodations</span>
      <h2 class="section-heading">Tents & Suites Built for Comfort</h2>
      <p class="section-subtitle">
        Every unit features climate control, luxury bedding, private ensuite bathrooms, and unobstructed views of the Jawai landscape.
      </p>
    </div>

    <div class="rooms-grid">
      <?php foreach ($rooms as $room): ?>
        <div class="room-card">
          <div class="room-img-wrap">
            <img src="<?php echo htmlspecialchars($room['image']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" class="room-img">
            <div class="room-price-tag">
              ₹<?php echo number_format($room['price']); ?> <span>/ night</span>
            </div>
          </div>
          <div class="room-body">
            <div class="room-meta">
              <span>📐 <?php echo htmlspecialchars($room['size']); ?></span>
              <span>👥 <?php echo htmlspecialchars($room['capacity']); ?></span>
            </div>
            <h3 class="room-title"><?php echo htmlspecialchars($room['name']); ?></h3>
            <p class="room-desc"><?php echo htmlspecialchars($room['description']); ?></p>
            
            <div class="room-amenities-pills">
              <?php 
                $amenitiesArr = explode(',', $room['amenities']);
                foreach (array_slice($amenitiesArr, 0, 4) as $am): 
              ?>
                <span class="amenity-pill"><?php echo trim(htmlspecialchars($am)); ?></span>
              <?php endforeach; ?>
            </div>

            <div style="display: flex; gap: 0.8rem;">
              <button class="btn btn-forest btn-sm" style="flex: 1;" onclick="openRoomBookingModal('<?php echo addslashes($room['name']); ?>', <?php echo $room['price']; ?>)">
                Book Suite
              </button>
              <a href="rooms.php" class="btn btn-outline-brass btn-sm">Details</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div style="text-align: center; margin-top: 3.5rem;">
      <a href="rooms.php" class="btn btn-outline-brass">View All Rooms & Pricing →</a>
    </div>
  </div>
</section>

<!-- 5. Restro & Menu Preview -->
<section class="section" id="dining">
  <div class="container">
    <div class="section-title-wrap">
      <span class="section-tag">Restaurant & Dining</span>
      <h2 class="section-heading">Fresh, Authentic Rajasthani Food</h2>
      <p class="section-subtitle">
        Prepared daily by experienced local chefs using seasonal farm vegetables, organic milk, and authentic regional spices.
      </p>
    </div>

    <!-- Dining Experiences Banner -->
    <div class="dining-experience-grid">
      <div class="dining-exp-card">
        <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1200&q=80" alt="Bush Dinner" class="dining-exp-bg">
        <div class="dining-exp-overlay"></div>
        <div class="dining-exp-content">
          <span class="pillar-badge" style="position: static; margin-bottom: 0.5rem; display: inline-block;">Outdoor Experience</span>
          <h3 class="dining-exp-title">Fireside Dinner Under the Stars</h3>
          <p class="dining-exp-desc">
            A private candlelit dining table set up on the granite rocks with warm lanterns, babool woodfire, and live Rajasthani folk music.
          </p>
          <button class="btn btn-brass btn-sm" onclick="openDiningModal('Private Bush Dinner Under The Stars')">Reserve Table</button>
        </div>
      </div>

      <div class="dining-exp-card">
        <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1200&q=80" alt="The Mumal Rasoi" class="dining-exp-bg">
        <div class="dining-exp-overlay"></div>
        <div class="dining-exp-content">
          <span class="pillar-badge" style="position: static; margin-bottom: 0.5rem; display: inline-block;">Indoor Dining</span>
          <h3 class="dining-exp-title">The Mumal Rasoi Dining Hall</h3>
          <p class="dining-exp-desc">
            Air-conditioned dining hall with classic Rajasthani architecture, offering thali meals, fresh breads, and breakfast buffets.
          </p>
          <button class="btn btn-brass btn-sm" onclick="openDiningModal('The Royal Mumal Rasoi')">Reserve Table</button>
        </div>
      </div>
    </div>

    <!-- Menu Category Filter Tabs -->
    <div class="menu-category-tabs">
      <button class="menu-tab-btn active" data-category="all">All Specialties</button>
      <button class="menu-tab-btn" data-category="Royal Mewari">Mewari Classics</button>
      <button class="menu-tab-btn" data-category="Bush & Bonfire Grill">Charcoal Grills</button>
      <button class="menu-tab-btn" data-category="Signature Desserts">Desserts</button>
      <button class="menu-tab-btn" data-category="Artisanal Drinks">Beverages</button>
    </div>

    <!-- Menu Items Grid -->
    <div class="menu-items-grid">
      <?php foreach ($featuredMenu as $dish): ?>
        <div class="menu-dish-card" data-category="<?php echo htmlspecialchars($dish['category']); ?>">
          <div class="dish-header">
            <div class="dish-title-wrap">
              <span class="diet-indicator <?php echo $dish['is_veg'] ? 'veg' : 'nonveg'; ?>" title="<?php echo $dish['is_veg'] ? 'Vegetarian' : 'Non-Vegetarian'; ?>"></span>
              <h4 class="dish-title"><?php echo htmlspecialchars($dish['name']); ?></h4>
            </div>
            <div class="dish-price">₹<?php echo number_format($dish['price']); ?></div>
          </div>
          <p class="dish-desc"><?php echo htmlspecialchars($dish['description']); ?></p>
          <div class="dish-meta-footer">
            <span class="dish-tag"><?php echo htmlspecialchars($dish['tag']); ?></span>
            <span class="text-muted">Spice: <?php echo htmlspecialchars($dish['spice_level']); ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div style="text-align: center; margin-top: 3rem;">
      <a href="restaurant.php" class="btn btn-outline-brass">Explore Full Restaurant Menu →</a>
    </div>
  </div>
</section>

<!-- 6. Jawai Safari Section -->
<section class="section" id="safari" style="background-color: var(--color-forest-dark); color: #F1F5F9;">
  <div class="container">
    <div class="section-title-wrap">
      <span class="section-tag" style="color: var(--color-brass-light);">Wildlife Safari</span>
      <h2 class="section-heading" style="color: #FFFFFF;">Jawai Leopard Expeditions</h2>
      <p class="section-subtitle" style="color: #CBD5E1;">
        Morning and evening 4x4 Gypsy drives into the leopard country, led by certified naturalists and indigenous Rabari trackers.
      </p>
    </div>

    <div class="safari-grid">
      <?php foreach ($safaris as $safari): ?>
        <div class="safari-card">
          <div class="safari-img-wrap">
            <img src="<?php echo htmlspecialchars($safari['image']); ?>" alt="<?php echo htmlspecialchars($safari['title']); ?>" class="safari-img">
            <span class="pillar-badge"><?php echo htmlspecialchars($safari['badge']); ?></span>
          </div>
          <div class="safari-body">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.6rem;">
              <span style="font-size: 0.85rem; color: var(--color-brass-dark); font-weight: 600;">⏱️ <?php echo htmlspecialchars($safari['duration']); ?></span>
              <span style="font-weight: 700; color: var(--color-forest); font-size: 1.1rem;">₹<?php echo number_format($safari['price_per_person']); ?> <span style="font-size: 0.75rem; color: var(--color-muted); font-weight: normal;">/ seat</span></span>
            </div>
            <h3 class="safari-title"><?php echo htmlspecialchars($safari['title']); ?></h3>
            <p class="safari-desc"><?php echo htmlspecialchars($safari['description']); ?></p>
            
            <ul class="safari-highlights-list">
              <?php 
                $hlArr = explode(',', $safari['highlights']);
                foreach (array_slice($hlArr, 0, 3) as $hl): 
              ?>
                <li><?php echo trim(htmlspecialchars($hl)); ?></li>
              <?php endforeach; ?>
            </ul>

            <div style="display: flex; gap: 0.8rem; margin-top: auto;">
              <button class="btn btn-forest btn-sm" style="flex: 1;" onclick="openSafariBookingModal('<?php echo addslashes($safari['title']); ?>', <?php echo $safari['price_per_person']; ?>, <?php echo $safari['private_gypsy_price']; ?>)">
                Book Slot
              </button>
              <a href="safari.php" class="btn btn-outline-brass btn-sm">Itinerary</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 7. The Story & Culture of Jawai -->
<section class="section" id="story">
  <div class="container">
    <div class="story-wrapper">
      <div class="story-img-composition">
        <img src="assets/images/safari_gypsy.jpg" alt="Safari vehicle on Jawai trail" class="story-main-img">
        <div class="story-floating-stat">
          <span class="stat-number">98%</span>
          <span class="stat-label">Sighting Record</span>
        </div>
      </div>

      <div>
        <span class="section-tag">Living in Harmony</span>
        <h2 class="section-heading" style="text-align: left;">The Unique Coexistence of Jawai</h2>
        <p style="color: var(--color-charcoal); font-size: 1.02rem; margin-bottom: 1.2rem; line-height: 1.7;">
          Jawai is one of the few places in the world where wild leopards live peacefully in close proximity to human settlements. The indigenous Rabari pastoral community, recognizable by their vibrant red turbans, has shared these granite hills with leopards for generations without conflict.
        </p>
        <p style="color: var(--color-muted); margin-bottom: 2rem; line-height: 1.6;">
          At Jawai Mumal, our safaris are conducted with utmost respect for wildlife. We strictly maintain safe viewing distances, do not use headlights or flash photography, and employ local trackers who have known these individual leopards since birth.
        </p>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; border-top: 1px solid var(--color-sand-border); padding-top: 1.5rem;">
          <div>
            <span style="font-size: 1.8rem; color: var(--color-forest); font-weight: 800; display: block;">100+</span>
            <span style="font-size: 0.8rem; color: var(--color-muted);">Local Leopards</span>
          </div>
          <div>
            <span style="font-size: 1.8rem; color: var(--color-forest); font-weight: 800; display: block;">280+</span>
            <span style="font-size: 0.8rem; color: var(--color-muted);">Bird Species</span>
          </div>
          <div>
            <span style="font-size: 1.8rem; color: var(--color-forest); font-weight: 800; display: block;">150+ Yrs</span>
            <span style="font-size: 0.8rem; color: var(--color-muted);">Zero Conflicts</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 8. Real Guest Reviews -->
<section class="section" id="reviews" style="background-color: #F3EFEA;">
  <div class="container">
    <div class="section-title-wrap">
      <span class="section-tag">Guest Experiences</span>
      <h2 class="section-heading">What Travelers Say About Jawai Mumal</h2>
      <p class="section-subtitle">Real feedback from families, wildlife photographers, and honeymooners.</p>
    </div>

    <div class="reviews-grid">
      <div class="review-card">
        <div class="review-stars">★★★★★</div>
        <h4 class="review-title">"Spotted 3 leopards on our very first drive"</h4>
        <p class="review-comment">
          "The morning safari was incredible. Our Rabari tracker spotted a female leopard with her cub on the rocks within 30 minutes of leaving camp. The luxury tents are huge, clean, and comfortable, with warm blankets and excellent tea."
        </p>
        <div class="reviewer-name">Rahul & Priya Sharma</div>
        <div class="reviewer-location">Mumbai • Stayed in Luxury Tented Suite (Oct 2026)</div>
      </div>

      <div class="review-card">
        <div class="review-stars">★★★★★</div>
        <h4 class="review-title">"Authentic food and wonderful staff"</h4>
        <p class="review-comment">
          "The Dal Baati and Laal Maas at the restaurant were genuinely delicious. The staff arranged a private bonfire outside our tent in the evening. Very peaceful place, away from city noise. Highly recommended."
        </p>
        <div class="reviewer-name">Amitabh Sen</div>
        <div class="reviewer-location">Kolkata • Stayed in Granite Cliff Villa (Oct 2026)</div>
      </div>

      <div class="review-card">
        <div class="review-stars">★★★★★</div>
        <h4 class="review-title">"Unbeatable hospitality and photography angles"</h4>
        <p class="review-comment">
          "As an avid wildlife photographer, having open-top 4x4 Gypsies with knowledgeable drivers made all the difference. The sunset views over the Jawai Dam with flamingos were mesmerizing."
        </p>
        <div class="reviewer-name">David & Sarah Miller</div>
        <div class="reviewer-location">London, UK • 3-Night Safari Package (Sep 2026)</div>
      </div>
    </div>
  </div>
</section>

<!-- 9. Contact, Route & Interactive Google Map -->
<section class="section" id="contact">
  <div class="container">
    <div class="section-title-wrap">
      <span class="section-tag">Find & Contact Us</span>
      <h2 class="section-heading">Plan Your Visit to Jawai</h2>
      <p class="section-subtitle">
        Get in touch with our team for room bookings, safari slots, or airport pickup arrangements.
      </p>
    </div>

    <div class="contact-grid">
      <!-- Contact Details & Travel Info -->
      <div class="contact-info-card">
        <h3 class="font-serif" style="font-size: 1.6rem; margin-bottom: 1.4rem;">Location & Connectivity</h3>
        
        <div class="contact-info-item">
          <div class="contact-info-icon">📍</div>
          <div>
            <h4 style="font-size: 0.95rem; margin-bottom: 0.2rem;">Resort Address</h4>
            <p style="color: var(--color-muted); font-size: 0.88rem;"><?php echo RESORT_ADDRESS; ?></p>
          </div>
        </div>

        <div class="contact-info-item">
          <div class="contact-info-icon">🚗</div>
          <div>
            <h4 style="font-size: 0.95rem; margin-bottom: 0.2rem;">Nearest Transit Points</h4>
            <p style="color: var(--color-muted); font-size: 0.88rem; line-height: 1.6;">
              • <strong>Falna Railway Station (FA)</strong>: 28 km (35 mins drive)<br>
              • <strong>Udaipur Airport (UDR)</strong>: 135 km (2.5 hrs via NH27)<br>
              • <strong>Jodhpur Airport (JDH)</strong>: 155 km (2.5 hrs drive)<br>
              • Taxi transfers available on request.
            </p>
          </div>
        </div>

        <div class="contact-info-item">
          <div class="contact-info-icon">📞</div>
          <div>
            <h4 style="font-size: 0.95rem; margin-bottom: 0.2rem;">Direct Reservations</h4>
            <p style="color: var(--color-muted); font-size: 0.88rem;">
              Phone: <a href="tel:<?php echo str_replace(' ', '', RESORT_PHONE); ?>" style="color: var(--color-forest); font-weight: 600;"><?php echo RESORT_PHONE; ?></a><br>
              WhatsApp: <a href="https://wa.me/<?php echo RESORT_WHATSAPP; ?>" style="color: #25d366; font-weight: 600;">+<?php echo RESORT_WHATSAPP; ?></a><br>
              Email: <a href="mailto:<?php echo RESORT_EMAIL; ?>"><?php echo RESORT_EMAIL; ?></a>
            </p>
          </div>
        </div>

        <!-- Google Map Embed -->
        <div style="border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--color-sand-border); height: 180px; margin-top: 1rem;">
          <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d115168.99596884639!2d73.07842255!3d25.0763567!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x394285817c805cd7%3A0xe54e20794ca730a8!2sJawai%20Bandh%2C%20Rajasthan!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" 
            width="100%" 
            height="100%" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>

      <!-- Realistic Inquiry Form -->
      <div class="contact-form-card">
        <h3 class="font-serif" style="font-size: 1.6rem; margin-bottom: 0.5rem;">Send Us a Message</h3>
        <p class="text-muted" style="font-size: 0.88rem; margin-bottom: 1.6rem;">Have questions regarding safari timings, child policies, or package deals? Write to us.</p>

        <form action="contact.php" method="POST">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" name="name" class="form-control" required placeholder="e.g. Rahul Sharma">
            </div>
            <div class="form-group">
              <label>Phone / WhatsApp</label>
              <input type="tel" name="phone" class="form-control" required placeholder="+91 98000 00000">
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
              <label>Email Address</label>
              <input type="email" name="email" class="form-control" required placeholder="name@email.com">
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
              <label>Subject</label>
              <select name="subject" class="form-control">
                <option>Room Booking Inquiry</option>
                <option>Safari Slot Availability</option>
                <option>Restaurant / Table Booking</option>
                <option>Group / Family Package</option>
                <option>Airport Pick-up & Transport</option>
              </select>
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
              <label>Your Message / Preferred Dates</label>
              <textarea name="message" class="form-control" rows="4" required placeholder="Please mention your tentative travel dates, number of guests, and any specific questions..."></textarea>
            </div>
          </div>
          <button type="submit" class="btn btn-forest" style="width: 100%;">Send Inquiry</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
