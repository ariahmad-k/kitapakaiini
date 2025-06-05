<?php
include '../../../koneksi.php';

// Validasi apakah id_produk ada
if (!isset($_GET['id_produk'])) {
    echo "ID produk tidak ditemukan.";
    exit;
}

$id_produk = $_GET['id_produk'];
$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk ='$id_produk'");

// Validasi apakah data produk ditemukan
if (!$query || mysqli_num_rows($query) == 0) {
    echo "Data produk tidak ditemukan.";
    exit;
}

$data = mysqli_fetch_assoc($query);

// Proses simpan perubahan
if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $kategori = $_POST['kategori'];

    if ($_FILES['gambar']['name'] != '') {
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        move_uploaded_file($tmp, "../../../assets/img/produk/$gambar");

        mysqli_query($koneksi, "UPDATE produk 
            SET nama_produk='$nama', harga_produk='$harga', kategori='$kategori', poto_produk='$gambar' 
            WHERE id_produk='$id_produk'");
    } else {
        mysqli_query($koneksi, "UPDATE produk 
            SET nama_produk='$nama', harga_produk='$harga', kategori='$kategori' 
            WHERE id_produk='$id_produk'");
    }

    header("Location: data_menu.php");
    exit;
}
$result = mysqli_query($koneksi, "SELECT * FROM produk");

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
                            <h1 class="mt-4">Data Menu</h1>
                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="data_menu.php">Data Menu</a></li>
                                <li class="breadcrumb-item active">Edit Menu</li>
                            </ol>
                        </div>
                        <div class="col-12 mb-3">
                            <form method="POST" enctype="multipart/form-data">
                                <label>Nama Menu:</label><br>
                                <input type="text" name="nama" value="<?= htmlspecialchars($data['nama'] ?? '') ?>" required><br>

                                <label>Harga:</label><br>
                                <input type="number" name="harga" value="<?= htmlspecialchars($data['harga'] ?? '') ?>" required><br>

                                <label>Kategori:</label><br>
                                <select name="kategori" required>
                                    <option value="makanan" <?= ($data['kategori'] === 'makanan') ? 'selected' : '' ?>>Makanan</option>
                                    <option value="minuman" <?= ($data['kategori'] === 'minuman') ? 'selected' : '' ?>>Minuman</option>
                                </select><br>

                                <!-- <label for="">Gambar Sebelumnya:</label><br>
                                <img src="../../../assets/img/produk/<?= $row['poto_produk'] ?>" width="100"><br> -->

                                <label>Upload Gambar Baru:</label><br>
                                <input type="file" name="gambar"><br><br>

                                <button type="submit" name="submit">Simpan</button>
                            </form>
                        </div>

                    </div>

                    <div class="row">

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Data Menu
                            </div>

                            <div class="card-body">

                                <table id="data_menu" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Id Produk</th>
                                            <th>Nama</th>
                                            <th>Harga</th>
                                            <th>Kategori</th>
                                            <th>Gambar</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Id Produk</th>
                                            <th>Nama</th>
                                            <th>Harga</th>
                                            <th>Kategori</th>
                                            <th>Gambar</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                            <tr>
                                                <td><?= $row['id_produk'] ?></td>
                                                <td><?= $row['nama_produk'] ?></td>
                                                <td>Rp <?= number_format($row['harga_produk']) ?></td>
                                                <td><?= $row['kategori'] ?></td>
                                                <td><img src="../../../assets/img/produk/<?= $row['poto_produk'] ?>" width="100"></td>
                                                <td>
                                                    <a href="produk_edit.php?id_produk=<?= $row['id_produk'] ?>">Edit</a>
                                                    <a href="produk_hapus.php?id_produk=<?= $row['id_produk'] ?>" onclick="return confirm('Hapus produk ini?')">Hapus</a>
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