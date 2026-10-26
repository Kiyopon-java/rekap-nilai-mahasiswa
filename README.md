# Rekap Nilai Mahasiswa

Aplikasi web berbasis **PHP** dan **MySQL** untuk merekap nilai mahasiswa, dibangun dengan template **AdminLTE**. Mendukung tambah, ubah, dan hapus data, foto mahasiswa, konversi otomatis nilai huruf dan predikat, pencarian, filter predikat, pengurutan, serta pagination.

Dibuat untuk memenuhi tugas mata kuliah **Web Programming Semester 5**.

## Fitur

- **CRUD data nilai**: tambah, lihat, ubah, dan hapus data mahasiswa lewat modal.
- **Foto mahasiswa**: foto kecil tampil di samping nama pada kolom *Nama Lengkap*. Path foto disimpan di kolom `file_path`. Jika foto tidak ada, ditampilkan foto bawaan.
- **Pencarian berdasarkan nama**: kotak teks *Cari Berdasarkan Nama*.
- **Pencarian berdasarkan predikat**: dropdown *Semua Predikat* yang langsung memfilter data.
- **Pengurutan berdasarkan Nilai Akhir**: klik ikon panah kecil di judul kolom. Urutan bergantian: naik, turun, lalu kembali ke urutan NIM.
- **Pagination**: pilihan **5, 10, atau 15** data per halaman, dengan tombol **First, Previous, Next, dan Last**.
- **Konversi otomatis**: nilai huruf dan predikat dihitung dari nilai akhir. Pratinjau muncul saat nilai diketik di modal, dan server menghitung ulang saat data disimpan.
- **Pencatatan waktu**: kolom *Tgl Dibuat* dan *Tgl Diubah* terisi otomatis oleh database.
- Pencarian, filter, urutan, limit, dan halaman tetap terjaga saat berpindah halaman serta setelah edit atau hapus.

## Skala Penilaian

| Nilai Akhir     | Nilai Huruf | Predikat         |
|-----------------|:-----------:|------------------|
| > 80,50 – 100   | A           | Sangat Memuaskan |
| > 65,50 – 80,50 | B           | Memuaskan        |
| > 50,50 – 65,50 | C           | Cukup Memuaskan  |
| > 34,50 – 50,50 | D           | Kurang Memuaskan |
| 0 – 34,50       | E           | Tidak Lulus      |

## Teknologi

- PHP (ekstensi `mysqli`)
- MySQL atau MariaDB
- HTML, CSS, dan JavaScript
- Template [AdminLTE](https://adminlte.io/) (Bootstrap 5)
- Bootstrap Icons dan font Source Sans 3 (dimuat lewat CDN)

## Struktur Proyek

```
phpdb/
├── assets/                # Gambar template (termasuk foto contoh)
├── css/                   # Stylesheet AdminLTE
├── js/                    # JavaScript AdminLTE
├── database/
│   └── phpdb.sql          # Ekspor database (struktur dan data dummy)
├── uploads/               # Foto yang diunggah pengguna (tidak ikut di-commit)
├── koneksi.php            # Koneksi database
├── fungsi.php             # Konversi nilai, escape HTML, dan pengelolaan foto
├── viewdata.php           # Halaman utama (tabel, pencarian, urutan, pagination, modal)
├── proses_tambah.php      # Proses tambah data dan unggah foto
├── proses_edit.php        # Proses ubah data dan ganti foto
├── proses_hapus.php       # Proses hapus data dan foto
└── README.md
```

## Prasyarat

- PHP 7.4 atau lebih baru
- MySQL atau MariaDB
- Web server lokal, misalnya [XAMPP](https://www.apachefriends.org/) atau [Laragon](https://laragon.org/)
- Koneksi internet (Bootstrap Icons dan font dimuat dari CDN)

## Cara Menjalankan

1. **Salin proyek** ke folder web server:
   - XAMPP: `C:\xampp\htdocs\`
   - Laragon: `C:\laragon\www\`

   Contoh hasil: `C:\xampp\htdocs\webpro5e\phpdb\viewdata.php`

2. **Jalankan Apache dan MySQL** dari XAMPP atau Laragon.

3. **Impor database.** Ada dua cara:
   - **phpMyAdmin** (XAMPP): buka `http://localhost/phpmyadmin`, pilih tab **Import**, pilih file `database/phpdb.sql`, lalu klik **Go**.
   - **HeidiSQL** (Laragon): sambungkan ke server, buka tab **Query**, muat isi `database/phpdb.sql`, lalu jalankan (F9).

   Berkas SQL membuat database `phpdb`, tabel `nilai`, dan 40 data dummy.

4. **Sesuaikan koneksi** di `koneksi.php` jika konfigurasi MySQL berbeda dari bawaan (user `root`, tanpa password):

   ```php
   $host = "127.0.0.1";
   $user = "root";
   $pass = "";
   $db   = "phpdb";
   ```

5. **Buka aplikasi** di browser:

   ```
   http://localhost/webpro5e/phpdb/viewdata.php
   ```

   Sesuaikan alamat dengan lokasi folder proyek di komputer Anda.

## Struktur Tabel

Tabel `nilai` memiliki kolom:

| Kolom         | Tipe          | Keterangan                              |
|---------------|---------------|-----------------------------------------|
| `id`          | INT           | Kunci utama, bertambah otomatis         |
| `nim`         | VARCHAR(15)   | Unik                                    |
| `nama`        | VARCHAR(100)  | Nama lengkap                            |
| `file_path`   | VARCHAR(255)  | Lokasi file foto (boleh kosong)         |
| `nilai_akhir` | DECIMAL(5,2)  | Nilai akhir (0 sampai 100)              |
| `nilai_huruf` | CHAR(1)       | Dihitung otomatis                       |
| `predikat`    | VARCHAR(30)   | Dihitung otomatis                       |
| `created_at`  | DATETIME      | Terisi otomatis saat data dibuat        |
| `updated_at`  | DATETIME      | Berubah otomatis saat data diperbarui   |

## Penggunaan

- **Tambah data**: klik **Tambah Data Nilai**, isi NIM, nama, foto (opsional), dan nilai akhir. Nilai huruf dan predikat terisi otomatis.
- **Ubah data**: klik ikon pensil. Foto bisa diganti (opsional), dan foto lama dihapus otomatis.
- **Hapus data**: klik ikon tempat sampah, lalu konfirmasi. Foto yang diunggah ikut terhapus.
- **Cari dan filter**: ketik nama lalu tekan Enter, dan pilih predikat dari dropdown. Keduanya bisa dipakai bersamaan.
- **Urutkan**: klik ikon panah di judul kolom *Nilai Akhir*.
- **Pagination**: pilih jumlah data per halaman (5, 10, atau 15), lalu gunakan tombol First, Previous, Next, dan Last.

## Catatan Penting

- **Foto contoh:** data dummy memakai gambar bawaan template di `assets/img/`, sehingga foto langsung tampil setelah impor. Foto yang diunggah sendiri disimpan di folder `uploads/`.
- **Folder `uploads/`:** harus dapat ditulis oleh web server agar unggah foto berjalan. Isinya tidak ikut di-commit (lihat `.gitignore`), hanya `.gitkeep` yang disertakan agar folder tetap ada.
- **Unggah foto:** format JPG, PNG, GIF, atau WEBP, maksimal 2 MB. Jenis file diperiksa dari isinya, bukan hanya dari ekstensi.
- **Keamanan:** seluruh query memakai *prepared statement*, teks yang ditampilkan di-escape untuk mencegah XSS, dan input divalidasi di server (NIM harus angka dan unik, nilai berada di rentang 0 sampai 100).
- **Koneksi:** pengaturan bawaan (user `root` tanpa password) hanya cocok untuk pengembangan lokal. Gunakan akun dan password khusus jika aplikasi dipasang di server publik.
- **Internet:** ikon dan font dimuat dari CDN, jadi halaman membutuhkan koneksi internet agar tampil sempurna.

## Pembuat

| Keterangan  | Detail            |
|-------------|-------------------|
| Nama        | [NAMA LENGKAP]    |
| Kelas       | [KELAS]           |
| NIM         | [NIM]             |
| Mata Kuliah | Web Programming   |
