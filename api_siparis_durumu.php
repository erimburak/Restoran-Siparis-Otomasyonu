<?php
error_reporting(0); // Hataları geçici olarak gizle
header('Content-Type: application/json');
require 'app/SiparisManager.php';

$pdo = new PDO("mysql:host=127.0.0.1;dbname=stok_yonetimi;charset=utf8mb4", 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

$siparisId = (int)($_GET['id'] ?? 0);
if ($siparisId > 0) {
    $stmt = $pdo->prepare("SELECT durum FROM siparisler WHERE id = ?");
    $stmt->execute([$siparisId]);
    $siparis = $stmt->fetch();
    echo json_encode($siparis ?: ['durum' => 'Bulunamadı']);
} else {
    echo json_encode(['durum' => 'Geçersiz ID']);
}