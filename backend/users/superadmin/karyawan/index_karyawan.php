<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membuat CRUD Dengan PHP Dan MySQL - Menampilkan data dari database</title>
    <link rel="stylesheet" type="text/css" href="style.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.dataTables.css" />

    <!-- Link CDN Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- mengubah ukuran tabel   -->
    <style>
/* Membuat tabel lebih kecil dan rapi */
.table-karyawan {
    font-size: 0.92rem;      /* Perkecil ukuran font */
    width: 100%;
    margin: 0 auto;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.table-karyawan th, .table-karyawan td {
    padding: 6px 10px !important;
    vertical-align: middle;
}

.table-karyawan th {
    background: #f8f9fa;
    font-weight: 600;
}

.table-karyawan td {
    background: #fff;
}

.table-karyawan tr:hover {
    background: #f1f3f5;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    font-size: 0.92rem;
}
</style>

    
</head>

<body>
    

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
            <a class="nav-link active" aria-current="page" href="users/admin/index_admin.php">Beranda</a>
            </li>

            <li class="nav-item">
            <a class="nav-link" href="input.php">+Tambah Karyawan</a>
            </li>
            
            <!-- <li class="nav-item">
            <a class="nav-link" href="kategori.php">Kategori</a>
            </li> -->
        </ul>
        </div>
    </div>
    </nav>


    <br/>

    <?php
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

    <!-- <br/>
    <a class="btn btn-primary" href="input.php">+ Tambah Data Baru</a> -->


    <h3>Data Karyawan</h3>

    <table id="myTable" class="display table table-striped table-bordered table-hover table-sm table-karyawan">
            <!-- table-sm dari Bootstrap = baris lebih ramping.
            table-striped = baris belang.
            table-bordered = ada garis.
            table-hover = highlight saat mouse di atas baris.
            table-karyawan = custom CSS yang kamu buat di atas. -->


        <thead>
            <tr>
                <!-- <th>No</th> -->
                <th  style="width: 80px;">ID_Kar</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>No. Telepon</th>
                <th>Alamat</th>
                <th  style="width: 80px;">Jenis_Kel</th>
                <th>Email</th>
                <th>Password</th>
                <th  style="width: 80px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include "koneksi.php";
            $query_mysql = mysqli_query($koneksi, "SELECT * FROM karyawan");
            $nomor = 1;
            while ($data = mysqli_fetch_array($query_mysql)) {
            ?>
            <tr>
                <td><?php echo $nomor++; ?></td>
                <td><?php echo $data['nama']; ?></td>
                <td><?php echo $data['jabatan']; ?></td>
                <td><?php echo $data['no_tlp']; ?></td>
                <td><?php echo $data['alamat']; ?></td>
                <td><?php echo $data['jenis_kelamin']; ?></td>
                <td><?php echo $data['email']; ?></td>
                <td><?php echo $data['password']; ?></td>

                <td>
                    <a class="btn btn-primary btn-sm" href="edit.php?id_karyawan=<?php echo $data['id_karyawan']; ?>">Edit</a>
                    <a class="btn btn-danger btn-sm" href="hapus.php?id_karyawan=<?php echo $data['id_karyawan']; ?>">Hapus</a>
                </td>


            </tr>
            <?php } ?>
        </tbody>
    </table>
    <!-- jQuery (wajib untuk DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.js"></script>

    <!-- Inisialisasi DataTables -->
    <script>
        $(document).ready(function () {
            $('#myTable').DataTable({
                responsive: true
            });
        });
    </script>

    <!-- Link CDN Bootstrap JS (Opsional, hanya kalau kamu pakai komponen interaktif Bootstrap) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
