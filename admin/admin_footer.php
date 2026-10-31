<?php
/**
 * Admin Layout Footer
 */
?>
    </main>

    <footer style="padding: 1.5rem 2rem; border-top: 1px solid var(--admin-border); font-size: 0.82rem; color: var(--admin-muted); display: flex; justify-content: space-between; align-items: center;">
      <span>© <?php echo date('Y'); ?> Jawai Mumal Luxury Resort & Leopard Safari.</span>
      <span>Hostinger Ready • PHP <?php echo phpversion(); ?></span>
    </footer>

  </div>

  <script>
    // Responsive sidebar toggle for mobile
    const adminToggle = document.getElementById('adminMenuToggle');
    const adminSidebar = document.getElementById('adminSidebar');
    if (adminToggle && adminSidebar) {
      adminToggle.addEventListener('click', () => {
        adminSidebar.classList.toggle('open');
      });
    }
  </script>
</body>
</html>
