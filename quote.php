<?php
// 찐새집 견적 위저드 접수 — Hostinger 웹호스팅(PHP). 접수 JSON을 public_html 밖 quotes/ 에 저장하고 메일 알림.
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: https://jjinsaezip.com');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false]); exit; }
$raw = file_get_contents('php://input');
if (strlen($raw) > 6*1024*1024) { http_response_code(413); echo json_encode(['ok'=>false,'err'=>'too_large']); exit; }
$d = json_decode($raw, true);
if (!is_array($d) || empty($d['contact'])) { http_response_code(400); echo json_encode(['ok'=>false,'err'=>'bad']); exit; }
$id = date('Ymd_His') . '_' . substr(bin2hex(random_bytes(3)),0,6);
$dir = dirname(__DIR__) . '/quotes';
if (!is_dir($dir)) { @mkdir($dir, 0700, true); }
$d['id'] = $id; $d['ip'] = $_SERVER['REMOTE_ADDR'] ?? ''; $d['ua'] = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 200); $d['at'] = date('c');
@file_put_contents("$dir/$id.json", json_encode($d, JSON_UNESCAPED_UNICODE));
// 헤르메스 웹훅(있으면 전달) — hermes_webhook.txt 에 URL 한 줄
$hook = @trim(@file_get_contents(dirname(__DIR__) . '/hermes_webhook.txt'));
if (!$hook) { $hook = 'http://187.53.142.145:8787/quote'; } // VPS 릴레이 → 회장 텔레그램 알림
if ($hook) { $ch = curl_init($hook); curl_setopt_array($ch, [CURLOPT_POST=>1, CURLOPT_POSTFIELDS=>json_encode($d, JSON_UNESCAPED_UNICODE), CURLOPT_HTTPHEADER=>['Content-Type: application/json'], CURLOPT_TIMEOUT=>12, CURLOPT_RETURNTRANSFER=>1]); @curl_exec($ch); curl_close($ch); }
// 메일 알림(사진 제외)
$sum = "새 견적 문의 [$id]\n유형: {$d['type']}\n평수: {$d['size']}\n높이: {$d['height']}\n오염: {$d['hazard']}\n희망일: {$d['when']}\n연락처: {$d['contact']}\n예상: {$d['estimate']}\n사진: " . count($d['photos'] ?? []) . "장\n";
@mail('honjobs@naver.com', "[찐새집] 견적 문의 $id", $sum, "From: noreply@jjinsaezip.com\r\nContent-Type: text/plain; charset=utf-8");
echo json_encode(['ok'=>true,'id'=>$id]);
