<?php
header('Content-Type: application/json; charset=utf-8');

$adminToken = getenv('ADMIN_TOKEN');
if (!$adminToken) { http_response_code(500); exit; }

$auth = $_SERVER['HTTP_AUTHORIZATION']
     ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
     ?? (function_exists('getallheaders') ? (getallheaders()['Authorization'] ?? '') : '')
     ?? '';

if ($auth !== 'Bearer ' . $adminToken) { http_response_code(403); exit; }

$leadsDir = __DIR__ . '/../data/leads';
$slug = isset($_GET['slug']) ? preg_replace('/[^a-z0-9\-]/', '', $_GET['slug']) : null;

if ($slug) {
    $path  = $leadsDir . '/' . $slug . '.json';
    $leads = file_exists($path) ? (json_decode(file_get_contents($path), true) ?? []) : [];
    echo json_encode(['ok' => true, 'slug' => $slug, 'leads' => array_values($leads)]);
} else {
    $all = [];
    if (is_dir($leadsDir)) {
        foreach (glob($leadsDir . '/*.json') as $f) {
            $s = basename($f, '.json');
            $all[$s] = json_decode(file_get_contents($f), true) ?? [];
        }
    }
    echo json_encode(['ok' => true, 'leads' => $all]);
}
