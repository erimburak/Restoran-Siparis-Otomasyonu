<?php
session_start();
ini_set('display_errors', 1); error_reporting(E_ALL);

require 'vendor/autoload.php';
require 'app/StokManager.php';

// Flash Mesaj Fonksiyonları
function setFlashMessage(string $message, string $type = 'is-success'): void { $_SESSION['flash_message'] = ['message' => $message, 'type' => $type]; }
function getFlashMessage(): ?array {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

// Veritabanı Bağlantısı
$host = '127.0.0.1'; $db = 'stok_yonetimi'; $user = 'root'; $pass = ''; $charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

// Blade Kurulumu
use Illuminate\Container\Container; use Illuminate\Events\Dispatcher; use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler; use Illuminate\View\Engines\CompilerEngine; use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory; use Illuminate\View\FileViewFinder;
$viewPaths = [__DIR__ . '/templates']; $cachePath = __DIR__ . '/cache';
$filesystem = new Filesystem; $container = new Container; $eventDispatcher = new Dispatcher($container);
$viewFinder = new FileViewFinder($filesystem, $viewPaths);
$viewFactory = new Factory(new EngineResolver, $viewFinder, $eventDispatcher);
$bladeCompiler = new BladeCompiler($filesystem, $cachePath);
$viewFactory->getEngineResolver()->register('blade', function () use ($bladeCompiler) { return new CompilerEngine($bladeCompiler); });

// Uygulama Mantığı
$stokManager = new StokManager($pdo);
$action = $_GET['action'] ?? 'liste';
$params = ['flash' => getFlashMessage()];

switch ($action) {
    case 'ekle':
        $params += ['urun' => null, 'formBaslik' => 'Yeni Ürün Ekle'];
        echo $viewFactory->make('form_blade', $params)->render();
        break;
    case 'duzenle':
        $urun = $stokManager->urunBul((int)$_GET['id']);
        $params += ['urun' => $urun, 'formBaslik' => 'Ürünü Düzenle'];
        echo $viewFactory->make('form_blade', $params)->render();
        break;
    case 'kaydet':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $veri = ['ad' => $_POST['ad'], 'stok' => (int)$_POST['stok'], 'fiyat' => (float)$_POST['fiyat']];
            if ($id > 0) {
                $stokManager->urunGuncelle($id, $veri); setFlashMessage('Ürün başarıyla güncellendi!', 'is-info');
            } else {
                $stokManager->urunEkle($veri); setFlashMessage('Yeni ürün başarıyla eklendi!');
            }
        }
        header('Location: blade.php'); exit;
    case 'sil':
        $urun = $stokManager->urunBul((int)$_GET['id']);
        if ($urun && !empty($urun['qr_code_path']) && file_exists($urun['qr_code_path'])) { unlink($urun['qr_code_path']); }
        $stokManager->urunSil((int)$_GET['id']);
        setFlashMessage('Ürün silindi.', 'is-danger');
        header('Location: blade.php'); exit;
    default:
        $params['urunler'] = $stokManager->getUrunler();
        echo $viewFactory->make('liste_blade', $params)->render();
        break;
}