<?php
include 'koneksi.php';

// Cek data POST
if (!isset( $_POST['nama'], $_POST['jabatan'], $_POST['no_tlp'], $_POST['alamat'], $_POST['jenis_kelamin'], $_POST['email'])) {
    die(" Data yang diperlukan belum lengkap!");
}

// validasi sederhana 
// $id_karyawan = $_POST['id_karyawan'];
$nama = $_POST['nama'];
$jabatan = $_POST['jabatan'];
$no_tlp = $_POST['no_tlp'];
$alamat = $_POST['alamat'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$email = $_POST['email'];
$password = $_POST['password'];


// Validasi sederhana
if ( $nama === ''|| $jabatan === '' ||$no_tlp === ''|| $alamat=== ''  ||$jenis_kelamin === ''|| $email === '' || $password === '') {
    die(" Semua field harus diisi!");
}

// Validasi jenis kelamin
if (!in_array($jenis_kelamin, ['L', 'P'])) {
    die("Nilai jenis kelamin tidak valid!");
}
// if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//     die("Email tidak valid!");
// }


// Gunakan prepared statement
$stmt = $koneksi->prepare("INSERT INTO karyawan (nama, jabatan, no_tlp, alamat, jenis_kelamin, email, password) VALUES (?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Prepare failed: (" . $koneksi->errno . ") " . $koneksi->error);
}

// id_karyawan, nama, jabatan, no_tlp, alamat, jenis_kelamin email
$stmt->bind_param("sssssss", $nama, $jabatan, $no_tlp, $alamat, $jenis_kelamin, $email, $password);


if ($stmt->execute()) {
    header("Location: index_karyawan.php?pesan=input");
    exit;
} else {
    die("Query gagal: (" . $stmt->errno . ") " . $stmt->error);
}
?>
