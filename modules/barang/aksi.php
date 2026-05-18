<?php
include "../../config/db.php";
/** @var mysqli $conn */ // <-- Tambahkan baris ini tepat di baris ke-3
// JIKA AKSES HAPUS
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM barang WHERE id_barang = '$id'");
    header("location:index.php"); // Kembali ke tabel barang
}

// JIKA TAMBAH
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    mysqli_query($conn, "INSERT INTO barang (nama_barang, harga, stok) VALUES ('$nama', '$harga', '$stok')");
    header("location:index.php");
}
?>