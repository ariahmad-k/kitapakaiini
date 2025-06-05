    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Input Data Karyawan</title>

    <!-- Style Custom -->
    <!-- <link rel="stylesheet" type="text/css" href="style.css"/> -->

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"/>
    
    </head>

    <body class="bg-light">

        <!-- Navbar Bootstrap 5 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Data Karyawan</a>
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
            <a class="nav-link" href="tambah.php">Tambah Data</a>
            </li>
            <li class="nav-item">
            <!-- <a class="nav-link" href="kategori.php">Kategori</a> -->
            </li>
        </ul>
        </div>
    </div>
    </nav>


    <div class="container mt-5">
        <h1 class="mb-3">Data Karyawan</h1>

        <a href="index_karyawan.php" class="btn btn-link mb-4">← Lihat Semua Data</a>

        <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Input Data Karyawan Baru</h3>
        </div>

        <div class="card-body">
            <form action="input-aksi.php" method="post">
            <!-- Nama -->
            <div class="mb-3">
                <label for="nama" class="form-label">Nama :</label>
                <input type="text" class="form-control" name="nama" id="nama" required/>
            </div>
            

            <!-- Jabatan -->
            <div class="mb-3">
            <label for="jabatan" class="form-label">Jabatan:</label>
            <select class="form-control" name="jabatan" id="jabatan" required>
                <option value="">-- Pilih Jabatan --</option>
                <option value="Admin">Admin</option>
                <option value="Kasir">Kasir</option>
                <option value="Owner">Owner</option>
            </select>
            </div>


            <!-- telepon -->
            <div class="mb-3">
                <label for="no_tlp" class="form-label">No. Telepon :</label>
                <input type="text" class="form-control" name="no_tlp" id="no_tlp" required/>
            </div>

            <!-- alamat -->
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat :</label>
                <input type="text" class="form-control" name="alamat" id="alamat" required/>
            </div>

            <!-- jenis kelamin -->
            <div class="mb-3">
            <label for="jenis_kelamin" class="form-label">Jenis Kelamin:</label>
            <select class="form-control" name="jenis_kelamin" id="jenis_kelamin" required>
                <option value="">-- Pilih --</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
            </div>



            <!-- email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <input type="email" class="form-control" name="email" id="email" required/>
            </div>

            <!-- pass -->
            <div class="mb-3">
                <label for="password" class="form-label">Password :</label>
                <input type="password" class="form-control" name="password" id="password" required/>
            </div>


            <!-- submit -->
            <div class="mb-3">
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
            </form>
        </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    
    </body>
    </html>


<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_karyawan = $_POST['id_karyawan'] ?? null;
    $nama = $_POST['nama'] ?? '';
    $jabatan = $_POST['jabatan'] ?? '';
    $no_tlp = $_POST['no_tlp'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validasi sederhana
    if ($id_karyawan === null || $nama === '' || $jabatan === '' || $no_tlp === '' || $alamat === '' || $jenis_kelamin === '' || $email === '' || $password === '') {
        die("Semua field harus diisi!");
    }

    // Validasi jenis kelamin
    if (!in_array($jenis_kelamin, ['L', 'P'])) {
        die("Nilai jenis kelamin tidak valid!");
    }

    // Prepared statement untuk update
    $stmt = $koneksi->prepare("UPDATE karyawan SET nama=?, jabatan=?, no_tlp=?, alamat=?, jenis_kelamin=?, email=?, password=?, WHERE id_karyawan=?");
    if (!$stmt) {
        die("Prepare failed: (" . $koneksi->errno . ") " . $koneksi->error);
    }

    $stmt->bind_param("sssssssi", $nama, $jabatan, $no_tlp, $alamat, $jenis_kelamin, $email, $password,  $id_karyawan);

    
if ($stmt->execute()) {
    echo "<script>
            alert('Data berhasil diupdate.');
            window.location.href = 'index_karyawan.php';
        </script>";
    exit;
} else {
    echo "Error saat update data: " . $stmt->error;
}
} else {
    echo "Data tidak lengkap atau metode request salah.";
}





?>
