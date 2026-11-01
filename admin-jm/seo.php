<?php
require __DIR__ . '/_init.php';

$g = seo_global_defaults();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['action'] ?? 'save';
    if ($act === 'reset_page') {
        $k = $_POST['page'] ?? '';
        if (isset(seo_pages()[$k])) { foreach (['title', 'desc', 'keywords', 'image'] as $f) set_setting("seo_{$k}_{$f}", ''); flash('ok', 'Restored the recommended SEO text for that page.'); }
        redirect('seo.php#pages');
    }
    $def = seo_defaults();
    foreach (seo_pages() as $k => $p) {
        foreach (['title', 'desc', 'keywords'] as $f) {
            $v = trim($_POST["p_{$k}_{$f}"] ?? '');
            set_setting("seo_{$k}_{$f}", $v === ($def[$k][$f] ?? '') ? '' : $v);
        }
        set_setting("seo_{$k}_image", trim($_POST["p_{$k}_image"] ?? ''));
    }
    $err = null; $up = save_upload('f_seo_image', $err);
    if ($up === null) flash('err', 'Share image: ' . $err);
    $img = $up ? $up : trim($_POST['seo_image'] ?? '');
    set_setting('seo_image', $img === $g['seo_image'] ? '' : $img);
    foreach (['seo_keywords', 'seo_locale', 'seo_business_type', 'seo_price_range', 'seo_same_as', 'seo_area_served', 'seo_amenities', 'seo_cuisine', 'seo_robots_extra'] as $k) {
        $v = trim($_POST[$k] ?? ''); set_setting($k, $v === ($g[$k] ?? '') ? '' : $v);
    }
    // Tracking IDs: keep only safe characters
    set_setting('seo_ga4', preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['seo_ga4'] ?? ''));
    set_setting('seo_gtm', preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['seo_gtm'] ?? ''));
    set_setting('seo_gsc', preg_replace('/[^A-Za-z0-9_\-]/', '', $_POST['seo_gsc'] ?? ''));
    set_setting('seo_bing', preg_replace('/[^A-Za-z0-9_\-]/', '', $_POST['seo_bing'] ?? ''));
    set_setting('seo_index', !empty($_POST['seo_index']) ? '1' : '0');
    set_setting('blog_in_menu', !empty($_POST['blog_in_menu']) ? '1' : '0');
    set_setting('blog_in_footer', !empty($_POST['blog_in_footer']) ? '1' : '0');
    flash('ok', 'SEO settings saved.');
    redirect('seo.php');
}

$def = seo_defaults();
$health = seo_health();
$okN = count(array_filter($health, fn($h) => $h[0]));
$posts = (int)val("SELECT COUNT(*) FROM jm_blog WHERE status = 'published'");
$site = seo_base();
admin_head('SEO & search ranking', 'seo');
?>
<style>
.hc{list-style:none;margin:0;padding:0;display:grid;gap:.45rem}
.hc li{display:flex;gap:.6rem;align-items:flex-start;font-size:.95rem}
.hc .dot{flex:0 0 22px;height:22px;border-radius:50%;display:grid;place-items:center;color:#fff;font-size:.8rem;margin-top:1px}
.hc .ok{background:#2f8f5b}.hc .no{background:#c0392b}.hc small{display:block;color:var(--muted)}
.snip{border:1px solid var(--line);border-radius:12px;padding:.8rem 1rem;background:#fff;margin:.2rem 0 1rem;max-width:640px}
.snip .u{font-size:.8rem;color:#4d5156;word-break:break-all}.snip .t{font-size:1.15rem;color:#1a0dab;margin:.1rem 0;line-height:1.3}.snip .d{font-size:.88rem;color:#4d5156;line-height:1.5}
.cnt{font-size:.78rem;color:var(--muted);float:right}.cnt.bad{color:#c0392b}.cnt.good{color:#2f8f5b}
.pg{border:1px solid var(--line);border-radius:14px;padding:1rem 1.1rem;margin-bottom:1rem;background:#fff}
.pg h3{margin:0 0 .6rem;font-size:1.05rem;display:flex;justify-content:space-between;align-items:center;gap:1rem}
.links{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.8rem}
.stickysave{position:sticky;bottom:0;background:linear-gradient(180deg,rgba(255,255,255,0),#fff 40%);padding:1rem 0;margin-top:1rem;z-index:5}
</style>

<div class="card">
  <h2>Search health check <span class="muted" style="font-weight:400">· <?= $okN ?> of <?= count($health) ?> good</span></h2>
  <ul class="hc">
    <?php foreach ($health as $h): ?><li><span class="dot <?= $h[0] ? 'ok' : 'no' ?>"><?= $h[0] ? '✓' : '!' ?></span><span><?= e($h[1]) ?><?php if (!$h[0] && $h[2]): ?><small><?= e($h[2]) ?></small><?php endif; ?></span></li><?php endforeach; ?>
  </ul>
  <div class="links">
    <a class="btn btn--line btn--sm" href="<?= e(url('sitemap.xml')) ?>" target="_blank" rel="noopener"><?= icon('external', 16) ?> Sitemap</a>
    <a class="btn btn--line btn--sm" href="<?= e(url('robots.txt')) ?>" target="_blank" rel="noopener"><?= icon('external', 16) ?> robots.txt</a>
    <a class="btn btn--line btn--sm" href="https://search.google.com/search-console" target="_blank" rel="noopener"><?= icon('external', 16) ?> Google Search Console</a>
    <a class="btn btn--line btn--sm" href="https://www.bing.com/webmasters" target="_blank" rel="noopener"><?= icon('external', 16) ?> Bing Webmaster</a>
    <a class="btn btn--line btn--sm" href="https://search.google.com/test/rich-results?url=<?= rawurlencode($site) ?>" target="_blank" rel="noopener"><?= icon('external', 16) ?> Rich results test</a>
    <a class="btn btn--line btn--sm" href="https://pagespeed.web.dev/analysis?url=<?= rawurlencode($site) ?>" target="_blank" rel="noopener"><?= icon('external', 16) ?> PageSpeed</a>
    <a class="btn btn--gold btn--sm" href="blog.php"><?= icon('pencil', 16) ?> Journal articles (<?= $posts ?>)</a>
  </div>
  <p class="help" style="margin-top:.8rem">Launch checklist: 1) add the site in Google Search Console and paste the verification code below; 2) submit <strong><?= e(seo_abs('sitemap.xml')) ?></strong>; 3) do the same in Bing; 4) add your Google Business Profile link under “Business info for Google”.</p>
</div>

<form method="post" enctype="multipart/form-data" id="seoform">
<?= csrf_field() ?><input type="hidden" name="action" value="save">

<div class="card" id="pages">
  <h2>Page titles & descriptions</h2>
  <p class="muted">Each page is pre-filled with researched, keyword-rich text. Edit anything; the preview shows how it can look on Google. Titles: about 30–60 characters. Descriptions: about 110–160.</p>
  <?php foreach (seo_pages() as $k => $p): $t = seo_field($k, 'title'); $d = seo_field($k, 'desc'); $kw = seo_field($k, 'keywords'); ?>
  <div class="pg" data-pg="<?= e($k) ?>">
    <h3><span><?= e($p[0]) ?> <span class="muted" style="font-weight:400">/<?= e($p[1]) ?></span></span>
      <button class="btn btn--line btn--sm" type="submit" form="reset_<?= e($k) ?>" data-confirm="Restore the recommended text for this page?">Restore recommended</button></h3>
    <div class="snip"><div class="u"><?= e(seo_abs($p[1])) ?></div><div class="t" data-s="t"><?= e($t) ?></div><div class="d" data-s="d"><?= e($d) ?></div></div>
    <div class="grid">
      <div class="c12"><label>Search title <span class="cnt" data-c="title"></span></label><input name="p_<?= e($k) ?>_title" value="<?= e($t) ?>" data-f="title" maxlength="120"></div>
      <div class="c12"><label>Search description <span class="cnt" data-c="desc"></span></label><textarea name="p_<?= e($k) ?>_desc" rows="2" data-f="desc" maxlength="320"><?= e($d) ?></textarea></div>
      <div class="c8"><label>Keywords (comma separated)</label><input name="p_<?= e($k) ?>_keywords" value="<?= e($kw) ?>"></div>
      <div class="c4"><label>Share image (optional)</label><input name="p_<?= e($k) ?>_image" value="<?= e(seo_raw("seo_{$k}_image")) ?>" placeholder="assets/uploads/…"></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="card">
  <h2>Sitewide search & sharing</h2>
  <div class="grid">
    <div class="c12"><label>Default share image (WhatsApp, Facebook, Google)</label>
      <div class="imgfield"><img id="pv_seoimg" src="<?= e(img(seo_cfg('seo_image'))) ?>" alt="">
        <div><input type="file" name="f_seo_image" accept="image/jpeg,image/png,image/webp" data-preview="pv_seoimg"><p class="help">Best size 1200 × 630 px.</p>
        <input name="seo_image" value="<?= e(seo_cfg('seo_image')) ?>"></div></div></div>
    <div class="c12"><label>Sitewide keywords (added to every page)</label><input name="seo_keywords" value="<?= e(seo_cfg('seo_keywords')) ?>"></div>
    <div class="c4"><label>Language / region</label><input name="seo_locale" value="<?= e(seo_cfg('seo_locale')) ?>"><p class="help">en-IN for English in India.</p></div>
    <div class="c8"><label style="display:flex;gap:.6rem;align-items:center;min-height:44px"><input type="checkbox" name="seo_index" value="1" <?= seo_cfg('seo_index') === '1' ? 'checked' : '' ?>> Allow search engines to index this website</label>
      <p class="help">Untick only while testing. (Coming soon and Maintenance modes are always hidden from search engines automatically.)</p></div>
  </div>
</div>

<div class="card">
  <h2>Business info for Google (rich results)</h2>
  <p class="muted">Used to build the structured data that helps Google show your hotel, restaurant, rooms, safaris and FAQs as rich results. Address, phone, coordinates and times come from Contact, logo & rules.</p>
  <div class="grid">
    <div class="c4"><label>Business type</label><select name="seo_business_type"><?php foreach (['Hotel', 'LodgingBusiness', 'Resort', 'BedAndBreakfast'] as $o): ?><option <?= seo_cfg('seo_business_type') === $o ? 'selected' : '' ?>><?= e($o) ?></option><?php endforeach; ?></select></div>
    <div class="c4"><label>Price range</label><input name="seo_price_range" value="<?= e(seo_cfg('seo_price_range')) ?>"><p class="help">₹ (budget) to ₹₹₹₹ (luxury).</p></div>
    <div class="c4"><label>Cuisine (one per line)</label><textarea name="seo_cuisine" rows="3"><?= e(seo_cfg('seo_cuisine')) ?></textarea></div>
    <div class="c12"><label>Profile links (one per line)</label><textarea name="seo_same_as" rows="4" placeholder="Google Business Profile / Maps link&#10;TripAdvisor&#10;Booking.com&#10;YouTube"><?= e(seo_cfg('seo_same_as')) ?></textarea><p class="help">Instagram and Facebook links from Contact settings are added automatically.</p></div>
    <div class="c6"><label>Areas served (one per line)</label><textarea name="seo_area_served" rows="4"><?= e(seo_cfg('seo_area_served')) ?></textarea></div>
    <div class="c6"><label>Amenities (one per line)</label><textarea name="seo_amenities" rows="4"><?= e(seo_cfg('seo_amenities')) ?></textarea></div>
  </div>
</div>

<div class="card">
  <h2>Tracking & verification</h2>
  <div class="grid">
    <div class="c6"><label>Google Search Console verification code</label><input name="seo_gsc" value="<?= e(seo_cfg('seo_gsc')) ?>" placeholder="content value of the meta tag"><p class="help">Search Console → add property → HTML tag → paste only the code inside content="…".</p></div>
    <div class="c6"><label>Bing Webmaster verification code</label><input name="seo_bing" value="<?= e(seo_cfg('seo_bing')) ?>"></div>
    <div class="c6"><label>Google Analytics 4 ID</label><input name="seo_ga4" value="<?= e(seo_cfg('seo_ga4')) ?>" placeholder="G-XXXXXXXXXX"></div>
    <div class="c6"><label>Google Tag Manager ID (optional, used instead of GA4)</label><input name="seo_gtm" value="<?= e(seo_cfg('seo_gtm')) ?>" placeholder="GTM-XXXXXXX"></div>
  </div>
</div>

<div class="card">
  <h2>Journal & crawling</h2>
  <p class="muted">The <?= $posts ?> journal articles are published and in the sitemap but are not linked from the menu or footer. Search engines find them through the sitemap; you can also switch on links below.</p>
  <div class="grid">
    <div class="c6"><label style="display:flex;gap:.6rem;align-items:center;min-height:44px"><input type="checkbox" name="blog_in_menu" value="1" <?= seo_cfg('blog_in_menu') === '1' ? 'checked' : '' ?>> Show “Journal” in the main menu</label></div>
    <div class="c6"><label style="display:flex;gap:.6rem;align-items:center;min-height:44px"><input type="checkbox" name="blog_in_footer" value="1" <?= seo_cfg('blog_in_footer') === '1' ? 'checked' : '' ?>> Show a Journal link in the footer</label></div>
    <div class="c12"><label>Extra robots.txt lines (advanced)</label><textarea name="seo_robots_extra" rows="3" placeholder="Disallow: /private/"><?= e(seo_cfg('seo_robots_extra')) ?></textarea></div>
  </div>
</div>

<div class="stickysave"><button class="btn btn--gold" type="submit"><?= icon('check', 18) ?> Save SEO settings</button></div>
</form>

<?php foreach (seo_pages() as $k => $p): ?>
<form id="reset_<?= e($k) ?>" method="post" style="display:none"><?= csrf_field() ?><input type="hidden" name="action" value="reset_page"><input type="hidden" name="page" value="<?= e($k) ?>"></form>
<?php endforeach; ?>

<script>
document.querySelectorAll('.pg').forEach(function(pg){
  var t=pg.querySelector('[data-f=title]'),d=pg.querySelector('[data-f=desc]'),st=pg.querySelector('[data-s=t]'),sd=pg.querySelector('[data-s=d]'),ct=pg.querySelector('[data-c=title]'),cd=pg.querySelector('[data-c=desc]');
  function upd(){
    st.textContent=t.value; sd.textContent=d.value;
    ct.textContent=t.value.length+' / 60'; ct.className='cnt '+(t.value.length>=30&&t.value.length<=62?'good':'bad');
    cd.textContent=d.value.length+' / 160'; cd.className='cnt '+(d.value.length>=110&&d.value.length<=165?'good':'bad');
  }
  t.addEventListener('input',upd); d.addEventListener('input',upd); upd();
});
</script>
<?php admin_foot(); ?>
