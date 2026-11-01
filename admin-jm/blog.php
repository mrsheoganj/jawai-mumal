<?php
require __DIR__ . '/_init.php'; require __DIR__ . '/_crud.php';
admin_crud([
  'table' => 'jm_blog', 'active' => 'blog', 'title' => 'Journal (SEO articles)', 'singular' => 'article', 'plural' => 'articles', 'thumb' => 'image', 'order' => 'published_at DESC, id DESC',
  'required' => 'title',
  'intro' => 'Not shown in the menu. Published articles are in the sitemap and open at /blog and /blog/your-slug',
  'fields' => [
    'title' => ['Article title (H1)', 'text', 'Put the main phrase people search for near the start.', null, 8], 'status' => ['Visibility', 'select', 'Draft = not public.', ['published' => 'Published', 'draft' => 'Draft'], 4],
    'slug' => ['Web address (slug)', 'text', 'Lowercase words with hyphens, e.g. best-time-to-visit-jawai. Changing it later changes the URL.', null, 6], 'category' => ['Category', 'text', 'e.g. Wildlife, Planning, Food', null, 3],
    'published_at' => ['Publish date & time', 'text', 'YYYY-MM-DD HH:MM:SS. A future date keeps it hidden until then.', null, 3],
    'meta_title' => ['Search title (SEO title)', 'text', 'Aim for 50–60 characters. Leave empty to use the article title + site name.', null, 12],
    'meta_description' => ['Search description (SEO)', 'textarea', 'Aim for 120–160 characters that make people want to click.'],
    'keywords' => ['Keywords', 'text', 'Comma separated phrases.', null, 12],
    'excerpt' => ['Short summary', 'textarea', 'Shown on the journal list and under the title.'],
    'body' => ['Article body (HTML)', 'textarea', 'Use <h2>, <h3>, <p>, <ul>, <ol>, <strong>. Internal links: {{rooms}} {{safari}} {{restaurant}} {{jawai}} {{contact}} {{book?type=safari}} {{blog/other-slug}}', null, 12, ['max' => 200000]],
    'image' => ['Cover photo', 'image'],
  ],
  'defaults' => ['status' => 'published', 'category' => 'Planning', 'published_at' => date('Y-m-d H:i:s')],
  'before_save' => function (array $v, int $id): array {
      $s = slugify($v['slug'] !== '' ? $v['slug'] : $v['title']); if ($s === '') $s = 'article'; $base = $s; $n = 2;
      while (val('SELECT COUNT(*) FROM jm_blog WHERE slug = ? AND id <> ?', [$s, $id])) $s = $base . '-' . $n++;
      $v['slug'] = $s;
      if (!$v['published_at'] || !strtotime($v['published_at'])) $v['published_at'] = date('Y-m-d H:i:s'); else $v['published_at'] = date('Y-m-d H:i:s', strtotime($v['published_at']));
      return $v;
  },
  'list' => [['Article', fn($r) => '<strong>' . e($r['title']) . '</strong><br><span class="muted">/blog/' . e($r['slug']) . '</span>'], ['Category', 'category'], ['Published', 'published_at'],
             ['Status', fn($r) => $r['status'] === 'published' ? pill_status('Confirmed') : pill_status('Cancelled')]],
  'row_actions' => fn($r) => '<a class="btn btn--line btn--sm" href="' . e(url('blog/' . $r['slug'])) . '" target="_blank" rel="noopener">' . icon('external', 16) . ' View</a>',
]);
