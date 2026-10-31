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

  /* Drawer backdrop */
  var bd = $('#navbd'); if (bd) bd.addEventListener('click', function () { nav.classList.remove('is-open'); if (tgl) tgl.setAttribute('aria-expanded', 'false'); });

  /* Word-by-word headings */
  $$('.sechead h2, .jscene__txt h2, .cta h2, .pagehead h1').forEach(function (h) {
    if (reduce || h.children.length) return;
    var words = h.textContent.trim().split(/\s+/); h.setAttribute('aria-label', h.textContent.trim());
    h.classList.add('words'); h.innerHTML = words.map(function (w, i) { return '<span aria-hidden="true" style="--w:' + i + '">' + w.replace(/&/g, '&amp;') + '&nbsp;</span>'; }).join('');
    var host = h.closest('.rv') || h; if (!h.closest('.rv')) { h.classList.add('rv'); }
  });

  /* Reveal on scroll */
  var rv = $$('.rv');
  if ('IntersectionObserver' in window && !reduce) {
    var io = new IntersectionObserver(function (es) { es.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); } }); }, { threshold: .12, rootMargin: '0px 0px -40px 0px' });
    rv.forEach(function (n) { io.observe(n); });
  } else rv.forEach(function (n) { n.classList.add('is-in'); });

  /* Hero slider */
  var hero = $('#hero');
  if (hero && +hero.dataset.count > 1) {
    var slides = $$('.hslide', hero), dots = $$('.hdots button', hero), cur = 0, timer = null, paused = reduce, pb = $('[data-hero=pause]', hero);
    var go = function (i) {
      cur = (i + slides.length) % slides.length;
      slides.forEach(function (s, k) { s.classList.toggle('is-active', k === cur); s.setAttribute('aria-hidden', k === cur ? 'false' : 'true'); });
      dots.forEach(function (d, k) { d.setAttribute('aria-selected', k === cur); });
    };
    var play = function () { clearInterval(timer); if (!paused) timer = setInterval(function () { go(cur + 1); }, 7000); };
    $$('[data-hero=prev]', hero).forEach(function (b) { b.addEventListener('click', function () { go(cur - 1); play(); }); });
    $$('[data-hero=next]', hero).forEach(function (b) { b.addEventListener('click', function () { go(cur + 1); play(); }); });
    dots.forEach(function (d, k) { d.addEventListener('click', function () { go(k); play(); }); });
    if (pb) pb.addEventListener('click', function () { paused = !paused; pb.setAttribute('aria-label', paused ? 'Play slideshow' : 'Pause slideshow'); play(); });
    hero.addEventListener('mouseenter', function () { clearInterval(timer); });
    hero.addEventListener('mouseleave', play);
    hero.addEventListener('focusin', function () { clearInterval(timer); });
    hero.addEventListener('focusout', play);
    play();
  }

  /* Count-up numbers */
  var counters = $$('.stat b, .jkm b');
  var runCount = function (el) {
    var m = el.textContent.trim().match(/^(\d+(?:\.\d+)?)(\D*)$/); if (!m || reduce) return;
    var end = parseFloat(m[1]), dec = (m[1].split('.')[1] || '').length, suf = m[2], t0 = null, dur = 1400;
    var step = function (t) { t0 = t0 || t; var p = Math.min(1, (t - t0) / dur), v = end * (1 - Math.pow(1 - p, 3)); el.textContent = (dec ? v.toFixed(dec) : Math.round(v)) + suf; if (p < 1) requestAnimationFrame(step); };
    el.textContent = '0' + suf; requestAnimationFrame(step);
  };
  if ('IntersectionObserver' in window) {
    var cio = new IntersectionObserver(function (es) { es.forEach(function (en) { if (en.isIntersecting) { runCount(en.target); cio.unobserve(en.target); } }); }, { threshold: .6 });
    counters.forEach(function (c) { cio.observe(c); });
  }

  /* Scroll progress + parallax (one rAF-throttled handler) */
  var prog = $('#progress'), heroBgs = $$('.hslide__bg'), ticking = false;
  function onFrame() {
    ticking = false; var y = window.scrollY, h = document.documentElement.scrollHeight - window.innerHeight;
    if (prog) prog.style.transform = 'scaleX(' + (h > 0 ? Math.min(1, y / h) : 0) + ')';
    if (reduce) return;
    heroBgs.forEach(function (b) { if (y < window.innerHeight * 1.2) b.style.translate = '0 ' + (y * .22) + 'px'; });
  }
  window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(onFrame); } }, { passive: true }); onFrame();


  /* Real route map (Leaflet + OpenStreetMap) and tabs */
  var rd = $('#routes-data');
  if (rd) {
    var RD = JSON.parse(rd.textContent), rmapEl = $('#routesmap'), rm = null, layers = {}, markers = {}, selected = null;
    var esc = function (t) { var d = document.createElement('div'); d.textContent = t == null ? '' : t; return d.innerHTML; };
    var setHot = function (id, v) { $$('[data-r="' + id + '"]').forEach(function (x) { x.classList.toggle('is-hot', v); }); var m = rmapEl.querySelector('[data-mk="' + id + '"]'); if (m) m.classList.toggle('is-hot', v); };
    var restyle = function (id) { var l = layers[id]; if (!l) return; var on = (selected === id); l.line.setStyle({ weight: on ? 7 : (l.geom ? 4 : 2), opacity: on ? 1 : .85, color: on ? '#c9973f' : '#8a6420' }); if (on) l.line.bringToFront(); };
    var buildMap = function () {
      if (rm || !window.L || !rmapEl) return; rmapEl.innerHTML = '';
      rm = L.map(rmapEl, { scrollWheelZoom: false }).setView([RD.hotel.lat, RD.hotel.lng], 10);
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18, attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors' }).addTo(rm);
      rmapEl.addEventListener('click', function () { rm.scrollWheelZoom.enable(); });
      L.marker([RD.hotel.lat, RD.hotel.lng], { zIndexOffset: 1000, icon: L.divIcon({ className: '', iconSize: [50, 50], iconAnchor: [25, 25], html: '<div class="mk mk--hotel"><img src="' + esc(RD.hotel.img) + '" alt=""></div>' }) }).addTo(rm)
        .bindTooltip(esc(RD.hotel.name), { direction: 'top', offset: [0, -24] });
      RD.points.forEach(function (p) {
        if (!p.has) return;
        var pts = p.geom && p.geom.length > 1 ? p.geom.map(function (c) { return [c[1], c[0]]; }) : [[RD.hotel.lat, RD.hotel.lng], [p.lat, p.lng]];
        var line = L.polyline(pts, p.geom ? { color: '#8a6420', weight: 4, opacity: .85 } : { color: '#8a6420', weight: 2, dashArray: '6 8' }).addTo(rm);
        var mk = L.marker([p.lat, p.lng], { icon: L.divIcon({ className: '', iconSize: [32, 32], iconAnchor: [16, 16], html: '<div class="mk mk--n" data-mk="' + p.id + '">' + p.n + '</div>' }) }).addTo(rm);
        mk.bindTooltip('<b>' + esc(p.name.replace(/ \(.*/, '')) + '</b><br>' + p.km + ' km · ' + esc(p.time), { direction: 'top', offset: [0, -14] });
        mk.bindPopup('<h4>' + esc(p.name) + '</h4><b>' + p.km + ' km</b> by road · ' + esc(p.time) + '<br>' + esc(p.compass) + ' of the hotel, ' + p.straight + ' km in a straight line<br><a href="' + esc(p.dir) + '" target="_blank" rel="noopener">Directions in Google Maps &rarr;</a>');
        mk.on('mouseover', function () { setHot(p.id, true); }); mk.on('mouseout', function () { setHot(p.id, false); });
        mk.on('click', function () { selectPlace(p.id, true); });
        layers[p.id] = { line: line, kind: p.kind, geom: !!p.geom }; markers[p.id] = mk;
      });
      fitKind(RD.defKind || 'all');
    };
    var fitKind = function (k) {
      if (!rm) return; var b = L.latLngBounds([[RD.hotel.lat, RD.hotel.lng]]), any = false;
      RD.points.forEach(function (p) {
        if (!p.has) return; var show = (k === 'all' || p.kind === k);
        [layers[p.id].line, markers[p.id]].forEach(function (l) { if (show) { if (!rm.hasLayer(l)) l.addTo(rm); } else if (rm.hasLayer(l)) rm.removeLayer(l); });
        if (show) { any = true; b.extend([p.lat, p.lng]); b.extend(layers[p.id].line.getBounds()); }
      });
      $$('.ritem').forEach(function (it) { it.classList.toggle('is-dim', !(k === 'all' || it.dataset.kind === k)); });
      if (!any) rm.setView([RD.hotel.lat, RD.hotel.lng], 12); else rm.fitBounds(b, { padding: [60, 60], maxZoom: 12 });
    };
    var setChip = function (k) { $$('.rfilter .chip').forEach(function (x) { x.setAttribute('aria-pressed', x.dataset.kind === k); }); };
    var showTab = function (k) {
      $$('[data-rtab]').forEach(function (t) { var on = t.dataset.rtab === k; t.setAttribute('aria-selected', on); var pn = $('#rp-' + t.dataset.rtab); if (pn) pn.hidden = !on; });
      if (k === 'map') { buildMap(); if (rm) setTimeout(function () { rm.invalidateSize(); }, 60); } else { var w = $('#rp-wheel'); if (w) w.classList.add('is-in'); }
    };
    var selectPlace = function (id, fromMap) {
      var prev = selected; selected = id; if (prev) restyle(prev); $$('.ritem').forEach(function (it) { it.classList.toggle('is-sel', it.dataset.r === String(id)); });
      showTab('map'); if (!rm) return;
      var p = RD.points.filter(function (x) { return String(x.id) === String(id); })[0]; if (!p) return;
      if (!rm.hasLayer(markers[id])) { setChip('all'); fitKind('all'); }
      restyle(id);
      var b = L.latLngBounds([[RD.hotel.lat, RD.hotel.lng], [p.lat, p.lng]]); b.extend(layers[id].line.getBounds());
      if (!fromMap) rm.flyToBounds(b, { padding: [70, 70], maxZoom: 12, duration: .8 });
      markers[id].openPopup();
    };
    $$('.rfilter .chip').forEach(function (c) { c.addEventListener('click', function () { setChip(c.dataset.kind); if (selected) { var o = selected; selected = null; restyle(o); $$('.ritem').forEach(function (it) { it.classList.remove('is-sel'); }); } fitKind(c.dataset.kind); }); });
    $$('[data-rtab]').forEach(function (t) { t.addEventListener('click', function () { showTab(t.dataset.rtab); }); });
    var tryBuild = function (n) { if (window.L) { buildMap(); } else if (n < 40) { setTimeout(function () { tryBuild(n + 1); }, 250); } };
    if ('IntersectionObserver' in window) { new IntersectionObserver(function (es, o) { if (es[0].isIntersecting) { tryBuild(0); o.disconnect(); } }, { rootMargin: '300px' }).observe(rmapEl); } else { tryBuild(0); }
    var focusMap = function (id) { if (window.matchMedia('(max-width: 900px)').matches) { $('#rp-map').scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' }); } setTimeout(function () { selectPlace(id, false); }, window.L ? 50 : 600); };
    $$('[data-showmap]').forEach(function (b) { b.addEventListener('click', function () { focusMap(b.dataset.showmap); }); });
    $$('.ritem').forEach(function (it) { it.addEventListener('click', function (e) { if (e.target.closest('a,button')) return; var b = it.querySelector('[data-showmap]'); if (b) focusMap(b.dataset.showmap); }); });
    $$('.rnode').forEach(function (n) { n.addEventListener('click', function () { focusMap(n.dataset.r); }); });
  }

  /* Route map <-> list highlighting */
  $$('[data-r]').forEach(function (el) {
    var id = el.dataset.r, on = function (v) { return function () { $$('[data-r="' + id + '"]').forEach(function (x) { x.classList.toggle('is-hot', v); }); }; };
    el.addEventListener('mouseenter', on(true)); el.addEventListener('mouseleave', on(false)); el.addEventListener('focus', on(true)); el.addEventListener('blur', on(false));
  });

  /* Smooth page transitions for internal links */
  document.addEventListener('click', function (e) {
    var a = e.target.closest && e.target.closest('a[href]'); if (!a || reduce || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || a.target === '_blank' || a.hasAttribute('download')) return;
    var u; try { u = new URL(a.href, location.href); } catch (x) { return; }
    if (u.origin !== location.origin || (u.pathname === location.pathname && u.hash) || /^(tel|mailto):/.test(a.href)) return;
    if (/\/admin(-jm)?\//.test(u.pathname)) return;
    e.preventDefault(); document.body.classList.add('is-leaving'); setTimeout(function () { location.href = a.href; }, 200);
  });
  window.addEventListener('pageshow', function (e) { if (e.persisted) document.body.classList.remove('is-leaving'); });

  /* Hide the floating WhatsApp button while a form is on screen (mobile) */
  var bb = $$('.bookbox, .hero');
  if (bb.length && 'IntersectionObserver' in window) {
    var onScreen = new Set(); var fio = new IntersectionObserver(function (es) { es.forEach(function (en) { en.isIntersecting ? onScreen.add(en.target) : onScreen.delete(en.target); }); document.body.classList.toggle('fab-hide', onScreen.size > 0); }, { threshold: .08 });
    bb.forEach(function (b) { fio.observe(b); });
  }

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
  function estStay() {
    var f = sIn && sIn.closest('form'), est = f && $('[data-est]', f); if (!est) return;
    if (sIn.value) { var m = new Date(sIn.value); m.setDate(m.getDate() + 1); sOut.min = iso(m); if (sOut.value && sOut.value <= sIn.value) sOut.value = ''; }
    var opt = sRoom ? sRoom.selectedOptions[0] : null, price = opt ? parseInt(opt.dataset.price || 0, 10) : 0, tax = opt ? parseFloat(opt.dataset.tax || 0) : 0;
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
