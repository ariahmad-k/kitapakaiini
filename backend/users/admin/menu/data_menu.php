<?php
session_start();
include('../../../koneksi.php');

// Cek hak akses
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['jabatan'], ['admin', 'owner'])) {
    header('Location: ../../login.php');
    exit;
}

// PERBAIKAN: Dua query terpisah untuk produk aktif dan tidak aktif
$produk_aktif = mysqli_query($koneksi, "SELECT * FROM produk WHERE status_produk = 'aktif' ORDER BY kategori, nama_produk");
$produk_tidak_aktif = mysqli_query($koneksi, "SELECT * FROM produk WHERE status_produk = 'tidak aktif' ORDER BY kategori, nama_produk");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Data Menu - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../../../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <?php include "../inc/navbar.php"; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <?php include "../inc/sidebar.php"; ?>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Data Menu</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Menu</li>
                    </ol>

                    <?php
                    // Blok Notifikasi
                    if (isset($_SESSION['notif'])) {
                        $notif = $_SESSION['notif'];
                        echo '<div class="alert alert-' . htmlspecialchars($notif['tipe']) . ' alert-dismissible fade show" role="alert">' . htmlspecialchars($notif['pesan']) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                        unset($_SESSION['notif']);
                    }
                    ?>
                    
                    <div class="mb-3">
                        <a href="tambah_menu.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Menu</a>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#aktif" type="button" role="tab" aria-controls="aktif" aria-selected="true">
                                        <i class="fas fa-check-circle me-1"></i>Produk Aktif
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="nonaktif-tab" data-bs-toggle="tab" data-bs-target="#nonaktif" type="button" role="tab" aria-controls="nonaktif" aria-selected="false">
                                        <i class="fas fa-eye-slash me-1"></i>Produk Tidak Aktif
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="aktif" role="tabpanel" aria-labelledby="aktif-tab">
                                    <table class="table table-striped table-bordered datatable-class">
                                        <thead>
                                            <tr><th>ID</th><th>Nama</th><th>Harga</th><th>Kategori</th><th>Gambar</th><th>Aksi</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($produk_aktif)) { ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['id_produk']) ?></td>
                                                <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                                                <td>Rp <?= number_format($row['harga']) ?></td>
                                                <td><?= htmlspecialchars(ucfirst($row['kategori'])) ?></td>
                                                <td><img src="../../../assets/img/produk/<?= htmlspecialchars($row['poto_produk']) ?>" width="100" alt="<?= htmlspecialchars($row['nama_produk']) ?>"></td>
                                                <td>
                                                    <a href="menu_edit.php?id_produk=<?= $row['id_produk'] ?>" class="btn btn-primary btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                                    <a href="menu_hapus.php?id_produk=<?= $row['id_produk'] ?>" class="btn btn-warning btn-sm" onclick="return confirm('Anda yakin ingin menonaktifkan produk ini?')" title="Nonaktifkan"><i class="fas fa-ban"></i></a>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="nonaktif" role="tabpanel" aria-labelledby="nonaktif-tab">
                                    <table class="table table-striped table-bordered datatable-class">
                                         <thead>
                                            <tr><th>ID</th><th>Nama</th><th>Harga</th><th>Kategori</th><th>Gambar</th><th>Aksi</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($produk_tidak_aktif)) { ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['id_produk']) ?></td>
                                                <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                                                <td>Rp <?= number_format($row['harga']) ?></td>
                                                <td><?= htmlspecialchars(ucfirst($row['kategori'])) ?></td>
                                                <td><img src="../../../assets/img/produk/<?= htmlspecialchars($row['poto_produk']) ?>" width="100" alt="<?= htmlspecialchars($row['nama_produk']) ?>"></td>
                                                <td>
                                                    <a href="menu_aktifkan.php?id_produk=<?= $row['id_produk'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Anda yakin ingin mengaktifkan kembali produk ini?')" title="Aktifkan"><i class="fas fa-check"></i></a>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', event => {
            const datatables = document.querySelectorAll('.datatable-class');
            datatables.forEach(datatable => {
                new simpleDatatables.DataTable(datatable);
            });
        });
    </script>
</body>
</html>