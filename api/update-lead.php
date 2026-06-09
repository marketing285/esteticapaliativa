<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$adminToken = getenv('ADMIN_TOKEN');
if (!$adminToken) { http_response_code(500); exit; }

$auth = $_SERVER['HTTP_AUTHORIZATION']
     ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
     ?? (function_exists('getallheaders') ? (getallheaders()['Authorization'] ?? '') : '')
     ?? '';

if ($auth !== 'Bearer ' . $adminToken) { http_response_code(403); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }

$body   = json_decode(file_get_contents('php://input'), true);
$action = $body['action'] ?? '';
$slug   = preg_replace('/[^a-z0-9\-]/', '', $body['slug'] ?? '');
$id     = $body['id'] ?? '';

$path = __DIR__ . '/../data/leads/' . $slug . '.json';
if (!file_exists($path)) {
    http_response_code(404);
    echo json_encode(['error' => 'Curso nao encontrado']);
    exit;
}

$fp = fopen($path, 'c+');
flock($fp, LOCK_EX);
$leads = json_decode(stream_get_contents($fp), true) ?? [];

if ($action === 'delete') {
    $leads = array_values(array_filter($leads, fn($l) => $l['id'] !== $id));
} elseif ($action === 'edit') {
    $data  = $body['data'] ?? [];
    $leads = array_map(function ($l) use ($id, $data) {
        if ($l['id'] === $id) {
            if (!empty($data['nome']))     $l['nome']     = trim($data['nome']);
            if (!empty($data['email']))    $l['email']    = trim($data['email']);
            if (!empty($data['whatsapp'])) $l['whatsapp'] = preg_replace('/\D/', '', $data['whatsapp']);
        }
        return $l;
    }, $leads);
} else {
    flock($fp, LOCK_UN);
    fclose($fp);
    http_response_code(400);
    echo json_encode(['error' => 'Acao invalida']);
    exit;
}

ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode(array_values($leads), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(['ok' => true]);
