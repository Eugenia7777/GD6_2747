# GD6_2747
## Catatan artisan serve dan composer run dev

Modul ini cukup memakai `php artisan serve` karena file CSS dan gambar diakses langsung dari folder `public` menggunakan `asset()`, sedangkan Bootstrap menggunakan CDN. Jadi, proses build menggunakan Vite tidak diperlukan.

## Versi Tools

- PHP: 8.5.12
- Composer: 2.10.3
- Laravel Installer: 5.32.0