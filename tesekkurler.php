<?php
session_start(); // Session'ı başlatmayı unutmuyoruz
require 'vendor/autoload.php';

$latte = new Latte\Engine;
$latte->setTempDirectory(__DIR__ . '/temp');

// Session'dan son siparişin ID'sini al
$siparisId = $_SESSION['son_siparis_id'] ?? null;

$params = [
    'aktifSayfa' => 'menu',
    'siparisId' => $siparisId, // ID'yi şablona gönder
    'flash' => null
];

$latte->render('templates/tesekkurler.latte', $params);