<?php
/**
 * Jawai Mumal - Restro & Royal Dining
 * Authentic 5-Star Boutique Hospitality
 */
$pageTitle = "The Mumal Rasoi & Bush Dining | Restro";
require_once __DIR__ . '/includes/header.php';

// Fetch all available menu items grouped by category
$menuItems = $pdo->query("SELECT * FROM menu_items WHERE available=1 ORDER BY category ASC, price DESC")->fetchAll();
$categories = array_unique(array_column($menuItems, 'category'));
?>

<!-- Restaurant Hero Header -->
<section style="background: linear-gradient(180deg, rgba(18, 31, 23, 0.5) 0%, rgba(18, 31, 23, 0.85) 100%), url('assets/images/dining_thali.jpg') center/cover no-repeat; padding: 7rem 0 4.5rem 0; text-align: center; color: #FFFFFF;">
  <div class="container">
    <span class="hero-badge">THE MUMAL RASOI • RESTAURANT & BARBECUE</span>
    <h1 class="hero-title" style="font-size: 3.2rem; margin-top: 0.8rem;">Restro & Dining Experiences</h1>
    <p class="hero-description" style="max-width: 680px;">
      Authentic regional Rajasthani cuisine, slow-cooked meats, fresh vegetarian thalis, and fireside bush dinners on granite rocks.
    </p>
    <button class="btn btn-brass" onclick="openDiningModal('Private Bush Dinner Under The Stars')">
      Reserve A Table
    </button>
  </div>
</section>

<!-- Dining Timings Bar -->
<div style="background: #FFFFFF; border-bottom: 1px solid var(--color-sand-border); padding: 1rem 0; box-shadow: var(--shadow-subtle);">
  <div class="container" style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 1rem; font-size: 0.88rem;">
    <div><strong>Breakfast:</strong> 07:30 AM – 10:30 AM</div>
    <div><strong>Lunch:</strong> 12:30 PM – 03:00 PM</div>
    <div><strong>Sunset Hi-Tea:</strong> 05:00 PM – 06:30 PM</div>
    <div><strong>Dinner:</strong> 07:30 PM – 10:30 PM</div>
  </div>
</div>

<!-- Dining Experiences Showcase -->
<section class="section" style="padding-top: 3.5rem;">
  <div class="container">
    <div class="dining-experience-grid">
      <div class="dining-exp-card" style="height: 400px;">
        <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1200&q=80" alt="Bush Dinner" class="dining-exp-bg">
        <div class="dining-exp-overlay"></div>
        <div class="dining-exp-content">
          <span class="pillar-badge" style="position: static; margin-bottom: 0.5rem; display: inline-block;">Signature Safari Dinner</span>
          <h3 class="dining-exp-title">Fireside Dinner Under the Stars</h3>
          <p class="dining-exp-desc">
            A private candlelit dining table set on the ancient granite rocks, illuminated by flaming lanterns with live Rajasthani folk musicians.
          </p>
          <button class="btn btn-brass btn-sm" onclick="openDiningModal('Private Bush Dinner Under The Stars')">Book Table</button>
        </div>
      </div>

      <div class="dining-exp-card" style="height: 400px;">
        <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1200&q=80" alt="The Mumal Rasoi" class="dining-exp-bg">
        <div class="dining-exp-overlay"></div>
        <div class="dining-exp-content">
          <span class="pillar-badge" style="position: static; margin-bottom: 0.5rem; display: inline-block;">Indoor Dining Hall</span>
          <h3 class="dining-exp-title">The Mumal Rasoi</h3>
          <p class="dining-exp-desc">
            Classic indoor dining hall with carved sandstone pillars, offering imperial thalis, slow-cooked curries, and fresh tandoori breads.
          </p>
          <button class="btn btn-brass btn-sm" onclick="openDiningModal('The Royal Mumal Rasoi')">Reserve Table</button>
        </div>
      </div>

      <div class="dining-exp-card" style="height: 400px;">
        <img src="https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=1200&q=80" alt="Granite Live Barbecue" class="dining-exp-bg">
        <div class="dining-exp-overlay"></div>
        <div class="dining-exp-content">
          <span class="pillar-badge" style="position: static; margin-bottom: 0.5rem; display: inline-block;">Open-Air Barbecue</span>
          <h3 class="dining-exp-title">Campfire Grill Terrace</h3>
          <p class="dining-exp-desc">
            Charcoal-grilled kebabs, smoked country paneer, and refreshing cocktails served around the evening bonfire.
          </p>
          <button class="btn btn-brass btn-sm" onclick="openDiningModal('Granite Live Barbecue Terrace')">Reserve Barbecue</button>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Full Interactive Digital Menu -->
<section class="section" style="background-color: #F3EFEA;">
  <div class="container">
    <div class="section-title-wrap">
      <span class="section-tag">A La Carte & Thalis</span>
      <h2 class="section-heading">Our Restaurant Menu</h2>
      <p class="section-subtitle">
        Prepared using pure ghee, Mathania chillies, and fresh local produce from nearby farms.
      </p>
    </div>

    <!-- Category Tabs -->
    <div class="menu-category-tabs">
      <button class="menu-tab-btn active" data-category="all">All Dishes (<?php echo count($menuItems); ?>)</button>
      <?php foreach ($categories as $cat): ?>
        <button class="menu-tab-btn" data-category="<?php echo htmlspecialchars($cat); ?>">
          <?php echo htmlspecialchars($cat); ?>
        </button>
      <?php endforeach; ?>
    </div>

    <!-- Menu Grid -->
    <div class="menu-items-grid">
      <?php foreach ($menuItems as $dish): ?>
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
            <span class="text-muted"><?php echo htmlspecialchars($dish['category']); ?> • Spice: <?php echo htmlspecialchars($dish['spice_level']); ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Table Booking CTA -->
    <div style="background: #FFFFFF; border: 1px solid var(--color-sand-border); border-radius: var(--radius-md); padding: 3rem; margin-top: 3.5rem; text-align: center; box-shadow: var(--shadow-subtle);">
      <h3 class="font-serif" style="font-size: 1.8rem; color: var(--color-charcoal); margin-bottom: 0.6rem;">Special Celebrations & Private Dinners</h3>
      <p style="color: var(--color-muted); max-width: 600px; margin: 0 auto 1.6rem auto; font-size: 0.95rem;">
        Planning an anniversary, birthday, or family get-together? We arrange customized candlelit table setups with personal service.
      </p>
      <button class="btn btn-forest" onclick="openDiningModal('Private Bush Dinner Under The Stars')">
        Reserve A Table Now
      </button>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
