@extends('layout_blade')
@section('content')
    <h2 class="title is-3">{{ $formBaslik }}</h2>
    <form action="blade.php?action=kaydet" method="post" id="product-form">
        @if ($urun) <input type="hidden" name="id" value="{{ $urun['id'] }}"> @endif

        <div class="field">
            <label class="label">Ürün Adı</label>
            <div class="control">
                <input class="input" type="text" name="ad" value="{{ $urun['ad'] ?? '' }}" required>
            </div>
        </div>

        <div class="field">
            <label class="label">Stok Adedi</label>
            <div class="control">
                <input class="input" type="number" name="stok" value="{{ $urun['stok'] ?? 0 }}" required>
            </div>
        </div>

        <div class="field">
            <label class="label">Fiyat (TL)</label>
            <div class="control">
                <input class="input" type="text" name="fiyat" value="{{ $urun['fiyat'] ?? 0.0 }}" required>
            </div>
        </div>

        <div class="field is-grouped">
            <div class="control">
                <button class="button is-link">Kaydet</button>
            </div>
            <div class="control">
                <a href="blade.php" class="button is-link is-light">İptal</a>
            </div>
        </div>
    </form>
@endsection