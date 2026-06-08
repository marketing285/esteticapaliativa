<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

// Token de autenticação do admin
define('ADMIN_TOKEN', 'gv-estetica-2026');

$auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if ($auth !== 'Bearer ' . ADMIN_TOKEN) {
    http_response_code(403);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$body = file_get_contents('php://input');
$config = json_decode($body, true);

if ($config === null) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON inválido']);
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
