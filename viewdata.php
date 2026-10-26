<?php
session_start();
require 'koneksi.php';
require 'fungsi.php';

// Pesan notifikasi (tampil sekali)
$pesan = $_SESSION['pesan'] ?? null;
unset($_SESSION['pesan']);

// ---------- Parameter dari URL ----------
$cari     = trim($_GET['cari'] ?? '');
$predikat = $_GET['predikat'] ?? '';
if (!in_array($predikat, daftarPredikat(), true)) {
    $predikat = '';
}
$batas = (int)($_GET['limit'] ?? 10);
if (!in_array($batas, [5, 10, 15], true)) {
    $batas = 10;
}
$urut = $_GET['urut'] ?? '';
if (!in_array($urut, ['asc', 'desc'], true)) {
    $urut = '';
}
$halaman = max(1, (int)($_GET['halaman'] ?? 1));

// ---------- Kondisi pencarian dan filter ----------
$where  = [];
$params = [];
$types  = '';

if ($cari !== '') {
    $where[]  = "nama LIKE ?";
    $params[] = "%$cari%";
    $types   .= 's';
}
if ($predikat !== '') {
    $where[]  = "predikat = ?";
    $params[] = $predikat;
    $types   .= 's';
}
$sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Pengurutan: tanpa pilihan = urut NIM; dengan pilihan = urut Nilai Akhir (NIM sebagai pembanding)
$sqlUrut = ($urut === '')
    ? 'ORDER BY nim ASC'
    : 'ORDER BY nilai_akhir ' . strtoupper($urut) . ', nim ASC';

// ---------- Hitung total ----------
$stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) FROM nilai $sqlWhere");
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $total);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// ---------- Halaman ----------
$totalHalaman = max(1, (int)ceil($total / $batas));
$halaman      = min($halaman, $totalHalaman);
$offset       = ($halaman - 1) * $batas;

// ---------- Ambil data halaman ini ----------
$stmt = mysqli_prepare($koneksi, "SELECT * FROM nilai $sqlWhere $sqlUrut LIMIT ? OFFSET ?");
$paramsData = array_merge($params, [$batas, $offset]);
mysqli_stmt_bind_param($stmt, $types . 'ii', ...$paramsData);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

$dari   = $total ? $offset + 1 : 0;
$sampai = min($offset + $batas, $total);

// ---------- Pembuat link (membawa pencarian, filter, limit, dan urutan) ----------
function url($ubah = [])
{
    global $cari, $predikat, $batas, $urut;
    $p = ['cari' => $cari, 'predikat' => $predikat, 'limit' => $batas, 'urut' => $urut, 'halaman' => 1];
    $p = array_merge($p, $ubah);
    $p = array_filter($p, function ($v) { return $v !== '' && $v !== null; });
    return '?' . http_build_query($p);
}

// Ikon panah dan arah urutan berikutnya: tanpa urutan -> naik -> turun -> tanpa urutan
$urutBerikut = ['' => 'asc', 'asc' => 'desc', 'desc' => ''][$urut];
$ikonUrut    = ['' => 'bi-arrow-down-up', 'asc' => 'bi-arrow-up', 'desc' => 'bi-arrow-down'][$urut];

// Nomor halaman yang ditampilkan (jendela 2 halaman di kiri dan kanan)
$awal  = max(1, $halaman - 2);
$akhir = min($totalHalaman, $halaman + 2);

// Dipakai modal edit dan hapus untuk kembali ke halaman yang sama
$queryString = $_SERVER['QUERY_STRING'] ?? '';
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekap Nilai Mahasiswa</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/adminlte.min.css">
</head>
<body class="bg-body-tertiary">
<div class="app-wrapper">

    <main class="app-main">
        <div class="app-content pt-3">
            <div class="container-fluid">

                <?php if ($pesan): ?>
                    <div class="alert alert-<?= e($pesan['tipe']) ?> alert-dismissible fade show" role="alert">
                        <?= e($pesan['teks']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Rekap Nilai Mahasiswa</h3>
                    </div>

                    <div class="card-body">

                        <!-- Toolbar: pencarian nama, filter predikat, tombol tambah -->
                        <form method="get" id="formFilter" class="row g-2 align-items-center mb-3">
                            <input type="hidden" name="urut" value="<?= e($urut) ?>">

                            <div class="col-md">
                                <span class="text-secondary">Direktori Nilai</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="submit" title="Cari">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    <input type="search" name="cari" class="form-control"
                                           placeholder="Cari Berdasarkan Nama" value="<?= e($cari) ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="predikat" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Predikat</option>
                                    <?php foreach (daftarPredikat() as $p): ?>
                                        <option value="<?= e($p) ?>" <?= $predikat === $p ? 'selected' : '' ?>><?= e($p) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-auto">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                    <i class="bi bi-person-plus"></i> Tambah Data Nilai
                                </button>
                            </div>
                        </form>

                        <!-- Tabel -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>NIM</th>
                                        <th>Nama Lengkap</th>
                                        <th class="text-nowrap">
                                            Nilai Akhir
                                            <a href="<?= e(url(['urut' => $urutBerikut])) ?>"
                                               class="text-decoration-none ms-1"
                                               title="Urutkan Nilai Akhir">
                                                <i class="bi <?= $ikonUrut ?>"></i>
                                            </a>
                                        </th>
                                        <th>Nilai Huruf</th>
                                        <th>Predikat</th>
                                        <th>Tgl Dibuat</th>
                                        <th>Tgl Diubah</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($total == 0): ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-secondary py-4">Data tidak ditemukan.</td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php while ($row = mysqli_fetch_assoc($hasil)): ?>
                                        <tr>
                                            <td><?= e($row['nim']) ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= e(fotoUrl($row['file_path'])) ?>" alt="Foto <?= e($row['nama']) ?>"
                                                         class="rounded-circle border me-2" width="36" height="36"
                                                         style="object-fit: cover;">
                                                    <span><?= e($row['nama']) ?></span>
                                                </div>
                                            </td>
                                            <td><?= number_format($row['nilai_akhir'], 2) ?></td>
                                            <td><?= e($row['nilai_huruf']) ?></td>
                                            <td><?= e($row['predikat']) ?></td>
                                            <td><?= e($row['created_at']) ?></td>
                                            <td><?= e($row['updated_at']) ?></td>
                                            <td class="text-end text-nowrap">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" title="Edit"
                                                        data-bs-toggle="modal" data-bs-target="#modalEdit"
                                                        data-id="<?= (int)$row['id'] ?>"
                                                        data-nim="<?= e($row['nim']) ?>"
                                                        data-nama="<?= e($row['nama']) ?>"
                                                        data-nilai="<?= e($row['nilai_akhir']) ?>"
                                                        data-foto="<?= e(fotoUrl($row['file_path'])) ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                                        data-bs-toggle="modal" data-bs-target="#modalHapus"
                                                        data-id="<?= (int)$row['id'] ?>"
                                                        data-nama="<?= e($row['nama']) ?>"
                                                        data-nim="<?= e($row['nim']) ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footer: info data, pilihan limit, dan pagination -->
                    <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <small class="text-secondary">Showing <?= $dari ?> to <?= $sampai ?> of <?= $total ?> users</small>
                            <select name="limit" form="formFilter" class="form-select form-select-sm w-auto"
                                    onchange="this.form.submit()" title="Jumlah data per halaman">
                                <?php foreach ([5, 10, 15] as $b): ?>
                                    <option value="<?= $b ?>" <?= $batas === $b ? 'selected' : '' ?>><?= $b ?> / halaman</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <ul class="pagination pagination-sm m-0">
                            <li class="page-item <?= $halaman <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= e(url(['halaman' => 1])) ?>" title="First Page">
                                    <i class="bi bi-chevron-double-left"></i><span class="d-none d-md-inline"> First</span>
                                </a>
                            </li>
                            <li class="page-item <?= $halaman <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= e(url(['halaman' => $halaman - 1])) ?>" title="Previous">
                                    <i class="bi bi-chevron-left"></i><span class="d-none d-md-inline"> Previous</span>
                                </a>
                            </li>

                            <?php for ($i = $awal; $i <= $akhir; $i++): ?>
                                <li class="page-item <?= $i === $halaman ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= e(url(['halaman' => $i])) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= $halaman >= $totalHalaman ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= e(url(['halaman' => $halaman + 1])) ?>" title="Next">
                                    <span class="d-none d-md-inline">Next </span><i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                            <li class="page-item <?= $halaman >= $totalHalaman ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= e(url(['halaman' => $totalHalaman])) ?>" title="Last Page">
                                    <span class="d-none d-md-inline">Last </span><i class="bi bi-chevron-double-right"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- MODAL TAMBAH -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="judulModalTambah" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="proses_tambah.php" method="post" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="judulModalTambah">Tambahkan Data Nilai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tambahNim" class="form-label">NIM</label>
                        <input type="text" class="form-control" id="tambahNim" name="nim"
                               placeholder="mis. 3202316041" maxlength="15" pattern="[0-9]{1,15}" required>
                    </div>
                    <div class="mb-3">
                        <label for="tambahNama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="tambahNama" name="nama"
                               placeholder="mis. Jane Doe" maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="tambahFoto" class="form-label">Foto (opsional)</label>
                        <input type="file" class="form-control" id="tambahFoto" name="foto"
                               accept="image/jpeg,image/png,image/gif,image/webp">
                        <div class="form-text">JPG, PNG, GIF, atau WEBP. Maksimal 2 MB.</div>
                    </div>
                    <div class="mb-3">
                        <label for="tambahNilai" class="form-label">Nilai Akhir</label>
                        <input type="number" class="form-control" id="tambahNilai" name="nilai_akhir"
                               placeholder="0 - 100" min="0" max="100" step="0.01" required>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label">Nilai Huruf</label>
                            <input type="text" id="tambahHuruf" class="form-control" readonly>
                        </div>
                        <div class="col-8">
                            <label class="form-label">Predikat</label>
                            <input type="text" id="tambahPredikat" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-text mt-2">Nilai huruf dan predikat dihitung otomatis.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="judulModalEdit" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="proses_edit.php" method="post" enctype="multipart/form-data" class="modal-content">
                <input type="hidden" name="id" id="editId">
                <input type="hidden" name="kembali" value="<?= e($queryString) ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="judulModalEdit">Edit Data Nilai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editNim" class="form-label">NIM</label>
                        <input type="text" class="form-control" id="editNim" name="nim"
                               maxlength="15" pattern="[0-9]{1,15}" required>
                    </div>
                    <div class="mb-3">
                        <label for="editNama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="editNama" name="nama"
                               maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="editFoto" class="form-label">Ganti Foto (opsional)</label>
                        <div class="d-flex align-items-center gap-3">
                            <img id="editFotoLama" src="" alt="Foto saat ini" class="rounded-circle border"
                                 width="48" height="48" style="object-fit: cover;">
                            <input type="file" class="form-control" id="editFoto" name="foto"
                                   accept="image/jpeg,image/png,image/gif,image/webp">
                        </div>
                        <div class="form-text">Kosongkan jika tidak ingin mengganti foto.</div>
                    </div>
                    <div class="mb-3">
                        <label for="editNilai" class="form-label">Nilai Akhir</label>
                        <input type="number" class="form-control" id="editNilai" name="nilai_akhir"
                               min="0" max="100" step="0.01" required>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label">Nilai Huruf</label>
                            <input type="text" id="editHuruf" class="form-control" readonly>
                        </div>
                        <div class="col-8">
                            <label class="form-label">Predikat</label>
                            <input type="text" id="editPredikat" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-text mt-2">Nilai huruf dan predikat dihitung otomatis.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL HAPUS -->
    <div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="judulModalHapus" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="proses_hapus.php" method="post" class="modal-content">
                <input type="hidden" name="id" id="hapusId">
                <input type="hidden" name="kembali" value="<?= e($queryString) ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="judulModalHapus">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1">Anda yakin ingin menghapus data nilai berikut?</p>
                    <p class="mb-0 fw-bold"><span id="hapusNim"></span> - <span id="hapusNama"></span></p>
                    <small class="text-secondary">Tindakan ini tidak dapat dibatalkan.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">Web Programming Semester 5</div>
        <strong>Copyright &copy; by [NAMA ANDA] 2026 <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.</strong>
        All rights reserved.
    </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
<script src="js/adminlte.min.js"></script>
<script>
    // Konversi nilai (skala resmi dosen). Server tetap menghitung ulang saat data disimpan.
    function konversi(n) {
        if (isNaN(n) || n < 0 || n > 100) return { huruf: '', predikat: '' };
        if (n > 80.50) return { huruf: 'A', predikat: 'Sangat Memuaskan' };
        if (n > 65.50) return { huruf: 'B', predikat: 'Memuaskan' };
        if (n > 50.50) return { huruf: 'C', predikat: 'Cukup Memuaskan' };
        if (n > 34.50) return { huruf: 'D', predikat: 'Kurang Memuaskan' };
        return { huruf: 'E', predikat: 'Tidak Lulus' };
    }

    function pratinjau(inputNilai, inputHuruf, inputPredikat) {
        var hasil = konversi(parseFloat(inputNilai.value));
        inputHuruf.value = hasil.huruf;
        inputPredikat.value = hasil.predikat;
    }

    // Modal tambah: huruf dan predikat muncul saat nilai diketik
    var tambahNilai = document.getElementById('tambahNilai');
    tambahNilai.addEventListener('input', function () {
        pratinjau(tambahNilai, document.getElementById('tambahHuruf'), document.getElementById('tambahPredikat'));
    });

    // Modal edit: isi dengan data dari tombol yang diklik
    var editNilai = document.getElementById('editNilai');
    document.getElementById('modalEdit').addEventListener('show.bs.modal', function (event) {
        var tombol = event.relatedTarget;
        document.getElementById('editId').value   = tombol.getAttribute('data-id');
        document.getElementById('editNim').value  = tombol.getAttribute('data-nim');
        document.getElementById('editNama').value = tombol.getAttribute('data-nama');
        editNilai.value = tombol.getAttribute('data-nilai');
        document.getElementById('editFotoLama').src = tombol.getAttribute('data-foto');
        document.getElementById('editFoto').value = '';
        pratinjau(editNilai, document.getElementById('editHuruf'), document.getElementById('editPredikat'));
    });
    editNilai.addEventListener('input', function () {
        pratinjau(editNilai, document.getElementById('editHuruf'), document.getElementById('editPredikat'));
    });

    // Modal hapus: isi dengan data dari tombol yang diklik
    document.getElementById('modalHapus').addEventListener('show.bs.modal', function (event) {
        var tombol = event.relatedTarget;
        document.getElementById('hapusId').value         = tombol.getAttribute('data-id');
        document.getElementById('hapusNim').textContent  = tombol.getAttribute('data-nim');
        document.getElementById('hapusNama').textContent = tombol.getAttribute('data-nama');
    });
</script>
</body>
</html>