<?php
// Endpoint público, sem auth — apenas incrementa contadores de eventos
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }

$event = trim($_POST['event'] ?? '');
if (!$event || strlen($event) > 120) { http_response_code(400); exit; }

// Sanitiza: apenas letras, números, underscore, hifen e espaço
$event = preg_replace('/[^a-zA-Z0-9_\-\sÀ-ú]/', '', $event);
if (!$event) { http_response_code(400); exit; }

$path = __DIR__ . '/../data/analytics.json';
$dir  = dirname($path);
if (!is_dir($dir)) mkdir($dir, 0755, true);

$fp = fopen($path, 'c+');
flock($fp, LOCK_EX);
$content = stream_get_contents($fp);
$data = ($content !== '' && $content !== false) ? (json_decode($content, true) ?? ['events' => []]) : ['events' => []];
$data['events'][$event] = ($data['events'][$event] ?? 0) + 1;
$data['updatedAt'] = date('c');
ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
flock($fp, LOCK_UN);
fclose($fp);

http_response_code(204);
