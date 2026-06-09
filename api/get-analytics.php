<?php
header('Content-Type: application/json; charset=utf-8');

$adminToken = getenv('ADMIN_TOKEN');
if (!$adminToken) { http_response_code(500); exit; }

$auth = $_SERVER['HTTP_AUTHORIZATION']
     ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
     ?? (function_exists('getallheaders') ? (getallheaders()['Authorization'] ?? '') : '')
     ?? '';

if ($auth !== 'Bearer ' . $adminToken) { http_response_code(403); exit; }

$path = __DIR__ . '/../data/analytics.json';
$data = file_exists($path)
    ? (json_decode(file_get_contents($path), true) ?? ['events' => []])
    : ['events' => []];

echo json_encode(['ok' => true, 'data' => $data]);
