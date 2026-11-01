<?php
/**
 * All icons come from one professional, open-source library: Lucide (ISC licence, https://lucide.dev), 24px stroke grid.
 * The only other mark is WhatsApp's own official logo (assets/brand/whatsapp.svg). includes/icons_data.php is generated from Lucide.
 * Usage: echo icon('bed', 20);
 */
function icon_set(): array {
    static $d = null;
    if ($d === null) $d = require __DIR__ . '/icons_data.php';
    return $d;
}
/** Inner SVG markup of stroke icons (used inside other SVGs, e.g. the distance wheel). */
function icon_paths(): array {
    static $p = null;
    if ($p === null) { $p = []; foreach (icon_set() as $k => $i) { if ($i['t'] === 's') $p[$k] = $i['d']; } }
    return $p;
}
function icon(string $name, int $size = 20, string $class = ''): string {
    // WhatsApp always uses the official full-colour logo (assets/brand/whatsapp.svg), never a redrawn icon.
    if ($name === 'whatsapp') return '<img class="i i--wa' . ($class !== '' ? ' ' . $class : '') . '" src="' . htmlspecialchars(url('assets/brand/whatsapp.svg'), ENT_QUOTES) . '" width="' . $size . '" height="' . $size . '" alt="" aria-hidden="true" decoding="async">';
    $set = icon_set();
    if (!isset($set[$name])) return '';
    $i = $set[$name];
    $cls = 'i' . ($class !== '' ? ' ' . $class : '');
    $attr = $i['t'] === 'f'
        ? 'fill="currentColor"'
        : 'fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"';
    return '<svg class="' . $cls . '" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" ' . $attr . ' aria-hidden="true" focusable="false">' . $i['d'] . '</svg>';
}
