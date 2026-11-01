(function () {
  'use strict';
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;


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


  /* Reveal on scroll */
  var rv = $$('.rv');
  if ('IntersectionObserver' in window && !reduce) {
    var io = new IntersectionObserver(function (es) { es.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); } }); }, { threshold: .12, rootMargin: '0px 0px -40px 0px' });
    rv.forEach(function (n) { io.observe(n); });
  } else rv.forEach(function (n) { n.classList.add('is-in'); });

  /* Hero slider: one set of slides for phones, one for computers; rebuilt when the screen size class changes */
  var hero = $('#hero');
  if (hero) {
    var all = $$('.hslide', hero), ctl = $('.hero__ctl', hero), dotsBox = $('.hdots', hero), pb = $('[data-hero=pause]', hero);
    var slides = [], dots = [], cur = 0, timer = null, paused = reduce;
    var go = function (i) {
      cur = (i + slides.length) % slides.length;
      slides.forEach(function (s, k) { s.classList.toggle('is-active', k === cur); s.setAttribute('aria-hidden', k === cur ? 'false' : 'true'); });
      dots.forEach(function (d, k) { d.setAttribute('aria-selected', k === cur); });
    };
    var play = function () { clearInterval(timer); if (!paused && slides.length > 1) timer = setInterval(function () { go(cur + 1); }, 7000); };
    var build = function () {
      var mob = window.matchMedia('(max-width: 700px)').matches;
      slides = all.filter(function (s) { return s.classList.contains(mob ? 'dv-m' : 'dv-d'); });
      all.forEach(function (s) { if (slides.indexOf(s) < 0) { s.classList.remove('is-active'); s.setAttribute('aria-hidden', 'true'); } });
      dotsBox.innerHTML = ''; dots = slides.map(function (s, k) {
        var b = document.createElement('button'); b.type = 'button'; b.setAttribute('role', 'tab'); b.setAttribute('aria-label', 'Slide ' + (k + 1));
        b.addEventListener('click', function () { go(k); play(); }); dotsBox.appendChild(b); return b;
      });
      ctl.hidden = slides.length < 2;
      go(0); play();
    };
    $$('[data-hero=prev]', hero).forEach(function (b) { b.addEventListener('click', function () { go(cur - 1); play(); }); });
    $$('[data-hero=next]', hero).forEach(function (b) { b.addEventListener('click', function () { go(cur + 1); play(); }); });
    if (pb) pb.addEventListener('click', function () { paused = !paused; pb.setAttribute('aria-label', paused ? 'Play slideshow' : 'Pause slideshow'); play(); });
    hero.addEventListener('mouseenter', function () { clearInterval(timer); });
    hero.addEventListener('mouseleave', play);
    hero.addEventListener('focusin', function () { clearInterval(timer); });
    hero.addEventListener('focusout', play);
    var mq = window.matchMedia('(max-width: 700px)');
    if (mq.addEventListener) mq.addEventListener('change', build); else if (mq.addListener) mq.addListener(build);
    build();
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
    var showRTab = function (k) {
      $$('[data-rtab]').forEach(function (t) { var on = t.dataset.rtab === k; t.setAttribute('aria-selected', on); var pn = $('#rp-' + t.dataset.rtab); if (pn) pn.hidden = !on; });
      if (k === 'map') { buildMap(); if (rm) setTimeout(function () { rm.invalidateSize(); }, 60); } else { var w = $('#rp-wheel'); if (w) w.classList.add('is-in'); }
    };
    var selectPlace = function (id, fromMap) {
      var prev = selected; selected = id; if (prev) restyle(prev); $$('.ritem').forEach(function (it) { it.classList.toggle('is-sel', it.dataset.r === String(id)); });
      showRTab('map'); if (!rm) return;
      var p = RD.points.filter(function (x) { return String(x.id) === String(id); })[0]; if (!p) return;
      if (!rm.hasLayer(markers[id])) { setChip('all'); fitKind('all'); }
      restyle(id);
      var b = L.latLngBounds([[RD.hotel.lat, RD.hotel.lng], [p.lat, p.lng]]); b.extend(layers[id].line.getBounds());
      if (!fromMap) rm.flyToBounds(b, { padding: [70, 70], maxZoom: 12, duration: .8 });
      markers[id].openPopup();
    };
    $$('.rfilter .chip').forEach(function (c) { c.addEventListener('click', function () { setChip(c.dataset.kind); if (selected) { var o = selected; selected = null; restyle(o); $$('.ritem').forEach(function (it) { it.classList.remove('is-sel'); }); } fitKind(c.dataset.kind); }); });
    $$('[data-rtab]').forEach(function (t) { t.addEventListener('click', function () { showRTab(t.dataset.rtab); }); });
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
  var horizon = new Date(); horizon.setFullYear(horizon.getFullYear() + 2);
  $$('input[type=date]').forEach(function (d) { d.min = iso(today); d.max = iso(horizon); });
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
  /* Client-side validation (the server checks everything again) */
  var rules = {
    name: function (v) { return v.length < 2 ? 'Please enter your name (at least 2 letters).' : (!/^[\p{L}\p{M}][\p{L}\p{M} .'\-]*$/u.test(v) ? 'Names can only contain letters, spaces and . \' -' : (v.length > 80 ? 'Name is too long.' : '')); },
    phone: function (v) {
      if (!v) return 'Please enter your phone number.';
      if (v.length > 25 || !/^\+?[0-9][0-9 ()\-]*$/.test(v)) return 'Use digits only (spaces, + and - are fine).';
      var d = v.replace(/\D/g, ''); if (d.length < 8) return 'That phone number is too short.';
      if (v[0] === '+') { if (d.indexOf('91') === 0) return /^91[6-9]\d{9}$/.test(d) ? '' : 'Indian numbers need 10 digits after +91.'; return d.length >= 8 && d.length <= 15 ? '' : 'Please enter a valid international number.'; }
      if (d.length === 12 && d.indexOf('91') === 0) d = d.slice(2); if (d.length === 11 && d[0] === '0') d = d.slice(1);
      return /^[6-9]\d{9}$/.test(d) ? '' : 'Enter a 10-digit mobile number starting with 6, 7, 8 or 9.';
    },
    email: function (v) { return v && !/^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/.test(v) ? 'That email address does not look right.' : ''; },
    notes: function (v, i) { return i.required && v.length < 5 ? 'Please write a little more so we can help.' : (v.length > 600 ? 'Please keep this under 600 characters.' : ''); }
  };
  function fieldMsg(i, f) {
    var v = i.value.trim();
    if (i.required && v === '') return 'This field is required.';
    if (rules[i.name]) { var m = rules[i.name](v, i); if (m) return m; }
    if (i.type === 'date' && v) {
      if (i.min && v < i.min) return 'Please choose today or a later date.';
      if (i.max && v > i.max) return 'Please choose a date within the next 2 years.';
      if (i.name === 'date_to') { var fi = $('[name=date_from]', f); if (fi && fi.value && v <= fi.value) return 'Check-out must be after check-in.'; }
    }
    return '';
  }
  function showMsg(i, m) {
    var box = i.parentNode.querySelector('.ferr');
    if (m) { if (!box) { box = document.createElement('p'); box.className = 'ferr'; box.setAttribute('role', 'alert'); i.parentNode.appendChild(box); } box.textContent = m; i.setAttribute('aria-invalid', 'true'); i.classList.add('is-bad'); }
    else { if (box) box.remove(); i.removeAttribute('aria-invalid'); i.classList.remove('is-bad'); }
  }
  $$('.bform').forEach(function (f) {
    var fields = $$('input[name]:not([type=hidden]):not([type=radio]):not(.hp input), textarea', f);
    fields.forEach(function (i) { i.addEventListener('blur', function () { if (i.value !== '' || i.classList.contains('is-bad')) showMsg(i, fieldMsg(i, f)); }); i.addEventListener('input', function () { if (i.classList.contains('is-bad')) showMsg(i, fieldMsg(i, f)); }); });
    f.addEventListener('submit', function (e) {
      var bad = null;
      fields.forEach(function (i) { var m = fieldMsg(i, f); showMsg(i, m); if (m && !bad) bad = i; });
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
