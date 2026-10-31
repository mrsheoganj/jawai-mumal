<?php
/**
 * Jawai Mumal - Jawai Leopard Safari & Wildlife Expeditions
 * Authentic 5-Star Boutique Hospitality
 */
$pageTitle = "Jawai Safari | Leopard & Wildlife Expeditions";
require_once __DIR__ . '/includes/header.php';

// Fetch all safaris
$safaris = $pdo->query("SELECT * FROM safaris WHERE status='active' ORDER BY id ASC")->fetchAll();
?>

<!-- Safari Hero Header -->
<section style="background: linear-gradient(180deg, rgba(18, 31, 23, 0.5) 0%, rgba(18, 31, 23, 0.85) 100%), url('assets/images/safari_leopard.jpg') center/cover no-repeat; padding: 7rem 0 4.5rem 0; text-align: center; color: #FFFFFF;">
  <div class="container">
    <span class="hero-badge">JAWAI LEOPARD EXPEDITIONS</span>
    <h1 class="hero-title" style="font-size: 3.2rem; margin-top: 0.8rem;">Jawai Leopard Safaris</h1>
    <p class="hero-description" style="max-width: 680px;">
      Explore the rocky granite terrain in customized open-top 4x4 Gypsies. Guided by native Rabari trackers with a 98% sighting record.
    </p>
    <button class="btn btn-brass" onclick="openModal('safariBookingModal')">
      Book Safari Gypsy / Seat
    </button>
  </div>
</section>

<!-- Safari Highlights Bar -->
<section class="section" style="padding-top: 3.5rem;">
  <div class="container">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.8rem; margin-bottom: 3.5rem;">
      <div style="background: #FFFFFF; padding: 1.8rem; border-radius: var(--radius-md); border: 1px solid var(--color-sand-border); box-shadow: var(--shadow-subtle); text-align: center;">
        <span class="stat-number">98%</span>
        <h4 style="color: var(--color-charcoal); margin: 0.4rem 0;">Sighting Probability</h4>
        <p style="font-size: 0.85rem; color: var(--color-muted);">Consistently high sighting rate across morning and evening drives.</p>
      </div>

      <div style="background: #FFFFFF; padding: 1.8rem; border-radius: var(--radius-md); border: 1px solid var(--color-sand-border); box-shadow: var(--shadow-subtle); text-align: center;">
        <span class="stat-number">4x4</span>
        <h4 style="color: var(--color-charcoal); margin: 0.4rem 0;">Custom Open Gypsies</h4>
        <p style="font-size: 0.85rem; color: var(--color-muted);">Raised stadium seats with 360-degree photography angles.</p>
      </div>

      <div style="background: #FFFFFF; padding: 1.8rem; border-radius: var(--radius-md); border: 1px solid var(--color-sand-border); box-shadow: var(--shadow-subtle); text-align: center;">
        <span class="stat-number">20+ Yrs</span>
        <h4 style="color: var(--color-charcoal); margin: 0.4rem 0;">Expert Local Trackers</h4>
        <p style="font-size: 0.85rem; color: var(--color-muted);">Indigenous Rabari guides who know the leopards and their territories.</p>
      </div>

      <div style="background: #FFFFFF; padding: 1.8rem; border-radius: var(--radius-md); border: 1px solid var(--color-sand-border); box-shadow: var(--shadow-subtle); text-align: center;">
        <span class="stat-number">280+</span>
        <h4 style="color: var(--color-charcoal); margin: 0.4rem 0;">Bird Species at Dam</h4>
        <p style="font-size: 0.85rem; color: var(--color-muted);">Demoiselle cranes, flamingos, and basking Indian crocodiles.</p>
      </div>
    </div>

    <!-- Safari Catalog Cards -->
    <div style="display: flex; flex-direction: column; gap: 3.5rem;">
      <?php foreach ($safaris as $index => $safari): ?>
        <div style="display: grid; grid-template-columns: <?php echo ($index % 2 == 0) ? '1.2fr 1fr' : '1fr 1.2fr'; ?>; gap: 3rem; background: #FFFFFF; border: 1px solid var(--color-sand-border); border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-card);">
          
          <div style="position: relative; min-height: 380px; order: <?php echo ($index % 2 == 0) ? '1' : '2'; ?>;">
            <img src="<?php echo htmlspecialchars($safari['image']); ?>" alt="<?php echo htmlspecialchars($safari['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
            <span class="pillar-badge"><?php echo htmlspecialchars($safari['badge']); ?></span>
          </div>

          <div style="padding: 2.8rem; display: flex; flex-direction: column; justify-content: center; order: <?php echo ($index % 2 == 0) ? '2' : '1'; ?>;">
            <div style="display: flex; gap: 1.2rem; color: var(--color-brass-dark); font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">
              <span>⏱️ <?php echo htmlspecialchars($safari['duration']); ?></span>
              <span>🚙 <?php echo htmlspecialchars($safari['vehicle']); ?></span>
            </div>

            <h2 class="font-serif" style="font-size: 1.8rem; margin-bottom: 0.8rem; color: var(--color-charcoal);"><?php echo htmlspecialchars($safari['title']); ?></h2>
            <p style="color: var(--color-muted); font-size: 0.95rem; margin-bottom: 1.4rem; line-height: 1.6;">
              <?php echo htmlspecialchars($safari['description']); ?>
            </p>

            <h4 style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.8px; color: var(--color-charcoal); margin-bottom: 0.8rem; font-weight: 700;">
              Expedition Highlights:
            </h4>
            <ul class="safari-highlights-list" style="margin-bottom: 1.8rem;">
              <?php 
                $hlArr = explode(',', $safari['highlights']);
                foreach ($hlArr as $hl): 
              ?>
                <li><?php echo trim(htmlspecialchars($hl)); ?></li>
              <?php endforeach; ?>
            </ul>

            <div style="background: var(--color-sand-bg); padding: 1.2rem; border-radius: var(--radius-sm); border: 1px solid var(--color-sand-border); margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
              <div>
                <span style="font-size: 0.78rem; color: var(--color-muted); display: block;">Per Seat (Shared):</span>
                <span style="font-weight: 700; color: var(--color-forest); font-size: 1.2rem;">₹<?php echo number_format($safari['price_per_person']); ?></span>
              </div>
              <div>
                <span style="font-size: 0.78rem; color: var(--color-muted); display: block;">Private 4x4 Gypsy:</span>
                <span style="font-weight: 700; color: var(--color-charcoal); font-size: 1.2rem;">₹<?php echo number_format($safari['private_gypsy_price']); ?></span>
              </div>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
              <button class="btn btn-forest" onclick="openSafariBookingModal('<?php echo addslashes($safari['title']); ?>', <?php echo $safari['price_per_person']; ?>, <?php echo $safari['private_gypsy_price']; ?>)">
                Book This Safari
              </button>
              <a href="https://wa.me/<?php echo RESORT_WHATSAPP; ?>?text=Inquiry%20regarding%20<?php echo urlencode($safari['title']); ?>" class="btn btn-outline-brass" target="_blank">
                WhatsApp Us
              </a>
            </div>
          </div>

        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Safari Guidelines & Tips -->
<section class="section" style="background-color: #F3EFEA;">
  <div class="container">
    <div class="section-title-wrap">
      <span class="section-tag">Safari Information</span>
      <h2 class="section-heading">What to Know Before Your Safari</h2>
      <p class="section-subtitle">Helpful guidelines to ensure a safe, enjoyable, and ethical wildlife experience.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.8rem;">
      <div style="background: #FFFFFF; padding: 1.8rem; border-radius: var(--radius-md); border: 1px solid var(--color-sand-border); box-shadow: var(--shadow-subtle);">
        <h4 style="color: var(--color-forest); margin-bottom: 0.5rem;">📸 Photography Tips</h4>
        <p style="font-size: 0.88rem; color: var(--color-muted); line-height: 1.6;">Recommended lenses: 70-200mm or 100-400mm. Beanbags provided in every Gypsy. Flash photography is strictly prohibited.</p>
      </div>

      <div style="background: #FFFFFF; padding: 1.8rem; border-radius: var(--radius-md); border: 1px solid var(--color-sand-border); box-shadow: var(--shadow-subtle);">
        <h4 style="color: var(--color-forest); margin-bottom: 0.5rem;">🧥 Clothing & Weather</h4>
        <p style="font-size: 0.88rem; color: var(--color-muted); line-height: 1.6;">Neutral colors (khaki, beige, olive) are recommended. Morning drives can be chilly; warm fleeces and blankets are provided in the Gypsy.</p>
      </div>

      <div style="background: #FFFFFF; padding: 1.8rem; border-radius: var(--radius-md); border: 1px solid var(--color-sand-border); box-shadow: var(--shadow-subtle);">
        <h4 style="color: var(--color-forest); margin-bottom: 0.5rem;">🛡️ Safe Distances</h4>
        <p style="font-size: 0.88rem; color: var(--color-muted); line-height: 1.6;">Our drivers and trackers follow ethical wildlife viewing practices. Leopards in Jawai are habituated to vehicles and behave naturally.</p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
