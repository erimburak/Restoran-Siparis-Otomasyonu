<?php
session_start();
ini_set('display_errors', 1); error_reporting(E_ALL);

require 'vendor/autoload.php';
require 'app/StokManager.php';

// Flash Mesaj Fonksiyonları ve diğer yardımcılar buraya eklenebilir...

// --- EKSİK OLAN VERİTABANI BAĞLANTISI ---
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
// -----------------------------------------

$mustache = new Mustache_Engine;
// StokManager'a artık PDO bağlantısını veriyoruz
$stokManager = new StokManager($pdo);

$action = $_GET['action'] ?? 'liste';

$layout = file_get_contents(__DIR__ . '/templates/layout.mustache');
$pageData = [];

switch ($action) {
    case 'ekle':
        $pageData['formBaslik'] = 'Yeni Ürün Ekle';
        $template = file_get_contents(__DIR__ . '/templates/form.mustache');
        $pageData['content'] = $mustache->render($template, $pageData);
        break;

    case 'duzenle':
        $urun = $stokManager->urunBul((int)$_GET['id']);
        $pageData['urun'] = $urun;
        $pageData['formBaslik'] = 'Ürünü Düzenle';
        $template = file_get_contents(__DIR__ . '/templates/form.mustache');
        $pageData['content'] = $mustache->render($template, $pageData);
        break;

    case 'kaydet':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $veri = ['ad' => $_POST['ad'], 'stok' => (int)$_POST['stok'], 'fiyat' => (float)$_POST['fiyat']];
            if ($id > 0) {
                $stokManager->urunGuncelle($id, $veri);
            } else {
                $stokManager->urunEkle($veri);
            }
        }
        header('Location: mustache.php');
        exit;

    case 'sil':
        $stokManager->urunSil((int)$_GET['id']);
        header('Location: mustache.php');
        exit;

    case 'liste':
    default:
        $urunler = $stokManager->getUrunler();
        $pageData['urunler'] = $urunler;
        $template = file_get_contents(__DIR__ . '/templates/liste.mustache');
        $pageData['content'] = $mustache->render($template, $pageData);
        break;
}

echo $mustache->render($layout, $pageData);