/**
 * Jawai Mumal - Client JavaScript
 * Interactive Booking Engine, Modals, Dynamic Pricing & Filters
 */

document.addEventListener('DOMContentLoaded', function () {
  // 1. Mobile Menu Toggle
  const mobileToggle = document.getElementById('mobileNavToggle');
  const navMenu = document.getElementById('navMenu');
  if (mobileToggle && navMenu) {
    mobileToggle.addEventListener('click', function () {
      navMenu.classList.toggle('active');
    });
  }

  // 2. Set Minimum Dates to Today for all date inputs
  const today = new Date().toISOString().split('T')[0];
  document.querySelectorAll('input[type="date"]').forEach(function (input) {
    if (!input.getAttribute('min')) {
      input.setAttribute('min', today);
    }
  });

  // 3. Hero Universal Booking Tabs
  const bookingTabs = document.querySelectorAll('.booking-tab-btn');
  const tabContents = document.querySelectorAll('.booking-tab-content');

  bookingTabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      const target = this.getAttribute('data-tab');

      bookingTabs.forEach(t => t.classList.remove('active'));
      tabContents.forEach(c => c.classList.remove('active'));

      this.classList.add('active');
      const activeContent = document.getElementById(target);
      if (activeContent) {
        activeContent.classList.add('active');
      }
    });
  });

  // 4. Menu Category Filters
  const menuTabBtns = document.querySelectorAll('.menu-tab-btn');
  const dishCards = document.querySelectorAll('.menu-dish-card');

  menuTabBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const category = this.getAttribute('data-category');

      menuTabBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');

      dishCards.forEach(function (card) {
        if (category === 'all' || card.getAttribute('data-category') === category) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });

  // 5. Generic Modal Controls
  window.openModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  };

  window.closeModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove('active');
      document.body.style.overflow = '';
    }
  };

  // Close modal when clicking on backdrop
  document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
    backdrop.addEventListener('click', function (e) {
      if (e.target === this) {
        this.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  });

  // 6. Dynamic Room Booking Modal Trigger
  window.openRoomBookingModal = function (roomName, roomPrice) {
    const roomSelect = document.getElementById('modalRoomSelect');
    if (roomSelect) {
      for (let i = 0; i < roomSelect.options.length; i++) {
        if (roomSelect.options[i].text.includes(roomName) || roomSelect.options[i].value === roomName) {
          roomSelect.selectedIndex = i;
          break;
        }
      }
    }
    updateStayPriceCalculation();
    openModal('stayBookingModal');
  };

  // 7. Dynamic Safari Booking Modal Trigger
  window.openSafariBookingModal = function (safariTitle, perPersonPrice, privatePrice) {
    const safariSelect = document.getElementById('modalSafariSelect');
    if (safariSelect) {
      for (let i = 0; i < safariSelect.options.length; i++) {
        if (safariSelect.options[i].text.includes(safariTitle) || safariSelect.options[i].value === safariTitle) {
          safariSelect.selectedIndex = i;
          break;
        }
      }
    }
    updateSafariPriceCalculation();
    openModal('safariBookingModal');
  };

  // 8. Dynamic Dining Reservation Modal Trigger
  window.openDiningModal = function (diningExperience) {
    const diningSelect = document.getElementById('modalDiningTypeSelect');
    if (diningSelect && diningExperience) {
      for (let i = 0; i < diningSelect.options.length; i++) {
        if (diningSelect.options[i].value.includes(diningExperience)) {
          diningSelect.selectedIndex = i;
          break;
        }
      }
    }
    openModal('diningBookingModal');
  };

  // 9. Stay Price Calculator
  function updateStayPriceCalculation() {
    const roomSelect = document.getElementById('modalRoomSelect');
    const checkInInput = document.getElementById('modalCheckIn');
    const checkOutInput = document.getElementById('modalCheckOut');
    const totalDisplay = document.getElementById('stayTotalPriceDisplay');
    const nightsDisplay = document.getElementById('stayNightsDisplay');

    if (!roomSelect || !checkInInput || !checkOutInput || !totalDisplay) return;

    const selectedOption = roomSelect.options[roomSelect.selectedIndex];
    const pricePerNight = parseInt(selectedOption.getAttribute('data-price') || 34000);

    const checkIn = new Date(checkInInput.value);
    const checkOut = new Date(checkOutInput.value);

    let nights = 1;
    if (checkIn && checkOut && checkOut > checkIn) {
      const diffTime = Math.abs(checkOut - checkIn);
      nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    }

    const total = nights * pricePerNight;
    if (nightsDisplay) nightsDisplay.textContent = nights + ' Night(s)';
    totalDisplay.textContent = '₹' + total.toLocaleString('en-IN');
  }

  const roomSelectEl = document.getElementById('modalRoomSelect');
  const checkInEl = document.getElementById('modalCheckIn');
  const checkOutEl = document.getElementById('modalCheckOut');
  if (roomSelectEl) roomSelectEl.addEventListener('change', updateStayPriceCalculation);
  if (checkInEl) checkInEl.addEventListener('change', function () {
    if (checkOutEl) {
      const nextDay = new Date(this.value);
      nextDay.setDate(nextDay.getDate() + 1);
      checkOutEl.min = nextDay.toISOString().split('T')[0];
      if (!checkOutEl.value || new Date(checkOutEl.value) <= new Date(this.value)) {
        checkOutEl.value = nextDay.toISOString().split('T')[0];
      }
    }
    updateStayPriceCalculation();
  });
  if (checkOutEl) checkOutEl.addEventListener('change', updateStayPriceCalculation);

  // 10. Safari Price Calculator
  function updateSafariPriceCalculation() {
    const safariSelect = document.getElementById('modalSafariSelect');
    const guestsInput = document.getElementById('modalSafariGuests');
    const vehicleTypeSelect = document.getElementById('modalVehicleType');
    const totalDisplay = document.getElementById('safariTotalPriceDisplay');

    if (!safariSelect || !guestsInput || !vehicleTypeSelect || !totalDisplay) return;

    const selectedOption = safariSelect.options[safariSelect.selectedIndex];
    const perPerson = parseInt(selectedOption.getAttribute('data-price') || 5500);
    const privatePrice = parseInt(selectedOption.getAttribute('data-private') || 22000);
    const guests = parseInt(guestsInput.value || 1);
    const isPrivate = vehicleTypeSelect.value.includes('Private');

    const total = isPrivate ? privatePrice : (perPerson * guests);
    totalDisplay.textContent = '₹' + total.toLocaleString('en-IN');
  }

  const safariSelectEl = document.getElementById('modalSafariSelect');
  const safariGuestsEl = document.getElementById('modalSafariGuests');
  const vehicleTypeEl = document.getElementById('modalVehicleType');
  if (safariSelectEl) safariSelectEl.addEventListener('change', updateSafariPriceCalculation);
  if (safariGuestsEl) safariGuestsEl.addEventListener('input', updateSafariPriceCalculation);
  if (vehicleTypeEl) vehicleTypeEl.addEventListener('change', updateSafariPriceCalculation);

  // Initial calculation runs
  updateStayPriceCalculation();
  updateSafariPriceCalculation();
});
