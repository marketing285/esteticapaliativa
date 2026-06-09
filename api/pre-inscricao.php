<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }

$body = json_decode(file_get_contents('php://input'), true);
if (!$body) { http_response_code(400); echo json_encode(['error' => 'JSON invalido']); exit; }

$nome     = trim($body['nome']     ?? '');
$email    = trim($body['email']    ?? '');
$whatsapp = preg_replace('/\D/', '', $body['whatsapp'] ?? '');
$cursoNome = trim($body['curso']   ?? '');

if (!$nome || !$email || !$whatsapp || !$cursoNome) {
    http_response_code(400);
    echo json_encode(['error' => 'Preencha todos os campos']);
    exit;
}

function toSlug($str) {
    $map = ['à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'a',
            'è'=>'e','é'=>'e','ê'=>'e','ë'=>'e',
            'ì'=>'i','í'=>'i','î'=>'i','ï'=>'i',
            'ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'o',
            'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u',
            'ç'=>'c','ñ'=>'n'];
    $str = mb_strtolower($str, 'UTF-8');
    $str = strtr($str, $map);
    $str = preg_replace('/[^a-z0-9]+/', '-', $str);
    return trim($str, '-');
}

$slug     = toSlug($cursoNome);
$leadsDir = __DIR__ . '/../data/leads';
if (!is_dir($leadsDir)) { mkdir($leadsDir, 0755, true); }
$path = $leadsDir . '/' . $slug . '.json';

$lead = [
    'id'       => uniqid('', true),
    'nome'     => $nome,
    'email'    => $email,
    'whatsapp' => $whatsapp,
    'curso'    => $cursoNome,
    'slug'     => $slug,
    'criadoEm' => date('c'),
];

$fp = fopen($path, 'c+');
flock($fp, LOCK_EX);
$content = stream_get_contents($fp);
$leads   = ($content !== '' && $content !== false) ? (json_decode($content, true) ?? []) : [];
$leads[] = $lead;
ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode(array_values($leads), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(['ok' => true, 'id' => $lead['id']]);
