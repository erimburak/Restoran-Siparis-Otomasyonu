<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


require 'vendor/autoload.php';
require 'app/StokManager.php';
require 'app/SiparisManager.php';

// Flash Mesaj Fonksiyonları
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
    return null;
}

// Veritabanı Bağlantısı
$host = '127.0.0.1';
$db   = 'stok_yonetimi';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}


// Latte ve Yönetici Sınıflarının Kurulumu
$latte = new Latte\Engine;
$latte->setTempDirectory(__DIR__ . '/temp');
$stokManager = new StokManager($pdo);
$siparisManager = new SiparisManager($pdo);

// Yönlendirici (Router) Mantığı
$action = $_GET['action'] ?? 'liste'; // Değişken burada, switch'ten önce tanımlanıyor.
$params = ['flash' => getFlashMessage()];

// Aktif sayfayı şablona bildirmek için değişken ekliyoruz.
if (in_array($action, ['liste', 'ekle', 'duzenle', 'kaydet', 'sil'])) {
    // Bu eylemlerin hepsi "Ürün Yönetimi" sekmesine aittir.
    $params['aktifSayfa'] = 'urunler';
} elseif ($action === 'siparisler' || $action === 'siparis_hazirlaniyor' || $action === 'siparis_tamamla') {
    // Bu eylemler "Aktif Siparişler" sekmesine aittir.
    $params['aktifSayfa'] = 'siparisler';
}
switch ($action) {
    case 'ekle':
        $params += ['urun' => null, 'formBaslik' => 'Yeni Ürün Ekle'];
        $latte->render('templates/form.latte', $params);
        break;

    case 'duzenle':
        $urun = $stokManager->urunBul((int)$_GET['id']);
        $params += ['urun' => $urun, 'formBaslik' => 'Ürünü Düzenle'];
        $latte->render('templates/form.latte', $params);
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
        header('Location: latte.php');
        exit;

    case 'sil':
        $urun = $stokManager->urunBul((int)$_GET['id']);
        if ($urun && !empty($urun['qr_code_path']) && file_exists($urun['qr_code_path'])) {
            unlink($urun['qr_code_path']);
        }
        $stokManager->urunSil((int)$_GET['id']);
        setFlashMessage('Ürün silindi.', 'is-danger');
        header('Location: latte.php');
        exit;
    
    case 'siparisler':
        $params['siparisler'] = $siparisManager->getAktifSiparisler();
        $latte->render('templates/siparisler.latte', $params);
        break;

    case 'siparis_hazirlaniyor':
        $siparisManager->setSiparisDurum((int)$_GET['id'], 'Hazırlanıyor');
        setFlashMessage("Sipariş hazırlanıyor olarak işaretlendi.", "is-info");
        header('Location: latte.php?action=siparisler');
        exit;

    case 'siparis_iptal':
        $siparisManager->setSiparisDurum((int)$_GET['id'], 'İptal Edildi');
        setFlashMessage("Sipariş iptal edildi.", "is-danger");
        header('Location: latte.php?action=siparisler');
        exit;
    
    case 'siparis_tamamla':
        $siparisManager->setSiparisDurum((int)$_GET['id'], 'Tamamlandı');
        setFlashMessage("Sipariş tamamlandı.", "is-success");
        header('Location: latte.php?action=siparisler');
        exit;

    case 'liste':
    default:
        $params['urunler'] = $stokManager->getUrunler();
        $latte->render('templates/liste.latte', $params);
        break;
}