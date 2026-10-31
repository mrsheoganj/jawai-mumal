<?php
/** "How to reach" section: animated radial route map (desktop) + journey list. Data: Admin > Distances & travel. */
$rt = rows("SELECT * FROM jm_routes WHERE status='active' ORDER BY sort_order, id");
if (!$rt) return;
$maxKm = max(array_map(fn($r) => (float)$r['km'], $rt)) ?: 1;
$N = count($rt); $cx = 400; $cy = 270;
$paths = icon_paths();
$pos = [];
foreach ($rt as $i => $r) {
    $ang = deg2rad(-90 + 360 * $i / max($N, 1) + (360 / max($N, 1)) / 2);   // spread evenly, start top-right
    $rad = 105 + 150 * sqrt(max(0.02, (float)$r['km'] / $maxKm));
    $pos[$i] = [$cx + $rad * cos($ang), $cy + $rad * sin($ang), cos($ang)];
}
$fmtKm = fn($k) => rtrim(rtrim(number_format((float)$k, 1), '0'), '.');
?>
<section class="sec routes" id="reach">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow"><?= et('routes.eyebrow', 'Getting here') ?></span><h2><?= et('routes.title', 'How far is Jawai?') ?></h2><p><?= et('routes.sub', 'Approximate road distances from Bera, where the hotel stands. Tap Directions for a live route in Google Maps.') ?></p></div>

    <div class="rmap rv" aria-hidden="false">
      <svg viewBox="0 0 800 540" role="img" aria-label="<?= et('routes.alt', 'Map showing distances from Jawai Mumal to nearby airports, railway stations and cities') ?>">
        <defs><radialGradient id="rg" cx="50%" cy="50%" r="50%"><stop offset="0" stop-color="#c9973f" stop-opacity=".28"/><stop offset="1" stop-color="#c9973f" stop-opacity="0"/></radialGradient></defs>
        <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="250" fill="url(#rg)"/>
        <?php foreach ([0.33, 0.66, 1] as $f): $rr = 105 + 150 * sqrt($f); ?><circle class="ring" cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= round($rr) ?>"/><text class="ringlbl" x="<?= $cx ?>" y="<?= round($cy - $rr - 4) ?>" text-anchor="middle"><?= round($maxKm * $f) ?> km</text><?php endforeach; ?>
        <?php foreach ($rt as $i => $r): [$x, $y, $c] = $pos[$i]; $d = 'M' . $cx . ' ' . $cy . ' L' . round($x, 1) . ' ' . round($y, 1); ?>
          <g class="rnode" data-r="<?= (int)$r['id'] ?>" style="--i:<?= $i ?>">
            <path class="rline" d="<?= $d ?>" pathLength="1"/>
            <circle class="rdot" r="4"><animateMotion dur="<?= 3 + $i * .6 ?>s" repeatCount="indefinite" path="<?= $d ?>" begin="<?= $i * .4 ?>s"/></circle>
            <circle class="rpin" cx="<?= round($x, 1) ?>" cy="<?= round($y, 1) ?>" r="23"/>
            <svg x="<?= round($x - 11, 1) ?>" y="<?= round($y - 11, 1) ?>" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="rico"><?= $paths[route_kind_icon($r['kind'])] ?? '' ?></svg>
            <text class="rkm" x="<?= round($x + ($c >= 0 ? 32 : -32), 1) ?>" y="<?= round($y - 1, 1) ?>" text-anchor="<?= $c >= 0 ? 'start' : 'end' ?>"><?= e($fmtKm($r['km'])) ?> km</text>
            <text class="rname" x="<?= round($x + ($c >= 0 ? 32 : -32), 1) ?>" y="<?= round($y + 16, 1) ?>" text-anchor="<?= $c >= 0 ? 'start' : 'end' ?>"><?= e($r['code'] ?: mb_strimwidth(preg_replace('/ \(.*/', '', $r['name']), 0, 18, '…')) ?></text>
          </g>
        <?php endforeach; ?>
        <g class="rhub"><circle class="pulse" cx="<?= $cx ?>" cy="<?= $cy ?>" r="44"/><circle class="pulse p2" cx="<?= $cx ?>" cy="<?= $cy ?>" r="44"/><circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="40" fill="#faf6ec"/><image href="<?= e(img(setting('logo_emblem'))) ?>" x="<?= $cx - 36 ?>" y="<?= $cy - 36 ?>" width="72" height="72"/></g>
      </svg>
    </div>

    <ol class="journey">
      <?php foreach ($rt as $i => $r): ?>
        <li class="jitem rv d<?= $i % 3 ?>" data-r="<?= (int)$r['id'] ?>" tabindex="0">
          <span class="jico"><?= icon(route_kind_icon($r['kind']), 22) ?></span>
          <div class="jbody">
            <span class="jkind"><?= e(route_kind_label($r['kind'])) ?><?= $r['code'] ? ' · ' . e($r['code']) : '' ?></span>
            <h3><?= e($r['name']) ?></h3>
            <?php if ($r['note']): ?><p><?= e($r['note']) ?></p><?php endif; ?>
            <a class="link" href="<?= e(route_link($r)) ?>" target="_blank" rel="noopener"><?= icon('navigation', 16) ?> <?= et('routes.dir', 'Directions') ?></a>
          </div>
          <div class="jkm"><b data-count="<?= e($fmtKm($r['km'])) ?>"><?= e($fmtKm($r['km'])) ?></b><small>km</small><span><?= e($r['travel_time']) ?></span></div>
        </li>
      <?php endforeach; ?>
    </ol>
    <p class="routes__foot rv"><?= icon('shield-check', 16) ?> <?= et('routes.note', 'Distances are approximate and change with traffic and road works. Call us for pick-up and directions.') ?></p>
  </div>
</section>
