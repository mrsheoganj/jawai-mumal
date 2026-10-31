<?php
require_once __DIR__ . '/includes/db.php';
http_response_code(404);
$pageTitle = 'Page not found'; $active = ''; $bodyClass = 'nav-solid';
include __DIR__ . '/includes/header.php';
?>
<section class="sec" style="padding-top:10rem;text-align:center"><div class="wrap">
  <span class="eyebrow eyebrow--dark"><?= et('e404.eyebrow', 'Lost in the hills?') ?></span>
  <h1 style="font-size:clamp(2.4rem,6vw,4rem)"><?= et('e404.title', 'We could not find that page') ?></h1>
  <p style="max-width:520px;margin:0 auto 2rem"><?= et('e404.text', 'The link may be old or mistyped. Let us take you back to the lodge.') ?></p>
  <div class="hero__cta" style="justify-content:center"><a class="btn btn--gold" href="<?= url('index.php') ?>"><?= et('e404.home', 'Back to home') ?></a><a class="btn btn--line" href="<?= url('contact.php') ?>"><?= et('e404.contact', 'Contact us') ?></a></div>
</div></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
