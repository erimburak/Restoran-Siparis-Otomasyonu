<?php
session_start();
require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;

function setFlashMessage(string $message, string $type = 'is-success'): void { $_SESSION['flash_message'] = ['message' => $message, 'type' => $type]; }

$pdo = new PDO("mysql:host=127.0.0.1;dbname=stok_yonetimi;charset=utf8mb4", 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

// Projenin çalıştığı alt klasörü (örn: /stok-uygulamasi) otomatik bulur
$projeKlasoru = dirname($_SERVER['PHP_SELF']); 

// --- DOĞRU ADRES BURASI ---
// QR kodlar müşteriyi localhost'taki doğrudan menu.php'ye yönlendirmeli
$baseUrl = "http://localhost" . $projeKlasoru . "/menu.php";
// -------------------------

if (!is_dir('qrcodes')) { mkdir('qrcodes'); }

$masalar = $pdo->query("SELECT * FROM masalar")->fetchAll();
foreach ($masalar as $masa) {
    // Sonuçta oluşacak URL: http://localhost/stok-uygulamasi/menu.php?masa=5
    $url = $baseUrl . "?masa=" . $masa['id']; 
    $dosyaYolu = 'qrcodes/masa_' . $masa['id'] . '.png';

    $qrCode = new QrCode($url, new Encoding('UTF-8'));
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    $result->saveToFile($dosyaYolu);

    $stmt = $pdo->prepare("UPDATE masalar SET qr_code_path = ? WHERE id = ?");
    $stmt->execute([$dosyaYolu, $masa['id']]);
}

setFlashMessage("Tüm masalar için QR kodları 'menu.php' adresi ile güncellendi.");
header("Location: masalar.php");
exit;