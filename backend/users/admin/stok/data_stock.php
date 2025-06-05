<?php
include '../../../koneksi.php';
$data = mysqli_query($koneksi, "
select sh.*, p.nama_produk as nama
FROM stok_harian sh
JOIN produk p ON sh.id_produk = p.id_produk
ORDER BY sh.tanggal DESC");

$hari = date('Y-m-d');
if (isset($_GET['tanggal'])) {
    $hari = $_GET['tanggal'];
}
?>

<!-- <h2>Daftar Stock Harian</h2> -->



<?php
// include('../../../koneksi.php');
// $result = mysqli_query($koneksi, "SELECT * FROM produk");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Dashboard - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../../../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <?php
    include "../inc/navbar.php";
    ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <?php
            include "../inc/sidebar.php";
            ?>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <div class="row">
                        <div class="col-xl-12 col-md-12">
                            <h1 class="mt-4">Data Stok Harian</h1>
                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Data Stok</li>
                            </ol>
                        </div>

                        <div class="col-12 mb-3">
                            <a href="stock_tambah.php" class="btn btn-primary">+ Tambah Stok Harian</a>
                        </div>
                    </div>

                    <div class="row">

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Data Stok Harian
                                <span class="float-end">
                                    <form method="GET" action="">
                                        <label for="tanggal">Pilih Tanggal:</label>
                                        <input type="date" name="tanggal" id="tanggal" value="<?= $hari ?>" required>
                                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                                    </form>
                            </div>
                            <div class="card-body">
                                <table id="data_menu" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Nama Menu</th>
                                            <th>Stok</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Nama Menu</th>
                                            <th>Stok</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php $no = 1;
                                        while ($row = mysqli_fetch_assoc($data)) : ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= $row['tanggal'] ?></td>
                                                <td><?= $row['nama'] ?></td>
                                                <td><?= $row['stok'] ?></td>
                                                <td>
                                                    <a href="edit_stock.php?id=<?= $row['id'] ?>">Edit</a>
                                                    <a href="stock_hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus stok?')">Hapus</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="../../../js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="../../../assets/demo/chart-area-demo.js"></script>
    <script src="../../../assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="../../../js/datatables-simple-demo.js"></script>
</body>

</html>
  