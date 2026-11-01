</main>
<?php
$o1 = [setting('owner1_name'), setting('owner1_role'), setting('owner1_phone')];
$o2 = [setting('owner2_name'), setting('owner2_role'), setting('owner2_phone')];
?>
<footer class="footer">
  <div class="wrap footer__grid">
    <div class="footer__brand">
      <img src="<?= e(img(setting('logo_dark'))) ?>" alt="<?= e(setting('site_name')) ?>" height="52">
      <p><?= e(setting('footer_text')) ?></p>
      <div class="social">
        <?php if (setting('instagram')): ?><a href="<?= e(setting('instagram')) ?>" aria-label="Instagram" rel="noopener" target="_blank"><?= icon('instagram', 20) ?></a><?php endif; ?>
        <?php if (setting('facebook')): ?><a href="<?= e(setting('facebook')) ?>" aria-label="Facebook" rel="noopener" target="_blank"><?= icon('facebook', 20) ?></a><?php endif; ?>
        <a href="<?= e(wa_link('Hello Jawai Mumal')) ?>" aria-label="WhatsApp" rel="noopener" target="_blank"><?= icon('whatsapp', 20) ?></a>
      </div>
    </div>
    <div>
      <h3><?= et('footer.explore', 'Explore') ?></h3>
      <ul>
        <li><a href="<?= url('rooms.php') ?>"><?= et('footer.l1', 'Rooms & stays') ?></a></li>
        <li><a href="<?= url('restaurant.php') ?>"><?= et('footer.l2', 'Restaurant & café') ?></a></li>
        <li><a href="<?= url('safari.php') ?>"><?= et('footer.l3', 'Jawai safari') ?></a></li>
        <li><a href="<?= url('jawai.php') ?>"><?= et('footer.l4', 'Explore Jawai') ?></a></li>
        <li><a href="<?= url('gallery.php') ?>">Gallery</a></li>
        <?php if (seo_cfg('blog_in_footer') === '1'): ?><li><a href="<?= url('blog') ?>"><?= et('footer.blog', 'Travel journal') ?></a></li><?php endif; ?>
        <li><a href="<?= url('book.php?type=event') ?>"><?= et('footer.l5', 'Party & events') ?></a></li>
        <li><a href="<?= url('policies.php') ?>"><?= et('footer.l6', 'Policies & terms') ?></a></li>
      </ul>
    </div>
    <div>
      <h3><?= et('footer.contact', 'Contact') ?></h3>
      <ul class="footer__contact">
        <li><?= icon('user', 18) ?><span><?= e($o1[0]) ?> <small><?= e($o1[1]) ?> · <?= e($o1[2]) ?></small></span><a class="wa-ico wa-ico--sm" href="<?= e(wa_to($o1[2], t('footer.wamsg', 'Hello, I would like to know more about Jawai Mumal.'))) ?>" target="_blank" rel="noopener" aria-label="<?= e(t('wa.aria', 'Chat on WhatsApp')) ?> — <?= e($o1[0]) ?>" title="WhatsApp <?= e($o1[0]) ?>"><?= icon('whatsapp', 20) ?></a></li>
        <li><?= icon('user', 18) ?><span><?= e($o2[0]) ?> <small><?= e($o2[1]) ?> · <?= e($o2[2]) ?></small></span><a class="wa-ico wa-ico--sm" href="<?= e(wa_to($o2[2], t('footer.wamsg', 'Hello, I would like to know more about Jawai Mumal.'))) ?>" target="_blank" rel="noopener" aria-label="<?= e(t('wa.aria', 'Chat on WhatsApp')) ?> — <?= e($o2[0]) ?>" title="WhatsApp <?= e($o2[0]) ?>"><?= icon('whatsapp', 20) ?></a></li>
        <?php if (setting('email')): ?><li><?= icon('mail', 18) ?><span><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></span></li><?php endif; ?>
        <li><?= icon('map-pin', 18) ?><span><?= nl2br(e(setting('address'))) ?></span></li>
      </ul>
    </div>
  </div>
  <div class="wrap footer__bar">
    <span>© <?= date('Y') ?> <?= e(setting('site_name')) ?>. <?= et('footer.rights', 'All rights reserved.') ?></span>
    <span><?= e(preg_replace('#^https?://#', '', setting('site_url'))) ?></span>
  </div>
</footer>

<nav class="mnav" aria-label="Quick navigation">
  <a href="<?= url('index.php') ?>" <?= ($active ?? '') === 'home' ? 'aria-current="page"' : '' ?>><?= icon('mountain', 22) ?><?= et('mnav.home', 'Home') ?></a>
  <a href="<?= url('rooms.php') ?>" <?= ($active ?? '') === 'stay' ? 'aria-current="page"' : '' ?>><?= icon('bed', 22) ?><?= et('mnav.stay', 'Stay') ?></a>
  <a class="mnav__book" href="<?= url('book.php') ?>"><?= icon('calendar-check', 24) ?><?= et('mnav.book', 'Book') ?></a>
  <a href="<?= url('restaurant.php') ?>" <?= ($active ?? '') === 'dining' ? 'aria-current="page"' : '' ?>><?= icon('utensils', 22) ?><?= et('mnav.dine', 'Dine') ?></a>
  <a href="<?= url('safari.php') ?>" <?= ($active ?? '') === 'safari' ? 'aria-current="page"' : '' ?>><?= icon('car', 22) ?><?= et('mnav.safari', 'Safari') ?></a>
</nav>
<?php if (!empty($GLOBALS['jm_preview'])): ?><div class="prevpill" role="status"><?= icon('eye', 16) ?> <?= $GLOBALS['jm_preview'] === 'coming_soon' ? 'Preview · visitors see Coming soon' : 'Preview · visitors see Maintenance' ?> <a href="?exit_preview=1">Exit preview</a></div><?php endif; ?>
<a class="wa-float" href="<?= e(wa_link(t('sdial.msg', 'Hello Jawai Mumal, I would like to know more.'))) ?>" target="_blank" rel="noopener" aria-label="<?= et('wa.aria', 'Chat on WhatsApp') ?>" title="<?= et('wa.aria', 'Chat on WhatsApp') ?>"><?= icon('whatsapp', 28) ?></a>
<?php seo_ld_print($GLOBALS['jm_seo'] ?? seo_resolve([])); ?>
<script src="<?= url('assets/js/site.js') ?>?v=1" defer></script>
</body>
</html>
