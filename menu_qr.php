<?php
require 'vendor/autoload.php';
require 'app/StokManager.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;

$host = '127.0.0.1'; $db = 'stok_yonetimi'; $user = 'root'; $pass = ''; $charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$stokManager = new StokManager($pdo);

$urunler = $stokManager->getUrunler();
$menuMetni = "--- URUN MENUSU ---\n";

foreach ($urunler as $urun) {
    $menuMetni .= "{$urun['ad']}: {$urun['fiyat']} TL\n";
}
$menuMetni .= "\nOluşturulma Tarihi: " . date('d-m-Y H:i:s');

$qrCode = new QrCode($menuMetni, new Encoding('UTF-8'));
$writer = new PngWriter();
$result = $writer->write($qrCode);

header('Content-Type: '.$result->getMimeType());
echo $result->getString();