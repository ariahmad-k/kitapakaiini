<?php
session_start();
include '../../../koneksi.php'; // Path dari users/superadmin/

// 1. OTENTIKASI & OTORISASI
if (!isset($_SESSION['user']) || $_SESSION['user']['jabatan'] !== 'owner') {
    header('Location: ../../../login.php');
    exit;
}

// --- Perubahan: Logika untuk menangani rentang tanggal ---
$tanggal_mulai = date('Y-m-d'); // Default hari ini
$tanggal_selesai = date('Y-m-d'); // Default hari ini

if (isset($_GET['tanggal_mulai']) && !empty($_GET['tanggal_mulai'])) {
    $tanggal_mulai = $_GET['tanggal_mulai'];
}
if (isset($_GET['tanggal_selesai']) && !empty($_GET['tanggal_selesai'])) {
    $tanggal_selesai = $_GET['tanggal_selesai'];
}

// 3. LOGIKA PENGAMBILAN DATA UNTUK KARTU RINGKASAN
// --- Perubahan: Query sekarang menggunakan BETWEEN ? AND ? ---

// a. Total Pendapatan
$stmt_pendapatan = mysqli_prepare($koneksi, "SELECT SUM(total_hargaall) AS total FROM pesanan_kasir WHERE DATE(tgl_pesanan) BETWEEN ? AND ?");
mysqli_stmt_bind_param($stmt_pendapatan, "ss", $tanggal_mulai, $tanggal_selesai);
mysqli_stmt_execute($stmt_pendapatan);
$result_pendapatan = mysqli_stmt_get_result($stmt_pendapatan);
$total_pendapatan = mysqli_fetch_assoc($result_pendapatan)['total'] ?? 0;

// b. Jumlah Transaksi
$stmt_transaksi = mysqli_prepare($koneksi, "SELECT COUNT(id_pesanan) AS jumlah FROM pesanan_kasir WHERE DATE(tgl_pesanan) BETWEEN ? AND ?");
mysqli_stmt_bind_param($stmt_transaksi, "ss", $tanggal_mulai, $tanggal_selesai);
mysqli_stmt_execute($stmt_transaksi);
$result_transaksi = mysqli_stmt_get_result($stmt_transaksi);
$jumlah_transaksi = mysqli_fetch_assoc($result_transaksi)['jumlah'] ?? 0;

// c. Total Item Terjual
$stmt_item = mysqli_prepare($koneksi, "SELECT SUM(dp.jumlah) AS total FROM detail_pesanan dp JOIN pesanan_kasir pk ON dp.id_pesanan = pk.id_pesanan WHERE DATE(pk.tgl_pesanan) BETWEEN ? AND ?");
mysqli_stmt_bind_param($stmt_item, "ss", $tanggal_mulai, $tanggal_selesai);
mysqli_stmt_execute($stmt_item);
$result_item = mysqli_stmt_get_result($stmt_item);
$total_item_terjual = mysqli_fetch_assoc($result_item)['total'] ?? 0;

// 4. LOGIKA PENGAMBILAN DATA UNTUK TABEL RINCIAN
$sql_rincian = "SELECT pk.id_pesanan, pk.tgl_pesanan, pk.total_hargaall, k.nama AS nama_kasir FROM pesanan_kasir pk JOIN karyawan k ON pk.id_karyawan = k.id_karyawan WHERE DATE(pk.tgl_pesanan) BETWEEN ? AND ? ORDER BY pk.tgl_pesanan DESC";
$stmt_rincian = mysqli_prepare($koneksi, $sql_rincian);
mysqli_stmt_bind_param($stmt_rincian, "ss", $tanggal_mulai, $tanggal_selesai);
mysqli_stmt_execute($stmt_rincian);
$result_rincian = mysqli_stmt_get_result($stmt_rincian);
$daftar_transaksi = [];
while ($row = mysqli_fetch_assoc($result_rincian)) {
    $daftar_transaksi[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Laporan Penjualan - Owner</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../../../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <?php include '../inc/navbar.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <?php include '../inc/sidebar.php'; ?>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Laporan Penjualan</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Laporan Penjualan</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header"><i class="fas fa-filter me-1"></i>Filter Laporan</div>
                        <div class="card-body">
                            <form method="GET" action="laporan_penjualan.php" id="filterForm">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai:</label>
                                        <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="<?php echo htmlspecialchars($tanggal_mulai); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai:</label>
                                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="<?php echo htmlspecialchars($tanggal_selesai); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary w-100">Tampilkan Laporan</button>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-today">Hari Ini</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-yesterday">Kemarin</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-last7days">7 Hari Terakhir</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-this-month">Bulan Ini</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <h4 class="mt-4">Ringkasan Periode <?php echo date('d M Y', strtotime($tanggal_mulai)) . ' - ' . date('d M Y', strtotime($tanggal_selesai)); ?></h4>
                    <div class="row">
                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <div class="fs-5">Total Pendapatan</div>
                                    <div class="fs-3 fw-bold">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">
                                    <div class="fs-5">Jumlah Transaksi</div>
                                    <div class="fs-3 fw-bold"><?php echo $jumlah_transaksi; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-warning text-dark mb-4">
                                <div class="card-body">
                                    <div class="fs-5">Total Item Terjual</div>
                                    <div class="fs-3 fw-bold"><?php echo $total_item_terjual; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header"><i class="fas fa-table me-1"></i>Rincian Transaksi</div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>No. Pesanan</th>
                                        <th>Total</th>
                                        <th>Kasir</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($daftar_transaksi as $transaksi): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($transaksi['tgl_pesanan'])); ?></td>
                                        <td><?php echo date('H:i:s', strtotime($transaksi['tgl_pesanan'])); ?></td>
                                        <td><?php echo htmlspecialchars($transaksi['id_pesanan']); ?></td>
                                        <td>Rp <?php echo number_format($transaksi['total_hargaall'], 0, ',', '.'); ?></td>
                                        <td><?php echo htmlspecialchars($transaksi['nama_kasir']); ?></td>
                                        <td>
                                            <a href="../../kasir/detail_pesanan.php?id=<?php echo $transaksi['id_pesanan']; ?>" class="btn btn-sm btn-info" target="_blank">Detail</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; KueBalok 2025</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../../../js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="../../../js/datatables-simple-demo.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tglMulai = document.getElementById('tanggal_mulai');
            const tglSelesai = document.getElementById('tanggal_selesai');
            const form = document.getElementById('filterForm');

            // Fungsi untuk format tanggal YYYY-MM-DD
            const formatDate = (date) => {
                let d = new Date(date),
                    month = '' + (d.getMonth() + 1),
                    day = '' + d.getDate(),
                    year = d.getFullYear();
                if (month.length < 2) month = '0' + month;
                if (day.length < 2) day = '0' + day;
                return [year, month, day].join('-');
            }

            const today = new Date();

            document.getElementById('btn-today').addEventListener('click', function() {
                tglMulai.value = formatDate(today);
                tglSelesai.value = formatDate(today);
                form.submit();
            });

            document.getElementById('btn-yesterday').addEventListener('click', function() {
                let yesterday = new Date();
                yesterday.setDate(today.getDate() - 1);
                tglMulai.value = formatDate(yesterday);
                tglSelesai.value = formatDate(yesterday);
                form.submit();
            });
            
            document.getElementById('btn-last7days').addEventListener('click', function() {
                let sevenDaysAgo = new Date();
                sevenDaysAgo.setDate(today.getDate() - 6);
                tglMulai.value = formatDate(sevenDaysAgo);
                tglSelesai.value = formatDate(today);
                form.submit();
            });

            document.getElementById('btn-this-month').addEventListener('click', function() {
                let firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                tglMulai.value = formatDate(firstDayOfMonth);
                tglSelesai.value = formatDate(today);
                form.submit();
            });
        });
    </script>
</body>
</html>