@extends('layout_blade')
@section('content')
    <a href="blade.php?action=ekle" class="button is-primary mb-4">...Yeni Ürün Ekle...</a>
    <a href="index.php?page=admin&action=duzenle&id={{ $urun['id'] }}" ...>
    <table class="table is-fullwidth is-bordered">
        {{-- ... tablo başlıkları ... --}}
        <tbody>
            @forelse ($urunler as $urun)
            <tr>
                <td>{{ $urun['id'] }}</td> <td>{{ $urun['ad'] }}</td> <td>{{ $urun['stok'] }}</td> <td>{{ $urun['fiyat'] }} TL</td>
                <td>
                    <a href="blade.php?action=duzenle&id={{ $urun['id'] }}" class="button is-small is-info">...</a>
                    <a href="blade.php?action=sil&id={{ $urun['id'] }}" class="button is-small is-danger js-confirm" data-message="Bu ürünü kalıcı olarak silmek istediğinizden emin misiniz?">...</a>
                    @if ($urun['qr_code_path'])
                        <a href="{{ $urun['qr_code_path'] }}" class="button is-small is-black" target="_blank">...</a>
                        <a href="qr.php?id={{ $urun['id'] }}&from=blade" class="button is-small is-warning js-confirm" data-message="Mevcut QR kod silinip yenisi oluşturulacak. Emin misiniz?">...</a>
                    @else
                        <a href="qr.php?id={{ $urun['id'] }}&from=blade" class="button is-small is-black js-confirm" data-message="Bu ürün için yeni bir QR kodu oluşturulacak. Emin misiniz?">...</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="has-text-centered">Henüz hiç ürün eklenmemiş.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection