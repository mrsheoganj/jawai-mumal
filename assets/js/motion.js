/* Motion layer: reveals, parallax, tilt, ripple, counters, smooth accordion, header behaviour. Loaded after site.js. */
(function () {
  'use strict';
  var d = document, root = d.documentElement;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var fine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  var $$ = function (s, c) { return Array.prototype.slice.call((c || d).querySelectorAll(s)); };
  var has = 'IntersectionObserver' in window;

  /* ---- images fade in when loaded (safe fallback after 4 s) */
  var lazy = $$('img[loading="lazy"]');
  lazy.forEach(function (im) {
    var ok = function () { im.classList.add('is-loaded'); };
    if (im.complete && im.naturalWidth) ok(); else { im.addEventListener('load', ok, { once: true }); im.addEventListener('error', ok, { once: true }); }
  });
  setTimeout(function () { lazy.forEach(function (im) { im.classList.add('is-loaded'); }); }, 4000);
  if (reduce || !has) { $$('.stat b').forEach(function () {}); }

  /* ---- stagger: elements revealed together get a gentle cascade */
  var groups = new Map();
  $$('.rv').forEach(function (el) {
    if (/\bd[1-9]\b/.test(el.className) || el.style.transitionDelay) return;
    var p = el.parentElement; if (!groups.has(p)) groups.set(p, []); groups.get(p).push(el);
  });
  groups.forEach(function (list) { if (list.length > 1) list.forEach(function (el, i) { el.style.transitionDelay = Math.min(i, 6) * 90 + 'ms'; }); });

  /* ---- our own observer: headings, image frames, stats */
  var parallax = [];
  var targets = $$('.sechead, .frame, .stat, .pillar');
  targets.forEach(function (el) { if (el.matches('.frame, .pillar')) el.classList.add('px'); });
  if (has && !reduce) {
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (en) {
        if (!en.isIntersecting) return;
        en.target.classList.add('is-in'); io.unobserve(en.target);
        if (en.target.classList.contains('stat')) count(en.target.querySelector('b'));
      });
    }, { threshold: .15, rootMargin: '0px 0px -8% 0px' });
    targets.forEach(function (el) { io.observe(el); });
  } else targets.forEach(function (el) { el.classList.add('is-in'); });

  /* ---- count-up numbers (e.g. 300+, 1957, 50–70) */
  function count(b) {
    if (!b || b.dataset.done || reduce) return; b.dataset.done = '1';
    var txt = b.textContent, nums = txt.match(/\d[\d,]*/g); if (!nums) return;
    var finals = nums.map(function (n) { return +n.replace(/,/g, ''); }), parts = txt.split(/\d[\d,]*/);
    var starts = finals.map(function (n) { return n >= 1000 && n <= 2100 ? n - 70 : 0; }), t0 = null, dur = 1700;
    function frame(t) {
      if (t0 === null) t0 = t; var p = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - p, 4), out = parts[0];
      finals.forEach(function (n, i) { out += Math.round(starts[i] + (n - starts[i]) * e).toLocaleString('en-IN').replace(/,/g, nums[i].indexOf(',') > -1 ? ',' : '') + (parts[i + 1] || ''); });
      b.textContent = out; if (p < 1) requestAnimationFrame(frame); else b.textContent = txt;
    }
    requestAnimationFrame(frame);
  }

  /* ---- parallax for image frames (visible ones only) + hero text drift */
  var vis = new Set(), hero = d.getElementById('hero'), heroIn = null, ticking = false;
  if (!reduce) {
    $$('.frame.px').concat(fine ? $$('.pillar.px') : []).forEach(function (el) { parallax.push(el); });
    if (has) {
      var vio = new IntersectionObserver(function (es) { es.forEach(function (en) { en.isIntersecting ? vis.add(en.target) : vis.delete(en.target); }); schedule(); }, { rootMargin: '80px' });
      parallax.forEach(function (el) { vio.observe(el); });
    }
    if (hero) heroIn = $$('.hero__in', hero);
  }
  var lastY = window.scrollY, navEl = d.getElementById('nav'), top = null;
  function frame() {
    ticking = false; var y = window.scrollY, vh = window.innerHeight;
    if (!reduce) {
      vis.forEach(function (el) {
        var im = el.querySelector('img'); if (!im) return; var r = el.getBoundingClientRect();
        var prog = Math.max(-1, Math.min(1, ((r.top + r.height / 2) - vh / 2) / (vh / 2 + r.height / 2)));
        im.style.translate = '0 ' + (prog * -r.height * .045).toFixed(1) + 'px';
      });
      if (hero && y < vh * 1.1) { var k = Math.min(1, y / (vh * .75)); heroIn.forEach(function (n) { n.style.opacity = (1 - k * 1.1).toFixed(3); n.style.translate = '0 ' + (y * .12).toFixed(1) + 'px'; }); }
    }
    /* header: hide while scrolling down, show on scroll up */
    if (navEl) {
      var dy = y - lastY;
      if (Math.abs(dy) > 6) { navEl.classList.toggle('is-hidden', dy > 0 && y > 260); lastY = y; }
      if (y < 120) navEl.classList.remove('is-hidden');
    }
    if (top) top.classList.toggle('is-on', y > 700);
  }
  function schedule() { if (!ticking) { ticking = true; requestAnimationFrame(frame); } }
  window.addEventListener('scroll', schedule, { passive: true }); window.addEventListener('resize', schedule, { passive: true });
  if (navEl) navEl.addEventListener('focusin', function () { navEl.classList.remove('is-hidden'); });

  /* ---- back-to-top */
  top = d.createElement('button'); top.type = 'button'; top.className = 'to-top'; top.setAttribute('aria-label', 'Back to top');
  top.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>';
  top.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' }); });
  d.body.appendChild(top); schedule();

  /* ---- ripple on buttons, chips and tabs (touch feedback) */
  if (!reduce) d.addEventListener('pointerdown', function (e) {
    var b = e.target.closest ? e.target.closest('.btn, .chip, .tab') : null; if (!b) return;
    var r = b.getBoundingClientRect(), s = Math.max(r.width, r.height) * 2, sp = d.createElement('span');
    sp.className = 'rpl'; sp.style.cssText = 'width:' + s + 'px;height:' + s + 'px;left:' + (e.clientX - r.left - s / 2) + 'px;top:' + (e.clientY - r.top - s / 2) + 'px';
    b.appendChild(sp); setTimeout(function () { if (sp.parentNode) sp.parentNode.removeChild(sp); }, 750);
  }, { passive: true });

  /* ---- gentle 3D tilt on cards (mouse only) */
  if (fine && !reduce) $$('.card, .pillar').forEach(function (el) {
    var raf = 0;
    el.addEventListener('pointermove', function (e) {
      if (e.pointerType !== 'mouse') return; var r = el.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
      cancelAnimationFrame(raf); raf = requestAnimationFrame(function () { el.style.transition = 'transform .12s linear, box-shadow .5s'; el.style.transform = 'perspective(900px) rotateX(' + (-y * 5).toFixed(2) + 'deg) rotateY(' + (x * 6).toFixed(2) + 'deg) translateY(-6px)'; });
    });
    el.addEventListener('pointerleave', function () { cancelAnimationFrame(raf); el.style.transition = ''; el.style.transform = ''; });
  });

  /* ---- smooth FAQ accordion (animated height) */
  if (!reduce && 'animate' in Element.prototype) $$('details').forEach(function (dt) {
    var sm = dt.querySelector('summary'); if (!sm) return;
    sm.addEventListener('click', function (e) {
      e.preventDefault(); if (dt._a) { dt._a.cancel(); }
      var opening = !dt.open, start = dt.offsetHeight; dt.style.overflow = 'hidden';
      if (opening) dt.open = true; else dt.classList.add('is-closing');
      var end = opening ? dt.scrollHeight : sm.offsetHeight;
      dt._a = dt.animate({ height: [start + 'px', end + 'px'] }, { duration: 420, easing: 'cubic-bezier(.22,1,.36,1)' });
      var done = function () { dt.style.overflow = ''; dt.classList.remove('is-closing'); if (!opening) dt.open = false; dt._a = null; };
      dt._a.onfinish = done; dt._a.oncancel = function () { dt.style.overflow = ''; dt._a = null; };
    });
  });
})();
