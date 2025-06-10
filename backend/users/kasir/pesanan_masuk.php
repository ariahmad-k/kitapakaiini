<?php
session_start();
include '../../koneksi.php';

// 1. OTENTIKASI & OTORISASI KASIR
if (!isset($_SESSION['user']) || $_SESSION['user']['jabatan'] !== 'kasir') {
    header('Location: ../../login.php');
    exit;
}

// 2. LOGIKA PEMROSESAN FORM (POST REQUEST)
// GANTI SELURUH BLOK if ($_SERVER...) ANDA DENGAN INI

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $redirect = true;

    // --- Aksi: Validasi Pesanan Online ---
    if (isset($_POST['validasi_pesanan'])) {
        $id_pesanan = $_POST['id_pesanan'];
        // PENTING: Ambil ID kasir yang sedang bertugas dari sesi
        $id_kasir_yang_validasi = $_SESSION['user']['id'];

        mysqli_begin_transaction($koneksi);
        try {
            // Hitung "Beban Dapur"
            $sql_beban = "SELECT SUM(dp.jumlah) AS total_item_aktif FROM detail_pesanan dp JOIN pesanan p ON dp.id_pesanan = p.id_pesanan WHERE p.status_pesanan IN ('pending', 'diproses') AND (dp.id_produk LIKE 'KB%' OR dp.id_produk LIKE 'KS%')";
            $result_beban = mysqli_query($koneksi, $sql_beban);
            $beban_dapur = mysqli_fetch_assoc($result_beban)['total_item_aktif'] ?? 0;
            
            $status_baru = ($beban_dapur < 50) ? 'diproses' : 'pending';

            // PERBAIKAN UTAMA: Query UPDATE sekarang juga mengisi id_karyawan
            $stmt_update = mysqli_prepare($koneksi, "UPDATE pesanan SET status_pesanan = ?, id_karyawan = ? WHERE id_pesanan = ? AND status_pesanan = 'menunggu_konfirmasi'");
            
            // PERBAIKAN UTAMA: Sesuaikan bind_param menjadi "sis" (string, integer, string)
            mysqli_stmt_bind_param($stmt_update, "sis", $status_baru, $id_kasir_yang_validasi, $id_pesanan);
            
            mysqli_stmt_execute($stmt_update);

            mysqli_commit($koneksi);
            $_SESSION['notif'] = ['pesan' => 'Pesanan berhasil divalidasi dan masuk antrean.', 'tipe' => 'success'];

        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            $_SESSION['notif'] = ['pesan' => 'Gagal memvalidasi pesanan. Error: ' . $e->getMessage(), 'tipe' => 'danger'];
        }
    }

    // --- Aksi: Selesaikan Pesanan ---
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

    if ($redirect) {
        header('Location: pesanan_masuk.php');
        exit;
    }
}


// 3. LOGIKA PENGAMBILAN DATA UNTUK DITAMPILKAN
// a. Ambil pesanan online yang butuh konfirmasi
$sql_online = "SELECT * FROM pesanan WHERE status_pesanan = 'menunggu_konfirmasi' ORDER BY tgl_pesanan ASC";
$result_online = mysqli_query($koneksi, $sql_online);
$pesanan_online = mysqli_fetch_all($result_online, MYSQLI_ASSOC);

// b. Ambil pesanan di antrean dapur
$sql_antrean = "SELECT * FROM pesanan WHERE status_pesanan IN ('pending', 'diproses') ORDER BY tgl_pesanan ASC";
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
        <div id="layoutSidenav_nav">
            <?php include 'inc/sidebar.php'; ?>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Pesanan Masuk & Antrean</h1>
                    <ol class="breadcrumb mb-4">
                        <!-- <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li> -->
                        <li class="breadcrumb-item active">Pesanan Masuk</li>
                    </ol>

                    <?php
                    if (isset($_SESSION['notif'])) {
                        $notif = $_SESSION['notif'];
                        echo '<div class="alert alert-' . htmlspecialchars($notif['tipe']) . ' alert-dismissible fade show" role="alert">' . htmlspecialchars($notif['pesan']) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                        unset($_SESSION['notif']);
                    }
                    ?>
                    
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white"><i class="fas fa-money-check-alt me-1"></i>Validasi Pesanan Online</div>
                                <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                                    <?php if (!empty($pesanan_online)): ?>
                                        <?php foreach ($pesanan_online as $pesanan): ?>
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?= htmlspecialchars($pesanan['nama_pemesan']) ?></h5>
                                                    <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($pesanan['id_pesanan']) ?></h6>
                                                    <p class="card-text">
                                                        <strong>Total:</strong> Rp <?= number_format($pesanan['total_harga']) ?><br>
                                                        <strong>Waktu:</strong> <?= date('H:i', strtotime($pesanan['tgl_pesanan'])) ?>
                                                    </p>
                                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#buktiBayarModal" data-bukti-bayar="<?= htmlspecialchars($pesanan['bukti_pembayaran']) ?>">
                                                        Lihat Bukti Bayar
                                                    </button>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Anda yakin pembayaran ini valid dan ingin memproses pesanan?');">
                                                        <input type="hidden" name="id_pesanan" value="<?= $pesanan['id_pesanan'] ?>">
                                                        <button type="submit" name="validasi_pesanan" class="btn btn-success btn-sm">Validasi & Proses</button>
                                                    </form>

                                                    <form method="POST" action="pesanan_dibatalkan.php" class="d-inline" onsubmit="return confirm('Anda yakin ingin menolak validasi pembayaran ini?');">
                                                        <input type="hidden" name="id_pesanan" value="<?= htmlspecialchars($pesanan['id_pesanan']) ?>">
                                                        <button type="submit" name="tolak_pembayaran" class="btn btn-danger btn-sm">Validasi Dibatalkan</button>
                                                    </form>

                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-center text-muted">Tidak ada pesanan online yang menunggu konfirmasi.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card mb-4">
                                <div class="card-header bg-warning"><i class="fas fa-blender-phone me-1"></i>Antrean Pesanan Dapur</div>
                                <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                                    <?php if (!empty($antrean_pesanan)): ?>
                                        <?php foreach ($antrean_pesanan as $antrean): ?>
                                            <?php $is_pending = $antrean['status_pesanan'] === 'pending'; ?>
                                            <div class="card mb-3 border-<?= $is_pending ? 'danger' : 'primary' ?>">
                                                <div class="card-header d-flex justify-content-between">
                                                    <strong><?= htmlspecialchars($antrean['nama_pemesan']) ?></strong>
                                                    <span class="badge bg-<?= $is_pending ? 'danger' : 'primary' ?>"><?= ucfirst($antrean['status_pesanan']) ?></span>
                                                </div>
                                                <div class="card-body">
                                                    <p>Pesanan dari: <strong><?= ucfirst($antrean['tipe_pesanan']) ?></strong></p>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Selesaikan pesanan ini?');">
                                                        <input type="hidden" name="id_pesanan" value="<?= $antrean['id_pesanan'] ?>">
                                                        <button type="submit" name="selesaikan_pesanan" class="btn btn-success btn-sm w-100">Pesanan Siap</button>
                                                    </form>
                                                    
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-center text-muted">Antrean dapur kosong.</p>
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