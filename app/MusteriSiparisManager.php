<?php

class MusteriSiparisManager
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getMusteriSiparisleri(int $musteriId): array
    {
        $stmt = $this->db->prepare("
            SELECT s.id, s.durum, s.siparis_zamani, m.masa_adi
            FROM siparisler s
            JOIN masalar m ON s.masa_id = m.id
            WHERE s.musteri_id = ?
            ORDER BY s.siparis_zamani DESC
        ");
        $stmt->execute([$musteriId]);
        
        $siparisler = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Siparişleri aktif ve geçmiş olarak ikiye ayıralım
        $aktifSiparisler = [];
        $gecmisSiparisler = [];

        foreach ($siparisler as $siparis) {
            if (in_array($siparis['durum'], ['Yeni', 'Hazırlanıyor'])) {
                $aktifSiparisler[] = $siparis;
            } else {
                $gecmisSiparisler[] = $siparis;
            }
        }

        return [
            'aktif' => $aktifSiparisler,
            'gecmis' => $gecmisSiparisler,
        ];
    }
}