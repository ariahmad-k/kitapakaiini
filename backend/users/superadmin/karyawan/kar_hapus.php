<?php
include '../../../koneksi.php';

// Ambil id_produk dari URL
$id_karyawan = $_GET['id_karyawan'] ?? null;

if ($id_karyawan === null) {
    // Jika tidak ada id_karyawan, redirect kembali ke index
    header("Location: data_karyawan.php?pesan=hapus_gagal");
    exit;
}

// Hapus data dari tabel 
mysqli_query($koneksi, "DELETE FROM karyawan WHERE id_karyawan='$id_karyawan'");

// Redirect ke index dengan pesan sukses
echo "<script>
        alert('Data berhasil dihapus.');
        window.location.href = 'data_karyawan.php';
     </script>";
header("Location: data_karyawan.php?pesan=hapus");
exit;

