<?php
session_start();
include '../backend/koneksi.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_data'])) {
    $nama_pemesan = trim($_POST['nama_pemesan']);
    $no_telepon = trim($_POST['no_telepon']);
    $cart_data = json_decode($_POST['cart_data'], true);

    if (empty($nama_pemesan) || empty($no_telepon) || empty($cart_data) || json_last_error() !== JSON_ERROR_NONE) {
        $_SESSION['notif_cart'] = ['pesan' => 'Data tidak lengkap. Harap isi semua kolom.', 'tipe' => 'danger'];
        header('Location: keranjang.php');
        exit;
    }

    mysqli_begin_transaction($koneksi);

    try {
        $total_harga_server = 0;
        $produk_valid = [];
        $stmt_produk = mysqli_prepare($koneksi, "SELECT id_produk, nama_produk, harga, stok, status_produk FROM produk WHERE id_produk = ?");

        foreach ($cart_data as $id => $item) {
            mysqli_stmt_bind_param($stmt_produk, "s", $id);
            mysqli_stmt_execute($stmt_produk);
            $produk_db = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_produk));

            if (!$produk_db || $produk_db['status_produk'] !== 'aktif') throw new Exception("Produk '{$item['nama']}' tidak tersedia.");
            if ($produk_db['stok'] < $item['jumlah']) throw new Exception("Stok untuk '{$item['nama']}' tidak mencukupi.");

            $total_harga_server += $produk_db['harga'] * $item['jumlah'];
            $produk_valid[$id] = $produk_db;
        }
        mysqli_stmt_close($stmt_produk);

        $id_pesanan_baru = "ONLINE-" . time();
        $tgl_pesanan = date("Y-m-d H:i:s");
        $tipe_pesanan = 'online';
        $status_pesanan = 'menunggu_pembayaran'; // Status awal yang benar
        $metode_pembayaran = 'transfer';
        $jenis_pesanan_form = 'take_away'; // Default untuk online

        $stmt_pesanan = mysqli_prepare($koneksi, "INSERT INTO pesanan (id_pesanan, tipe_pesanan, jenis_pesanan, nama_pemesan, no_telepon, tgl_pesanan, total_harga, metode_pembayaran, status_pesanan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_pesanan, "ssssssdss", $id_pesanan_baru, $tipe_pesanan, $jenis_pesanan_form, $nama_pemesan, $no_telepon, $tgl_pesanan, $total_harga_server, $metode_pembayaran, $status_pesanan);
        mysqli_stmt_execute($stmt_pesanan);

        $stmt_detail = mysqli_prepare($koneksi, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_saat_transaksi, sub_total) VALUES (?, ?, ?, ?, ?)");
        foreach ($cart_data as $id => $item) {
            $harga_saat_ini = $produk_valid[$id]['harga'];
            $sub_total = $harga_saat_ini * $item['jumlah'];
            mysqli_stmt_bind_param($stmt_detail, "ssidd", $id_pesanan_baru, $id, $item['jumlah'], $harga_saat_ini, $sub_total);
            mysqli_stmt_execute($stmt_detail);
        }

        mysqli_commit($koneksi);
        header("Location: konfirmasi.php?id=" . $id_pesanan_baru);
        exit;

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $_SESSION['notif_cart'] = ['pesan' => 'Gagal membuat pesanan: ' . $e->getMessage(), 'tipe' => 'danger'];
        header('Location: keranjang.php');
        exit;
    }
} else {
    header('Location: menu.php');
    exit;
}
?>