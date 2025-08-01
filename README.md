# Restoran Sipariş ve Stok Yönetim Sistemi (PHP)

Bu proje, modern PHP framework'lerinin (Laravel, CakePHP, Nette) arkasındaki mimariyi anlama hedefiyle, standart (düz) PHP kullanılarak sıfırdan geliştirilmiş tam fonksiyonel bir Restoran Sipariş Otomasyonu'dur. "Yaparak öğrenme" felsefesiyle, bir fikrin nasıl adım adım ete kemiğe büründüğünün canlı bir kanıtıdır.

![Sipariş Takip Ekranı](https://i.imgur.com/83p1y3s.png)
*(Bu görseli projenizden aldığınız daha güncel bir ekran görüntüsü ile değiştirebilirsiniz.)*

## 📖 Projenin Hikayesi

Her şey, sadece teoride kalmak yerine, bir uygulamanın mimarisini, veri akışını ve güvenliğini en temelden inşa etme merakıyla başladı. Bu yolculuk, basit bir stok yönetim uygulamasından, masaya özel QR kod ile sipariş alabilen, canlı sipariş takibi sunan ve çift arayüzlü (yönetici ve müşteri) tam bir otomasyona dönüştü. Proje boyunca karşılaşılan her hata, yeni bir öğrenme fırsatı olarak görüldü ve sistem bu tecrübelerle daha da güçlendirildi.

## 🚀 Yetenekler ve Özellikler

### Yönetim Paneli
* ✅ **Ürün & Stok Yönetimi:** Tam CRUD (Ekle/Sil/Güncelle/Listele) yetenekleri ve verilen siparişlerle stokların otomatik güncellenmesi.
* ✅ **Masa Yönetimi:** Masaları listeleme, aktif siparişi olan masaları anlık olarak görsel olarak ayırt etme ve masanın tüm sipariş geçmişini tek tuşla sıfırlama.
* ✅ **Canlı Sipariş Ekranı:** Yeni gelen ve hazırlanmakta olan siparişleri anlık olarak görüntüleme.
* ✅ **Sipariş Durum Yönetimi:** Gelen siparişlerin durumunu "Hazırlanıyor", "Tamamlandı" veya "İptal Edildi" olarak güncelleme.
* ✅ **Dinamik QR Kod Üretimi:** Yönetim panelinden tek tuşla tüm masalar için veritabanına kayıtlı, o masaya özel menü QR kodları oluşturma ve yenileme.

### Müşteri Arayüzü
* ✅ **QR Kod ile Sipariş:** Her masaya özel QR kod ile doğrudan o masanın interaktif sipariş ekranına erişim.
* ✅ **İnteraktif Sepet:** `Session` tabanlı alışveriş sepeti ile kolayca ürün ekleme ve sipariş oluşturma.
* ✅ **Canlı Sipariş Takibi:** **AJAX** ile geliştirilmiş, sayfa yenilenmesine gerek kalmadan sipariş durumlarının ("Alındı", "Hazırlanıor", "Tamamlandı", "İptal") anlık olarak güncellendiği canlı takip ekranı.
* ✅ **Sipariş Geçmişi:** `Cookie` tabanlı müşteri tanıma ile müşterinin kendi aktif ve geçmiş siparişlerini tek bir ekrandan görmesi.

## 🛠️ Kullanılan Teknolojiler

-   **Backend:** Standart (Vanilla) **PHP 8+** (OOP & PDO)
-   **Veritabanı:** **MySQL / MariaDB**
-   **Frontend:** HTML, JavaScript (ES6), AJAX (`fetch` API)
-   **UI/UX Kütüphaneleri:**
    -   [**Bulma.io**](https://bulma.io/): Modern, mobil uyumlu ve sade bir kullanıcı arayüzü için.
    -   [**SweetAlert2**](https://sweetalert2.github.io/): Profesyonel ve etkileşimli uyarı/onay pencereleri için.
-   **PHP Kütüphaneleri (Composer ile):**
    -   `latte/latte`: Güvenli ve güçlü şablon motoru.
    -   `endroid/qr-code`: Dinamik QR kod üretimi için.
    -   *(Proje geliştirme sürecinde incelenen diğer kütüphaneler: `jenssegers/blade`, `mustache/mustache`, `phpmailer/phpmailer`, `spomky-labs/otphp`)*

## ⚙️ Kurulum Adımları

### Gereksinimler
* Bir yerel sunucu ortamı (XAMPP, Laragon, WAMP, MAMP vb.)
* PHP 8.0 veya üstü (GD eklentisi aktif olmalı)
* MySQL / MariaDB
* [Composer](https://getcomposer.org/)

### Kurulum

1.  **Projeyi Klonlayın:**
    ```bash
    git clone [https://github.com/kullanici-adiniz/proje-adi.git](https://github.com/kullanici-adiniz/proje-adi.git)
    cd proje-adi
    ```

2.  **PHP Bağımlılıklarını Yükleyin:**
    Proje ana dizininde terminali açın ve Composer ile gerekli paketleri yükleyin:
    ```bash
    composer install
    ```

3.  **Veritabanını Kurun:**
    * phpMyAdmin veya benzeri bir araç kullanarak `stok_yonetimi` adında, `utf8mb4_turkish_ci` karşılaştırmalı yeni bir veritabanı oluşturun.
    * Oluşturduğunuz veritabanını seçip "SQL" sekmesine tıklayın ve aşağıdaki kodun tamamını yapıştırıp çalıştırın.
  
      
   Veritabanı Kurulumu için Gerekli SQL Kodunu Göster
    '
    ```sql
    CREATE TABLE `musteriler` ( `id` int(11) NOT NULL AUTO_INCREMENT, `benzersiz_kimlik` varchar(255) COLLATE utf8mb4_turkish_ci NOT NULL, `olusturulma_zamani` datetime NOT NULL DEFAULT current_timestamp(), PRIMARY KEY (`id`), UNIQUE KEY `benzersiz_kimlik` (`benzersiz_kimlik`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
    CREATE TABLE `masalar` ( `id` int(11) NOT NULL AUTO_INCREMENT, `masa_adi` varchar(255) COLLATE utf8mb4_turkish_ci NOT NULL, `qr_code_path` varchar(255) COLLATE utf8mb4_turkish_ci DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
    INSERT INTO `masalar` (`id`, `masa_adi`) VALUES (1, 'Masa 1'), (2, 'Masa 2'), (3, 'Masa 3'), (4, 'Masa 4'), (5, 'Masa 5'), (6, 'Masa 6'), (7, 'Masa 7'), (8, 'Masa 8'), (9, 'Masa 9'), (10, 'Masa 10');
    CREATE TABLE `urunler` ( `id` int(11) NOT NULL AUTO_INCREMENT, `ad` varchar(255) COLLATE utf8mb4_turkish_ci NOT NULL, `stok` int(11) NOT NULL DEFAULT 0, `fiyat` decimal(10,2) NOT NULL DEFAULT 0.00, `qr_code_path` varchar(255) COLLATE utf8mb4_turkish_ci DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
    CREATE TABLE `siparisler` ( `id` int(11) NOT NULL AUTO_INCREMENT, `masa_id` int(11) NOT NULL, `musteri_id` int(11) DEFAULT NULL, `siparis_zamani` datetime NOT NULL DEFAULT current_timestamp(), `durum` enum('Yeni','Hazırlanıyor','Tamamlandı','İptal Edildi') COLLATE utf8mb4_turkish_ci NOT NULL DEFAULT 'Yeni', PRIMARY KEY (`id`), KEY `masa_id` (`masa_id`), KEY `musteri_id` (`musteri_id`), CONSTRAINT `siparisler_ibfk_1` FOREIGN KEY (`masa_id`) REFERENCES `masalar` (`id`), CONSTRAINT `siparisler_ibfk_2` FOREIGN KEY (`musteri_id`) REFERENCES `musteriler` (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
    CREATE TABLE `siparis_detaylari` ( `id` int(11) NOT NULL AUTO_INCREMENT, `siparis_id` int(11) NOT NULL, `urun_id` int(11) NOT NULL, `adet` int(11) NOT NULL, `fiyat` decimal(10,2) NOT NULL, PRIMARY KEY (`id`), KEY `siparis_id` (`siparis_id`), KEY `urun_id` (`urun_id`), CONSTRAINT `siparis_detaylari_ibfk_1` FOREIGN KEY (`siparis_id`) REFERENCES `siparisler` (`id`) ON DELETE CASCADE, CONSTRAINT `siparis_detaylari_ibfk_2` FOREIGN KEY (`urun_id`) REFERENCES `urunler` (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
    ```
    </details>

4.  **Gerekli Klasörleri Oluşturun:**
    Proje ana dizininde aşağıdaki klasörlerin var olduğundan ve web sunucusunun bu klasörlere **yazma izni** olduğundan emin olun:
    * `temp/` (Latte cache için)
    * `qrcodes/` (Oluşturulacak QR kod resimleri için)

5.  **Veritabanı Bilgilerini Kontrol Edin:**
    Tüm `.php` dosyalarının (`latte.php`, `menu.php` vb.) en üstündeki PDO bağlantı satırlarında, veritabanı kullanıcı adınızın (`root`) ve şifrenizin (varsayılan: `''`) doğru olduğundan emin olun.

## 💻 Kullanım

1.  XAMPP veya benzeri bir yazılımdan Apache ve MySQL servislerini başlatın.
2.  Uygulamaya aşağıdaki adreslerden erişebilirsiniz:

    * **Yönetim Paneli:**
        `http://localhost/proje-klasor-adi/latte.php`
    * **Müşteri Menüsü (Örnek Masa 1 için):**
        `http://localhost/proje-klasor-adi/menu.php?masa=1`

Masa QR kodlarını oluşturmak için Yönetim Paneli'ndeki **Masa Yönetimi** sayfasına gidin ve **"Tüm QR Kodları Oluştur/Yenile"** butonuna tıklayın.

## 🌟 Gelecek Vizyonu ve İyileştirmeler

Bu proje, daha da geliştirilmek için sağlam bir temel sunmaktadır. Potansiyel gelecek adımları:
-   **Yönetici Giriş Sistemi:** Yönetim panelini bir kullanıcı adı ve şifre ile koruma altına almak.
-   **Dashboard ve Raporlama:** Günlük ciro, en çok satan ürünler gibi istatistikleri gösteren bir ana panel ekranı.
-   **Gerçek Zamanlı Güncellemeler:** Sipariş ekranını AJAX polling yerine WebSockets ile anlık hale getirmek.
-   **Merkezi Yapılandırma:** Veritabanı bilgilerini tek bir `config.php` dosyasında toplamak.

## Lisans

Bu proje MIT Lisansı ile lisanslanmıştır.
