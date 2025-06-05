<?php
include "../../../koneksi.php";

$id_karyawan = isset($_GET['id_karyawan']) ? $_GET['id_karyawan'] : null;

if ($id_karyawan === null) {
    echo "<div class='alert alert-danger'>ID tidak ditemukan.</div>";
    exit;
}

$query_mysql = mysqli_query($koneksi, "SELECT * FROM karyawan WHERE id_karyawan='$id_karyawan'");

if (mysqli_num_rows($query_mysql) == 0) {
    echo "<div class='alert alert-danger'>Data tidak ditemukan.</div>";
    exit;
}

$data = mysqli_fetch_assoc($query_mysql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_karyawan = $_POST['id_karyawan'] ?? null;
    $nama = $_POST[''] ?? '';
    $jabatan = $_POST['jabatan'] ?? '';
    $no_tlp = $_POST['no_tlp'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validasi sederhana 
    if ($id_karyawan === null || $nama === '' || $jabatan === '' || $no_tlp === '' || $email === '' || $password === '') {
        die("Semua field harus diisi!");
    }

    
    // Prepared statement untuk update
    $stmt = $koneksi->prepare("UPDATE karyawan SET username=?, jabatan=?, no_tlp=?, email=?, password=?, WHERE id_karyawan=?");
    if (!$stmt) {
        die("Prepare failed: (" . $koneksi->errno . ") " . $koneksi->error);
    }

    $stmt->bind_param("sssssi", $nama, $jabatan, $no_tlp, $email, $password,  $id_karyawan);

    if ($stmt->execute()) {
        echo "<script>
                alert('Data berhasil diupdate.');
                window.location.href = 'data_karyawan.php';
            </script>";
        exit;
    } else {
        echo "Error saat update data: " . $stmt->error;
}
} else {
    echo "Data tidak lengkap atau metode request salah.";
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
                                <li class="breadcrumb-item"><a href="../karyawan/data_karyawan.php">Data Karyawan</a></li>
                                <li class="breadcrumb-item active">Edit Karyawan</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">

                            <form action="update.php" method="post">
                                <input type="hidden" name="id_karyawan" value="<?php echo htmlspecialchars($data['id_karyawan']); ?>">

                                <!-- nama -->
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama :</label>
                                    <input type="text" class="form-control" name="nama" id="nama" required value="<?php echo htmlspecialchars($data['username']); ?>" />
                                </div>

                                <!-- Jabatan -->
                                <div class="mb-3">
                                    <label for="jabatan" class="form-label">Jabatan:</label>
                                    <select class="form-control" name="jabatan" id="jabatan" required>
                                        <option value="">-- Pilih Jabatan --</option>
                                        <option value="Admin" <?php if ($data['jabatan'] == 'Admin') echo 'selected'; ?>>Admin</option>
                                        <option value="Kasir" <?php if ($data['jabatan'] == 'Kasir') echo 'selected'; ?>>Kasir</option>
                                        <option value="Owner" <?php if ($data['jabatan'] == 'Owner') echo 'selected'; ?>>Owner</option>
                                    </select>
                                </div>

                                <!-- telepon  -->
                                <div class="mb-3">
                                    <label for="no_tlp" class="form-label">No. Telepon :</label>
                                    <input type="text" class="form-control" name="no_tlp" id="no_tlp" required value="<?php echo htmlspecialchars($data['no_tlp']); ?>" />
                                </div>

                                <!-- Email  -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email :</label>
                                    <input type="email" class="form-control" name="email" id="email" required value="<?php echo htmlspecialchars($data['email']); ?>" />
                                </div>

                                <!-- Email  -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password :</label>
                                    <input type="password" class="form-control" name="password" id="password" required value="<?php echo htmlspecialchars($data['password']); ?>" />
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-success">Simpan</button>
                                </div>
                            </form>
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