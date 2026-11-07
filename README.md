## Catatan artisan serve dan composer run dev
Modul ini cukup memakai php artisan serve karena CSS dan gambar dipanggil langsung dari folder public lewat asset(), dan Bootstrap diambil dari CDN. Tidak ada file yang perlu dikompilasi, jadi Vite tidak dibutuhkan.

## Versi Tools
PHP: 8.5.12
Composer: 2.10.3
Laravel Installer: 5.32.0