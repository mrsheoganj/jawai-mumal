<?php
/** Public holding page for Coming Soon and Maintenance modes. Included by jm_gate(); everything here is editable in Admin > Page text & images. */
$gm = $GLOBALS['jm_gate_mode'] ?? 'coming_soon';
$isCS = $gm === 'coming_soon';
$target = $isCS ? launch_ts() : launch_ts('maint_until');
http_response_code(503); header('Retry-After: ' . ($target && $target > time() ? min(86400, max(300, $target - time())) : 3600)); header('X-Robots-Tag: noindex');
header('Cache-Control: no-store');
$name = setting('site_name');
$whenText = $target ? (new DateTime('@' . $target))->setTimezone(new DateTimeZone('Asia/Kolkata'))->format('l, j F Y \a\t g:i A') . ' IST' : '';
?><!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($isCS ? t('gate.cs.pagetitle', 'Launching soon') : t('gate.mt.pagetitle', 'Back soon')) ?> | <?= e($name) ?></title>
<meta name="robots" content="noindex,nofollow"><meta name="theme-color" content="#ffffff"><meta name="format-detection" content="telephone=no">
<link rel="icon" href="<?= url('assets/brand/favicon.ico') ?>" sizes="any"><link rel="apple-touch-icon" href="<?= url('assets/brand/icon-180.png') ?>">
<?= font_head() ?>
<link rel="stylesheet" href="<?= asset('assets/css/site.css') ?>">
<style>
  body{background:#fff;padding:0}
  .gate{min-height:100svh;display:grid;grid-template-rows:1fr auto;isolation:isolate}
  .gate__main{display:grid;place-items:center;text-align:center;padding:clamp(2rem,6vw,4rem) 1rem 1rem}
  .gate__in{max-width:760px;width:100%}
  .gate__mark{position:relative;width:170px;height:170px;margin:0 auto 1.4rem;display:grid;place-items:center}
  .gate__mark img{width:136px;height:136px;border-radius:50%;animation:pre-in 1s var(--ease) both}
  .gate__mark svg{position:absolute;inset:0;width:100%;height:100%;color:var(--gold);transform:rotate(-90deg)}
  .gate__mark circle{stroke-dasharray:352;stroke-dashoffset:352;animation:ring 2s .2s var(--ease) forwards}
  .gate h1{font-size:clamp(2.2rem,6vw,3.8rem);margin-bottom:.4em}
  .gate p.lead{color:var(--muted);max-width:560px;margin:0 auto 1.8rem;font-size:1.08rem}
  .cd{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.7rem;max-width:560px;margin:0 auto 1.2rem}
  .cd div{border:1px solid var(--line);border-radius:16px;padding:1rem .4rem;background:#fff;box-shadow:var(--sh)}
  .cd b{display:block;font:600 clamp(1.9rem,6vw,3rem)/1 var(--serif);color:var(--ink);font-variant-numeric:tabular-nums}
  .cd span{font:600 .72rem var(--sans);letter-spacing:.18em;text-transform:uppercase;color:var(--gold-d)}
  .gate__when{font-weight:500;color:var(--ink-2);margin:0 0 1.8rem}
  .gate__live{display:none;margin:0 0 1.5rem}.gate.is-over .cd,.gate.is-over .gate__when{display:none}.gate.is-over .gate__live{display:block}
  .gate__wa{display:flex;flex-direction:column;align-items:center;gap:.6rem;color:var(--muted);font-size:.95rem}
  .gate__foot{text-align:center;padding:1rem;font-size:.85rem;color:var(--muted);background:#fff}
  body{animation:none}
</style>
</head>
<body>
<div class="gate" id="gate">
  <div class="gate__main"><div class="gate__in">
    <div class="gate__mark" aria-hidden="true"><svg viewBox="0 0 120 120"><circle cx="60" cy="60" r="56" fill="none" stroke="currentColor" stroke-width="1.5"/></svg><img src="<?= e(img(setting('logo_emblem'))) ?>" alt="" width="136" height="136"></div>
    <span class="eyebrow eyebrow--dark" style="justify-content:center"><?= $isCS ? et('gate.cs.eyebrow', 'Hotel · Restaurant · Safari') : et('gate.mt.eyebrow', 'Scheduled maintenance') ?></span>
    <h1><?= $isCS ? et('gate.cs.title', 'Stay, dine and explore Jawai') : et('gate.mt.title', 'We will be right back') ?></h1>
    <p class="lead"><?= $isCS ? et('gate.cs.text', 'Comfortable rooms, pure-vegetarian Rajasthani cooking and leopard safaris in Bera, Pali. Online booking opens in') : et('gate.mt.text', 'The website is briefly unavailable while we carry out scheduled maintenance. Thank you for your patience.') ?></p>
    <?php if ($target): ?>
      <div class="cd" role="timer" aria-label="<?= $isCS ? et('gate.cs.cdlabel', 'Time until online booking opens') : et('gate.mt.cdlabel', 'Time until we are back') ?>" data-target="<?= (int)$target ?>">
        <div><b data-u="d">00</b><span><?= et('gate.days', 'Days') ?></span></div><div><b data-u="h">00</b><span><?= et('gate.hours', 'Hours') ?></span></div><div><b data-u="m">00</b><span><?= et('gate.mins', 'Minutes') ?></span></div><div><b data-u="s">00</b><span><?= et('gate.secs', 'Seconds') ?></span></div>
      </div>
      <p class="gate__when"><?= $isCS ? et('gate.cs.when', 'Online booking opens on') : et('gate.mt.when', 'Expected back on') ?> <?= e($whenText) ?></p>
      <p class="gate__live"><strong><?= $isCS ? et('gate.cs.live', 'Online booking is now open.') : et('gate.mt.live', 'We should be back now.') ?></strong> <a class="link" href="">Refresh the page</a></p>
    <?php endif; ?>
    <div class="gate__wa"><a class="wa-ico wa-ico--lg" href="<?= e(wa_link(t('gate.wamsg', 'Hello Jawai Mumal, I would like to ask about a booking.'))) ?>" target="_blank" rel="noopener" aria-label="<?= et('wa.aria', 'Chat on WhatsApp') ?>" title="<?= et('wa.aria', 'Chat on WhatsApp') ?>"><?= icon('whatsapp', 34) ?></a><span><?= et('gate.wa', 'For enquiries and bookings, message us on WhatsApp') ?></span></div>
  </div></div>
  <div class="gate__scene">
    <div class="gate__foot">© <?= date('Y') ?> <?= e($name) ?> · <?= e(preg_replace('/\s+/', ' ', str_replace("\n", ', ', setting('address')))) ?></div></div>
</div>
<script>
(function(){
  var cd=document.querySelector('.cd'); if(!cd)return; var T=parseInt(cd.dataset.target,10)*1000, g=document.getElementById('gate');
  var el={d:cd.querySelector('[data-u=d]'),h:cd.querySelector('[data-u=h]'),m:cd.querySelector('[data-u=m]'),s:cd.querySelector('[data-u=s]')};
  var p=function(n){return String(n).padStart(2,'0')};
  function tick(){var l=Math.max(0,Math.floor((T-Date.now())/1000));
    el.d.textContent=p(Math.floor(l/86400));el.h.textContent=p(Math.floor(l%86400/3600));el.m.textContent=p(Math.floor(l%3600/60));el.s.textContent=p(l%60);
    if(l===0){g.classList.add('is-over');clearInterval(iv);var done=false;try{done=sessionStorage.getItem('gateReload')==='1';sessionStorage.setItem('gateReload','1')}catch(e){}if(!done)setTimeout(function(){location.reload()},2500)}}
  var iv=setInterval(tick,1000);tick();
})();
</script>
</body></html>
