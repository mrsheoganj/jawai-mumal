<?php
/** "How to reach": real map (OpenStreetMap), true-bearing distance wheel and journey list. Data: Admin > Distances & travel + hotel coordinates. */
$rt = rows("SELECT * FROM jm_routes WHERE status='active' ORDER BY sort_order, id");
if (!$rt) return;
$hLat = (float)setting('map_lat'); $hLng = (float)setting('map_lng');
$hasHotel = has_coords(setting('map_lat'), setting('map_lng'));
$fmtKm = fn($k) => rtrim(rtrim(number_format((float)$k, 1), '0'), '.');
$maxS = 1; $items = [];
foreach ($rt as $r) {
    $o = ['id' => (int)$r['id'], 'name' => $r['name'], 'kind' => $r['kind'], 'code' => $r['code'], 'km' => (float)$r['km'], 'time' => $r['travel_time'], 'note' => $r['note'], 'dir' => route_link($r), 'has' => false];
    if ($hasHotel && has_coords($r['lat'], $r['lng'])) {
        $la = (float)$r['lat']; $ln = (float)$r['lng'];
        $o = array_merge($o, ['has' => true, 'lat' => $la, 'lng' => $ln, 'straight' => round(geo_haversine_km($hLat, $hLng, $la, $ln), 1), 'bearing' => round(geo_bearing($hLat, $hLng, $la, $ln))]);
        $o['compass'] = geo_compass($o['bearing']);
        $o['geom'] = $r['geom'] ? (json_decode($r['geom'], true) ?: null) : null;
        $maxS = max($maxS, $o['straight']);
    }
    $items[] = $o;
}
$mapOk = $hasHotel && count(array_filter($items, fn($x) => $x['has'])) > 0;
$cx = 400; $cy = 275; $paths = icon_paths();
$kinds = array_values(array_unique(array_column(array_filter($items, fn($x) => $x['has']), 'kind')));
$defKind = in_array('rail', $kinds, true) ? 'rail' : 'all';
foreach ($items as $n => &$it) { $it['n'] = $n + 1; } unset($it);
$payload = ['defKind' => $defKind, 'hotel' => ['lat' => $hLat, 'lng' => $hLng, 'name' => setting('site_name'), 'img' => img(setting('logo_emblem'))], 'points' => $items];
?>
<section class="sec routes" id="reach">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
  <div class="wrap">
    <div class="sechead rv"><span class="eyebrow eyebrow--dark"><?= et('routes.eyebrow', 'Getting here') ?></span><h2><?= et('routes.title', 'How far is Jawai?') ?></h2><p><?= et('routes.sub', 'Real driving distances from the nearest railway stations, airports and cities to our door in Bera. Tap a place to see its route on the map.') ?></p></div>

    <div class="rgrid">
      <ol class="rlist">
        <?php foreach ($items as $i => $o): ?>
          <li class="ritem rv d<?= $i % 3 ?>" data-r="<?= $o['id'] ?>" data-kind="<?= e($o['kind']) ?>">
            <span class="rnum" aria-hidden="true"><?= $o['n'] ?></span>
            <div class="rtxt">
              <span class="rkind"><?= icon(route_kind_icon($o['kind']), 15) ?> <?= e(route_kind_label($o['kind'])) ?><?= $o['code'] ? ' · ' . e($o['code']) : '' ?></span>
              <h3><?= e($o['name']) ?></h3>
              <?php if ($o['has']): ?><span class="rdirchip"><span class="jarrow" style="transform:rotate(<?= (int)$o['bearing'] - 45 ?>deg)"><?= icon('navigation', 13) ?></span><?= e($o['compass']) ?> of hotel · <?= e($fmtKm($o['straight'])) ?> km straight</span><?php endif; ?>
              <?php if ($o['note']): ?><p><?= e($o['note']) ?></p><?php endif; ?>
              <div class="rlinks"><a class="link" href="<?= e($o['dir']) ?>" target="_blank" rel="noopener"><?= icon('navigation', 16) ?> <?= et('routes.dir', 'Directions') ?></a><?php if ($mapOk && $o['has']): ?><button type="button" class="link link--btn" data-showmap="<?= $o['id'] ?>"><?= icon('map-pin', 16) ?> <?= et('routes.show', 'Show on map') ?></button><?php endif; ?></div>
            </div>
            <div class="rdist"><b data-count="<?= e($fmtKm($o['km'])) ?>"><?= e($fmtKm($o['km'])) ?></b><small>km</small><span><?= e($o['time']) ?></span><em><?= et('routes.byroad', 'by road') ?></em></div>
          </li>
        <?php endforeach; ?>
      </ol>

      <?php if ($mapOk): ?>
      <div class="rview">
        <div class="rtabs" role="tablist" aria-label="Map view">
          <button type="button" role="tab" id="rt-map" data-rtab="map" aria-selected="true" aria-controls="rp-map"><?= icon('map-pin', 18) ?> <?= et('routes.tab.map', 'Real map') ?></button>
          <button type="button" role="tab" id="rt-wheel" data-rtab="wheel" aria-selected="false" aria-controls="rp-wheel"><?= icon('compass', 18) ?> <?= et('routes.tab.wheel', 'Distance wheel') ?></button>
        </div>
        <div class="rpanel" id="rp-map" role="tabpanel" aria-labelledby="rt-map">
          <div class="rfilter" role="group" aria-label="Filter places">
            <?php foreach ($kinds as $k): ?><button type="button" class="chip" data-kind="<?= e($k) ?>" aria-pressed="<?= $defKind === $k ? 'true' : 'false' ?>"><?= e(route_kind_plural($k)) ?></button><?php endforeach; ?>
            <button type="button" class="chip" data-kind="all" aria-pressed="<?= $defKind === 'all' ? 'true' : 'false' ?>"><?= et('routes.f.all', 'Show all') ?></button>
          </div>
          <div class="rlive" id="routesmap" role="application" aria-label="<?= et('routes.maplabel', 'Interactive map showing real roads from the hotel to airports, stations and cities') ?>"><p class="rlive__fallback"><?= et('routes.nomap', 'The interactive map needs an internet connection.') ?> <a class="link" href="<?= e(maps_open_url()) ?>" target="_blank" rel="noopener"><?= et('routes.openmaps', 'Open in Google Maps') ?></a></p></div>
          <p class="rlegend"><span><i class="lg lg--road"></i> <?= et('routes.l.road', 'Real driving route') ?></span><span><i class="lg lg--hotel"></i> <?= et('routes.l.hotel', 'Jawai Mumal') ?></span><a class="link" href="<?= e(maps_open_url()) ?>" target="_blank" rel="noopener"><?= icon('external', 14) ?> <?= et('routes.openmaps', 'Open in Google Maps') ?></a></p>
        </div>

        <div class="rpanel rmap" id="rp-wheel" role="tabpanel" aria-labelledby="rt-wheel" hidden>
          <svg viewBox="0 0 800 560" role="img" aria-label="<?= et('routes.alt', 'Distance wheel: each place is drawn in its true compass direction from the hotel and at its true straight-line distance') ?>">
            <?php foreach ([0.25, 0.5, 0.75, 1] as $f): $rr = 70 + 185 * $f; ?><circle class="ring" cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= round($rr) ?>"/><text class="ringlbl" x="<?= $cx + 6 ?>" y="<?= round($cy - $rr - 4) ?>"><?= round($maxS * $f) ?> km</text><?php endforeach; ?>
            <g class="cmp"><path d="M<?= $cx ?> 12 V538 M<?= $cx - 255 ?> <?= $cy ?> H<?= $cx + 255 ?>" /><text x="<?= $cx ?>" y="9" text-anchor="middle">N</text><text x="<?= $cx ?>" y="556" text-anchor="middle">S</text><text x="<?= $cx + 268 ?>" y="<?= $cy + 5 ?>">E</text><text x="<?= $cx - 268 ?>" y="<?= $cy + 5 ?>" text-anchor="end">W</text></g>
            <?php foreach ($items as $i => $o): if (!$o['has']) continue; $rad = 70 + 185 * ($o['straight'] / $maxS); $a = deg2rad($o['bearing'] - 90); $x = $cx + $rad * cos($a); $y = $cy + $rad * sin($a); $right = cos($a) >= 0; $d = 'M' . $cx . ' ' . $cy . ' L' . round($x, 1) . ' ' . round($y, 1); ?>
              <g class="rnode" data-r="<?= $o['id'] ?>" style="--i:<?= $i ?>">
                <path class="rline" d="<?= $d ?>" pathLength="1"/>
                <circle class="rdot" r="4"><animateMotion dur="<?= 3 + $i * .6 ?>s" repeatCount="indefinite" path="<?= $d ?>" begin="<?= $i * .4 ?>s"/></circle>
                <circle class="rpin" cx="<?= round($x, 1) ?>" cy="<?= round($y, 1) ?>" r="22"/>
                <text class="rn" x="<?= round($x, 1) ?>" y="<?= round($y + 6, 1) ?>" text-anchor="middle"><?= $o['n'] ?></text>
                <text class="rkm" x="<?= round($x + ($right ? 30 : -30), 1) ?>" y="<?= round($y - 2, 1) ?>" text-anchor="<?= $right ? 'start' : 'end' ?>"><?= e($fmtKm($o['km'])) ?> km</text>
                <text class="rname" x="<?= round($x + ($right ? 30 : -30), 1) ?>" y="<?= round($y + 15, 1) ?>" text-anchor="<?= $right ? 'start' : 'end' ?>"><?= e(($o['code'] ?: mb_strimwidth(preg_replace('/ \(.*/', '', $o['name']), 0, 16, '…')) . ' · ' . $o['compass']) ?></text>
              </g>
            <?php endforeach; ?>
            <g class="rhub"><circle class="pulse" cx="<?= $cx ?>" cy="<?= $cy ?>" r="40"/><circle class="pulse p2" cx="<?= $cx ?>" cy="<?= $cy ?>" r="40"/><circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="38" fill="#fff" stroke="#c9973f" stroke-width="2"/><image href="<?= e(img(setting('logo_emblem'))) ?>" x="<?= $cx - 34 ?>" y="<?= $cy - 34 ?>" width="68" height="68"/></g>
          </svg>
          <p class="rlegend"><span><?= et('routes.wheel.note', 'North is up. Each place sits in its true direction from the hotel and at its true straight-line distance; the number beside it is the real driving distance.') ?></span></p>
        </div>
        <script type="application/json" id="routes-data"><?= json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js" defer></script>
      </div>
      <?php endif; ?>
    </div>
    <p class="routes__foot rv"><?= icon('shield-check', 16) ?> <?= et('routes.note', 'Distances come from OpenStreetMap road routing; drive times include an allowance for traffic and village roads. Message us on WhatsApp for pick-up and directions.') ?></p>
  </div>
</section>
