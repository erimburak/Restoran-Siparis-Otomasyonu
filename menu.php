<?php
session_start();
ini_set('display_errors', 1); error_reporting(E_ALL);

require 'vendor/autoload.php';
require 'app/StokManager.php';
require 'app/MusteriSiparisManager.php'; // Yeni sipariş yöneticimizi dahil ediyoruz



// 1. Masa Numarasını Al
$masaId = (int)($_GET['masa'] ?? 0);
if ($masaId <= 0) { die("Hata: Geçersiz masa numarası."); }
$_SESSION['masa_id'] = $masaId;


// 2. Sepete Ekleme İsteğini İşle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['urun_id'])) {
    $urunId = (int)$_POST['urun_id'];
    $sepet = $_SESSION['sepet'] ?? [];
    $sepet[$urunId] = ['adet' => ($sepet[$urunId]['adet'] ?? 0) + 1];
    $_SESSION['sepet'] = $sepet;
    header("Location: menu.php?masa=" . $masaId); exit;
}

// 3. Veritabanı ve Yöneticileri Kur
$pdo = new PDO("mysql:host=127.0.0.1;dbname=stok_yonetimi;charset=utf8mb4", 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$stokManager = new StokManager($pdo);
$musteriSiparisManager = new MusteriSiparisManager($pdo);

// 4. Müşteriyi Tanı ve Siparişlerini Çek
$musteriKimlik = $_COOKIE['musteri_kimlik'] ?? null;
$musteriId = null;
$musteriSiparisleri = ['aktif' => [], 'gecmis' => []];

if ($musteriKimlik) {
    $stmt = $pdo->prepare("SELECT id FROM musteriler WHERE benzersiz_kimlik = ?");
    $stmt->execute([$musteriKimlik]);
    $musteri = $stmt->fetch();
    if ($musteri) {
        $musteriId = $musteri['id'];
        $musteriSiparisleri = $musteriSiparisManager->getMusteriSiparisleri($musteriId);
    }
}

// 5. Menü ve Sepet Verilerini Hazırla
$urunler = $stokManager->getUrunler();
$sepet = $_SESSION['sepet'] ?? [];
$sepetDetaylari = [];
$toplamFiyat = 0;

if (!empty($sepet)) {
    foreach ($sepet as $urunId => $sepetItem) {
        $urun = $stokManager->urunBul($urunId);
        if ($urun) {
            $urun['adet'] = $sepetItem['adet'];
            $sepetDetaylari[] = $urun;
            $toplamFiyat += $urun['fiyat'] * $sepetItem['adet'];
        }
    }
}

// 6. Tüm Verileri Şablona Gönder
$latte = new Latte\Engine;
$latte->setTempDirectory(__DIR__ . '/temp');
$latte->render('templates/menu.latte', [
    'urunler' => $urunler,
    'masaId' => $masaId,
    'sepet' => $sepetDetaylari,
    'toplamFiyat' => $toplamFiyat,
    'aktifSiparisler' => $musteriSiparisleri['aktif'],
    'gecmisSiparisler' => $musteriSiparisleri['gecmis'],
    'musteriVar' => (bool)$musteriId,
    'flash' => null
]);