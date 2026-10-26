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

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    kembali('danger', 'Data tidak valid.');
}

// ---- Ambil path foto sebelum datanya dihapus ----
$stmt = mysqli_prepare($koneksi, "SELECT file_path FROM nilai WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $fotoLama);
$ditemukan = mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$ditemukan) {
    kembali('danger', 'Data tidak ditemukan atau sudah dihapus.');
}

// ---- Hapus data ----
try {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM nilai WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $terhapus = mysqli_stmt_affected_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
} catch (mysqli_sql_exception $ex) {
    kembali('danger', 'Gagal menghapus data.');
}

if (!$terhapus) {
    kembali('danger', 'Data tidak ditemukan atau sudah dihapus.');
}

// Data sudah terhapus: file fotonya ikut dihapus (foto contoh template tidak tersentuh)
hapusFoto($fotoLama);

kembali('success', 'Data nilai berhasil dihapus.');