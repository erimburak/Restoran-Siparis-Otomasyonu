<?php
session_start();
require 'vendor/autoload.php';

$siparisId = (int)($_GET['id'] ?? 0);
if ($siparisId <= 0) { die("Hata: Geçerli bir sipariş numarası belirtilmedi."); }

$pdo = new PDO("mysql:host=127.0.0.1;dbname=stok_yonetimi;charset=utf8mb4", 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

$stmt = $pdo->prepare("
    SELECT s.id, s.durum, s.siparis_zamani, m.masa_adi, m.id as masa_id 
    FROM siparisler s
    JOIN masalar m ON s.masa_id = m.id
    WHERE s.id = ?
");
$stmt->execute([$siparisId]);
$siparis = $stmt->fetch();

if (!$siparis) { die("Sipariş bulunamadı."); }

$latte = new Latte\Engine;
$latte->setTempDirectory(__DIR__ . '/temp');

// Müşteri arayüzü için son sipariş ID'sini de gönderelim
$latte->render('templates/siparis_takip.latte', [
    'siparis' => $siparis,
    'sonSiparisId' => $_SESSION['son_siparis_id'] ?? $siparisId,
    'flash' => null,
]);