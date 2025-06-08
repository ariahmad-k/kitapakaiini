<?php
date_default_timezone_set('Asia/Jakarta');  
// Kode koneksi Anda yang sudah ada
$koneksi = mysqli_connect("localhost", "root", "", "1");

if (!$koneksi) {
    die("Koneksi Gagal:" . mysqli_connect_error());
}

?>