<?php
session_start();
include '../../koneksi.php';

// 1. OTENTIKASI & OTORISASI KASIR
if (!isset($_SESSION['user']) || $_SESSION['user']['jabatan'] !== 'kasir') {
    header('Location: ../../login.php');
    exit;
}

// 2. LOGIKA PEMROSESAN SEMUA AKSI (POST REQUEST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Aksi: Validasi Pesanan Online
    if (isset($_POST['validasi_pesanan'])) {
        $id_pesanan = $_POST['id_pesanan'];
        $id_kasir_yang_validasi = $_SESSION['user']['id'];

        mysqli_begin_transaction($koneksi);
        try {
            $sql_beban = "SELECT SUM(dp.jumlah) AS total_item_aktif FROM detail_pesanan dp JOIN pesanan p ON dp.id_pesanan = p.id_pesanan WHERE p.status_pesanan IN ('pending', 'diproses') AND (dp.id_produk LIKE 'KB%' OR dp.id_produk LIKE 'KS%')";
            $result_beban = mysqli_query($koneksi, $sql_beban);
            $beban_dapur = mysqli_fetch_assoc($result_beban)['total_item_aktif'] ?? 0;
            $status_baru = ($beban_dapur < 50) ? 'diproses' : 'pending';

            $stmt_update = mysqli_prepare($koneksi, "UPDATE pesanan SET status_pesanan = ?, id_karyawan = ? WHERE id_pesanan = ? AND status_pesanan = 'menunggu_konfirmasi'");
            mysqli_stmt_bind_param($stmt_update, "sis", $status_baru, $id_kasir_yang_validasi, $id_pesanan);
            mysqli_stmt_execute($stmt_update);

            mysqli_commit($koneksi);
            $_SESSION['notif'] = [
                'pesan' => "Pesanan #$id_pesanan berhasil divalidasi.",
                'tipe' => 'success',
                'print_url' => "detail_pesanan.php?id=$id_pesanan"
            ];
        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            $_SESSION['notif'] = ['pesan' => 'Gagal memvalidasi pesanan: ' . $e->getMessage(), 'tipe' => 'danger'];
        }
    }

    // Aksi: Selesaikan Pesanan
    if (isset($_POST['selesaikan_pesanan'])) {
        $id_pesanan = $_POST['id_pesanan'];
        $stmt_selesai = mysqli_prepare($koneksi, "UPDATE pesanan SET status_pesanan = 'selesai' WHERE id_pesanan = ?");
        mysqli_stmt_bind_param($stmt_selesai, "s", $id_pesanan);
        if (mysqli_stmt_execute($stmt_selesai)) {
            $_SESSION['notif'] = ['pesan' => "Pesanan #$id_pesanan telah ditandai selesai.", 'tipe' => 'info'];
        } else {
            $_SESSION['notif'] = ['pesan' => 'Gagal menyelesaikan pesanan.', 'tipe' => 'danger'];
        }
    }

    // Aksi: Batalkan Pesanan (Soft Delete)
    if (isset($_POST['batalkan_pesanan'])) {
        $id_pesanan = $_POST['id_pesanan'];
        $stmt_batal = mysqli_prepare($koneksi, "UPDATE pesanan SET status_pesanan = 'dibatalkan' WHERE id_pesanan = ? AND status_pesanan = 'menunggu_konfirmasi'");
        mysqli_stmt_bind_param($stmt_batal, "s", $id_pesanan);
        if (mysqli_stmt_execute($stmt_batal)) {
            $_SESSION['notif'] = ['pesan' => "Pesanan #$id_pesanan telah dibatalkan.", 'tipe' => 'warning'];
        } else {
            $_SESSION['notif'] = ['pesan' => 'Gagal membatalkan pesanan.', 'tipe' => 'danger'];
        }
    }

    header('Location: pesanan_masuk.php');
    exit;
}

// 3. LOGIKA PENGAMBILAN DATA UNTUK DITAMPILKAN
$sql_online = "SELECT * FROM pesanan WHERE status_pesanan = 'menunggu_konfirmasi' ORDER BY tgl_pesanan ASC";
$result_online = mysqli_query($koneksi, $sql_online);
$pesanan_online = mysqli_fetch_all($result_online, MYSQLI_ASSOC);

$sql_antrean = "SELECT * FROM pesanan WHERE status_pesanan IN ('pending', 'diproses') ORDER BY FIELD(status_pesanan, 'diproses', 'pending'), tgl_pesanan ASC";
$result_antrean = mysqli_query($koneksi, $sql_antrean);
$antrean_pesanan = mysqli_fetch_all($result_antrean, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Pesanan Masuk & Antrean - Kasir</title>
    <link href="../../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <?php include 'inc/navbar.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav"><?php include 'inc/sidebar.php'; ?></div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Pesanan Masuk & Antrean</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pesanan Masuk</li>
                    </ol>

                    <?php
                    // Logika notifikasi untuk menampilkan tombol cetak
                    if (isset($_SESSION['notif'])) {
                        $notif = $_SESSION['notif'];
                        echo '<div class="alert alert-' . htmlspecialchars($notif['tipe']) . ' alert-dismissible fade show" role="alert">';
                        echo '<span>' . htmlspecialchars($notif['pesan']) . '</span>';
                        if (isset($notif['print_url'])) {
                            echo '<a href="' . htmlspecialchars($notif['print_url']) . '" target="_blank" class="btn btn-sm btn-light ms-3 fw-bold"><i class="fas fa-print"></i> Cetak Struk Dapur</a>';
                        }
                        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                        unset($_SESSION['notif']);
                    }
                    ?>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white"><i class="fas fa-money-check-alt me-1"></i>Validasi Pesanan Online (<?= count($pesanan_online) ?>)</div>
                                <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                                    <?php if (!empty($pesanan_online)): ?>
                                        <?php foreach ($pesanan_online as $pesanan): ?>
                                            <div class="card mb-3 shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <h5 class="card-title mb-0"><?= htmlspecialchars($pesanan['nama_pemesan']) ?></h5>
                                                        <small class="text-muted"><?= date('H:i', strtotime($pesanan['tgl_pesanan'])) ?></small>
                                                    </div>
                                                    <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($pesanan['id_pesanan']) ?></h6>

                                                    <ul class="list-unstyled mt-2 mb-2">
                                                        <?php
                                                        $stmt_detail = mysqli_prepare($koneksi, "SELECT dp.jumlah, p.nama_produk FROM detail_pesanan dp JOIN produk p ON dp.id_produk = p.id_produk WHERE dp.id_pesanan = ?");
                                                        mysqli_stmt_bind_param($stmt_detail, "s", $pesanan['id_pesanan']);
                                                        mysqli_stmt_execute($stmt_detail);
                                                        $result_detail = mysqli_stmt_get_result($stmt_detail);
                                                        while ($item = mysqli_fetch_assoc($result_detail)):
                                                        ?>
                                                            <li><span class="badge bg-secondary me-2"><?= htmlspecialchars($item['jumlah']) ?>x</span> <?= htmlspecialchars($item['nama_produk']) ?></li>
                                                        <?php endwhile;
                                                        mysqli_stmt_close($stmt_detail); ?>
                                                    </ul>
                                                    <p class="card-text mb-3 border-top pt-2"><strong>Total: Rp <?= number_format($pesanan['total_harga']) ?></strong></p>

                                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#buktiBayarModal" data-bukti-bayar="<?= htmlspecialchars($pesanan['bukti_pembayaran']) ?>">Bukti Bayar</button>
                                                    <form method="POST" action="" class="d-inline" onsubmit="return confirm('Validasi pesanan ini?');"><input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>"><button type="submit" name="validasi_pesanan" class="btn btn-success btn-sm">Validasi</button></form>
                                                    <form method="POST" action="" class="d-inline" onsubmit="return confirm('Batalkan pesanan ini?');"><input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>"><button type="submit" name="batalkan_pesanan" class="btn btn-danger btn-sm">Batalkan</button></form>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-center text-muted py-5">Tidak ada pesanan online yang menunggu konfirmasi.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card mb-4">
                                <div class="card-header bg-warning"><i class="fas fa-blender-phone me-1"></i>Antrean Pesanan Dapur (<?= count($antrean_pesanan) ?>)</div>
                                <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                                    <?php if (!empty($antrean_pesanan)): ?>
                                        <?php foreach ($antrean_pesanan as $antrean): ?>
                                            <?php $is_pending = $antrean['status_pesanan'] === 'pending'; ?>
                                            <div class="card mb-3 border-2 shadow-sm <?= $is_pending ? 'border-danger' : 'border-primary' ?>">
                                                <div class="card-header d-flex justify-content-between align-items-center"><strong><?= htmlspecialchars($antrean['nama_pemesan']) ?></strong><span class="badge bg-<?= $is_pending ? 'danger' : 'primary' ?>"><?= ucfirst($antrean['status_pesanan']) ?></span></div>
                                                <div class="card-body">
                                                    <p class="mb-1"><strong>ID:</strong> <?= htmlspecialchars($antrean['id_pesanan']) ?></p>
                                                    <p><strong>Sumber:</strong> <?= ucfirst($antrean['tipe_pesanan']) ?></p>
                                                    <strong>Pesanan:</strong>
                                                    <ul class="list-unstyled mt-1">
                                                        <?php
                                                        $stmt_detail_antrean = mysqli_prepare($koneksi, "SELECT dp.jumlah, p.nama_produk FROM detail_pesanan dp JOIN produk p ON dp.id_produk = p.id_produk WHERE dp.id_pesanan = ?");
                                                        mysqli_stmt_bind_param($stmt_detail_antrean, "s", $antrean['id_pesanan']);
                                                        mysqli_stmt_execute($stmt_detail_antrean);
                                                        $result_detail_antrean = mysqli_stmt_get_result($stmt_detail_antrean);
                                                        while ($item_antrean = mysqli_fetch_assoc($result_detail_antrean)):
                                                        ?>
                                                            <li><span class="badge bg-dark me-2"><?= htmlspecialchars($item_antrean['jumlah']) ?>x</span> <?= htmlspecialchars($item_antrean['nama_produk']) ?></li>
                                                        <?php endwhile;
                                                        mysqli_stmt_close($stmt_detail_antrean); ?>
                                                    </ul>
                                                    <form method="POST" action="" class="d-grid mt-3" onsubmit="return confirm('Selesaikan pesanan ini?');"><input type="hidden" name="id_pesanan" value="<?= $antrean['id_pesanan'] ?>"><button type="submit" name="selesaikan_pesanan" class="btn btn-success">Selesaikan Pesanan</button></form>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-center text-muted py-5">Antrean dapur kosong.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="buktiBayarModal" tabindex="-1" aria-labelledby="buktiBayarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="buktiBayarModalLabel">Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="gambarBuktiBayar" src="" class="img-fluid" alt="Bukti Pembayaran">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/scripts.js"></script>
    <script>
        const buktiBayarModal = document.getElementById('buktiBayarModal');
        buktiBayarModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const namaFileBukti = button.getAttribute('data-bukti-bayar');
            const gambarModal = buktiBayarModal.querySelector('#gambarBuktiBayar');
            // Ganti path ini jika lokasi folder upload Anda berbeda
            gambarModal.src = '../../assets/img/bukti_bayar/' + namaFileBukti;
        });
    </script>
</body>

</html>