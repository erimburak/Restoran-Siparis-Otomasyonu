<?php

class StokManager
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getUrunler(): array
    {
        $stmt = $this->db->query("SELECT * FROM urunler ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function urunBul(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM urunler WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function urunEkle(array $yeniUrun): void
    {
        $stmt = $this->db->prepare("INSERT INTO urunler (ad, stok, fiyat) VALUES (?, ?, ?)");
        $stmt->execute([
            $yeniUrun['ad'],
            $yeniUrun['stok'],
            $yeniUrun['fiyat']
        ]);
    }

    public function urunGuncelle(int $id, array $guncelVeri): void
    {
        $stmt = $this->db->prepare("UPDATE urunler SET ad = ?, stok = ?, fiyat = ? WHERE id = ?");
        $stmt->execute([
            $guncelVeri['ad'],
            $guncelVeri['stok'],
            $guncelVeri['fiyat'],
            $id
        ]);
    }

    public function urunSil(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM urunler WHERE id = ?");
        $stmt->execute([$id]);
    }
    
    public function setQrCodePath(int $id, string $path): void
    {
        $stmt = $this->db->prepare("UPDATE urunler SET qr_code_path = ? WHERE id = ?");
        $stmt->execute([$path, $id]);
    }
}