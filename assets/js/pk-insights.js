/* Insights dashboard: fetches /pk-insights?api=data|live and draws the charts as plain SVG/HTML (no libraries). */
(function () {
  var app = document.getElementById('app'), API = app.getAttribute('data-api'), range = +app.getAttribute('data-range') || 7;
  try { var saved = +localStorage.getItem('pkRange'); if ([1, 7, 30, 90].indexOf(saved) > -1 && !/range=/.test(location.search)) range = saved; } catch (e) {}
  var data = null, redraw = [];
  var S = ['var(--s1)', 'var(--s2)', 'var(--s3)', 'var(--s4)'];
  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function num(x) { return (+x || 0).toLocaleString('en-IN'); }
  function pc(a, b) { return b ? Math.round(a / b * 1000) / 10 : 0; }
  function nice(max) { if (max <= 4) return 4; var p = Math.pow(10, Math.floor(Math.log10(max))), f = max / p, n = f <= 1 ? 1 : f <= 2 ? 2 : f <= 5 ? 5 : 10; var v = n * p; return v < max ? v * 2 : v; }
  function get(u) { return fetch(u, { credentials: 'same-origin', cache: 'no-store' }).then(function (r) { if (r.status === 401) { location.reload(); throw 0; } return r.json(); }); }
  function ago(t, now) { var s = Math.max(0, (now || Date.now() / 1000) - t); return s < 60 ? Math.floor(s) + 's ago' : s < 3600 ? Math.floor(s / 60) + ' min ago' : Math.floor(s / 3600) + ' h ago'; }
  function delta(cur, prev, goodUp) {
    if (!prev && !cur) return '<span>no data yet</span>'; if (!prev) return '<span>new this period</span>';
    var d = Math.round((cur - prev) / prev * 100), up = d >= 0, good = goodUp ? up : !up; if (d === 0) return '<span>same as before</span>';
    return '<span class="' + (good ? 'up' : 'down') + '">' + (up ? '▲ ' : '▼ ') + Math.abs(d) + '%</span> <span>vs previous ' + (data.days === 1 ? 'day' : data.days + ' days') + '</span>';
  }
  function card(title, sub, body, extra) { return '<section class="pk-card"><div class="pk-h"><h2>' + esc(title) + '</h2>' + (sub ? '<small>' + esc(sub) + '</small>' : '') + '</div>' + body + (extra || '') + '</section>'; }
  function table(head, rows) { return '<details class="viewtbl"><summary>View as table</summary><table class="tbl"><thead><tr>' + head.map(function (h) { return '<th>' + esc(h) + '</th>'; }).join('') + '</tr></thead><tbody>' + rows.map(function (r) { return '<tr>' + r.map(function (c, i) { return '<td class="' + (i ? 'num' : '') + '">' + esc(c) + '</td>'; }).join('') + '</tr>'; }).join('') + '</tbody></table></details>'; }

  // ---------- chart primitives
  function tipFor(el) { var t = el.querySelector('.tip'); if (!t) { t = document.createElement('div'); t.className = 'tip'; t.hidden = true; el.appendChild(t); } return t; }
  function showTip(el, html, x, y) { var t = tipFor(el); t.innerHTML = html; t.hidden = false; var w = el.clientWidth; t.style.left = Math.min(Math.max(x, 60), w - 60) + 'px'; t.style.top = Math.max(y, 28) + 'px'; }
  function hideTip(el) { var t = el.querySelector('.tip'); if (t) t.hidden = true; }
  function axisY(m, iw, ih, max, w) {
    var out = '', k; for (k = 0; k <= 4; k++) { var v = max / 4 * k, y = m.t + ih - ih * k / 4; out += '<line class="gridline" x1="' + m.l + '" x2="' + (w - m.r) + '" y1="' + y + '" y2="' + y + '"/><text x="' + (m.l - 8) + '" y="' + (y + 4) + '" text-anchor="end" class="ax-t">' + (v >= 1000 ? (v / 1000) + 'K' : v) + '</text>'; } return out;
  }
  function line(el, o) {
    var w = Math.max(280, el.clientWidth), h = o.h || 250, m = { l: 38, r: 14, t: 12, b: 28 }, iw = w - m.l - m.r, ih = h - m.t - m.b, n = o.labels.length, all = [];
    o.series.forEach(function (s) { all = all.concat(s.vals); }); var max = nice(Math.max.apply(null, all.concat([1])));
    var x = function (i) { return m.l + (n === 1 ? iw / 2 : i * iw / (n - 1)); }, y = function (v) { return m.t + ih - v / max * ih; };
    var svg = '<svg viewBox="0 0 ' + w + ' ' + h + '" width="' + w + '" height="' + h + '" role="img" aria-label="' + esc(o.aria) + '"><g class="ax">' + axisY(m, iw, ih, max, w).replace(/ax-t/g, 'ax-t') + '</g>';
    var step = Math.ceil(n / Math.max(2, Math.floor(iw / 84)));
    o.labels.forEach(function (l, i) { if (i % step === 0 && (n - 1 - i >= step * .6 || i === n - 1 || n - 1 - i === 0)) svg += '<text x="' + x(i) + '" y="' + (h - 8) + '" text-anchor="' + (i === 0 ? 'start' : i === n - 1 ? 'end' : 'middle') + '" class="ax-t">' + esc(o.short ? o.short(l) : l) + '</text>'; });
    o.series.forEach(function (s, si) {
      var pts = s.vals.map(function (v, i) { return x(i) + ',' + y(v); });
      if (si === 0) svg += '<path d="M' + x(0) + ',' + y(0) + ' L' + pts.join(' L') + ' L' + x(n - 1) + ',' + y(0) + ' Z" style="fill:' + S[si] + ';opacity:.1"/>';
      svg += '<polyline points="' + pts.join(' ') + '" fill="none" style="stroke:' + S[si] + '" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>';
      svg += '<circle cx="' + x(n - 1) + '" cy="' + y(s.vals[n - 1]) + '" r="4" style="fill:' + S[si] + ';stroke:var(--card)" stroke-width="2"/>';
    });
    svg += '<line id="xh" y1="' + m.t + '" y2="' + (m.t + ih) + '" class="axisline" style="opacity:0"/><g id="dots"></g><rect x="' + m.l + '" y="' + m.t + '" width="' + iw + '" height="' + ih + '" fill="transparent" id="hit"/></svg>';
    el.innerHTML = svg; var hit = el.querySelector('#hit'), xh = el.querySelector('#xh'), dots = el.querySelector('#dots');
    function move(ev) {
      var r = hit.getBoundingClientRect(), px = (ev.touches ? ev.touches[0].clientX : ev.clientX) - r.left, i = Math.round(px / (r.width || 1) * (n - 1)); i = Math.max(0, Math.min(n - 1, i));
      xh.setAttribute('x1', x(i)); xh.setAttribute('x2', x(i)); xh.style.opacity = 1;
      dots.innerHTML = o.series.map(function (s, si) { return '<circle cx="' + x(i) + '" cy="' + y(s.vals[i]) + '" r="4.5" style="fill:' + S[si] + ';stroke:var(--card)" stroke-width="2"/>'; }).join('');
      showTip(el, '<b>' + esc(o.labels[i]) + '</b>' + o.series.map(function (s, si) { return '<span><i style="display:inline-block;width:8px;height:8px;border-radius:2px;margin-right:6px;background:' + S[si] + '"></i>' + esc(s.name) + ': <b style="display:inline">' + num(s.vals[i]) + '</b></span><br>'; }).join(''), x(i) / w * el.clientWidth, y(Math.max.apply(null, o.series.map(function (s) { return s.vals[i]; }))));
    }
    hit.addEventListener('mousemove', move); hit.addEventListener('touchstart', move, { passive: true }); hit.addEventListener('touchmove', move, { passive: true });
    hit.addEventListener('mouseleave', function () { xh.style.opacity = 0; dots.innerHTML = ''; hideTip(el); });
  }
  function cols(el, o) {
    var w = Math.max(240, el.clientWidth), h = o.h || 170, m = { l: o.noY ? 8 : 34, r: 8, t: 10, b: 26 }, iw = w - m.l - m.r, ih = h - m.t - m.b, n = o.vals.length, max = nice(Math.max.apply(null, o.vals.concat([1]))), band = iw / n, bw = Math.min(24, band * .7);
    var svg = '<svg viewBox="0 0 ' + w + ' ' + h + '" width="' + w + '" height="' + h + '" role="img" aria-label="' + esc(o.aria) + '">' + (o.noY ? '<line class="gridline" x1="' + m.l + '" x2="' + (w - m.r) + '" y1="' + (m.t + ih) + '" y2="' + (m.t + ih) + '"/>' : '<g class="ax">' + axisY(m, iw, ih, max, w) + '</g>');
    var step = o.every || Math.ceil(n / Math.max(2, Math.floor(iw / 52)));
    o.vals.forEach(function (v, i) {
      var cx = m.l + band * i + band / 2, bh = v / max * ih, yy = m.t + ih - bh, r = Math.min(4, bw / 2, bh);
      if (v > 0) svg += '<path d="M' + (cx - bw / 2) + ',' + (yy + r) + ' Q' + (cx - bw / 2) + ',' + yy + ' ' + (cx - bw / 2 + r) + ',' + yy + ' L' + (cx + bw / 2 - r) + ',' + yy + ' Q' + (cx + bw / 2) + ',' + yy + ' ' + (cx + bw / 2) + ',' + (yy + r) + ' L' + (cx + bw / 2) + ',' + (m.t + ih) + ' L' + (cx - bw / 2) + ',' + (m.t + ih) + ' Z" style="fill:' + (o.color || S[0]) + '"/>';
      if (i % step === 0 && (o.every || n - 1 - i >= step * .6 || n - 1 - i === 0)) svg += '<text x="' + cx + '" y="' + (h - 8) + '" text-anchor="middle" class="ax-t">' + esc(o.labels[i]) + '</text>';
      svg += '<rect x="' + (m.l + band * i) + '" y="' + m.t + '" width="' + band + '" height="' + ih + '" fill="transparent" data-i="' + i + '"/>';
    });
    el.innerHTML = svg + '</svg>';
    Array.prototype.forEach.call(el.querySelectorAll('rect[data-i]'), function (r) {
      r.addEventListener('mouseenter', function () { var i = +r.getAttribute('data-i'); showTip(el, '<b>' + esc(o.tipLabel ? o.tipLabel(i) : o.labels[i]) + '</b>' + num(o.vals[i]) + ' ' + esc(o.unit || ''), (m.l + band * i + band / 2) / w * el.clientWidth, m.t + ih - o.vals[i] / max * ih); });
      r.addEventListener('mouseleave', function () { hideTip(el); });
    });
  }
  function hbars(rows, fmtLabel) {
    if (!rows.length) return '<p class="muted">No data yet.</p>'; var max = Math.max.apply(null, rows.map(function (r) { return r.n; }).concat([1]));
    return '<div class="hb">' + rows.map(function (r) { return '<div class="hb__row" title="' + esc(r.l) + ': ' + num(r.n) + '"><span>' + esc(fmtLabel ? fmtLabel(r.l) : r.l) + '</span><div class="hb__track"><div class="hb__fill" style="width:' + Math.max(2, r.n / max * 100) + '%"></div></div><b>' + num(r.n) + '</b></div>'; }).join('') + '</div>';
  }
  function seg(rows, colors) {
    var tot = rows.reduce(function (a, r) { return a + r.n; }, 0); if (!tot) return '<p class="muted">No data yet.</p>';
    var bar = '<div class="seg" role="img" aria-label="' + esc(rows.map(function (r) { return r.l + ' ' + pc(r.n, tot) + '%'; }).join(', ')) + '">' + rows.map(function (r, i) { var p = r.n / tot * 100; return '<div style="width:' + p + '%;background:' + colors[i % colors.length] + '" title="' + esc(r.l) + ': ' + num(r.n) + ' (' + Math.round(p) + '%)">' + (p >= 14 ? Math.round(p) + '%' : '') + '</div>'; }).join('') + '</div>';
    return bar + '<div class="legend" style="margin-top:.6rem">' + rows.map(function (r, i) { return '<span><i style="background:' + colors[i % colors.length] + '"></i>' + esc(r.l) + '</span>'; }).join('') + '</div>';
  }
  function funnel(steps) {
    var top = steps[0].n || 1, cl = ['var(--f1)', 'var(--f2)', 'var(--f3)', 'var(--f4)'];
    return '<div class="funnel">' + steps.map(function (s, i) { var drop = i ? (steps[i - 1].n ? pc(s.n, steps[i - 1].n) + '% of previous step' : '—') : 'all visits'; return '<div class="funnel__row"><div class="funnel__top"><span>' + esc(s.l) + '</span><span><b style="color:var(--ink)">' + num(s.n) + '</b> · ' + drop + '</span></div><div class="funnel__bar" style="width:' + Math.max(1.5, s.n / top * 100) + '%;background:' + cl[i] + '"></div></div>'; }).join('') + '</div>';
  }
  function liveBars(el, vals) { cols(el, { vals: vals, labels: vals.map(function (v, i) { return i === 29 ? 'now' : (29 - i) + 'm'; }), every: 5, h: 110, noY: true, aria: 'Page views per minute, last 30 minutes', unit: 'page views', tipLabel: function (i) { return (29 - i) === 0 ? 'This minute' : (29 - i) + ' min ago'; } }); }

  // ---------- page
  function kpis(d) {
    var c = d.cur, p = d.prev, ppv = c.sessions ? (c.pageviews / c.sessions).toFixed(1) : '0.0', pppv = p.sessions ? (p.pageviews / p.sessions).toFixed(1) : '0.0';
    var br = pc(c.bounce, c.sessions), pbr = pc(p.bounce, p.sessions), cv = pc(c.requests, c.visitors), pcv = pc(p.requests, p.visitors);
    var t = [['Visitors', num(c.visitors), delta(c.visitors, p.visitors, 1), 'People who opened the site (each person once per day)'], ['Page views', num(c.pageviews), delta(c.pageviews, p.pageviews, 1)], ['Pages per visit', ppv, delta(+ppv, +pppv, 1)],
      ['Left after one page', br + '%', delta(br, pbr, 0), 'Visits that viewed a single page'], ['Booking requests', num(c.requests), delta(c.requests, p.requests, 1)], ['Visitors who asked', cv + '%', delta(cv, pcv, 1), 'Requests ÷ visitors'],
      ['WhatsApp taps', num(c.wa), delta(c.wa, p.wa, 1)], ['Call taps', num(c.call), delta(c.call, p.call, 1)]];
    return '<div class="pk-grid g-kpi">' + t.map(function (k) { return '<div class="pk-card kpi" title="' + esc(k[3] || '') + '"><small>' + esc(k[0]) + '</small><b>' + k[1] + '</b><span>' + k[2] + '</span></div>'; }).join('') + '</div>';
  }
  function build() {
    var d = data, tabs = [[1, 'Today'], [7, '7 days'], [30, '30 days'], [90, '90 days']];
    var h = '<div class="toolbar"><div class="tabs" role="tablist">' + tabs.map(function (t) { return '<a href="?range=' + t[0] + '" data-r="' + t[0] + '" class="' + (range === t[0] ? 'is-on' : '') + '">' + t[1] + '</a>'; }).join('') + '</div><small>' + esc(d.from) + (d.days > 1 ? ' – ' + esc(d.to) : '') + (d.since ? ' · counting since ' + esc(d.since) : ' · no visits recorded yet') + '</small></div>';
    h += '<div class="sechead">Right now</div><div class="pk-grid g-main"><section class="pk-card" id="liveCard"><div class="pk-h"><h2>Live visitors</h2><small>last 5 minutes · updates every few seconds</small></div><div style="display:flex;gap:1.4rem;align-items:flex-end;flex-wrap:wrap"><div><div class="big" id="onl">–</div><div class="muted">on the website now</div></div><div class="muted" id="todaySo" style="margin-left:auto;text-align:right"></div></div><div class="chart" id="liveChart" style="margin-top:.8rem"></div><div class="mini-note">Page views per minute, last 30 minutes</div><h3 style="font-size:.78rem;color:var(--mut);margin:1rem 0 .3rem;font-family:inherit;text-transform:uppercase;letter-spacing:.07em">Pages being viewed</h3><div id="livePages"></div></section>'
      + '<section class="pk-card"><div class="pk-h"><h2>Live activity</h2><small>newest first</small></div><ul class="feed" id="feed"></ul></section></div>';
    h += '<div class="sechead">Overview · ' + (d.days === 1 ? 'today' : 'last ' + d.days + ' days') + '</div>' + kpis(d);
    h += '<div style="margin-top:1rem">' + card('Visitors and page views', d.days === 1 ? 'by hour' : 'by day', '<div class="legend"><span><i class="ln" style="background:var(--s1)"></i>Visitors</span><span><i class="ln" style="background:var(--s2)"></i>Page views</span></div><div class="chart" id="trend"></div>', table(['Period', 'Visitors', 'Page views'].concat(d.days === 1 ? [] : ['Requests']), d.series.map(function (s) { return d.days === 1 ? [s.l, s.v, s.pv] : [s.l, s.v, s.pv, s.b]; }))) + '</div>';
    if (d.days > 1) h += '<div style="margin-top:1rem">' + card('Booking requests', 'received per day', '<div class="chart" id="reqs"></div>') + '</div>';
    h += '<div class="pk-grid g2" style="margin-top:1rem">' + card('When people visit', 'page views by hour of day (India time)', '<div class="chart" id="hours"></div>') + (d.days > 1 ? card('Busiest days of the week', 'visitors', '<div class="chart" id="wdays"></div>') : card('Top pages today', '', hbars(d.pages.slice(0, 8).map(function (p) { return { l: p.l, n: p.pv }; })))) + '</div>';
    h += '<div class="sechead">Where visitors come from</div><div class="pk-grid g2">' + card('Traffic sources', 'how visitors arrived', hbars(d.sources), table(['Source', 'Visits'], d.sources.map(function (r) { return [r.l, r.n]; }))) + card('Websites that sent visitors', 'top referring sites', hbars(d.refs.map(function (r) { return { l: r.l, n: r.n }; })), '<p class="mini-note">Google visits: <b style="color:var(--ink)">' + num(d.google) + '</b> in this period.</p>') + '</div>';
    h += '<div class="sechead">What they look at</div><div class="pk-grid g2">' + card('Most viewed pages', 'page views', '<table class="tbl"><thead><tr><th>Page</th><th>Visitors</th><th>Views</th></tr></thead><tbody>' + (d.pages.length ? d.pages.map(function (p) { return '<tr><td>' + esc(p.l) + '</td><td class="num">' + num(p.v) + '</td><td class="num"><b>' + num(p.pv) + '</b></td></tr>'; }).join('') : '<tr><td colspan="3" class="muted">No data yet.</td></tr>') + '</tbody></table>')
      + card('First page seen', 'where visits start', hbars(d.entry)) + '</div>';
    h += '<div class="sechead">Who they are</div><div class="pk-grid g3">' + card('Devices', '', seg(d.devices, S), table(['Device', 'Visits'], d.devices.map(function (r) { return [r.l, r.n]; }))) + card('Countries', 'from browser time zone', hbars(d.countries)) + card('Browsers & systems', '', '<div class="legend" style="margin:0 0 .3rem"><b>Browsers</b></div>' + hbars(d.browsers.slice(0, 5)) + '<div class="legend" style="margin:.9rem 0 .3rem"><b>Operating systems</b></div>' + hbars(d.os.slice(0, 5))) + '</div>';
    h += '<div class="sechead">Bookings & enquiries</div><div class="pk-grid g2">' + card('From visit to request', 'conversion funnel', funnel(d.funnel), '<p class="mini-note">Visitors are counted per browser session.</p>')
      + card('Requests in this period', '', (d.byType.length ? hbars(d.byType) + '<div style="margin-top:1rem">' + seg(Object.keys(d.byStatus).map(function (k) { return { l: k, n: d.byStatus[k] }; }), Object.keys(d.byStatus).map(function (k) { return { New: 'var(--info)', Contacted: 'var(--warn)', Confirmed: 'var(--ok)', Completed: 'var(--axis)', Cancelled: 'var(--bad)' }[k] || 'var(--axis)'; })) + '</div>' : '<p class="muted">No requests in this period yet.</p>'), '<h3 style="font-size:.78rem;color:var(--mut);margin:1rem 0 .3rem;font-family:inherit;text-transform:uppercase;letter-spacing:.07em">Latest requests</h3>' + (d.latest.length ? '<table class="tbl"><tbody>' + d.latest.map(function (r) { return '<tr><td><b>' + esc(r.name) + '</b><br><span class="muted">' + esc(r.type) + (r.item ? ' · ' + esc(r.item) : '') + '</span></td><td>' + esc(r.status) + '</td></tr>'; }).join('') + '</tbody></table>' : '<p class="muted">None yet.</p>') + '<p class="mini-note"><a href="' + esc(API.replace('pk-insights', 'pk-crm')) + '">Open the CRM →</a></p>') + '</div>';
    h += '<div class="sechead">Search engines & content</div><div class="pk-grid g2">' + card('Search engine visits', 'crawlers that loaded your pages', (d.bots.length ? '<table class="tbl"><thead><tr><th>Crawler</th><th>Last visit</th><th>Pages</th></tr></thead><tbody>' + d.bots.map(function (b) { return '<tr><td>' + esc(b.l) + '</td><td>' + esc(b.last) + '</td><td class="num"><b>' + num(b.n) + '</b></td></tr>'; }).join('') + '</tbody></table>' : '<p class="muted">No search-engine crawl recorded yet. When Googlebot or Bingbot visits, it appears here.</p>'),
      (d.botFeed.length ? '<h3 style="font-size:.78rem;color:var(--mut);margin:1rem 0 .3rem;font-family:inherit;text-transform:uppercase;letter-spacing:.07em">Recent crawls</h3><ul class="feed">' + d.botFeed.map(function (b) { return '<li><time>' + esc(b.at) + '</time><span><b>' + esc(b.bot) + '</b> read ' + esc(b.l) + '</span></li>'; }).join('') + '</ul>' : '')) + card('Journal articles', 'most read', hbars(d.blog)) + '</div>';
    h += '<p class="note">Privacy-friendly statistics: no cookies are used and no IP address is stored. A visitor is a one-way code that changes every day, so the same person on two days counts as two visitors. People who browse with "Do Not Track" on, the hotel team when signed in, and bots are not counted as visitors. Server time ' + esc(d.server) + '.</p>';
    app.innerHTML = h;
    Array.prototype.forEach.call(app.querySelectorAll('.tabs a'), function (a) { a.addEventListener('click', function (e) { e.preventDefault(); range = +a.getAttribute('data-r'); try { localStorage.setItem('pkRange', range); } catch (x) {} load(); }); });
    paint(); liveRender(window.__live);
  }
  function paint() {
    var d = data, el;
    if ((el = document.getElementById('trend'))) line(el, { labels: d.series.map(function (s) { return s.l; }), short: d.days === 1 ? null : function (l) { return l.replace(/^\w+ /, ''); }, series: [{ name: 'Visitors', vals: d.series.map(function (s) { return s.v; }) }, { name: 'Page views', vals: d.series.map(function (s) { return s.pv; }) }], aria: 'Visitors and page views over time' });
    if ((el = document.getElementById('reqs'))) cols(el, { vals: d.series.map(function (s) { return s.b || 0; }), labels: d.series.map(function (s) { return s.l.replace(/^\w+ /, ''); }), tipLabel: function (i) { return d.series[i].l; }, unit: 'requests', color: S[1], h: 150, aria: 'Booking requests per day' });
    if ((el = document.getElementById('hours'))) cols(el, { vals: d.hours, labels: d.hours.map(function (v, i) { return i + 'h'; }), tipLabel: function (i) { return (i < 10 ? '0' : '') + i + ':00 – ' + (i < 10 ? '0' : '') + i + ':59'; }, unit: 'page views', aria: 'Page views by hour of day' });
    if ((el = document.getElementById('wdays'))) cols(el, { vals: d.weekdays, labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], unit: 'visitors', aria: 'Visitors by weekday' });
  }
  function liveRender(L) {
    if (!L) return; window.__live = L; var el;
    if ((el = document.getElementById('onl'))) el.textContent = num(L.online);
    document.getElementById('liveTxt').textContent = L.online + ' online';
    if ((el = document.getElementById('todaySo'))) el.innerHTML = '<b style="color:var(--ink)">' + num(L.today.visitors) + '</b> visitors · <b style="color:var(--ink)">' + num(L.today.pageviews) + '</b> page views<br><b style="color:var(--ink)">' + num(L.today.requests) + '</b> requests · <b style="color:var(--ink)">' + num(L.today.wa + L.today.call) + '</b> WhatsApp / call taps today';
    if ((el = document.getElementById('liveChart'))) liveBars(el, L.perMinute);
    if ((el = document.getElementById('livePages'))) el.innerHTML = L.pages.length ? hbars(L.pages) : '<p class="muted">Nobody is browsing at this moment.</p>';
    if ((el = document.getElementById('feed'))) el.innerHTML = L.feed.length ? L.feed.map(function (f) { return '<li><time>' + ago(f.t, L.now) + '</time><span>Someone ' + esc(f.a) + ' <span class="p">' + esc(f.l.length > 44 ? f.l.slice(0, 43) + '…' : f.l) + '</span></span><span class="src">' + esc([f.dev, f.cc, f.src].filter(Boolean).join(' · ')) + '</span></li>'; }).join('') : '<li class="muted">No visits recorded yet.</li>';
  }
  function load() { get(API + '?api=data&range=' + range).then(function (d) { data = d; build(); }); }
  function tick() { if (document.hidden) return; get(API + '?api=live').then(liveRender).catch(function () {}); }
  load(); get(API + '?api=live').then(liveRender); setInterval(tick, 8000); setInterval(function () { if (!document.hidden) get(API + '?api=data&range=' + range).then(function (d) { data = d; paint(); }); }, 60000);
  var rt; window.addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(function () { if (data) { paint(); liveRender(window.__live); } }, 150); });
})();
