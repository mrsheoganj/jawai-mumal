<?php
/**
 * Public Footer Component for Jawai Mumal
 */
?>

  <!-- Floating WhatsApp Direct Contact -->
  <a href="https://wa.me/<?php echo RESORT_WHATSAPP; ?>?text=Hello%20Jawai%20Mumal%20Resort,%20I%20would%20like%20to%20inquire%20about%20booking%20a%20luxury%20stay,%20restaurant%20table,%20and%20leopard%20safari." 
     class="floating-whatsapp" target="_blank" rel="noopener noreferrer" title="Chat with Concierge on WhatsApp">
    💬
  </a>

  <!-- Booking Modals Container -->
  <?php include __DIR__ . '/modals.php'; ?>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <!-- Brand Summary -->
        <div>
          <div class="nav-brand" style="margin-bottom: 1rem;">
            <img src="assets/images/favicon.svg" alt="Jawai Mumal Emblem" class="nav-logo-icon">
            <div class="nav-brand-text">
              <span class="nav-brand-title">JAWAI MUMAL</span>
              <span class="nav-brand-subtitle">Hotel • Restro • Jawai Safari</span>
            </div>
          </div>
          <p class="footer-brand-desc">
            Nestled amid billion-year-old volcanic granite boulders and the harmonious pastoral culture of the Rabari tribe. Where untamed leopard wilderness embraces centuries of royal Mewari hospitality.
          </p>
        </div>

        <!-- Quick Links -->
        <div>
          <h4 class="footer-col-title">Experiences</h4>
          <ul class="footer-links-list">
            <li><a href="rooms.php">Luxury Tented Suites</a></li>
            <li><a href="rooms.php">Granite Cliff Villas</a></li>
            <li><a href="restaurant.php">Royal Mewari Rasoi</a></li>
            <li><a href="restaurant.php">Bush Dining Under Stars</a></li>
            <li><a href="safari.php">Dawn Leopard Safari</a></li>
            <li><a href="safari.php">Sunset Granite Kopjes</a></li>
          </ul>
        </div>

        <!-- Navigation Links -->
        <div>
          <h4 class="footer-col-title">Explore</h4>
          <ul class="footer-links-list">
            <li><a href="index.php">Home Sanctuary</a></li>
            <li><a href="index.php#story">Rabari Coexistence</a></li>
            <li><a href="index.php#gallery">Wildlife Gallery</a></li>
            <li><a href="index.php#reviews">Guest Acclaim</a></li>
            <li><a href="index.php#contact">Concierge & Route</a></li>
            <li><a href="admin/login.php" style="color: var(--gold-primary);">Staff & Admin Portal</a></li>
          </ul>
        </div>

        <!-- Contact & Location -->
        <div>
          <h4 class="footer-col-title">Sanctuary Coordinates</h4>
          <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 0.8rem; line-height: 1.6;">
            📍 <?php echo RESORT_ADDRESS; ?>
          </p>
          <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 0.4rem;">
            📞 <a href="tel:<?php echo str_replace(' ', '', RESORT_PHONE); ?>" style="color: var(--gold-light);"><?php echo RESORT_PHONE; ?></a>
          </p>
          <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 1.5rem;">
            ✉️ <a href="mailto:<?php echo RESORT_EMAIL; ?>" style="color: var(--gold-light);"><?php echo RESORT_EMAIL; ?></a>
          </p>
          <div>
            <button class="btn btn-outline-gold btn-sm" onclick="openModal('stayBookingModal')">Check Dates</button>
          </div>
        </div>
      </div>

      <!-- Bottom Credits -->
      <div class="footer-bottom">
        <div>
          © <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All Rights Reserved. Crafted for Hostinger & Modern Web.
        </div>
        <div style="display: flex; gap: 1.5rem;">
          <a href="index.php#contact">Privacy Policy</a>
          <a href="index.php#contact">Terms of Stay</a>
          <a href="admin/login.php">Admin Login</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Main JavaScript File -->
  <script src="assets/js/main.js"></script>
</body>
</html>
