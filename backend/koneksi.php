<?php
// session_start();

$host = "localhost";
$username = "root";
$password = "";
$database = "1";


// isi nama host, username mysql, dan password mysql anda
$koneksi = mysqli_connect($host, $username, $password, $database);
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
