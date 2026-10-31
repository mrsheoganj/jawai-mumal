(function () {
  'use strict';
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* Preloader (once per session) */
  var pre = $('#preloader');
  if (pre && !document.documentElement.classList.contains('no-pre')) {
    var start = Date.now(), hide = function () {
      var wait = Math.max(0, 1500 - (Date.now() - start));
      setTimeout(function () { pre.classList.add('is-done'); try { sessionStorage.setItem('jmPre', '1'); } catch (e) {} }, wait);
    };
    if (document.readyState === 'complete') hide(); else window.addEventListener('load', hide);
    setTimeout(hide, 4000);
  }

  /* Header */
  var nav = $('#nav'), tgl = $('#navtoggle');
  var solidAlways = document.body.classList.contains('nav-solid');
  var onScroll = function () { nav.classList.toggle('is-solid', solidAlways || window.scrollY > 40); };
  onScroll(); window.addEventListener('scroll', onScroll, { passive: true });
  if (tgl) {
    tgl.addEventListener('click', function () {
      var o = nav.classList.toggle('is-open'); tgl.setAttribute('aria-expanded', o);
    });
    $$('#navlinks a').forEach(function (a) { a.addEventListener('click', function () { nav.classList.remove('is-open'); tgl.setAttribute('aria-expanded', 'false'); }); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { nav.classList.remove('is-open'); } });
  }

  /* Reveal on scroll */
  var rv = $$('.rv');
  if ('IntersectionObserver' in window && !reduce) {
    var io = new IntersectionObserver(function (es) { es.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); } }); }, { threshold: .12, rootMargin: '0px 0px -40px 0px' });
    rv.forEach(function (n) { io.observe(n); });
  } else rv.forEach(function (n) { n.classList.add('is-in'); });

  /* Booking tabs */
  var tabs = $$('.tab');
  function showTab(k, focus) {
    tabs.forEach(function (t) {
      var on = t.dataset.tab === k;
      t.setAttribute('aria-selected', on); t.tabIndex = on ? 0 : -1;
      var p = $('#panel-' + t.dataset.tab); if (p) p.hidden = !on;
      if (on && focus) t.focus();
    });
  }
  tabs.forEach(function (t, i) {
    t.addEventListener('click', function () { showTab(t.dataset.tab); });
    t.addEventListener('keydown', function (e) {
      var n = e.key === 'ArrowRight' ? i + 1 : e.key === 'ArrowLeft' ? i - 1 : null;
      if (n === null) return; e.preventDefault(); n = (n + tabs.length) % tabs.length; showTab(tabs[n].dataset.tab, true);
    });
  });
  $$('[data-open-tab]').forEach(function (a) {
    a.addEventListener('click', function () {
      showTab(a.dataset.openTab);
      var s = a.dataset.safari, r = a.dataset.room;
      if (s) { var ss = $('#f-s'); if (ss) { ss.value = s; ss.dispatchEvent(new Event('change')); } }
      if (r) { var rs = $('#s-room'); if (rs) { rs.value = r; rs.dispatchEvent(new Event('change')); } }
    });
  });

  /* Dates & estimates */
  var iso = function (d) { return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0'); };
  var today = new Date(), tom = new Date(Date.now() + 864e5);
  $$('input[type=date]').forEach(function (d) { d.min = iso(today); });
  var money = function (n) { return '₹' + Math.round(n).toString().replace(/\B(?=(\d{2})*\d{3}(?!\d))/g, ','); };
  var sIn = $('#s-in'), sOut = $('#s-out'), sRoom = $('#s-room');
  var tax = parseFloat((document.body.dataset.tax || '12'));
  function estStay() {
    var f = sIn && sIn.closest('form'), est = f && $('[data-est]', f); if (!est) return;
    if (sIn.value) { var m = new Date(sIn.value); m.setDate(m.getDate() + 1); sOut.min = iso(m); if (sOut.value && sOut.value <= sIn.value) sOut.value = ''; }
    var price = sRoom ? parseInt(sRoom.selectedOptions[0].dataset.price || 0, 10) : 0;
    if (sIn.value && sOut.value && price) {
      var n = Math.round((new Date(sOut.value) - new Date(sIn.value)) / 864e5);
      if (n > 0) { est.textContent = n + (n > 1 ? ' nights · ' : ' night · ') + 'approx. ' + money(price * n * (1 + tax / 100)) + ' incl. GST'; return; }
    }
    est.textContent = '';
  }
  [sIn, sOut, sRoom].forEach(function (n) { n && n.addEventListener('change', estStay); });
  estStay();
  var fS = $('#f-s'), fG = $('#f-g');
  function estSafari() {
    if (!fS) return; var f = fS.closest('form'), est = $('[data-est]', f); if (!est) return;
    var o = fS.selectedOptions[0], mode = ($('input[name=mode]:checked', f) || {}).value, g = parseInt(fG.value, 10) || 1;
    if (!o) return; var amt = mode === 'private' ? parseInt(o.dataset.pv, 10) : parseInt(o.dataset.pp, 10) * g;
    est.textContent = 'approx. ' + money(amt) + (mode === 'private' ? ' per jeep' : ' for ' + g);
  }
  if (fS) { [fS, fG].forEach(function (n) { n.addEventListener('change', estSafari); }); $$('input[name=mode]').forEach(function (n) { n.addEventListener('change', estSafari); }); estSafari(); }

  /* Client-side validation (server re-validates) */
  $$('.bform').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      var bad = null;
      $$('[required]', f).forEach(function (i) {
        var ok = i.value.trim() !== ''; if (i.name === 'phone') ok = i.value.replace(/\D/g, '').length >= 8;
        i.style.borderColor = ok ? '' : 'var(--err)'; if (!ok && !bad) bad = i;
      });
      if (bad) { e.preventDefault(); bad.focus(); }
    });
  });

  /* Filters (rooms / menu / gallery) */
  $$('[data-filter-group]').forEach(function (g) {
    var items = $$(g.dataset.filterGroup), chips = $$('.chip', g);
    chips.forEach(function (c) {
      c.addEventListener('click', function () {
        chips.forEach(function (x) { x.setAttribute('aria-pressed', x === c); });
        var v = c.dataset.filter;
        items.forEach(function (it) { it.hidden = !(v === 'all' || it.dataset.cat === v); });
      });
    });
  });

  /* Lightbox */
  var lb = $('#lb');
  if (lb) {
    var links = $$('.mason a'), idx = 0, im = $('img', lb), cap = $('p', lb);
    var vis = function () { return links.filter(function (l) { return !l.hidden; }); };
    var show = function (i) { var v = vis(); if (!v.length) return; idx = (i + v.length) % v.length; im.src = v[idx].href; im.alt = v[idx].dataset.title || ''; cap.textContent = v[idx].dataset.title || ''; };
    var open = function () { lb.classList.add('is-open'); document.body.style.overflow = 'hidden'; $('.lb__x', lb).focus(); };
    var close = function () { lb.classList.remove('is-open'); document.body.style.overflow = ''; };
    links.forEach(function (l) { l.addEventListener('click', function (e) { e.preventDefault(); show(vis().indexOf(l)); open(); }); });
    $('.lb__x', lb).addEventListener('click', close);
    $('.lb__p', lb).addEventListener('click', function () { show(idx - 1); });
    $('.lb__n', lb).addEventListener('click', function () { show(idx + 1); });
    lb.addEventListener('click', function (e) { if (e.target === lb) close(); });
    document.addEventListener('keydown', function (e) {
      if (!lb.classList.contains('is-open')) return;
      if (e.key === 'Escape') close(); if (e.key === 'ArrowRight') show(idx + 1); if (e.key === 'ArrowLeft') show(idx - 1);
    });
  }
})();
