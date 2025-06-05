<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Karyawan</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .container {
            max-width: 700px;
            margin-top: 40px;
        }

        #preview {
            display: block;
            margin-top: 10px;
            max-width: 150px;
        }
    </style>
</head>

<body>

<!-- Navbar Bootstrap 5 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Data Karyawan </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto"> <!-- ms-auto = menu di sebelah kanan -->
            <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="index.php">Beranda</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="tambah.php">Tambah Karyawan</a>
            </li>
            <li class="nav-item">
            <!-- <a class="nav-link" href="kategori.php">Kategori</a> -->
            </li>
        </ul>
        </div>
    </div>
    </nav>

    <div class="container">
        <h1 class="mb-3 text-center">Edit Data Karyawan</h1>
        <a href="index_karyawan.php" class="btn btn-secondary mb-4">← Kembali ke Data</a>

<?php
include "koneksi.php";

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
?>




    <form action="update.php" method="post">
        <input type="hidden" name="id_karyawan" value="<?php echo htmlspecialchars($data['id_karyawan']); ?>">

        <!-- nama -->
        <div class="mb-3">
            <label for="nama" class="form-label">Nama :</label>
            <input type="text" class="form-control" name="nama" id="nama" required value="<?php echo htmlspecialchars($data['nama']); ?>" />
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

        <!-- Alamat  -->
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat :</label>
            <input type="text" class="form-control" name="alamat" id="alamat" required value="<?php echo htmlspecialchars($data['alamat']); ?>" />
        </div>

        <!-- Jenis Kelamin  -->
        <div class="mb-3">
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin:</label>
            <select class="form-control" name="jenis_kelamin" id="jenis_kelamin" required>
                <option value="">-- Pilih --</option>
                <option value="L" <?php if ($data['jenis_kelamin'] == 'L') echo 'selected'; ?>>Laki-laki</option>
                <option value="P" <?php if ($data['jenis_kelamin'] == 'P') echo 'selected'; ?>>Perempuan</option>
            </select>
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

</body>
</html>
