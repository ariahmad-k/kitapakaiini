<?php
session_start();
include('../../../koneksi.php');

// Cek hak akses admin/owner
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['jabatan'], ['admin', 'owner'])) {
    header('Location: ../../../login.php');
    exit;
}

// Cek apakah id_produk ada dan valid
if (isset($_GET['id_produk'])) {
    $id = $_GET['id_produk'];

    // if (!ctype_digit($id)) {
    //     $_SESSION['notif'] = ['pesan' => 'ID produk tidak valid.', 'tipe' => 'warning'];
    //     header("Location: data_menu.php");
    //     exit;
    // }

    // Update status produk menjadi aktif
    $query = "UPDATE produk SET status_produk = 'aktif' WHERE id_produk = ?";
    $stmt = mysqli_prepare($koneksi, $query);

    mysqli_stmt_bind_param($stmt, "s", $id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['notif'] = ['pesan' => 'Produk berhasil aktifkan dan  akan tampil di menu kasir/pelanggan.', 'tipe' => 'success'];
    } else {
        $_SESSION['notif'] = ['pesan' => 'Gagal mengaktifkan  produk. Error: ' . mysqli_error($koneksi), 'tipe' => 'danger'];
    }
    
    mysqli_stmt_close($stmt);

} else {
    $_SESSION['notif'] = ['pesan' => 'Aksi tidak valid, ID produk tidak ditemukan.', 'tipe' => 'warning'];
}

header("Location: data_menu.php");
exit;
?>
