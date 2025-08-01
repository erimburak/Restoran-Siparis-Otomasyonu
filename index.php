<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Gerekli tüm sınıfları ve ortak fonksiyonları dahil et
require 'vendor/autoload.php';
require 'app/StokManager.php';
require 'app/View.php';

// --- FONKSİYONLARIN İÇİ DOLU OLARAK BURADA ---
function setFlashMessage(string $message, string $type = 'is-success'): void
{
    $_SESSION['flash_message'] = ['message' => $message, 'type' => $type];
}

function getFlashMessage(): ?array
{
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null; // Önemli: Mesaj yoksa null döndür
}

// Ortak Kurulum
$host = '127.0.0.1'; $db = 'stok_yonetimi'; $user = 'root'; $pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
$stokManager = new StokManager($pdo);

// Ana Yönlendirici (Router)
$page = $_GET['page'] ?? 'admin';

if ($page === 'admin') {
    // --- YÖNETİM PANELİ MANTIĞI (BLADE KULLANACAĞIZ) ---
    $action = $_GET['action'] ?? 'liste';
    $params = ['flash' => getFlashMessage()];

    switch ($action) {
        case 'ekle':
            $params += ['urun' => null, 'formBaslik' => 'Yeni Ürün Ekle'];
            View::render('form_blade', $params, 'blade');
            break;
        case 'duzenle':
            $urun = $stokManager->urunBul((int)$_GET['id']);
            $params += ['urun' => $urun, 'formBaslik' => 'Ürünü Düzenle'];
            View::render('form_blade', $params, 'blade');
            break;
        case 'kaydet':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = (int)($_POST['id'] ?? 0);
                $veri = ['ad' => $_POST['ad'], 'stok' => (int)$_POST['stok'], 'fiyat' => (float)$_POST['fiyat']];
                if ($id > 0) {
                    $stokManager->urunGuncelle($id, $veri);
                    setFlashMessage('Ürün başarıyla güncellendi!', 'is-info');
                } else {
                    $stokManager->urunEkle($veri);
                    setFlashMessage('Yeni ürün başarıyla eklendi!');
                }
            }
            header('Location: index.php?page=admin'); 
            exit;
        case 'sil':
            $id = (int)$_GET['id'];
            $urun = $stokManager->urunBul($id);
            if ($urun && !empty($urun['qr_code_path']) && file_exists($urun['qr_code_path'])) {
                unlink($urun['qr_code_path']);
            }
            $stokManager->urunSil($id);
            setFlashMessage('Ürün silindi.', 'is-danger');
            header('Location: index.php?page=admin'); 
            exit;
        default: // 'liste'
            $params['urunler'] = $stokManager->getUrunler();
            View::render('liste_blade', $params, 'blade');
            break;
    }
} elseif ($page === 'menu') {
    // --- MÜŞTERİ MENÜSÜ MANTIĞI (LATTE KULLANACAĞIZ) ---
    $urunler = $stokManager->getUrunler();
    View::render('menu', ['urunler' => $urunler, 'flash' => null], 'latte');
}