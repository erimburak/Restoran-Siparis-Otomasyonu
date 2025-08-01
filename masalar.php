<?php
session_start();
require 'vendor/autoload.php';

function setFlashMessage(string $message, string $type = 'is-success'): void { $_SESSION['flash_message'] = ['message' => $message, 'type' => $type]; }
function getFlashMessage(): ?array {
    if (isset($_SESSION['flash_message'])) { $flash = $_SESSION['flash_message']; unset($_SESSION['flash_message']); return $flash; }
    return null;
}

$pdo = new PDO("mysql:host=127.0.0.1;dbname=stok_yonetimi;charset=utf8mb4", 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

// --- YÖNLENDİRME MANTIĞI BURADA ---
$action = $_GET['action'] ?? 'liste';

if ($action === 'resetle') {
    $masaId = (int)$_GET['id'];
    if ($masaId > 0) {
        // Bu masaya ait tüm siparişleri bul
        $stmt = $pdo->prepare("SELECT id FROM siparisler WHERE masa_id = ?");
        $stmt->execute([$masaId]);
        $siparisler = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($siparisler) {
            // Veritabanımızda ON DELETE CASCADE olduğu için,
            // sadece ana siparişleri silmemiz, detaylarını da otomatik siler.
            $stmt_delete = $pdo->prepare("DELETE FROM siparisler WHERE masa_id = ?");
            $stmt_delete->execute([$masaId]);
        }
        setFlashMessage("Masa #{$masaId} başarıyla sıfırlandı. Tüm sipariş geçmişi silindi.", 'is-success');
    }
    header("Location: masalar.php");
    exit;
}
// ---------------------------------

// Ana listeleme sorgusunu güncelliyoruz
$masalar = $pdo->query("
    SELECT 
        m.id, 
        m.masa_adi, 
        m.qr_code_path,
        COUNT(s.id) as aktif_siparis_sayisi
    FROM masalar m
    LEFT JOIN siparisler s ON m.id = s.masa_id AND s.durum IN ('Yeni', 'Hazırlanıyor')
    GROUP BY m.id
    ORDER BY m.id ASC
")->fetchAll(PDO::FETCH_ASSOC);

$latte = new Latte\Engine;
$latte->setTempDirectory(__DIR__ . '/temp');
$latte->render('templates/masalar.latte', [
    'masalar' => $masalar,
    'flash' => getFlashMessage(),
    'aktifSayfa' => 'masalar'
]);