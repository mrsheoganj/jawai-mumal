<?php
/**
 * Animated Jawai landscape (SVG + SMIL): layered depth with pointer / scroll parallax and a gentle 3D tilt,
 * a day-and-night loop, a resting leopard, safari jeeps on a dirt track, flamingos on Jawai Dam and birds.
 */
mt_srand(11);
// Leopard rosettes: deterministic positions over the body (clipped to the body outline)
$ros = [];
for ($x = -128; $x <= 92; $x += 15) {
    for ($y = -70; $y <= -6; $y += 13) {
        if (mt_rand(0, 9) < 2) continue;
        $ros[] = [round($x + mt_rand(-5, 5) + (($y / 13) % 2 ? 6 : 0), 1), round($y + mt_rand(-3, 3), 1), mt_rand(55, 85) / 100, mt_rand(0, 359)];
    }
}
$dots = [];   // solid spots on head / neck / legs
foreach ([[104,-98],[112,-90],[96,-90],[120,-108],[110,-104],[88,-84],[100,-78],[128,-86],[116,-80],[134,-80],[150,-14],[138,-10],[124,-8],[110,-14],[100,-4],[-100,-8],[-80,-4],[-62,-6]] as $d) $dots[] = $d;
?>
<section class="jscene" aria-label="<?= et('scene.alt', 'Animated illustration of the Jawai hills from dawn to night') ?>">
  <svg viewBox="0 0 1440 560" preserveAspectRatio="xMidYMax slice" role="img" aria-label="<?= et('scene.svgalt', 'Granite hills, a leopard resting on the rocks, safari jeeps on a dirt track, Jawai Dam with flamingos and birds through a day') ?>">
    <defs>
      <linearGradient id="tintNight" x1="0" y1="0" x2="0" y2="1"><stop offset=".3" stop-color="#0a1230" stop-opacity="0"/><stop offset=".55" stop-color="#0a1230" stop-opacity="1"/></linearGradient>
      <linearGradient id="tintDusk" x1="0" y1="0" x2="0" y2="1"><stop offset=".25" stop-color="#ff8a2a" stop-opacity="0"/><stop offset=".6" stop-color="#ff8a2a" stop-opacity="1"/></linearGradient>
      <linearGradient id="skyDay" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="0" y2="560"><stop offset="0" stop-color="#5f9bc7"/><stop offset=".55" stop-color="#cfe3e6"/><stop offset="1" stop-color="#f7e9c8"/></linearGradient>
      <linearGradient id="skyWarm" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="0" y2="560"><stop offset="0" stop-color="#6d5a8e"/><stop offset=".5" stop-color="#ea8d57"/><stop offset="1" stop-color="#ffd58c"/></linearGradient>
      <linearGradient id="skyNight" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="0" y2="560"><stop offset="0" stop-color="#050918"/><stop offset="1" stop-color="#1b2650"/></linearGradient>
      <radialGradient id="sunG"><stop offset="0" stop-color="#fffbe6"/><stop offset=".35" stop-color="#ffd987"/><stop offset="1" stop-color="#ffb347" stop-opacity="0"/></radialGradient>
      <radialGradient id="moonG"><stop offset="0" stop-color="#fff"/><stop offset=".5" stop-color="#e6ecff"/><stop offset="1" stop-color="#9fb0ff" stop-opacity="0"/></radialGradient>
      <linearGradient id="farHill" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#a9a3bd"/><stop offset="1" stop-color="#cdc7d3"/></linearGradient>
      <linearGradient id="midHill" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#8d7774"/><stop offset=".7" stop-color="#6b5755"/><stop offset="1" stop-color="#58463f"/></linearGradient>
      <radialGradient id="dome" cx=".32" cy=".18" r=".95"><stop offset="0" stop-color="#8a7570"/><stop offset=".45" stop-color="#5b4845"/><stop offset="1" stop-color="#2c211f"/></radialGradient>
      <radialGradient id="domeSmall" cx=".3" cy=".2" r=".9"><stop offset="0" stop-color="#9b8580"/><stop offset=".5" stop-color="#66524e"/><stop offset="1" stop-color="#3b2d2a"/></radialGradient>
      <linearGradient id="water" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#a8cbd0"/><stop offset="1" stop-color="#3f7a84"/></linearGradient>
      <linearGradient id="track" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#d7b980"/><stop offset="1" stop-color="#b48f55"/></linearGradient>
      <linearGradient id="leoBody" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#c58a30"/><stop offset=".5" stop-color="#dcaa52"/><stop offset="1" stop-color="#f1e0b6"/></linearGradient>
      <linearGradient id="jeepBody" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#c9b07e"/><stop offset="1" stop-color="#8f7a50"/></linearGradient>
      <radialGradient id="shadow"><stop offset="0" stop-color="#000" stop-opacity=".55"/><stop offset="1" stop-color="#000" stop-opacity="0"/></radialGradient>
      <radialGradient id="beam" cx="0" cy=".5" r="1"><stop offset="0" stop-color="#fff3b0" stop-opacity=".85"/><stop offset="1" stop-color="#fff3b0" stop-opacity="0"/></radialGradient>
      <filter id="soft" x="-20%" y="-20%" width="140%" height="140%"><feGaussianBlur stdDeviation="6"/></filter>
      <clipPath id="leoClip"><path id="leoShape" d="M-134,-24C-140,-48-118,-66-92,-66C-62,-66-30,-60-4,-62C22,-64 42,-72 60,-84C72,-92 82,-104 96,-110C108,-115 126,-112 138,-103C148,-97 156,-92 162,-86C166,-82 165,-76 159,-74C152,-72 148,-72 142,-70C138,-66 132,-62 124,-60C114,-58 104,-56 98,-50C92,-44 90,-36 90,-28C104,-24 130,-20 150,-14C160,-12 166,-6 160,-1C154,3 140,2 126,2L70,2L-40,2C-62,2-80,2-100,0C-124,-2-134,-10-134,-24Z"/></clipPath>
      <symbol id="ros" viewBox="-8 -8 16 16"><ellipse rx="5.2" ry="4.4" fill="#c58a35" stroke="#26150b" stroke-width="2.5" stroke-dasharray="10 2.6 7 2.2 5 2" stroke-linecap="round"/><ellipse cx="-.4" cy=".2" rx="2" ry="1.5" fill="#a8681e" opacity=".55"/></symbol>
      <g id="wheel"><circle r="17" fill="#17120f"/><circle r="12.5" fill="#2a2420"/><circle r="9" fill="#9aa0a3"/><g stroke="#5c6165" stroke-width="2.2"><path d="M0-9V9M-9 0H9M-6.4-6.4L6.4 6.4M6.4-6.4L-6.4 6.4"/></g><circle r="3" fill="#3b4043"/></g>
      <g id="jeep">
        <ellipse cx="90" cy="3" rx="92" ry="6" fill="url(#shadow)"/>
        <circle cx="6" cy="-46" r="12" fill="#1b1613"/><circle cx="6" cy="-46" r="8" fill="#2c2622"/>
        <path d="M10,-24L10,-52Q10,-60 21,-60L112,-60L124,-60Q150,-62 168,-49L173,-40L173,-26Q173,-21 167,-21L10,-21Z" fill="url(#jeepBody)"/>
        <path d="M12,-52Q12,-58 21,-58L112,-58" fill="none" stroke="#e8d7ae" stroke-width="2" opacity=".7"/>
        <path d="M122,-58Q148,-60 166,-48" fill="none" stroke="#e8d7ae" stroke-width="2" opacity=".55"/>
        <path d="M10,-38L170,-38" stroke="#6f5d3a" stroke-width="1.6" opacity=".6"/>
        <rect x="167" y="-44" width="6" height="16" rx="2" fill="#1e1814"/><circle cx="169" cy="-38" r="4.2" fill="#fff4b8"/>
        <path d="M110,-60L116,-90L98,-90L96,-60Z" fill="#9fc2cc" opacity=".35"/>
        <path d="M116,-90L111,-60M98,-90L96,-60M96,-90H117" stroke="#3a2e1d" stroke-width="3" fill="none" stroke-linecap="round"/>
        <rect x="20" y="-76" width="76" height="16" rx="3" fill="#5a4630"/><rect x="18" y="-92" width="9" height="34" rx="3" fill="#6a553a"/>
        <g class="pax"><g fill="#6d7a52"><rect x="30" y="-92" width="17" height="18" rx="6"/><rect x="52" y="-92" width="17" height="18" rx="6"/><rect x="76" y="-92" width="17" height="18" rx="6"/></g>
          <g fill="#d9a77a"><circle cx="38.5" cy="-100" r="6.4"/><circle cx="60.5" cy="-100" r="6.4"/><circle cx="84.5" cy="-100" r="6.4"/></g>
          <g fill="#3b3a2c"><path d="M31,-102Q38.5,-112 46,-102Z"/><path d="M53,-102Q60.5,-111 68,-102Z"/><path d="M77,-102Q84.5,-112 92,-102Z"/></g>
          <g fill="#1a1a1a"><rect x="62" y="-103" width="9" height="4" rx="1.5"/></g></g>
        <path d="M24,-60V-108H100V-60" fill="none" stroke="#3a2e1d" stroke-width="3.6" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M24,-108H100" stroke="#e6d6ad" stroke-width="1" opacity=".5"/>
        <path d="M-6,-24H12" stroke="#2a2018" stroke-width="3"/>
        <g transform="translate(32 -17)"><use href="#wheel"><animateTransform attributeName="transform" type="rotate" from="0" to="360" dur=".8s" repeatCount="indefinite"/></use></g><g transform="translate(140 -17)"><use href="#wheel"><animateTransform attributeName="transform" type="rotate" from="0" to="360" dur=".8s" repeatCount="indefinite"/></use></g>
        <path d="M14,-17A18 18 0 0 1 50,-17M122,-17A18 18 0 0 1 158,-17" fill="none" stroke="#1d1712" stroke-width="3.4" opacity=".85"/>
      </g>
    </defs>

    <!-- SKY -->
    <rect x="-600" y="-900" width="2640" height="1460" fill="url(#skyDay)"/>
    <rect x="-600" y="-900" width="2640" height="1460" fill="url(#skyWarm)" opacity="0"><animate attributeName="opacity" dur="30s" repeatCount="indefinite" keyTimes="0;.08;.22;.4;.52;.62;1" values=".95;.5;0;0;.95;.1;.95"/></rect>
    <rect x="-600" y="-900" width="2640" height="1460" fill="url(#skyNight)" opacity="0"><animate attributeName="opacity" dur="30s" repeatCount="indefinite" keyTimes="0;.5;.62;.88;.97;1" values="0;0;1;1;0;0"/></rect>
    <g fill="#fff" opacity="0"><animate attributeName="opacity" dur="30s" repeatCount="indefinite" keyTimes="0;.55;.66;.86;.95;1" values="0;0;1;1;0;0"/>
      <?php foreach ([[120,60],[260,120],[380,50],[520,95],[640,40],[780,110],[900,60],[1010,130],[1130,55],[1250,100],[1350,45],[190,170],[460,160],[700,170],[960,175],[1200,170],[420,-40],[560,-120],[700,-60],[850,-150],[980,-30],[1100,-110],[500,10],[900,15]] as $k => $p): ?><circle cx="<?= $p[0] ?>" cy="<?= $p[1] ?>" r="<?= $k % 3 ? 1.4 : 2 ?>"><animate attributeName="opacity" values="1;.25;1" dur="<?= 2 + $k % 4 ?>s" begin="<?= $k * .3 ?>s" repeatCount="indefinite"/></circle><?php endforeach; ?></g>
    <g><circle r="130" fill="url(#sunG)"/><circle r="30" fill="#fff6d2"/><animateMotion dur="30s" repeatCount="indefinite" path="M-100 430 Q720 -190 1540 430" keyPoints="0;1;1" keyTimes="0;.58;1" calcMode="linear"/></g>
    <g><circle r="90" fill="url(#moonG)"/><circle r="22" fill="#f3f5ff"/><circle cx="8" cy="-4" r="19" fill="#050918" opacity=".18"/><animateMotion dur="30s" repeatCount="indefinite" path="M-100 360 Q720 -110 1540 360" keyPoints="0;0;1;1" keyTimes="0;.55;.98;1" calcMode="linear"/></g>
    <!-- drifting clouds -->
    <g opacity=".75" filter="url(#soft)" fill="#fff"><g><ellipse cx="0" cy="120" rx="120" ry="16"/><ellipse cx="60" cy="108" rx="70" ry="16"/><animateTransform attributeName="transform" type="translate" from="-200 0" to="1700 0" dur="140s" repeatCount="indefinite"/></g>
      <g><ellipse cx="0" cy="190" rx="150" ry="14"/><ellipse cx="-50" cy="180" rx="80" ry="14"/><animateTransform attributeName="transform" type="translate" from="-300 0" to="1650 0" dur="190s" begin="-80s" repeatCount="indefinite"/></g></g>

    <!-- FAR RIDGE (haze) -->
    <g data-depth=".12"><path d="M-40 360C80 300 200 262 330 292C450 318 540 244 670 256C800 268 870 326 990 306C1110 286 1190 236 1320 268C1400 288 1450 318 1490 306L1490 560L-40 560Z" fill="url(#farHill)" opacity=".9"/>
      <path d="M-40 360C80 300 200 262 330 292C450 318 540 244 670 256C800 268 870 326 990 306C1110 286 1190 236 1320 268C1400 288 1450 318 1490 306" fill="none" stroke="#fff" stroke-width="2" opacity=".35"/></g>
    <!-- MID GRANITE HILLS -->
    <g data-depth=".28"><path d="M-40 432C40 384 120 336 220 346C300 356 332 410 404 404C490 398 520 314 640 304C760 294 790 380 880 390C970 400 1010 334 1110 328C1200 322 1250 394 1330 398C1390 400 1440 384 1490 372L1490 560L-40 560Z" fill="url(#midHill)"/>
      <g fill="url(#domeSmall)"><ellipse cx="610" cy="318" rx="62" ry="22"/><ellipse cx="1096" cy="340" rx="48" ry="16"/><ellipse cx="248" cy="358" rx="40" ry="13"/></g>
      <path d="M-40 432C40 384 120 336 220 346C300 356 332 410 404 404C490 398 520 314 640 304C760 294 790 380 880 390C970 400 1010 334 1110 328C1200 322 1250 394 1330 398C1390 400 1440 384 1490 372" fill="none" stroke="#e9d9c8" stroke-width="2" opacity=".3"/></g>

    <!-- DIRT TRACK + JEEPS -->
    <g data-depth=".5">
      <path d="M-40 452C240 430 520 448 760 440C1000 432 1240 450 1490 436L1490 478C1240 492 1000 470 760 480C520 490 240 472 -40 486Z" fill="url(#track)"/>
      <path d="M-40 452C240 430 520 448 760 440C1000 432 1240 450 1490 436" fill="none" stroke="#f0dcae" stroke-width="2" opacity=".6"/>
      <g opacity=".92"><g><animateTransform attributeName="transform" type="translate" values="1700 444;-300 444" dur="52s" begin="-17s" repeatCount="indefinite"/><g transform="scale(-.34 .34)"><use href="#jeep"/></g></g></g>
      <g><animateTransform attributeName="transform" type="translate" values="-360 478;1800 478" dur="36s" begin="-9s" repeatCount="indefinite"/>
        <g><animateTransform attributeName="transform" type="translate" values="0 0;0 -1.6;0 0" dur=".42s" repeatCount="indefinite"/>
          <g transform="scale(.8)"><use href="#jeep"/><circle cx="-6" cy="-8" r="6" fill="#d8c9a4" opacity="0"><animate attributeName="opacity" values="0;.5;0" dur="1.4s" begin="0s" repeatCount="indefinite"/><animate attributeName="cx" values="-6;-90" dur="1.4s" begin="0s" repeatCount="indefinite"/><animate attributeName="cy" values="-8;-30" dur="1.4s" begin="0s" repeatCount="indefinite"/><animate attributeName="r" values="5;24" dur="1.4s" begin="0s" repeatCount="indefinite"/></circle><circle cx="-6" cy="-8" r="6" fill="#d8c9a4" opacity="0"><animate attributeName="opacity" values="0;.5;0" dur="1.4s" begin="0.45s" repeatCount="indefinite"/><animate attributeName="cx" values="-6;-90" dur="1.4s" begin="0.45s" repeatCount="indefinite"/><animate attributeName="cy" values="-8;-30" dur="1.4s" begin="0.45s" repeatCount="indefinite"/><animate attributeName="r" values="5;24" dur="1.4s" begin="0.45s" repeatCount="indefinite"/></circle><circle cx="-6" cy="-8" r="6" fill="#d8c9a4" opacity="0"><animate attributeName="opacity" values="0;.5;0" dur="1.4s" begin="0.9s" repeatCount="indefinite"/><animate attributeName="cx" values="-6;-90" dur="1.4s" begin="0.9s" repeatCount="indefinite"/><animate attributeName="cy" values="-8;-30" dur="1.4s" begin="0.9s" repeatCount="indefinite"/><animate attributeName="r" values="5;24" dur="1.4s" begin="0.9s" repeatCount="indefinite"/></circle>
            <path d="M173,-38L430,-76L430,-2Z" fill="url(#beam)" opacity="0"><animate attributeName="opacity" dur="30s" repeatCount="indefinite" keyTimes="0;.55;.64;.88;.97;1" values="0;0;.7;.7;0;0"/></path></g></g></g>
    </g>

    <!-- JAWAI DAM + FLAMINGOS -->
    <g data-depth=".55"><path d="M-40 480C160 462 320 472 480 466C640 460 760 470 900 468C1100 464 1280 472 1490 466L1490 560L-40 560Z" fill="url(#water)" opacity=".95"/>
      <g stroke="#e9f5f1" stroke-width="2" fill="none" stroke-linecap="round" opacity=".6">
        <path d="M120 492q30-8 60 0t60 0"><animate attributeName="d" dur="5s" repeatCount="indefinite" values="M120 492q30-8 60 0t60 0;M120 492q30 8 60 0t60 0;M120 492q30-8 60 0t60 0"/></path>
        <path d="M420 508q30-8 60 0t60 0"><animate attributeName="d" dur="6s" repeatCount="indefinite" values="M420 508q30 8 60 0t60 0;M420 508q30-8 60 0t60 0;M420 508q30 8 60 0t60 0"/></path>
        <path d="M1000 490q30-8 60 0t60 0"><animate attributeName="d" dur="5.5s" repeatCount="indefinite" values="M1000 490q30-8 60 0t60 0;M1000 490q30 8 60 0t60 0;M1000 490q30-8 60 0t60 0"/></path></g>
      <?php foreach ([[200, 480, 1.1], [246, 484, .9], [292, 481, 1.0], [1040, 482, .95], [1086, 480, 1.1]] as $k => $f): ?>
        <g transform="translate(<?= $f[0] ?> <?= $f[1] ?>) scale(<?= $f[2] ?>)"><ellipse cx="0" cy="3" rx="14" ry="3" fill="#000" opacity=".18"/><path d="M-2 0V-24M3 0V-23" stroke="#e48aa2" stroke-width="2"/><ellipse cx="2" cy="-28" rx="12" ry="7.5" fill="#f4a8ba"/><path d="M-9 -30Q-4 -20 4 -24" fill="none" stroke="#e48aa2" stroke-width="2"/>
          <path d="M9 -30C16 -42 10 -52 17 -54" stroke="#f4a8ba" stroke-width="3.4" fill="none" stroke-linecap="round"><animate attributeName="d" dur="<?= 5 + $k ?>s" begin="<?= $k * .7 ?>s" repeatCount="indefinite" values="M9 -30C16 -42 10 -52 17 -54;M9 -30C18 -36 22 -30 27 -25;M9 -30C16 -42 10 -52 17 -54"/></path>
          <g><circle cx="17" cy="-54" r="2.8" fill="#f4a8ba"><animate attributeName="cx" dur="<?= 5 + $k ?>s" begin="<?= $k * .7 ?>s" repeatCount="indefinite" values="17;27;17"/><animate attributeName="cy" dur="<?= 5 + $k ?>s" begin="<?= $k * .7 ?>s" repeatCount="indefinite" values="-54;-25;-54"/></circle></g></g>
      <?php endforeach; ?></g>

    <!-- FOREGROUND BOULDER + LEOPARD -->
    <g data-depth=".85">
      <ellipse cx="720" cy="548" rx="330" ry="22" fill="url(#shadow)" opacity=".7"/>
      <path d="M360 560C380 470 450 410 540 396C640 380 780 384 860 420C910 442 936 500 950 560Z" fill="url(#dome)"/>
      <path d="M420 470C470 428 560 404 650 398" fill="none" stroke="#c9b4a4" stroke-width="3" opacity=".35" stroke-linecap="round"/>
      <path d="M500 520C560 500 640 494 720 500" fill="none" stroke="#1d1514" stroke-width="3" opacity=".45"/>
      <path d="M780 420C840 436 884 470 902 520" fill="none" stroke="#1d1514" stroke-width="4" opacity=".35"/>
      <g transform="translate(700 398) scale(1.12)">
        <ellipse cx="10" cy="3" rx="168" ry="9" fill="url(#shadow)"/>
        <!-- tail -->
        <path id="tailA" d="M-134,-26C-172,-26-198,-6-192,24C-188,42-168,50-150,44" fill="none" stroke="#c98f35" stroke-width="15" stroke-linecap="round"><animate attributeName="d" dur="5s" repeatCount="indefinite" values="M-134,-26C-172,-26-198,-6-192,24C-188,42-168,50-150,44;M-134,-26C-176,-30-206,-14-204,14C-202,34-184,48-164,50;M-134,-26C-172,-26-198,-6-192,24C-188,42-168,50-150,44"/></path>
        <path d="M-134,-26C-172,-26-198,-6-192,24C-188,42-168,50-150,44" fill="none" stroke="#2a190e" stroke-width="15" stroke-dasharray="7 17" stroke-linecap="butt" opacity=".85"><animate attributeName="d" dur="5s" repeatCount="indefinite" values="M-134,-26C-172,-26-198,-6-192,24C-188,42-168,50-150,44;M-134,-26C-176,-30-206,-14-204,14C-202,34-184,48-164,50;M-134,-26C-172,-26-198,-6-192,24C-188,42-168,50-150,44"/></path>
        <path d="M-158,46L-146,43" stroke="#1b1008" stroke-width="15" stroke-linecap="round"/>
        <g><animateTransform attributeName="transform" type="scale" values="1 1;1 1.014;1 1" dur="3.6s" repeatCount="indefinite"/>
          <!-- far foreleg + far hind leg -->
          <path d="M84,-26C104,-22 128,-14 146,-9C156,-7 160,-1 154,2L92,3Z" fill="#a87a35"/>
          <path d="M-76,-30C-70,-8 -60,0 -52,2L-96,2C-92,-12 -88,-22 -76,-30Z" fill="#b8883b"/>
          <!-- body -->
          <use href="#leoShape" fill="url(#leoBody)"/>
          <g clip-path="url(#leoClip)">
            <?php foreach ($ros as $r): ?><use href="#ros" x="<?= $r[0] - 7 ?>" y="<?= $r[1] - 7 ?>" width="14" height="14" transform="rotate(<?= $r[3] ?> <?= $r[0] ?> <?= $r[1] ?>) translate(<?= $r[0] * (1 - $r[2]) ?> <?= $r[1] * (1 - $r[2]) ?>) scale(<?= $r[2] ?>)"/><?php endforeach; ?>
            <?php foreach ($dots as $d): ?><circle cx="<?= $d[0] ?>" cy="<?= $d[1] ?>" r="<?= mt_rand(14, 22) / 10 ?>" fill="#26150b"/><?php endforeach; ?>
            <!-- volume: light from the sun on the back, shade underneath -->
            <path d="M-134,-60C-90,-86 20,-84 70,-92L140,-108L140,-60L-134,-20Z" fill="#fff" opacity=".13"/>
            <path d="M-134,-8C-60,-22 40,-22 160,-8L160,10L-134,10Z" fill="#2a1608" opacity=".28"/>
            <path d="M-40,-62C0,-72 40,-76 72,-88" fill="none" stroke="#ffe8b0" stroke-width="5" opacity=".28" stroke-linecap="round"/>
            <!-- pale chin / muzzle / throat -->
            <path d="M162,-82C160,-72 150,-70 142,-70C136,-64 128,-58 118,-57L100,-52L108,-70L138,-80Z" fill="#f4e6c6"/>
          </g>
          <path d="M-66,-60C-94,-54 -116,-32 -106,-6" fill="none" stroke="#6e4415" stroke-width="2.2" opacity=".4" stroke-linecap="round"/>
          <path d="M-98,2C-98,-7 -72,-9 -48,-6C-42,-4 -40,2 -48,3L-98,3Z" fill="#d3a04a"/><path d="M-50,-4L-48,3M-56,-5L-55,3" stroke="#2a190e" stroke-width="1.1" opacity=".5"/>
          <!-- ears -->
          <g><path d="M86,-104C84,-114 92,-120 99,-116C102,-112 101,-106 98,-103Z" fill="#c98f35" stroke="#1f130b" stroke-width="2"/><path d="M91,-111C93,-115 96,-114 97,-111" fill="none" stroke="#e8b9a7" stroke-width="1.6"/>
             <path d="M101,-108C100,-120 109,-125 116,-120C119,-116 117,-109 113,-106Z" fill="#d49a3d" stroke="#1f130b" stroke-width="2.2"/><path d="M106,-115C108,-119 112,-118 113,-114" fill="none" stroke="#e8b9a7" stroke-width="1.6"/><path d="M103,-109C104,-117 109,-121 115,-119" fill="none" stroke="#1f130b" stroke-width="3" opacity=".6"/>
             <animateTransform attributeName="transform" type="rotate" values="0 100 -108;0 100 -108;-7 100 -108;0 100 -108;0 100 -108" keyTimes="0;.7;.74;.78;1" dur="7s" repeatCount="indefinite"/></g>
          <!-- face -->
          <path d="M126,-99Q135,-107 145,-99Q135,-93 126,-99Z" fill="#e6b52a" stroke="#1d110a" stroke-width="1.8"/>
          <ellipse cx="135.5" cy="-99" rx="1.8" ry="3.4" fill="#120a05"><animate attributeName="ry" values="3.4;3.4;.3;3.4;3.4" keyTimes="0;.9;.93;.96;1" dur="6s" repeatCount="indefinite"/></ellipse>
          <path d="M122,-104C128,-108 138,-108 146,-104" fill="none" stroke="#26150b" stroke-width="2.4" stroke-linecap="round"/>
          <path d="M158,-85C163,-84 165,-79 161,-76C158,-75 155,-77 154,-80Z" fill="#3d2418"/><path d="M160,-80C154,-72 146,-71 140,-70" fill="none" stroke="#2a190e" stroke-width="1.8" stroke-linecap="round"/>
          <g stroke="#fff" stroke-width=".9" opacity=".8" stroke-linecap="round"><path d="M150,-76L178,-80M150,-74L176,-72M148,-72L172,-64"/></g>
          <g fill="#26150b"><circle cx="104" cy="-102" r="2"/><circle cx="114" cy="-104" r="1.8"/><circle cx="96" cy="-96" r="1.8"/><circle cx="122" cy="-110" r="1.6"/></g>
          <!-- night eye glow -->
          <circle cx="135.5" cy="-99" r="10" fill="#ffd45e" opacity="0"><animate attributeName="opacity" dur="30s" repeatCount="indefinite" keyTimes="0;.56;.66;.88;.96;1" values="0;0;.6;.6;0;0"/></circle>
          <!-- near foreleg -->
          <path d="M86,-30C100,-24 128,-18 150,-13C162,-10 168,-3 160,1C152,4 138,2 118,3L88,3C84,-6 84,-20 86,-30Z" fill="url(#leoBody)"/>
          <g fill="#26150b"><circle cx="136" cy="-9" r="1.7"/><circle cx="120" cy="-6" r="1.6"/><circle cx="104" cy="-14" r="1.9"/><circle cx="150" cy="-8" r="1.5"/></g>
          <path d="M148,-4L150,3M154,-3L156,2" stroke="#2a190e" stroke-width="1.2" opacity=".6"/>
        </g>
      </g>
      <!-- ground cover on the rock -->
      <path d="M470 440C520 432 580 430 620 438" fill="none" stroke="#7a8452" stroke-width="3" stroke-linecap="round" opacity=".5"/>
    </g>

    <!-- ACACIA -->
    <g data-depth=".7"><g transform="translate(1210 372)"><path d="M0 130C2 80 -4 46 2 8M2 24C-30 10-62 16-90 -4M2 16C36 0 72 6 100 -14" stroke="#2a1d1b" stroke-width="7" fill="none" stroke-linecap="round"/>
      <path d="M-128 -6C-102 -34 -42 -38 -4 -18C20 -40 84 -44 128 -14C96 -14 42 -8 0 -4C-42 -4 -96 -2 -128 -6Z" fill="#2f3b24"/><path d="M-110 -14C-70 -30 -20 -30 12 -22C46 -34 92 -32 118 -18" fill="none" stroke="#5f7a3d" stroke-width="3" opacity=".6"/></g></g>

    <!-- NIGHT + DUSK TINT OVER THE LAND (gives every layer the same light) -->
    <rect y="0" width="1440" height="560" fill="url(#tintNight)" opacity="0" style="mix-blend-mode:multiply"><animate attributeName="opacity" dur="30s" repeatCount="indefinite" keyTimes="0;.5;.62;.88;.97;1" values="0;0;.7;.7;0;0"/></rect>
    <rect y="0" width="1440" height="560" fill="url(#tintDusk)" opacity="0" style="mix-blend-mode:soft-light"><animate attributeName="opacity" dur="30s" repeatCount="indefinite" keyTimes="0;.05;.16;.42;.52;.6;1" values=".6;.35;0;0;.6;0;.6"/></rect>

    <!-- birds -->
    <?php foreach ([[0, 120, 26, 0], [-120, 160, 36, 3], [-260, 100, 30, 6], [-380, 190, 40, 9]] as $b): ?>
      <g fill="none" stroke="#2a1d1b" stroke-width="2.2" stroke-linecap="round" opacity=".8"><path d="M0 0q8-9 16 0q8-9 16 0"><animate attributeName="d" dur=".9s" repeatCount="indefinite" values="M0 0q8-9 16 0q8-9 16 0;M0 -4q8 5 16 4q8 1 16 -4;M0 0q8-9 16 0q8-9 16 0"/></path>
        <animateMotion dur="<?= $b[2] ?>s" begin="<?= $b[3] ?>s" repeatCount="indefinite" path="M<?= $b[0] ?> <?= $b[1] ?>C400 <?= $b[1] - 60 ?> 900 <?= $b[1] + 30 ?> 1560 <?= $b[1] - 30 ?>"/></g>
    <?php endforeach; ?>

    <!-- FOREGROUND GRASS -->
    <g data-depth="1.15" class="grass" fill="#3a4a2a"><?php for ($x = -20; $x <= 1460; $x += 30): $h = 26 + (($x * 7) % 34); ?><path d="M<?= $x ?> 566q4-<?= $h ?> <?= 6 + ($x % 5) ?>-<?= $h + 12 ?>q-2 10 4 <?= $h + 12 ?>Z" style="animation-delay:<?= -(abs($x) % 9) * .3 ?>s"/><?php endfor; ?></g>
    <rect y="550" width="1440" height="14" fill="#2c3a20"/>
  </svg>
  <div class="jscene__txt wrap"><span class="eyebrow"><?= et('scene.eyebrow', 'A day in Jawai') ?></span><h2><?= et('scene.title', 'From first light to starry night') ?></h2><p><?= et('scene.text', 'Leopards stir on the granite at dawn, jeeps roll out along the dam, flamingos wade by day and the hills glow gold at dusk. Come for a night, stay for the sunrise.') ?></p></div>
  <script>
  (function(){
    var sc=document.currentScript.parentNode, svg=sc.querySelector('svg'), reduce=matchMedia('(prefers-reduced-motion: reduce)').matches;
    var layers=[].slice.call(sc.querySelectorAll('[data-depth]')), mx=0,my=0,tx=0,ty=0,run=false,pointer=false,t0=performance.now();
    function frame(now){
      if(!run) return;
      if(!pointer){ var t=(now-t0)/1000; tx=Math.sin(t*.35)*.55; ty=Math.cos(t*.27)*.25; }   // slow idle camera drift
      mx+=(tx-mx)*.06; my+=(ty-my)*.06;
      var r=sc.getBoundingClientRect(), sy=(r.top+r.height/2-innerHeight/2)/innerHeight;
      for(var i=0;i<layers.length;i++){ var d=parseFloat(layers[i].getAttribute('data-depth')); layers[i].setAttribute('transform','translate('+(mx*d*-30).toFixed(1)+' '+((my*d*-9)+(sy*d*-24)).toFixed(1)+')'); }
      svg.style.transform='perspective(1500px) rotateY('+(mx*1.8).toFixed(2)+'deg) rotateX('+(my*-1.2).toFixed(2)+'deg) scale(1.06)';
      requestAnimationFrame(frame);
    }
    function fit(){ var r=sc.getBoundingClientRect(), tall=r.width/Math.max(r.height,1)<1.1; svg.setAttribute('viewBox',tall?'360 0 720 560':'0 0 1440 560'); svg.setAttribute('preserveAspectRatio',tall?'xMidYMax meet':'xMidYMax slice'); }
    fit(); addEventListener('resize',fit);
    if(reduce){ if(svg.pauseAnimations) svg.pauseAnimations(); return; }
    sc.addEventListener('pointermove',function(e){ var r=sc.getBoundingClientRect(); pointer=true; tx=((e.clientX-r.left)/r.width-.5)*2; ty=((e.clientY-r.top)/r.height-.5)*2; });
    sc.addEventListener('pointerleave',function(){ pointer=false; });
    if('IntersectionObserver' in window) new IntersectionObserver(function(es){ var on=es[0].isIntersecting; if(on&&!run){ run=true; requestAnimationFrame(frame); if(svg.unpauseAnimations) svg.unpauseAnimations(); } if(!on){ run=false; if(svg.pauseAnimations) svg.pauseAnimations(); } }).observe(sc);
    else { run=true; requestAnimationFrame(frame); }
  })();
  </script>
</section>
