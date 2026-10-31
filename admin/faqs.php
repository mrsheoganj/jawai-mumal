<?php
require __DIR__ . '/_init.php'; require __DIR__ . '/_crud.php';
admin_crud([
  'table' => 'jm_faqs', 'active' => 'faqs', 'title' => 'FAQs', 'singular' => 'question', 'order' => 'sort_order, id', 'required' => 'question',
  'fields' => ['question' => ['Question', 'text', '', null, 9], 'sort_order' => ['Order', 'number', '', null, 3], 'answer' => ['Answer', 'textarea']],
  'defaults' => ['sort_order' => 50],
  'list' => [['Question', fn($r) => '<strong>' . e($r['question']) . '</strong>'], ['Order', 'sort_order']],
]);
