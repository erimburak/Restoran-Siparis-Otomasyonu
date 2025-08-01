<?php

class SiparisManager
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAktifSiparisler(): array
    {
        // JOIN'lerin LEFT JOIN olduğundan emin olalım.
        $stmt = $this->db->query("
            SELECT s.id, s.durum, s.siparis_zamani, m.masa_adi, 
                   GROUP_CONCAT(CONCAT(u.ad, ' (', sd.adet, ' adet)') SEPARATOR '<br>') as urun_listesi
            FROM siparisler s
            LEFT JOIN masalar m ON s.masa_id = m.id
            LEFT JOIN siparis_detaylari sd ON s.id = sd.siparis_id
            LEFT JOIN urunler u ON sd.urun_id = u.id
            WHERE s.durum IN ('Yeni', 'Hazırlanıyor')
            GROUP BY s.id
            ORDER BY s.siparis_zamani ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setSiparisDurum(int $siparisId, string $yeniDurum): void
    {
        if (!in_array($yeniDurum, ['Hazırlanıyor', 'Tamamlandı', 'İptal Edildi'])) {
            return;
        }
        $stmt = $this->db->prepare("UPDATE siparisler SET durum = ? WHERE id = ?");
        $stmt->execute([$yeniDurum, $siparisId]);
    }
}