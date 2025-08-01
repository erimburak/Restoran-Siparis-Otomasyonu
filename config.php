<?php
// E-posta gönderme ayarları
return [
    'mail' => [
        'host' => 'smtp.gmail.com',
        'username' => 'burakerim15@gmail.com', // E-postayı göndereceğin Gmail adresin
        'password' => 'iktg dvjl ffxt iqok',    // Google'dan aldığın 16 haneli Uygulama Şifresi
        'port' => 587,
        'encryption' => 'tls', // Veya 465 portu için 'ssl'
        'from_address' => 'burakerim15@gmail.com',
        'from_name' => 'Restoran Sipariş Sistemi'
    ]
];