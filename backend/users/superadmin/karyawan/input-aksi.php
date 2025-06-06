<?php
include '../../../koneksi.php';

// Cek data POST
if (!isset( $_POST['nama'], $_POST['jabatan'], $_POST['no_tlp'], $_POST['email'])) {
    die(" Data yang diperlukan belum lengkap!");
}

// validasi sederhana 
// $id_karyawan = $_POST['id_karyawan'];
$username = $_POST['username'] ?? ''; // Username bisa kosong jika tidak diperlukan
$nama = $_POST['nama'];
$jabatan = $_POST['jabatan'];
$no_tlp = $_POST['no_tlp'];
$email = $_POST['email'];
$password = $_POST['password'];

// Validasi sederhana
if ( $nama === ''|| $jabatan === '' ||$no_tlp === ''|| $email === '' || $password === '') {
    die(" Semua field harus diisi!");
}

// Gunakan prepared statement
$stmt = $koneksi->prepare("INSERT INTO karyawan (nama, username, jabatan, no_tlp, email, password) VALUES (?,?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Prepare failed: (" . $koneksi->errno . ") " . $koneksi->error);
}

// id_karyawan, nama, jabatan, no_tlp, alamat, jenis_kelamin email
$stmt->bind_param("ssssss", $nama, $username, $jabatan, $no_tlp, $email, $password);


if ($stmt->execute()) {
    header("Location: data_karyawan.php?pesan=input");
    exit;
} else {
    die("Query gagal: (" . $stmt->errno . ") " . $stmt->error);
}
?>
