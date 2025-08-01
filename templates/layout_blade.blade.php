<!DOCTYPE html>
<html>
<head>
    <title>Stok Uygulaması (Blade)</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.0/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <section class="section">
        <div class="container">
            <h1 class="title is-2 has-text-centered">Stok Yönetim Sistemi <span class="tag is-link is-light">Blade</span></h1>
            <div class="tabs is-centered">
                <ul>
                    {{-- Artık linkler ?page=... şeklinde --}}
                    <li><a href="index.php?page=admin">Yönetim Paneli (Blade)</a></li>
                    <li><a href="index.php?page=menu">Müşteri Menüsü (Latte)</a></li>
                </ul>
            </div>
            @yield('content')
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    @if (isset($flash) && $flash)
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    @if (isset($flash) && $flash)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({ toast: true, position: 'top-end', icon: '{{ $flash['type'] === 'is-danger' ? 'error' : 'success' }}', title: '{{ $flash['message'] }}', showConfirmButton: false, timer: 3500, timerProgressBar: true });
        });
    </script>
    @endif
    <script>
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ONAY GEREKTİREN TÜM LİNKLER İÇİN
        const confirmLinks = document.querySelectorAll('.js-confirm');
        confirmLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault(); // Linkin normal çalışmasını anında durdur
                const message = this.dataset.message; // data-message içeriğini al
                const href = this.href; // Linkin gideceği adresi al

                Swal.fire({
                    title: 'Emin misiniz?',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Evet, eminim!',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Eğer kullanıcı "Evet" derse, linke git
                        window.location.href = href;
                    }
                });
            });
        });

        // KAYDETME FORMU İÇİN
        const productForm = document.getElementById('product-form');
        if (productForm) {
            productForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Formun normal çalışmasını anında durdur
                
                Swal.fire({
                    title: 'Kaydetmek istediğinize emin misiniz?',
                    text: "Değişiklikler veritabanına işlenecek.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Evet, kaydet!',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Eğer kullanıcı "Evet" derse, formu gönder
                        productForm.submit();
                    }
                });
            });
        }
    });
    </script>
</body>
</html> 