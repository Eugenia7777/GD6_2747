## Catatan artisan serve dan composer run dev
Modul ini cukup memakai php artisan serve karena CSS dan gambar dipanggil langsung dari folder public lewat asset(), dan Bootstrap diambil dari CDN. Tidak ada file yang perlu dikompilasi, jadi Vite tidak dibutuhkan.

## Versi Tools
PHP: 8.5.12
Composer: 2.10.3
Laravel Installer: 5.32.0

### Mengapa Seeder Tidak Menambah Data Berulang?

Jumlah data pada tabel `promotors` tetap 3 baris dan tabel `konsers` tetap 5 baris meskipun perintah `php artisan migrate:fresh --seed` dijalankan berulang kali.

Hal ini karena perintah `migrate:fresh` menghapus seluruh tabel terlebih dahulu, kemudian membuat ulang tabel berdasarkan migration. Setelah itu, opsi `--seed` menjalankan `DatabaseSeeder` untuk memasukkan kembali data awal yang sudah ditentukan. Oleh karena itu, data sebelumnya tidak menumpuk karena selalu dibuat ulang dari awal dengan jumlah yang sama.