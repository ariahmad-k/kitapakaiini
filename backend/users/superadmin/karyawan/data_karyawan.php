<?php
include '../../../koneksi.php';
$query_mysql = mysqli_query($koneksi, "SELECT * FROM karyawan");
$nomor = 1;

$hari = date('Y-m-d');
if (isset($_GET['tanggal'])) {
    $hari = $_GET['tanggal'];
}

if (isset($_POST['pesan'])) {
    $pesan = $_POST['pesan'];
    if ($pesan == "input") {
        echo "Data berhasil diinput.";
    } else if ($pesan == "update") {
        echo "Data berhasil diupdate.";
    } else if ($pesan == "hapus") {
        echo "Data berhasil dihapus.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Dashboard - Pemilik</title>
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
                            <h1 class="mt-4">Data Karyawan</h1>
                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Data Karyawan</li>
                            </ol>
                        </div>

                        <div class="col-12 mb-3">
                            <a href="kar_input.php" class="btn btn-primary">+ Tambah Data Karyawan</a>
                        </div>
                    </div>

                    <div class="row">

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Data Karyawan
                            </div>
                            <div class="card-body">
                                <table id="data_menu" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;">ID Kar</th>
                                            <th>Nama</th>
                                            <th>Jabatan</th>
                                            <th>No. Telepon</th>
                                            <th>Email</th>
                                            <th>Password</th>
                                            <th style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($data = mysqli_fetch_array($query_mysql)) {
                                        ?>
                                            <tr>
                                                <td><?php echo $nomor++; ?></td>
                                                <td><?php echo $data['username']; ?></td>
                                                <td><?php echo $data['jabatan']; ?></td>
                                                <td><?php echo $data['no_tlp']; ?></td>
                                                <td><?php echo $data['email']; ?></td>
                                                <td><?php echo $data['password']; ?></td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a class="btn btn-primary btn-sm" href="kar_edit.php?id_karyawan=<?php echo $data['id_karyawan']; ?>">Edit</a>
                                                        <a class="btn btn-danger btn-sm" href="kar_hapus.php?id_karyawan=<?php echo $data['id_karyawan']; ?>">Hapus</a>
                                                    </div>
                                                </td>

                                            </tr>
                                        <?php } ?>
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