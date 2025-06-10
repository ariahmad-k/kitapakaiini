<?php
session_start();
include('../../koneksi.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tolak_pembayaran'])) {
    $id_pesanan = $_POST['id_pesanan'] ?? null;

    
    // Pastikan ada id_pesanan yg dikirim
    if (isset($_POST['id_pesanan'])) {
    $id = $_POST['id_pesanan'];


        // Querry menolak pesanan masuk 
        $query = "UPDATE pesanan SET status_pesanan = 'dibatalkan' WHERE id_pesanan = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $id_pesanan);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Simpan notifikasi jika perlu
        $_SESSION['notif'] = ['pesan' => 'Pembayaran berhasil dibatalkan.', 'tipe' => 'success'];

        // Redirect ke halaman pesanan_ditolak.php
        header("Location: pesanan_masuk.php?id_pesanan=" . urlencode($id_pesanan));
        exit;
    } else {
        // Jika id_pesanan tidak ada, redirect ke halaman error atau daftar pesanan
        $_SESSION['notif'] = ['pesan' => 'ID pesanan tidak valid.', 'tipe' => 'danger'];
        header("Location: pesanan_masuk.php");
        exit;
    }
} else {
    // Jika akses langsung tanpa POST, redirect ke halaman pesanan_masuk
    header("Location: pesanan_masuk.php");
    exit;
}
?>
