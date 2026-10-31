<?php /** Animated Jawai landscape: a day/night loop with granite hills, a resting leopard, Jawai Dam, flamingos and birds. Pure SVG + SMIL. */ ?>
<section class="jscene" aria-label="<?= et('scene.alt', 'Animated illustration of the Jawai hills from dawn to night') ?>">
  <svg viewBox="0 0 1440 560" preserveAspectRatio="xMidYMax slice" role="img" aria-label="<?= et('scene.svgalt', 'Granite hills, a leopard resting on the rocks, Jawai Dam and birds through a day') ?>">
    <defs>
      <linearGradient id="skyDay" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#6fa5c9"/><stop offset=".6" stop-color="#d9e8e4"/><stop offset="1" stop-color="#f6e7c4"/></linearGradient>
      <linearGradient id="skyWarm" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#7a5a86"/><stop offset=".55" stop-color="#e98f55"/><stop offset="1" stop-color="#ffd48a"/></linearGradient>
      <linearGradient id="skyNight" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#070b1f"/><stop offset="1" stop-color="#1f2a52"/></linearGradient>
      <linearGradient id="water" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#9ec3c8"/><stop offset="1" stop-color="#4d7f87"/></linearGradient>
      <radialGradient id="sunG"><stop offset="0" stop-color="#fff6d8"/><stop offset=".4" stop-color="#ffd27a"/><stop offset="1" stop-color="#ffb347" stop-opacity="0"/></radialGradient>
      <radialGradient id="moonG"><stop offset="0" stop-color="#ffffff"/><stop offset=".5" stop-color="#e9edff"/><stop offset="1" stop-color="#9fb0ff" stop-opacity="0"/></radialGradient>
    </defs>

    <!-- sky layers crossfade through the day -->
    <rect width="1440" height="560" fill="url(#skyDay)"/>
    <rect width="1440" height="560" fill="url(#skyWarm)" opacity="0"><animate attributeName="opacity" dur="30s" repeatCount="indefinite" keyTimes="0;.08;.22;.4;.52;.62;1" values=".95;.5;0;0;.95;.1;.95"/></rect>
    <rect width="1440" height="560" fill="url(#skyNight)" opacity="0"><animate attributeName="opacity" dur="30s" repeatCount="indefinite" keyTimes="0;.5;.62;.88;.97;1" values="0;0;1;1;0;0"/></rect>

    <!-- stars -->
    <g fill="#fff" opacity="0"><animate attributeName="opacity" dur="30s" repeatCount="indefinite" keyTimes="0;.55;.66;.86;.95;1" values="0;0;1;1;0;0"/>
      <?php foreach ([[120,60],[260,120],[380,50],[520,95],[640,40],[780,110],[900,60],[1010,130],[1130,55],[1250,100],[1350,45],[190,170],[460,160],[700,170],[960,175],[1200,170]] as $k => $p): ?>
        <circle cx="<?= $p[0] ?>" cy="<?= $p[1] ?>" r="<?= $k % 3 ? 1.4 : 2 ?>"><animate attributeName="opacity" values="1;.25;1" dur="<?= 2 + $k % 4 ?>s" begin="<?= ($k * .3) ?>s" repeatCount="indefinite"/></circle>
      <?php endforeach; ?>
    </g>

    <!-- sun and moon on arcs -->
    <g><circle r="120" fill="url(#sunG)"/><circle r="30" fill="#fff3c9"/>
      <animateMotion dur="30s" repeatCount="indefinite" path="M-100 430 Q720 -190 1540 430" keyPoints="0;1;1" keyTimes="0;.58;1" calcMode="linear"/></g>
    <g><circle r="90" fill="url(#moonG)"/><circle r="22" fill="#f3f5ff"/><circle cx="8" cy="-4" r="19" fill="#070b1f" opacity=".18"/>
      <animateMotion dur="30s" repeatCount="indefinite" path="M-100 360 Q720 -110 1540 360" keyPoints="0;0;1;1" keyTimes="0;.55;.98;1" calcMode="linear"/></g>

    <!-- far hills (parallax) -->
    <g class="px" data-speed="0.05"><path d="M0 380 C110 310 220 270 340 300 C440 325 520 250 640 262 C760 274 830 330 950 310 C1070 290 1150 240 1270 270 C1350 290 1410 320 1440 310 L1440 560 L0 560Z" fill="#8e7f8f" opacity=".55"/></g>
    <g class="px" data-speed="0.1"><path d="M0 430 C70 380 140 330 230 340 C310 350 340 410 410 404 C490 398 520 310 640 300 C760 290 790 376 880 386 C970 396 1010 330 1110 324 C1200 318 1250 390 1330 394 C1380 396 1415 380 1440 372 L1440 560 L0 560Z" fill="#6f5a58"/>
      <g fill="#5a4745" opacity=".7"><ellipse cx="610" cy="305" rx="46" ry="14"/><ellipse cx="1090" cy="332" rx="36" ry="11"/><ellipse cx="260" cy="346" rx="30" ry="9"/></g></g>

    <!-- Jawai Dam -->
    <g class="px" data-speed="0.14"><path d="M0 470 C160 452 320 462 480 456 C640 450 760 460 900 458 C1100 454 1280 462 1440 456 L1440 560 L0 560Z" fill="url(#water)" opacity=".9"/>
      <g stroke="#e8f4f0" stroke-width="2" fill="none" stroke-linecap="round" opacity=".6">
        <path d="M120 486 q30 -8 60 0 t60 0"><animate attributeName="d" dur="5s" repeatCount="indefinite" values="M120 486 q30 -8 60 0 t60 0;M120 486 q30 8 60 0 t60 0;M120 486 q30 -8 60 0 t60 0"/></path>
        <path d="M420 500 q30 -8 60 0 t60 0"><animate attributeName="d" dur="6s" repeatCount="indefinite" values="M420 500 q30 8 60 0 t60 0;M420 500 q30 -8 60 0 t60 0;M420 500 q30 8 60 0 t60 0"/></path>
        <path d="M900 482 q30 -8 60 0 t60 0"><animate attributeName="d" dur="5.5s" repeatCount="indefinite" values="M900 482 q30 -8 60 0 t60 0;M900 482 q30 8 60 0 t60 0;M900 482 q30 -8 60 0 t60 0"/></path>
        <path d="M1180 504 q30 -8 60 0 t60 0"><animate attributeName="d" dur="6.5s" repeatCount="indefinite" values="M1180 504 q30 8 60 0 t60 0;M1180 504 q30 -8 60 0 t60 0;M1180 504 q30 8 60 0 t60 0"/></path></g>
      <!-- flamingos wading (heads dip) -->
      <?php foreach ([[210, 462, 1], [250, 466, .85], [290, 463, 1.05], [1010, 464, .95], [1050, 461, 1.1]] as $k => $f): ?>
        <g transform="translate(<?= $f[0] ?> <?= $f[1] ?>) scale(<?= $f[2] ?>)"><path d="M0 0 v-22" stroke="#e58aa0" stroke-width="2"/><ellipse cx="0" cy="-26" rx="9" ry="6" fill="#f3a3b5"/>
          <path d="M5 -28 C12 -40 8 -50 14 -52" stroke="#f3a3b5" stroke-width="3" fill="none" stroke-linecap="round"><animate attributeName="d" dur="<?= 5 + $k ?>s" begin="<?= $k * .7 ?>s" repeatCount="indefinite" values="M5 -28 C12 -40 8 -50 14 -52;M5 -28 C14 -34 18 -30 24 -26;M5 -28 C12 -40 8 -50 14 -52"/></path>
          <circle cx="14" cy="-52" r="2.4" fill="#f3a3b5"><animate attributeName="cy" dur="<?= 5 + $k ?>s" begin="<?= $k * .7 ?>s" repeatCount="indefinite" values="-52;-26;-52"/><animate attributeName="cx" dur="<?= 5 + $k ?>s" begin="<?= $k * .7 ?>s" repeatCount="indefinite" values="14;24;14"/></circle></g>
      <?php endforeach; ?></g>

    <!-- foreground granite dome with the leopard -->
    <g class="px" data-speed="0.2">
      <path d="M380 560 C400 470 470 396 560 380 C660 362 760 392 810 470 C830 500 850 530 870 560Z" fill="#4a3a39"/>
      <path d="M470 440 C520 400 600 384 660 392" stroke="#5d4a48" stroke-width="3" fill="none" opacity=".7"/>
      <!-- leopard resting on the dome, looking right -->
      <g transform="translate(610 376)">
        <path d="M-70 8 C-110 6 -122 -22 -104 -38" stroke="#2c1d17" stroke-width="9" fill="none" stroke-linecap="round"><animate attributeName="d" dur="4.5s" repeatCount="indefinite" values="M-70 8 C-110 6 -122 -22 -104 -38;M-70 8 C-114 14 -134 -6 -122 -26;M-70 8 C-110 6 -122 -22 -104 -38"/></path>
        <path d="M-72 4 C-66 -22 -30 -30 6 -28 C34 -27 52 -22 64 -12 C72 -6 70 6 56 10 L-40 12 C-60 12 -70 10 -72 4Z" fill="#c8923f"/>
        <path d="M-58 4 C-50 -16 -22 -22 6 -22" stroke="#a4702b" stroke-width="2" fill="none"/>
        <g fill="#3a2418"><circle cx="-40" cy="-8" r="3.2"/><circle cx="-24" cy="-17" r="3"/><circle cx="-8" cy="-9" r="3.4"/><circle cx="10" cy="-19" r="3"/><circle cx="26" cy="-10" r="3.2"/><circle cx="-30" cy="4" r="2.6"/><circle cx="2" cy="3" r="2.8"/><circle cx="36" cy="-2" r="2.6"/></g>
        <path d="M54 8 L82 8 C88 8 90 12 84 14 L52 14Z" fill="#c8923f"/>
        <path d="M58 -14 C60 -30 76 -40 92 -36 C106 -32 112 -22 108 -12 C104 0 90 4 76 2 C64 1 58 -4 58 -14Z" fill="#c8923f"/>
        <path d="M70 -34 L72 -46 L82 -37Z M92 -37 L100 -46 L102 -32Z" fill="#a4702b"/>
        <g fill="#3a2418"><circle cx="80" cy="-24" r="2"/><circle cx="92" cy="-30" r="2"/></g>
        <ellipse cx="98" cy="-17" rx="3.2" ry="2.4" fill="#3a2418"/>
        <g><ellipse cx="86" cy="-18" rx="2.6" ry="2.2" fill="#ffe08a"><animate attributeName="ry" dur="6s" repeatCount="indefinite" values="2.2;2.2;.2;2.2;2.2" keyTimes="0;.9;.93;.96;1"/></ellipse><ellipse cx="86" cy="-18" rx="7" ry="6" fill="#ffd45e" opacity="0"><animate attributeName="opacity" dur="30s" repeatCount="indefinite" keyTimes="0;.56;.66;.88;.96;1" values="0;0;.55;.55;0;0"/></ellipse></g>
      </g>
    </g>

    <!-- acacia -->
    <g class="px" data-speed="0.16" transform="translate(1180 360)"><path d="M0 120 C2 70 -4 40 2 6 M2 20 C-30 8 -60 14 -88 -4 M2 14 C36 -2 70 4 98 -14" stroke="#2a1d1b" stroke-width="6" fill="none" stroke-linecap="round"/>
      <path d="M-120 -4 C-96 -30 -40 -34 -4 -16 C20 -36 80 -40 120 -12 C90 -12 40 -6 0 -2 C-40 -2 -90 0 -120 -4Z" fill="#2f3a24"/></g>

    <!-- birds -->
    <?php foreach ([[0, 120, 26, 0], [-120, 160, 36, 3], [-260, 100, 30, 6], [-380, 190, 40, 9]] as $b): ?>
      <g fill="none" stroke="#2a1d1b" stroke-width="2.2" stroke-linecap="round" opacity=".8"><path d="M0 0 q8 -9 16 0 q8 -9 16 0"><animate attributeName="d" dur=".9s" repeatCount="indefinite" values="M0 0 q8 -9 16 0 q8 -9 16 0;M0 -4 q8 5 16 4 q8 1 16 -4;M0 0 q8 -9 16 0 q8 -9 16 0"/></path>
        <animateMotion dur="<?= $b[2] ?>s" begin="<?= $b[3] ?>s" repeatCount="indefinite" path="M<?= $b[0] ?> <?= $b[1] ?> C400 <?= $b[1] - 60 ?> 900 <?= $b[1] + 30 ?> 1560 <?= $b[1] - 30 ?>"/></g>
    <?php endforeach; ?>

    <!-- grass in front, swaying -->
    <g class="grass" fill="#3a4a2a"><?php for ($x = 0; $x <= 1440; $x += 34): $h = 26 + (($x * 7) % 30); ?>
      <path d="M<?= $x ?> 560 q4 -<?= $h ?> <?= 6 + ($x % 5) ?> -<?= $h + 10 ?> q-2 <?= 10 ?> 4 <?= $h + 10 ?>Z" style="animation-delay:<?= -(($x % 9) * .3) ?>s"/>
    <?php endfor; ?></g>
    <rect y="548" width="1440" height="12" fill="#2e3b21"/>
  </svg>
  <div class="jscene__txt wrap"><span class="eyebrow"><?= et('scene.eyebrow', 'A day in Jawai') ?></span><h2><?= et('scene.title', 'From first light to starry night') ?></h2><p><?= et('scene.text', 'Leopards stir on the granite at dawn, flamingos wade the dam by day, and the hills glow gold at dusk. Come for a night, stay for the sunrise.') ?></p></div>
</section>
