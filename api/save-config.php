<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$adminToken = getenv('ADMIN_TOKEN');
if (!$adminToken) {
    http_response_code(500);
    echo json_encode(['error' => 'ADMIN_TOKEN nao configurado no servidor']);
    exit;
}

$auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if ($auth !== 'Bearer ' . $adminToken) {
    http_response_code(403);
    echo json_encode(['error' => 'Nao autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo nao permitido']);
    exit;
}

$body = file_get_contents('php://input');
$config = json_decode($body, true);

if ($config === null) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON invalido']);
    exit;
}

$path = __DIR__ . '/../data/config.json';
$result = file_put_contents($path, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao salvar config']);
    exit;
}

echo json_encode(['ok' => true, 'savedAt' => date('Y-m-d H:i:s')]);
