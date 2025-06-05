    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Input Data Karyawan</title>

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
