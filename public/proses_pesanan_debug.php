<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Tes Penyimpanan ke Database</h1>";

include 'includes/koneksi.php';

if (!$koneksi) {
    die("<h2>GAGAL KONEKSI KE DATABASE: " . mysqli_connect_error() . "</h2>");
}
echo "<h2>Koneksi ke database BERHASIL.</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pemesan = trim($_POST['nama_pemesan']);
    $id_pesanan_baru = "TES-" . time();
    $tipe_pesanan = 'online';
    $status_pesanan = 'menunggu_pembayaran';
    $total_harga = 10000; // Harga dummy untuk tes

    // Ini adalah query INSERT paling sederhana (TIDAK AMAN, HANYA UNTUK TES)
    $sql = "INSERT INTO pesanan (id_pesanan, tipe_pesanan, nama_pemesan, status_pesanan, total_harga, tgl_pesanan, metode_pembayaran) 
            VALUES ('$id_pesanan_baru', '$tipe_pesanan', '$nama_pemesan', '$status_pesanan', $total_harga, NOW(), 'qris')";

    echo "<p>Mencoba menjalankan query: <br><code>$sql</code></p>";

    $hasil = mysqli_query($koneksi, $sql);

    if ($hasil) {
        echo "<h1>BERHASIL! Data pesanan berhasil masuk ke database.</h1>";
    } else {
        echo "<h1>GAGAL! Terjadi error saat INSERT: " . mysqli_error($koneksi) . "</h1>";
    }
} else {
    echo "<p>Tidak ada data POST.</p>";
}
die();
?>