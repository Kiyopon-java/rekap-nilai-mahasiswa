<?php
// ============================================================
// fungsi.php - fungsi bantu: konversi nilai, escape HTML, foto
// ============================================================

const FOTO_DIR  = 'uploads/';            // folder foto unggahan (relatif terhadap folder proyek)
const FOTO_MAKS = 2 * 1024 * 1024;       // ukuran foto maksimal: 2 MB
const FOTO_BAWAAN = 'assets/img/avatar.png'; // foto pengganti kalau foto tidak ada

// Mengubah nilai angka menjadi huruf dan predikat (skala resmi dosen)
function konversiNilai($nilai)
{
    $nilai = (float)$nilai;

    if ($nilai > 80.50) {
        return ['huruf' => 'A', 'predikat' => 'Sangat Memuaskan'];
    } elseif ($nilai > 65.50) {
        return ['huruf' => 'B', 'predikat' => 'Memuaskan'];
    } elseif ($nilai > 50.50) {
        return ['huruf' => 'C', 'predikat' => 'Cukup Memuaskan'];
    } elseif ($nilai > 34.50) {
        return ['huruf' => 'D', 'predikat' => 'Kurang Memuaskan'];
    } else {
        return ['huruf' => 'E', 'predikat' => 'Tidak Lulus'];
    }
}

// Daftar predikat untuk dropdown filter
function daftarPredikat()
{
    return ['Sangat Memuaskan', 'Memuaskan', 'Cukup Memuaskan', 'Kurang Memuaskan', 'Tidak Lulus'];
}

// Mengamankan teks sebelum ditampilkan di HTML
function e($teks)
{
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

// Alamat foto yang aman ditampilkan: kalau file tidak ada, pakai foto bawaan
function fotoUrl($path)
{
    if (!empty($path) && is_file(__DIR__ . '/' . $path)) {
        return $path;
    }
    return FOTO_BAWAAN;
}

// Memproses foto yang diunggah.
// Hasil: ['ok' => true/false, 'path' => 'uploads/xxx.jpg' atau null, 'error' => pesan]
function uploadFoto($berkas, $nim)
{
    // Tidak ada foto yang dipilih: bukan kesalahan, foto bersifat opsional
    if (!isset($berkas) || $berkas['error'] === UPLOAD_ERR_NO_FILE) {
        return ['ok' => true, 'path' => null, 'error' => ''];
    }

    if ($berkas['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'path' => null, 'error' => 'Foto gagal diunggah.'];
    }

    if ($berkas['size'] > FOTO_MAKS) {
        return ['ok' => false, 'path' => null, 'error' => 'Ukuran foto maksimal 2 MB.'];
    }

    // Periksa isi file yang sebenarnya, bukan hanya namanya
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($berkas['tmp_name']);
    $diizinkan = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    if (!isset($diizinkan[$mime]) || @getimagesize($berkas['tmp_name']) === false) {
        return ['ok' => false, 'path' => null, 'error' => 'File harus berupa gambar (JPG, PNG, GIF, atau WEBP).'];
    }

    $folder = __DIR__ . '/' . FOTO_DIR;
    if (!is_dir($folder) && !mkdir($folder, 0755, true)) {
        return ['ok' => false, 'path' => null, 'error' => 'Folder uploads tidak dapat dibuat.'];
    }

    // Nama file dibuat sendiri, nama asli dari pengguna tidak dipakai
    $nama = preg_replace('/[^0-9]/', '', $nim) . '_' . bin2hex(random_bytes(4)) . '.' . $diizinkan[$mime];

    if (!move_uploaded_file($berkas['tmp_name'], $folder . $nama)) {
        return ['ok' => false, 'path' => null, 'error' => 'Foto tidak dapat disimpan.'];
    }

    return ['ok' => true, 'path' => FOTO_DIR . $nama, 'error' => ''];
}

// Menghapus file foto. Hanya file di dalam folder uploads yang boleh dihapus,
// jadi foto contoh dari assets/img tidak akan ikut terhapus.
function hapusFoto($path)
{
    if (empty($path) || strpos($path, FOTO_DIR) !== 0) {
        return;
    }
    $nama = substr($path, strlen(FOTO_DIR));
    if ($nama === '' || $nama !== basename($nama)) {
        return;
    }
    $lengkap = __DIR__ . '/' . FOTO_DIR . $nama;
    if (is_file($lengkap)) {
        @unlink($lengkap);
    }
}