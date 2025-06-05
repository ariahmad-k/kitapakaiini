<?php
include '../../../koneksi.php';
$id = $_GET['id'];
mysqli_query($koneksi, "DELETE FROM stok_harian WHERE id='$id'");
header("Location: data_stock.php");
?>