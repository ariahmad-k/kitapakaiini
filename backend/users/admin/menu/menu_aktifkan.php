<?php
session_start();
include('../../../koneksi.php');

// Cek hak akses admin/owner
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['jabatan'], ['admin', 'owner'])) {
    header('Location: ../../login.php');
    exit;
}

// Pastikan ada id_produk yang dikirim
if (isset($_GET['id_produk'])) {
    $id = $_GET['id_produk'];

    // Query UPDATE untuk mengaktifkan kembali produk
    $query = "UPDATE produk SET status_produk = 'aktif' WHERE id_produk = ?";
    $stmt = mysqli_prepare($koneksi, $query);

    mysqli_stmt_bind_param($stmt, "s", $id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['notif'] = ['pesan' => 'Produk berhasil diaktifkan kembali.', 'tipe' => 'success'];
    } else {
        $_SESSION['notif'] = ['pesan' => 'Gagal mengaktifkan produk. Error: ' . mysqli_error($koneksi), 'tipe' => 'danger'];
    }

    mysqli_stmt_close($stmt);

} else {
    $_SESSION['notif'] = ['pesan' => 'Aksi tidak valid, ID produk tidak ditemukan.', 'tipe' => 'warning'];
}

header("Location: data_menu.php");
exit;
?>