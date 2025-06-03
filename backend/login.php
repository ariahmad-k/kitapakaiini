<?php
session_start();
include 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);

    if (!empty($nama) && !empty($password) && !empty($jabatan)) {
        $query = mysqli_query($koneksi, "SELECT * FROM karyawan WHERE username='$nama' AND password='$password' AND jabatan='$jabatan'");
        $data = mysqli_fetch_assoc($query);

        if ($data) {
            $_SESSION['nama'] = $data['nama'];
            $_SESSION['password'] = $data['password'];
            $_SESSION['jabatan'] = $data['jabatan'];

            switch (strtolower($data['jabatan'])) {
                case 'owner':
                    header("Location: users/superadmin/index.php");
                    exit;
                case 'admin':
                    header("Location: users/admin/index.php");
                    exit;
                case 'kasir':
                    header("Location: users/kasir/index.php");
                    exit;
                default:
                    $error = "Jabatan tidak dikenali.";
            }
        } else {
            $error = "Nama, password, atau Jabatan salah.";
        }
    } else {
        $error = "Semua field wajib diisi.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>LOGIN MULTI USER</title>
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/dist/css/floating-labels.css" rel="stylesheet">
</head>
<body>
    <form class="form-signin" action="" method="post">
        <div class="text-center mb-4">
            <img class="mb-4" src="assets/brand/kue-balok.png" alt="" width="72" height="72">
            <h1 class="h3 mb-3 font-weight-normal">Form Login</h1>
            <p>Masukkan Username dan Password dengan benar!</p>
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-label-group">
            <input type="text" class="form-control" name="nama" placeholder="username" required autofocus>
            <label for="nama">Masukkan Username</label>
        </div>

        <div class="form-label-group">
            <input type="password" class="form-control" name="password" placeholder="password" required>
            <label for="password">Masukkan Password</label>
        </div>

        <div class="form-label-group">
            <select class="form-control" name="jabatan" required>
                <option value="">-- Pilih jabatan --</option>
                <option value="owner">Owner</option>
                <option value="admin">Admin</option>
                <option value="kasir">Kasir</option>
            </select>
        </div>

        <div class="checkbox mb-3">
            <label>
                <input type="checkbox" value="remember-me"> Remember me
            </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <p class="mt-5 mb-3 text-muted text-center">&copy; Kue Balok Mang Wiro 2025</p>
    </form>
</body>
</html>
