<?php
session_start();
require 'koneksi.php';
require 'fungsi.php';

function kembali($tipe, $teks)
{
    $_SESSION['pesan'] = ['tipe' => $tipe, 'teks' => $teks];
    header('Location: viewdata.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: viewdata.php');
    exit;
}

$nim   = trim($_POST['nim'] ?? '');
$nama  = trim($_POST['nama'] ?? '');
$nilai = $_POST['nilai_akhir'] ?? '';

// ---- Validasi di sisi server ----
if (!preg_match('/^[0-9]{1,15}$/', $nim)) {
    kembali('danger', 'NIM harus berupa angka (maksimal 15 digit).');
}
if ($nama === '' || mb_strlen($nama) > 100) {
    kembali('danger', 'Nama wajib diisi (maksimal 100 karakter).');
}
if (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
    kembali('danger', 'Nilai akhir harus berupa angka antara 0 sampai 100.');
}

// ---- Cek NIM ganda ----
$cek = mysqli_prepare($koneksi, "SELECT id FROM nilai WHERE nim = ?");
mysqli_stmt_bind_param($cek, 's', $nim);
mysqli_stmt_execute($cek);
mysqli_stmt_store_result($cek);
$sudahAda = mysqli_stmt_num_rows($cek) > 0;
mysqli_stmt_close($cek);

if ($sudahAda) {
    kembali('danger', "NIM $nim sudah terdaftar.");
}

// ---- Proses foto (opsional) ----
$foto = uploadFoto($_FILES['foto'] ?? null, $nim);
if (!$foto['ok']) {
    kembali('danger', $foto['error']);
}
$filePath = $foto['path'];   // null kalau tidak ada foto

// ---- Simpan ----
$nilai = round((float)$nilai, 2);
$k     = konversiNilai($nilai);

try {
    $stmt = mysqli_prepare($koneksi,
        "INSERT INTO nilai (nim, nama, file_path, nilai_akhir, nilai_huruf, predikat) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sssdss', $nim, $nama, $filePath, $nilai, $k['huruf'], $k['predikat']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} catch (mysqli_sql_exception $ex) {
    // Simpan gagal: foto yang sudah terunggah ikut dibersihkan
    hapusFoto($filePath);
    kembali('danger', 'Gagal menyimpan data.');
}

kembali('success', 'Data nilai berhasil ditambahkan.');