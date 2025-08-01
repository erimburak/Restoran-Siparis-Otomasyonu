<?php
session_start();

// Eğer session'da gerekli yönetici ve restoran bilgileri yoksa,
// kullanıcıyı login sayfasına geri gönder.
if (!isset($_SESSION['yonetici_id']) || !isset($_SESSION['restoran_id'])) {
    header('Location: login.php');
    exit;
}

// Session'daki bilgileri daha kolay kullanmak için değişkenlere atayalım
$yoneticiId = $_SESSION['yonetici_id'];
$restoranId = $_SESSION['restoran_id'];
$restoranAdi = $_SESSION['restoran_adi'];