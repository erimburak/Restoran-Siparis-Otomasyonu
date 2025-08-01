<?php
session_start();
require 'vendor/autoload.php';
require 'app/StokManager.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;

function setFlashMessage(string $message, string $type = 'is-success'): void
{
    $_SESSION['flash_message'] = ['message' => $message, 'type' => $type];
}

$host = '127.0.0.1'; $db = 'stok_yonetimi'; $user = 'root'; $pass = ''; $charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$stokManager = new StokManager($pdo);

$id = (int)($_GET['id'] ?? 0);
$urun = $stokManager->urunBul($id);

if ($urun) {
    $qrMetni = "Ürün: {$urun['ad']}\nFiyat: {$urun['fiyat']} TL\nStok: {$urun['stok']}";
    $dosyaYolu = 'qrcodes/urun_' . $urun['id'] . '.png';

    $qrCode = new QrCode($qrMetni, new Encoding('UTF-8'));
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    
    if (!is_dir('qrcodes')) { mkdir('qrcodes'); }
    $result->saveToFile($dosyaYolu);

    $stokManager->setQrCodePath($urun['id'], $dosyaYolu);

    setFlashMessage("{$urun['ad']} için QR kodu oluşturuldu/güncellendi.", 'is-success');
    header('Location: blade.php'); // Yönlendirme blade.php'ye yapılıyor
    exit;
}