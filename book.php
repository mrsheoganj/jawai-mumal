<?php
/**
 * Jawai Mumal - Universal Booking Handler & Confirmation Voucher
 * Handles Stays, Dining & Safari Reservations
 */
require_once __DIR__ . '/includes/db.php';

$bookingType = $_GET['type'] ?? 'stay';
$voucherData = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($bookingType === 'stay') {
        $guestName = trim($_POST['guest_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $roomName = trim($_POST['room_name'] ?? 'The Royal Mumal Tented Suite');
        $checkIn = $_POST['check_in'] ?? date('Y-m-d');
        $checkOut = $_POST['check_out'] ?? date('Y-m-d', strtotime('+2 days'));
        $guests = (int)($_POST['guests'] ?? 2);
        $specialRequests = trim($_POST['special_requests'] ?? '');

        // Fetch room price
        $rStmt = $pdo->prepare("SELECT price FROM rooms WHERE name = ? LIMIT 1");
        $rStmt->execute([$roomName]);
        $room = $rStmt->fetch();
        $pricePerNight = $room ? (int)$room['price'] : 34000;

        $d1 = new DateTime($checkIn);
        $d2 = new DateTime($checkOut);
        $nights = $d1->diff($d2)->days;
        if ($nights < 1) $nights = 1;

        $totalPrice = $nights * $pricePerNight;
        $bookingCode = 'JM-STAY-' . rand(100, 999);

        $stmt = $pdo->prepare("INSERT INTO stay_bookings (booking_code, guest_name, email, phone, room_name, check_in, check_out, nights, guests, total_price, payment_status, status, special_requests) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Paid', 'Confirmed', ?)");
        $stmt->execute([$bookingCode, $guestName, $email, $phone, $roomName, $checkIn, $checkOut, $nights, $guests, $totalPrice, $specialRequests]);

        $voucherData = [
            'type' => 'Luxury Stay Reservation',
            'code' => $bookingCode,
            'name' => $guestName,
            'email' => $email,
            'phone' => $phone,
            'item' => $roomName,
            'meta' => "Check-in: $checkIn | Check-out: $checkOut ($nights Nights)",
            'guests' => $guests,
            'total' => $totalPrice,
            'notes' => $specialRequests
        ];
        $_SESSION['toast'] = "Suite booking confirmed successfully! Ref: $bookingCode";

    } elseif ($bookingType === 'dining') {
        $guestName = trim($_POST['guest_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $diningType = trim($_POST['dining_type'] ?? 'Private Bush Dinner Under The Stars');
        $diningDate = $_POST['dining_date'] ?? date('Y-m-d');
        $timeSlot = $_POST['time_slot'] ?? '07:30 PM';
        $partySize = (int)($_POST['party_size'] ?? 2);
        $tablePreference = trim($_POST['table_preference'] ?? 'Rock Summit Table');
        $specialRequests = trim($_POST['special_requests'] ?? '');

        $bookingCode = 'JM-DINE-' . rand(100, 999);

        $stmt = $pdo->prepare("INSERT INTO dining_bookings (booking_code, guest_name, email, phone, dining_date, time_slot, party_size, dining_type, table_preference, status, special_requests) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Confirmed', ?)");
        $stmt->execute([$bookingCode, $guestName, $email, $phone, $diningDate, $timeSlot, $partySize, $diningType, $tablePreference, $specialRequests]);

        $voucherData = [
            'type' => 'Royal Dining Table Reservation',
            'code' => $bookingCode,
            'name' => $guestName,
            'email' => $email,
            'phone' => $phone,
            'item' => $diningType,
            'meta' => "Date: $diningDate at $timeSlot (Table: $tablePreference)",
            'guests' => $partySize,
            'total' => 0, // A la carte / included
            'notes' => $specialRequests
        ];
        $_SESSION['toast'] = "Table reservation confirmed! Ref: $bookingCode";

    } elseif ($bookingType === 'safari') {
        $guestName = trim($_POST['guest_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $safariTitle = trim($_POST['safari_title'] ?? 'Dawn Leopard Tracker Expedition');
        $safariDate = $_POST['safari_date'] ?? date('Y-m-d', strtotime('+1 day'));
        $timeSlot = $_POST['time_slot'] ?? '05:30 AM';
        $guests = (int)($_POST['guests'] ?? 2);
        $vehicleType = trim($_POST['vehicle_type'] ?? 'Shared 4x4 Gypsy');

        // Fetch pricing
        $sStmt = $pdo->prepare("SELECT price_per_person, private_gypsy_price FROM safaris WHERE title = ? LIMIT 1");
        $sStmt->execute([$safariTitle]);
        $safari = $sStmt->fetch();
        
        $perPerson = $safari ? (int)$safari['price_per_person'] : 5500;
        $privatePrice = $safari ? (int)$safari['private_gypsy_price'] : 22000;
        
        $isPrivate = strpos($vehicleType, 'Private') !== false;
        $totalPrice = $isPrivate ? $privatePrice : ($perPerson * $guests);

        $bookingCode = 'JM-SAF-' . rand(100, 999);
        $assignedGypsy = 'Gypsy #' . rand(1, 4) . ' (Forest Camo)';
        $assignedGuide = 'Kumbha Ram Rabari (Master Tracker)';

        $stmt = $pdo->prepare("INSERT INTO safari_bookings (booking_code, guest_name, email, phone, safari_title, safari_date, time_slot, guests, vehicle_type, assigned_gypsy, assigned_guide, total_price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Confirmed')");
        $stmt->execute([$bookingCode, $guestName, $email, $phone, $safariTitle, $safariDate, $timeSlot, $guests, $vehicleType, $assignedGypsy, $assignedGuide, $totalPrice]);

        $voucherData = [
            'type' => 'Leopard Safari Expedition',
            'code' => $bookingCode,
            'name' => $guestName,
            'email' => $email,
            'phone' => $phone,
            'item' => $safariTitle,
            'meta' => "Date: $safariDate at $timeSlot | Vehicle: $vehicleType",
            'guests' => $guests,
            'total' => $totalPrice,
            'notes' => "Assigned Guide: $assignedGuide | $assignedGypsy"
        ];
        $_SESSION['toast'] = "Safari expedition reserved! Ref: $bookingCode";
    }
}

// If directly accessed without POST, redirect home
if (!$voucherData) {
    header("Location: index.php");
    exit;
}

$pageTitle = "Booking Voucher #" . $voucherData['code'];
require_once __DIR__ . '/includes/header.php';
?>

<!-- Voucher Section -->
<section class="section" style="padding: 4rem 0 6rem 0;">
  <div class="container" style="max-width: 780px;">
    
    <div style="background: #FFFFFF; border: 1px solid var(--color-sand-border); border-radius: var(--radius-md); padding: 3rem; box-shadow: var(--shadow-card); position: relative;">
      
      <!-- Voucher Header -->
      <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid var(--color-sand-border); padding-bottom: 1.8rem; margin-bottom: 1.8rem; flex-wrap: wrap; gap: 1rem;">
        <div>
          <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.3rem;">
            <img src="assets/images/favicon.svg" alt="Emblem" style="width: 32px; height: 32px;">
            <span class="font-royal" style="font-size: 1.35rem; font-weight: 800; color: var(--color-forest);">JAWAI MUMAL</span>
          </div>
          <p style="font-size: 0.78rem; color: var(--color-brass-dark); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;">
            Official Reservation Voucher
          </p>
        </div>
        <div style="text-align: right;">
          <span style="font-size: 0.78rem; color: var(--color-muted); display: block; text-transform: uppercase;">Booking Reference</span>
          <span class="font-royal" style="font-size: 1.4rem; font-weight: 800; color: var(--color-forest);"><?php echo htmlspecialchars($voucherData['code']); ?></span>
        </div>
      </div>

      <!-- Success Notice -->
      <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: var(--radius-sm); padding: 1rem 1.4rem; display: flex; align-items: center; gap: 0.8rem; margin-bottom: 2rem;">
        <span style="font-size: 1.4rem; color: #16A34A;">✓</span>
        <div>
          <h4 style="color: #166534; font-size: 0.95rem;">Reservation Confirmed & Recorded</h4>
          <p style="color: #15803D; font-size: 0.82rem;">Your booking is registered. Please save or print this voucher for your check-in.</p>
        </div>
      </div>

      <!-- Voucher Details Table -->
      <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2.5rem;">
        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--color-sand-border); padding-bottom: 0.6rem;">
          <span style="color: var(--color-muted); font-size: 0.9rem;">Category</span>
          <span style="font-weight: 600; color: var(--color-charcoal);"><?php echo htmlspecialchars($voucherData['type']); ?></span>
        </div>

        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--color-sand-border); padding-bottom: 0.6rem;">
          <span style="color: var(--color-muted); font-size: 0.9rem;">Guest Name</span>
          <span style="font-weight: 600; color: var(--color-charcoal);"><?php echo htmlspecialchars($voucherData['name']); ?></span>
        </div>

        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--color-sand-border); padding-bottom: 0.6rem;">
          <span style="color: var(--color-muted); font-size: 0.9rem;">Experience / Suite</span>
          <span style="font-weight: 600; color: var(--color-forest);"><?php echo htmlspecialchars($voucherData['item']); ?></span>
        </div>

        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--color-sand-border); padding-bottom: 0.6rem;">
          <span style="color: var(--color-muted); font-size: 0.9rem;">Itinerary & Timings</span>
          <span style="font-weight: 600; color: var(--color-charcoal);"><?php echo htmlspecialchars($voucherData['meta']); ?></span>
        </div>

        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--color-sand-border); padding-bottom: 0.6rem;">
          <span style="color: var(--color-muted); font-size: 0.9rem;">Party Size</span>
          <span style="font-weight: 600; color: var(--color-charcoal);"><?php echo htmlspecialchars($voucherData['guests']); ?> Guests</span>
        </div>

        <?php if (!empty($voucherData['notes'])): ?>
          <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--color-sand-border); padding-bottom: 0.6rem;">
            <span style="color: var(--color-muted); font-size: 0.9rem;">Notes & Allocation</span>
            <span style="font-size: 0.88rem; color: var(--color-charcoal-light);"><?php echo htmlspecialchars($voucherData['notes']); ?></span>
          </div>
        <?php endif; ?>

        <?php if ($voucherData['total'] > 0): ?>
          <div style="display: flex; justify-content: space-between; padding-top: 0.8rem; align-items: center;">
            <span class="font-royal" style="color: var(--color-charcoal); font-size: 1.05rem;">Total Amount (INR)</span>
            <span class="font-royal" style="font-size: 1.8rem; font-weight: 800; color: var(--color-forest);">
              ₹<?php echo number_format($voucherData['total']); ?>
            </span>
          </div>
        <?php endif; ?>
      </div>

      <!-- Action Buttons -->
      <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <button class="btn btn-forest" onclick="window.print()" style="flex: 1;">
          🖨️ Print Voucher
        </button>
        <a href="https://wa.me/<?php echo RESORT_WHATSAPP; ?>?text=I%20have%20confirmed%20booking%20<?php echo urlencode($voucherData['code']); ?>%20under%20the%20name%20<?php echo urlencode($voucherData['name']); ?>." 
           class="btn btn-outline-brass" target="_blank" style="flex: 1; text-align: center;">
          📱 Share via WhatsApp
        </a>
        <a href="index.php" class="btn btn-outline-brass" style="flex: 1; text-align: center;">
          Back to Home
        </a>
      </div>

    </div>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
