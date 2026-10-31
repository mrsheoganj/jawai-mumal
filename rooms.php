<?php
/**
 * Jawai Mumal - Hotel Stays, Tents & Suites
 * Realistic 5-Star Boutique Hospitality
 */
$pageTitle = "Hotel Rooms, Luxury Tents & Suites";
require_once __DIR__ . '/includes/header.php';

// Fetch all available rooms from database
$allRooms = $pdo->query("SELECT * FROM rooms ORDER BY price ASC")->fetchAll();
?>

<!-- Rooms Hero Header -->
<section style="background: linear-gradient(180deg, rgba(18, 31, 23, 0.5) 0%, rgba(18, 31, 23, 0.85) 100%), url('assets/images/room_tent_suite.jpg') center/cover no-repeat; padding: 7rem 0 4.5rem 0; text-align: center; color: #FFFFFF;">
  <div class="container">
    <span class="hero-badge">JAWAI MUMAL ACCOMMODATIONS</span>
    <h1 class="hero-title" style="font-size: 3.2rem; margin-top: 0.8rem;">Luxury Tents & Suites</h1>
    <p class="hero-description" style="max-width: 650px;">
      Crafted with teakwood furniture, modern ensuite bathrooms, climate control, and private verandas overlooking the granite kopjes.
    </p>
  </div>
</section>

<!-- Suites Catalog -->
<section class="section" style="padding-top: 3.5rem;">
  <div class="container">
    <div style="display: flex; flex-direction: column; gap: 3.5rem;">
      <?php foreach ($allRooms as $index => $room): ?>
        <div style="display: grid; grid-template-columns: <?php echo ($index % 2 == 0) ? '1.2fr 1fr' : '1fr 1.2fr'; ?>; gap: 3rem; background: #FFFFFF; border: 1px solid var(--color-sand-border); border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-card);">
          
          <div style="position: relative; min-height: 380px; order: <?php echo ($index % 2 == 0) ? '1' : '2'; ?>;">
            <img src="<?php echo htmlspecialchars($room['image']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
            <div class="room-price-tag">
              ₹<?php echo number_format($room['price']); ?> <span>/ night + taxes</span>
            </div>
          </div>

          <div style="padding: 2.8rem; display: flex; flex-direction: column; justify-content: center; order: <?php echo ($index % 2 == 0) ? '2' : '1'; ?>;">
            <div style="display: flex; gap: 1.2rem; color: var(--color-brass-dark); font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">
              <span>🏷️ <?php echo htmlspecialchars($room['type']); ?></span>
              <span>📐 <?php echo htmlspecialchars($room['size']); ?></span>
              <span>👥 <?php echo htmlspecialchars($room['capacity']); ?></span>
            </div>
            
            <h2 class="font-serif" style="font-size: 2rem; margin-bottom: 0.8rem; color: var(--color-charcoal);"><?php echo htmlspecialchars($room['name']); ?></h2>
            <p style="color: var(--color-muted); font-size: 0.95rem; margin-bottom: 1.5rem; line-height: 1.6;">
              <?php echo htmlspecialchars($room['description']); ?>
            </p>

            <h4 style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.8px; color: var(--color-charcoal); margin-bottom: 0.8rem; font-weight: 700;">
              Room Amenities & Features:
            </h4>
            <div class="room-amenities-pills" style="margin-bottom: 2rem;">
              <?php 
                $amenitiesArr = explode(',', $room['amenities']);
                foreach ($amenitiesArr as $am): 
              ?>
                <span class="amenity-pill">✓ <?php echo trim(htmlspecialchars($am)); ?></span>
              <?php endforeach; ?>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
              <button class="btn btn-forest" onclick="openRoomBookingModal('<?php echo addslashes($room['name']); ?>', <?php echo $room['price']; ?>)">
                Book This Suite
              </button>
              <a href="https://wa.me/<?php echo RESORT_WHATSAPP; ?>?text=Inquiry%20regarding%20<?php echo urlencode($room['name']); ?>" class="btn btn-outline-brass" target="_blank">
                WhatsApp Us
              </a>
            </div>
          </div>

        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Realistic Hotel Inclusions & Policies -->
<section class="section" style="background-color: #F3EFEA;">
  <div class="container">
    <div class="section-title-wrap">
      <span class="section-tag">Booking Information</span>
      <h2 class="section-heading">Tariff Inclusions & Policies</h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.8rem; margin-bottom: 3rem;">
      <div style="background: #FFFFFF; padding: 1.8rem; border-radius: var(--radius-md); border: 1px solid var(--color-sand-border); box-shadow: var(--shadow-subtle);">
        <h4 style="color: var(--color-forest); margin-bottom: 0.5rem; font-size: 1.05rem;">🍳 Included in Every Stay</h4>
        <p style="font-size: 0.88rem; color: var(--color-muted); line-height: 1.6;">
          • Buffet Breakfast at The Mumal Rasoi<br>
          • Sunset Evening Hi-Tea with local snacks<br>
          • Complimentary High-Speed Wi-Fi<br>
          • Mineral water bottles & tea/coffee maker
        </p>
      </div>

      <div style="background: #FFFFFF; padding: 1.8rem; border-radius: var(--radius-md); border: 1px solid var(--color-sand-border); box-shadow: var(--shadow-subtle);">
        <h4 style="color: var(--color-forest); margin-bottom: 0.5rem; font-size: 1.05rem;">⏰ Check-in & Check-out</h4>
        <p style="font-size: 0.88rem; color: var(--color-muted); line-height: 1.6;">
          • <strong>Check-in Time</strong>: 02:00 PM<br>
          • <strong>Check-out Time</strong>: 11:00 AM<br>
          • Early check-in / late check-out subject to availability.
        </p>
      </div>

      <div style="background: #FFFFFF; padding: 1.8rem; border-radius: var(--radius-md); border: 1px solid var(--color-sand-border); box-shadow: var(--shadow-subtle);">
        <h4 style="color: var(--color-forest); margin-bottom: 0.5rem; font-size: 1.05rem;">👶 Child Policy</h4>
        <p style="font-size: 0.88rem; color: var(--color-muted); line-height: 1.6;">
          • Children up to 5 years stay complimentary.<br>
          • Ages 6 to 11: Extra bed available at nominal charges.<br>
          • Ages 12 and above counted as adult guest.
        </p>
      </div>

      <div style="background: #FFFFFF; padding: 1.8rem; border-radius: var(--radius-md); border: 1px solid var(--color-sand-border); box-shadow: var(--shadow-subtle);">
        <h4 style="color: var(--color-forest); margin-bottom: 0.5rem; font-size: 1.05rem;">📋 Cancellation Policy</h4>
        <p style="font-size: 0.88rem; color: var(--color-muted); line-height: 1.6;">
          • 100% refund for cancellations 14 days prior.<br>
          • 50% refund between 7 to 13 days.<br>
          • Non-refundable within 7 days of arrival date.
        </p>
      </div>
    </div>

    <div style="text-align: center;">
      <button class="btn btn-forest" onclick="openModal('stayBookingModal')">
        Check Dates & Reserve Room
      </button>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
