<?php
/**
 * Booking Modals & Dialogs for Jawai Mumal
 * Clean, Realistic 5-Star Boutique Design
 */
?>

<!-- 1. Stay Booking Modal -->
<div class="modal-backdrop" id="stayBookingModal">
  <div class="modal-dialog">
    <button class="modal-close-btn" onclick="closeModal('stayBookingModal')">✕</button>
    <div style="margin-bottom: 1.5rem;">
      <span class="section-tag">Hotel Accommodations</span>
      <h3 class="font-serif" style="font-size: 1.8rem; margin-top: 0.3rem; color: var(--color-charcoal);">Reserve Your Room / Suite</h3>
      <p style="font-size: 0.88rem; color: var(--color-muted);">Tariff includes buffet breakfast, evening hi-tea, and high-speed Wi-Fi.</p>
    </div>

    <form action="book.php?type=stay" method="POST" id="stayBookingForm">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div class="form-group" style="grid-column: 1 / -1;">
          <label for="modalRoomSelect">Select Suite</label>
          <select name="room_name" id="modalRoomSelect" class="form-control" required>
            <?php foreach ($navRooms as $r): ?>
              <option value="<?php echo htmlspecialchars($r['name']); ?>" data-price="<?php echo $r['price']; ?>">
                <?php echo htmlspecialchars($r['name']); ?> — ₹<?php echo number_format($r['price']); ?> / night
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="modalCheckIn">Check-in Date</label>
          <input type="date" name="check_in" id="modalCheckIn" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
        </div>

        <div class="form-group">
          <label for="modalCheckOut">Check-out Date</label>
          <input type="date" name="check_out" id="modalCheckOut" class="form-control" required value="<?php echo date('Y-m-d', strtotime('+2 days')); ?>">
        </div>

        <div class="form-group">
          <label for="modalStayGuests">Total Guests</label>
          <select name="guests" id="modalStayGuests" class="form-control" required>
            <option value="1">1 Adult</option>
            <option value="2" selected>2 Adults</option>
            <option value="3">2 Adults + 1 Child</option>
            <option value="4">4 Adults (Suite Family)</option>
          </select>
        </div>

        <div class="form-group">
          <label>Estimated Total</label>
          <div style="background: var(--color-sand-bg); padding: 0.7rem 0.9rem; border-radius: 4px; border: 1px solid var(--color-sand-border); display: flex; justify-content: space-between; align-items: center;">
            <span id="stayNightsDisplay" style="font-size: 0.85rem; color: var(--color-muted);">2 Night(s)</span>
            <span id="stayTotalPriceDisplay" style="font-weight: 700; color: var(--color-forest); font-size: 1.1rem;">₹68,000</span>
          </div>
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
          <label for="modalStayGuestName">Full Name</label>
          <input type="text" name="guest_name" id="modalStayGuestName" class="form-control" placeholder="e.g. Rahul Sharma" required>
        </div>

        <div class="form-group">
          <label for="modalStayEmail">Email Address</label>
          <input type="email" name="email" id="modalStayEmail" class="form-control" placeholder="name@email.com" required>
        </div>

        <div class="form-group">
          <label for="modalStayPhone">Phone / WhatsApp</label>
          <input type="tel" name="phone" id="modalStayPhone" class="form-control" placeholder="+91 98000 00000" required>
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
          <label for="modalStayRequests">Special Requests / Arrival Notes</label>
          <textarea name="special_requests" id="modalStayRequests" class="form-control" rows="2" placeholder="Airport pick-up, anniversary setup, quiet corner tent..."></textarea>
        </div>
      </div>

      <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
        <button type="button" class="btn btn-outline-brass" onclick="closeModal('stayBookingModal')">Cancel</button>
        <button type="submit" class="btn btn-forest">Confirm Reservation</button>
      </div>
    </form>
  </div>
</div>

<!-- 2. Dining Reservation Modal -->
<div class="modal-backdrop" id="diningBookingModal">
  <div class="modal-dialog">
    <button class="modal-close-btn" onclick="closeModal('diningBookingModal')">✕</button>
    <div style="margin-bottom: 1.5rem;">
      <span class="section-tag">Restro & Dining</span>
      <h3 class="font-serif" style="font-size: 1.8rem; margin-top: 0.3rem; color: var(--color-charcoal);">Table Reservation</h3>
      <p style="font-size: 0.88rem; color: var(--color-muted);">Enjoy freshly cooked Rajasthani delicacies and fireside barbecue.</p>
    </div>

    <form action="book.php?type=dining" method="POST" id="diningBookingForm">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div class="form-group" style="grid-column: 1 / -1;">
          <label for="modalDiningTypeSelect">Dining Experience</label>
          <select name="dining_type" id="modalDiningTypeSelect" class="form-control" required>
            <option value="Private Bush Dinner Under The Stars">Fireside Dinner Under the Stars (Rock Table with Lanterns)</option>
            <option value="The Royal Mumal Rasoi">The Mumal Rasoi Dining Hall</option>
            <option value="Granite Live Barbecue Terrace">Campfire Grill Terrace</option>
            <option value="Jawai Sunset High Tea on Kopjes">Sunset Hi-Tea on Rocks</option>
          </select>
        </div>

        <div class="form-group">
          <label for="modalDiningDate">Date</label>
          <input type="date" name="dining_date" id="modalDiningDate" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
        </div>

        <div class="form-group">
          <label for="modalTimeSlot">Seating Time</label>
          <select name="time_slot" id="modalTimeSlot" class="form-control" required>
            <option value="01:00 PM">Lunch: 01:00 PM</option>
            <option value="02:00 PM">Lunch: 02:00 PM</option>
            <option value="05:30 PM">Sunset Hi-Tea: 05:30 PM</option>
            <option value="07:30 PM" selected>Dinner: 07:30 PM</option>
            <option value="08:30 PM">Dinner: 08:30 PM</option>
          </select>
        </div>

        <div class="form-group">
          <label for="modalPartySize">Party Size</label>
          <select name="party_size" id="modalPartySize" class="form-control" required>
            <option value="2" selected>2 Guests</option>
            <option value="4">4 Guests</option>
            <option value="6">6 Guests</option>
            <option value="8">8+ Guests (Family)</option>
          </select>
        </div>

        <div class="form-group">
          <label for="modalTablePref">Table Location</label>
          <select name="table_preference" id="modalTablePref" class="form-control">
            <option value="Rock Summit Lantern Table">Rock Summit Lantern Table</option>
            <option value="Courtyard Garden View">Courtyard Garden View</option>
            <option value="Bonfire Edge Seating">Bonfire Edge Seating</option>
            <option value="Indoor AC Hall">Indoor AC Hall</option>
          </select>
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
          <label for="modalDiningGuestName">Full Name</label>
          <input type="text" name="guest_name" id="modalDiningGuestName" class="form-control" placeholder="e.g. Priya Sharma" required>
        </div>

        <div class="form-group">
          <label for="modalDiningEmail">Email Address</label>
          <input type="email" name="email" id="modalDiningEmail" class="form-control" placeholder="name@email.com" required>
        </div>

        <div class="form-group">
          <label for="modalDiningPhone">Phone Number</label>
          <input type="tel" name="phone" id="modalDiningPhone" class="form-control" placeholder="+91 98000 00000" required>
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
          <label for="modalDiningRequests">Dietary & Seating Preferences</label>
          <textarea name="special_requests" id="modalDiningRequests" class="form-control" rows="2" placeholder="Vegetarian / Jain / Mild spice preference, birthday celebration..."></textarea>
        </div>
      </div>

      <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
        <button type="button" class="btn btn-outline-brass" onclick="closeModal('diningBookingModal')">Cancel</button>
        <button type="submit" class="btn btn-forest">Confirm Table</button>
      </div>
    </form>
  </div>
</div>

<!-- 3. Safari Booking Modal -->
<div class="modal-backdrop" id="safariBookingModal">
  <div class="modal-dialog">
    <button class="modal-close-btn" onclick="closeModal('safariBookingModal')">✕</button>
    <div style="margin-bottom: 1.5rem;">
      <span class="section-tag">Jawai Safari</span>
      <h3 class="font-serif" style="font-size: 1.8rem; margin-top: 0.3rem; color: var(--color-charcoal);">Book Leopard Safari Slot</h3>
      <p style="font-size: 0.88rem; color: var(--color-muted);">Includes licensed 4x4 open Gypsy, naturalist, and tea on rocks.</p>
    </div>

    <form action="book.php?type=safari" method="POST" id="safariBookingForm">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div class="form-group" style="grid-column: 1 / -1;">
          <label for="modalSafariSelect">Select Safari Route</label>
          <select name="safari_title" id="modalSafariSelect" class="form-control" required>
            <?php foreach ($navSafaris as $s): ?>
              <option value="<?php echo htmlspecialchars($s['title']); ?>" data-price="<?php echo $s['price_per_person']; ?>" data-private="<?php echo $s['private_gypsy_price']; ?>">
                <?php echo htmlspecialchars($s['title']); ?> (₹<?php echo number_format($s['price_per_person']); ?>/seat)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="modalSafariDate">Date of Safari</label>
          <input type="date" name="safari_date" id="modalSafariDate" class="form-control" required value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
        </div>

        <div class="form-group">
          <label for="modalSafariSlot">Timing Slot</label>
          <select name="time_slot" id="modalSafariSlot" class="form-control" required>
            <option value="05:45 AM">Morning Safari (05:45 AM – 09:15 AM)</option>
            <option value="04:15 PM" selected>Evening Golden Hour (04:15 PM – 07:15 PM)</option>
            <option value="07:30 AM">Dam Wetland Tour (07:30 AM – 10:30 AM)</option>
            <option value="09:00 AM">Rabari Village Walk (09:00 AM – 11:30 AM)</option>
          </select>
        </div>

        <div class="form-group">
          <label for="modalVehicleType">Gypsy Option</label>
          <select name="vehicle_type" id="modalVehicleType" class="form-control" required>
            <option value="Shared 4x4 Gypsy">Shared 4x4 Gypsy (Pay per seat)</option>
            <option value="Private 4x4 Gypsy">Private 4x4 Gypsy (Exclusive vehicle)</option>
          </select>
        </div>

        <div class="form-group">
          <label for="modalSafariGuests">Number of Guests</label>
          <input type="number" name="guests" id="modalSafariGuests" class="form-control" min="1" max="6" value="2" required>
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
          <label>Estimated Safari Fee</label>
          <div style="background: var(--color-sand-bg); padding: 0.7rem 0.9rem; border-radius: 4px; border: 1px solid var(--color-sand-border); display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 0.85rem; color: var(--color-muted);">Includes Naturalist, Tracker, Forest Entry & Tea</span>
            <span id="safariTotalPriceDisplay" style="font-weight: 700; color: var(--color-forest); font-size: 1.15rem;">₹11,000</span>
          </div>
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
          <label for="modalSafariGuestName">Full Name</label>
          <input type="text" name="guest_name" id="modalSafariGuestName" class="form-control" placeholder="e.g. David Miller" required>
        </div>

        <div class="form-group">
          <label for="modalSafariEmail">Email Address</label>
          <input type="email" name="email" id="modalSafariEmail" class="form-control" placeholder="name@email.com" required>
        </div>

        <div class="form-group">
          <label for="modalSafariPhone">Phone / WhatsApp</label>
          <input type="tel" name="phone" id="modalSafariPhone" class="form-control" placeholder="+91 98000 00000" required>
        </div>
      </div>

      <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
        <button type="button" class="btn btn-outline-brass" onclick="closeModal('safariBookingModal')">Cancel</button>
        <button type="submit" class="btn btn-forest">Confirm Safari Slot</button>
      </div>
    </form>
  </div>
</div>

<!-- Toast Notifications -->
<?php if (isset($_SESSION['toast'])): ?>
  <div class="toast-container">
    <div class="toast">
      <span>✓</span>
      <span><?php echo htmlspecialchars($_SESSION['toast']); ?></span>
    </div>
  </div>
  <?php unset($_SESSION['toast']); ?>
<?php endif; ?>
