<?php
include '../../../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_karyawan = $_POST['id_karyawan'] ?? null;
    $nama = $_POST['nama'] ?? '';
    $jabatan = $_POST['jabatan'] ?? '';
    $no_tlp = $_POST['no_tlp'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validasi sederhana 
    if ($id_karyawan === null || $nama === '' || $jabatan === '' || $no_tlp === '' || $email === '' || $password === '') {
        die("Semua field harus diisi!");
    }

    // Prepared statement untuk update
    $stmt = $koneksi->prepare("UPDATE karyawan SET username=?, jabatan=?, no_tlp=?, email=?, password=? WHERE id_karyawan=?");
    if (!$stmt) {
        die("Prepare failed: (" . $koneksi->errno . ") " . $koneksi->error);
    }

    $stmt->bind_param("sssssi", $nama, $jabatan, $no_tlp, $email, $password,  $id_karyawan);

    if ($stmt->execute()) {
        echo "<script>
                alert('Data berhasil diupdate.');
                window.location.href = 'data_karyawan.php';
            </script>";
        exit;
    } else {
        echo "Error saat update data: " . $stmt->error;
}
} else {
    echo "Data tidak lengkap atau metode request salah.";
}
?>
