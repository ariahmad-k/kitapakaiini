<?php
session_start();
include('../../koneksi.php');

// Otentikasi & Otorisasi
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['jabatan'], ['admin', 'owner'])) {
    header('Location: ../../login.php');
    exit;
}

// === Query untuk Kartu Notifikasi Stok Kritis ===
$batas_stok_kritis = 20; // Tentukan batas stok dianggap kritis
$sql_stok_kritis = "SELECT nama_produk, stok FROM produk WHERE status_produk = 'aktif' AND stok < ? ORDER BY stok ASC";
$stmt_stok = mysqli_prepare($koneksi, $sql_stok_kritis);
mysqli_stmt_bind_param($stmt_stok, "i", $batas_stok_kritis);
mysqli_stmt_execute($stmt_stok);
$result_stok = mysqli_stmt_get_result($stmt_stok);
$produk_stok_kritis = [];
while ($row = mysqli_fetch_assoc($result_stok)) {
    $produk_stok_kritis[] = $row;
}

// === Query untuk Kartu Ringkasan Produk ===
$sql_total_aktif = "SELECT COUNT(id_produk) as total FROM produk WHERE status_produk = 'aktif'";
$result_total_aktif = mysqli_query($koneksi, $sql_total_aktif);
$total_menu_aktif = mysqli_fetch_assoc($result_total_aktif)['total'] ?? 0;

$sql_total_nonaktif = "SELECT COUNT(id_produk) as total FROM produk WHERE status_produk = 'tidak aktif'";
$result_total_nonaktif = mysqli_query($koneksi, $sql_total_nonaktif);
$total_menu_nonaktif = mysqli_fetch_assoc($result_total_nonaktif)['total'] ?? 0;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Dashboard - Admin</title>
    <link href="../../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <?php include 'inc/navbar.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <?php include 'inc/sidebar.php'; ?>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard Admin</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Selamat Datang, <?php echo htmlspecialchars($_SESSION['user']['nama']); ?>!</li>
                    </ol>

                    <div class="row">
                        <div class="col-xl-6 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body d-flex align-items-center">
                                    <i class="fas fa-utensils fa-3x me-3"></i>
                                    <div>
                                        <h4>Manajemen Menu</h4>
                                        <p class="mb-0">Tambah, edit, atau hapus produk.</p>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="data_menu.php">Kelola Sekarang</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="card bg-info text-white mb-4">
                                <div class="card-body d-flex align-items-center">
                                    <i class="fas fa-boxes fa-3x me-3"></i>
                                    <div>
                                        <h4>Manajemen Stok</h4>
                                        <p class="mb-0">Update stok harian produk.</p>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="manajemen_stok.php">Kelola Sekarang</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header bg-danger text-white">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Notifikasi Stok Kritis (Di Bawah <?php echo $batas_stok_kritis; ?>)
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($produk_stok_kritis)): ?>
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($produk_stok_kritis as $produk): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <?php echo htmlspecialchars($produk['nama_produk']); ?>
                                                    <span class="badge bg-danger rounded-pill"><?php echo $produk['stok']; ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <div class="alert alert-success mb-0">
                                            <i class="fas fa-check-circle"></i> Semua stok produk dalam kondisi aman.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-chart-pie me-1"></i>
                                    Ringkasan Produk
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                        <span>Total Menu Aktif</span>
                                        <strong class="fs-4"><?php echo $total_menu_aktif; ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Total Menu Tidak Aktif</span>
                                        <strong class="fs-4"><?php echo $total_menu_nonaktif; ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
            </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../../js/scripts.js"></script>
</body>
</html>