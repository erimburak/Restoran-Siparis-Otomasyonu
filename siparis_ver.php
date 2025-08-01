<?php
session_start();

require 'vendor/autoload.php';
require 'app/StokManager.php';
$pdo = new PDO("mysql:host=127.0.0.1;dbname=stok_yonetimi;charset=utf8mb4", 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$stokManager = new StokManager($pdo);

// Session'dan sepeti ve masa numarasını al
$sepet = $_SESSION['sepet'] ?? [];
$masaId = $_SESSION['masa_id'] ?? 0;

// Eğer sepet boşsa veya masa belli değilse, işlemi baştan durdur
if (empty($sepet) || $masaId === 0) {
    header('Location: menu.php');
    exit;
}

// 1. Müşteriyi Tanıma veya Yeni Müşteri Oluşturma
$musteriKimlik = $_COOKIE['musteri_kimlik'] ?? null;
$musteriId = null;

if ($musteriKimlik) {
    // Eğer çerez varsa, veritabanından müşteri ID'sini bul
    $stmt = $pdo->prepare("SELECT id FROM musteriler WHERE benzersiz_kimlik = ?");
    $stmt->execute([$musteriKimlik]);
    $musteri = $stmt->fetch();
    if ($musteri) {
        $musteriId = $musteri['id'];
    }
}

if (!$musteriId) {
    // Eğer çerez yoksa veya geçersizse, yeni bir müşteri oluştur
    $yeniKimlik = bin2hex(random_bytes(16)); // Güvenli, rastgele bir kimlik
    $stmt = $pdo->prepare("INSERT INTO musteriler (benzersiz_kimlik) VALUES (?)");
    $stmt->execute([$yeniKimlik]);
    $musteriId = $pdo->lastInsertId();
    // Bu kimliği müşterinin tarayıcısına 1 yıl geçerli bir çerez olarak bırak
    setcookie('musteri_kimlik', $yeniKimlik, time() + (86400 * 365), "/"); 
}

// 2. Siparişi veritabanına kaydet
try {
    $pdo->beginTransaction();

    // Ana sipariş kaydını `siparisler` tablosuna oluştur (musteri_id ile birlikte)
    $stmt = $pdo->prepare("INSERT INTO siparisler (masa_id, musteri_id, durum) VALUES (?, ?, 'Yeni')");
    $stmt->execute([$masaId, $musteriId]);
    $siparisId = $pdo->lastInsertId();

    // Sepetteki her ürünü `siparis_detaylari` tablosuna ekle
    $stmt_detay = $pdo->prepare("INSERT INTO siparis_detaylari (siparis_id, urun_id, adet, fiyat) VALUES (?, ?, ?, ?)");
    foreach ($sepet as $urunId => $sepetItem) {
        $urun = $stokManager->urunBul($urunId);
        if ($urun) {
            $stmt_detay->execute([$siparisId, $urunId, $sepetItem['adet'], $urun['fiyat']]);
            // STOK DÜŞÜRME KODU BURADAN KALDIRILDI
        }
    }
    
    $pdo->commit(); // Tüm işlemler başarılıysa veritabanına onayla

    // 3. İşlem sonrası session'ları temizle ve yönlendir
    unset($_SESSION['sepet']);
    unset($_SESSION['masa_id']);
    $_SESSION['son_siparis_id'] = $siparisId;
    
    // İstediğin gibi 'tesekkurler.php'ye yönlendiriyoruz
    header('Location: tesekkurler.php');
    exit;

} catch (Exception $e) {
    $pdo->rollBack(); // Hata oluşursa tüm işlemleri geri al
    die("Sipariş oluşturulurken bir veritabanı hatası oluştu: " . $e->getMessage());
}