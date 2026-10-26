<?php
session_start();
require 'koneksi.php';
require 'fungsi.php';

// Kembali ke halaman utama dengan pesan; filter dan halaman tetap terjaga
function kembali($tipe, $teks)
{
    $_SESSION['pesan'] = ['tipe' => $tipe, 'teks' => $teks];
    $qs = preg_replace('/[\x00-\x1F\x7F]/', '', $_POST['kembali'] ?? '');
    header('Location: viewdata.php' . ($qs !== '' ? '?' . $qs : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: viewdata.php');
    exit;
}

$id    = (int)($_POST['id'] ?? 0);
$nim   = trim($_POST['nim'] ?? '');
$nama  = trim($_POST['nama'] ?? '');
$nilai = $_POST['nilai_akhir'] ?? '';

// ---- Validasi di sisi server ----
if ($id <= 0) {
    kembali('danger', 'Data tidak valid.');
}
if (!preg_match('/^[0-9]{1,15}$/', $nim)) {
    kembali('danger', 'NIM harus berupa angka (maksimal 15 digit).');
}
if ($nama === '' || mb_strlen($nama) > 100) {
    kembali('danger', 'Nama wajib diisi (maksimal 100 karakter).');
}
if (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
    kembali('danger', 'Nilai akhir harus berupa angka antara 0 sampai 100.');
}

// ---- Pastikan data ada dan ambil foto lama ----
$stmt = mysqli_prepare($koneksi, "SELECT file_path FROM nilai WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $fotoLama);
$ditemukan = mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$ditemukan) {
    kembali('danger', 'Data tidak ditemukan.');
}

// ---- Cek NIM dipakai data lain atau tidak ----
$cek = mysqli_prepare($koneksi, "SELECT id FROM nilai WHERE nim = ? AND id <> ?");
mysqli_stmt_bind_param($cek, 'si', $nim, $id);
mysqli_stmt_execute($cek);
mysqli_stmt_store_result($cek);
$dipakai = mysqli_stmt_num_rows($cek) > 0;
mysqli_stmt_close($cek);

if ($dipakai) {
    kembali('danger', "NIM $nim sudah dipakai data lain.");
}

// ---- Foto baru (opsional) ----
$foto = uploadFoto($_FILES['foto'] ?? null, $nim);
if (!$foto['ok']) {
    kembali('danger', $foto['error']);
}
$fotoBaru = $foto['path'];   // null kalau tidak ada foto baru

// ---- Simpan perubahan ----
$nilai = round((float)$nilai, 2);
$k     = konversiNilai($nilai);

try {
    if ($fotoBaru !== null) {
        $stmt = mysqli_prepare($koneksi,
            "UPDATE nilai SET nim = ?, nama = ?, file_path = ?, nilai_akhir = ?, nilai_huruf = ?, predikat = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'sssdssi', $nim, $nama, $fotoBaru, $nilai, $k['huruf'], $k['predikat'], $id);
    } else {
        $stmt = mysqli_prepare($koneksi,
            "UPDATE nilai SET nim = ?, nama = ?, nilai_akhir = ?, nilai_huruf = ?, predikat = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'ssdssi', $nim, $nama, $nilai, $k['huruf'], $k['predikat'], $id);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} catch (mysqli_sql_exception $ex) {
    // Gagal: foto baru yang sudah terunggah dibersihkan
    hapusFoto($fotoBaru);
    kembali('danger', 'Gagal memperbarui data.');
}

// Berhasil dan ada foto baru: foto lama dihapus (foto contoh template tidak ikut terhapus)
if ($fotoBaru !== null) {
    hapusFoto($fotoLama);
}

kembali('success', 'Data nilai berhasil diperbarui.');