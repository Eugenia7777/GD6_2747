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

## Modul 7 – Tugas Rumah: Laporan dan Ekspor PDF
Debugging 3: Tanggal Sampai yang Hilang

Kesalahan: Filter tanggal sampai menggunakan where('created_at', '<=', $sampai).

Penyebabnya: Kolom created_at menyimpan tanggal dan jam, sedangkan nilai filter hanya berisi tanggal. Nilai tanggal seperti 2026-11-09 dianggap sebagai 2026-11-09 00:00:00. Pesanan yang dibuat pada hari tersebut pukul 14.30 menjadi lebih besar dari batas waktu tersebut, sehingga tidak ikut ditampilkan.
Perbaikan: Gunakan whereDate() agar perbandingan hanya berdasarkan tanggal dan mengabaikan jam.

->when($request->query('sampai'), function ($query, $sampai) {
    $query->whereDate('created_at', '<=', $sampai);
})

Debugging 4: PDF Berantakan
Kesalahan: View PDF masih menggunakan @extends('layouts.main'), sehingga layout halaman web ikut dimuat.

Penyebabnya: Layout web berisi navbar, footer, dan Bootstrap yang dimuat dari CDN. Dompdf tidak mengambil CSS Bootstrap dari CDN, sehingga tampilan PDF tidak mendapatkan gaya yang sesuai dan kolom tabel menjadi tidak rapi.
Perbaikan: Buat view PDF yang berdiri sendiri tanpa @extends('layouts.main'), navbar, dan footer halaman web. Gunakan struktur HTML sederhana serta tuliskan CSS langsung di dalam view laporan-pdf.blade.php, karena Dompdf tidak memuat CSS dari CDN dan hanya mendukung CSS 2.1.

## Modul 8 – API dan Autentikasi

**Debugging 1: Pesanan dari Semua Isian**
Kesalahan: Menggunakan Pesanan::create($request->all()); untuk membuat pesanan.

Berbahaya karena $request->all() mengambil seluruh data yang dikirim pengguna, termasuk data yang seharusnya ditentukan oleh server seperti konser_id, harga_satuan, total, dan status.
Pengguna dapat mengirim nilai seperti harga_satuan sebesar 1, total sebesar 1, dan status sebesar lunas. Jika kolom tersebut termasuk dalam $fillable pada model Pesanan, nilai yang dikirim pengguna dapat tersimpan ke database. Akibatnya, pengguna dapat membuat pesanan dengan harga yang tidak sesuai atau mengubah status pesanan menjadi lunas tanpa pembayaran.

Perbaikan: Gunakan $request->validate([...]) untuk memvalidasi dan mengambil hanya data yang boleh diisi pengguna, yaitu nama_pemesan, email, dan jumlah. Selanjutnya, konser_id diambil dari konser yang dipilih, harga_satuan dan total dihitung oleh server, serta status ditentukan oleh server menggunakan Pesanan::MENUNGGU_BAYAR.

**Debugging 2: Postman Menerima HTML**
Kesalahan: Request GET /api/user tidak menyertakan header Accept: application/json.

Penyebabnya: Laravel menganggap request berasal dari browser dan bukan program API. Middleware auth akan menangani pengguna yang belum login dengan mengarahkan pengguna ke halaman login.

Jika route login belum dibuat, Laravel menampilkan error Route [login] not defined. Jika route login sudah tersedia, request dapat diarahkan ke halaman login HTML dengan status 200, bukan mendapatkan respons JSON dengan status 401.

Perbaikan: Tambahkan header berikut pada request API:

Accept: application/json
Dengan header tersebut, Laravel mengetahui bahwa klien mengharapkan respons JSON. Jika pengguna belum terautentikasi, API akan membalas status 401 dengan JSON berisi pesan Unauthenticated..

**Debugging 3: Login yang selalu 419**
Kesalahannya karena Form login menggunakan method `POST`, tetapi tidak memiliki directive `@csrf`.

Berbahaya karena Laravel memeriksa token CSRF pada setiap request POST untuk memastikan request berasal dari form yang sah. Tanpa token CSRF, Laravel menolak request dan menampilkan error `419 Page Expired`.

Mematikan pemeriksaan CSRF bukan solusi yang tepat karena dapat membuat aplikasi rentan terhadap serangan Cross-Site Request Forgery (CSRF). Penyerang dapat mencoba mengirim request atas nama pengguna yang sedang login tanpa sepengetahuan pengguna tersebut.

Perbaikan: Tambahkan `@csrf` di dalam form:

```blade
<form action="{{ route('login.proses') }}" method="POST">
    @csrf
    <input type="email" name="email">
    <input type="password" name="password">
    <button type="submit">Masuk</button>
</form>
```

**Debugging 4: Login buatan sendiri**
Kesalahannya karena kode login melakukan pencarian pengguna berdasarkan email, kemudian membandingkan password dari request secara langsung dengan nilai password di database.

Berbahaya karena:
1. Password dibandingkan secara langsung, sehingga kode mengasumsikan password disimpan dalam bentuk teks biasa. Password seharusnya disimpan menggunakan hash agar tidak dapat dibaca secara langsung.
2. Pesan kesalahan berbeda antara email yang tidak terdaftar dan password yang salah. Hal ini dapat digunakan untuk mengetahui email mana yang terdaftar di aplikasi.
3. Kode tidak menggunakan `Auth::attempt()` untuk melakukan proses autentikasi sesuai mekanisme Laravel.
4. Kode tidak melakukan regenerasi session setelah login berhasil. Hal ini dapat meningkatkan risiko session fixation.
5. Kode tidak menggunakan `redirect()->intended()`, sehingga pengguna tidak diarahkan kembali ke halaman yang sebelumnya ingin dibuka setelah login.

Perbaikan: Gunakan `Auth::attempt()` untuk memeriksa email dan password yang tersimpan dalam bentuk hash. Setelah login berhasil, lakukan regenerasi session dan gunakan `redirect()->intended()`.

Contoh perbaikan:
```php
$credentials = $request->validate([
    'email' => ['required', 'email'],
    'password' => ['required'],
]);

if (! Auth::attempt($credentials)) {
    return back()
        ->withErrors(['email' => 'Email atau kata sandi salah.'])
        ->onlyInput('email');
}

$request->session()->regenerate();

return redirect()->intended(route('pesanan.index'));
```

**Debugging 5: Tombol hilang, halaman tetap terbuka**

Kesalahannya karena Route untuk halaman tambah konser dan proses penyimpanan konser belum dilindungi middleware `auth`. Penggunaan `@auth` pada view hanya menyembunyikan tombol Tambah Konser dari pengunjung yang belum login, tetapi tidak mencegah mereka mengakses route secara langsung melalui address bar.

Berbahaya karena pengunjung yang belum login tetap dapat membuka halaman `/konser/create` dan mengirimkan data konser baru melalui route POST `/konser`. Hal ini memungkinkan orang yang tidak memiliki akses admin untuk melakukan perubahan data.

Perbaikan: Route untuk mengelola konser harus dimasukkan ke dalam grup middleware `auth`, sehingga hanya admin yang sudah login dapat mengaksesnya.

```php
Route::middleware('auth')->group(function () {
    Route::get('/konser/create', [KonserController::class, 'create'])
        ->name('konser.create');

    Route::post('/konser', [KonserController::class, 'store'])
        ->name('konser.store');
});
```

Dengan demikian, `@auth` digunakan untuk mengatur tampilan tombol, sedangkan middleware `auth` digunakan untuk melindungi route dari akses yang tidak diizinkan.