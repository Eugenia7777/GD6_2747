## Catatan artisan serve dan composer run dev
Modul ini cukup memakai php artisan serve karena CSS dan gambar dipanggil langsung dari folder public lewat asset(), dan Bootstrap diambil dari CDN. Tidak ada file yang perlu dikompilasi, jadi Vite tidak dibutuhkan.

## Versi Tools
PHP: 8.5.12
Composer: 2.10.3
Laravel Installer: 5.32.0

### Mengapa Seeder Tidak Menambah Data Berulang?

Jumlah data pada tabel `promotors` tetap 3 baris dan tabel `konsers` tetap 5 baris meskipun perintah `php artisan migrate:fresh --seed` dijalankan berulang kali.

Hal ini karena perintah `migrate:fresh` menghapus seluruh tabel terlebih dahulu, kemudian membuat ulang tabel berdasarkan migration. Setelah itu, opsi `--seed` menjalankan `DatabaseSeeder` untuk memasukkan kembali data awal yang sudah ditentukan. Oleh karena itu, data sebelumnya tidak menumpuk karena selalu dibuat ulang dari awal dengan jumlah yang sama.

## Cara Menjalankan Proyek TiketWar

1. Nyalakan MySQL di XAMPP dan buat database `tiketwar` di phpMyAdmin.
2. Masuk ke folder proyek: `cd tiketwar`.
3. Install dependency: `composer install`.
4. Salin `.env.example` menjadi `.env`:
   ```bash
   copy .env.example .env
   ```
5. Sesuaikan konfigurasi database di `.env`, lalu jalankan:
   ```bash
   php artisan key:generate
   ```
6. Jalankan migration dan seeder:
   ```bash
   php artisan migrate:fresh --seed
   ```
7. Jalankan server:
   ```bash
   php artisan serve
   ```

## Jawaban Latihan Debugging

1. **Koneksi ditolak:** MySQL belum berjalan atau konfigurasi database di `.env` salah. Perbaiki dengan menjalankan MySQL dan memeriksa konfigurasi database.
2. **Model belum punya `$fillable`:** Kolom belum didaftarkan pada `$fillable`. Tambahkan nama kolom yang diizinkan pada model.
3. **419 Page Expired:** Token CSRF belum disertakan. Tambahkan `@csrf` di dalam form.
4. **405 Method Not Allowed:** Method form tidak sesuai dengan route. Tambahkan `@method('PUT')` jika route menggunakan PUT.

## Jawaban Pertanyaan Seeder

Data tidak bertambah berulang kali karena `migrate:fresh` menghapus seluruh tabel dan membuatnya kembali. Setelah itu, `--seed` menjalankan DatabaseSeeder untuk memasukkan data awal kembali, sehingga jumlah data tetap sama.

## Jawaban Latihan Debugging 
1. Total dari form: menjelaskan bahaya mengambil nilai total dari input form karena pengguna bisa memanipulasinya melalui DevTools. Perbaikannya: total harus dihitung oleh server menggunakan harga konser × jumlah tiket.
2. Pengecekan stok tanpa kunci: menjelaskan risiko dua pembeli memesan tiket bersamaan karena memakai data stok lama. Perbaikannya: gunakan DB::transaction() dan lockForUpdate() untuk membaca stok terbaru sebelum mengurangi stok.