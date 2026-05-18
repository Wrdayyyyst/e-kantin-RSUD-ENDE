<?php
session_start();
include "../../config/db.php";
/** @var mysqli $conn */ // Meredam tanda silang merah Intelephense VS Code

// Pengaman Akun
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:../../login.php");
    exit;
}
$role = strtolower($_SESSION['role']);
if ($role !== 'admin' && $role !== 'pengelola' && $role !== 'pemilik') {
    header("location:../../index.php");
    exit;
}

// 1. JIKA MENERIMA PERINTAH HAPUS (GET)
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    
    mysqli_query($conn, "DELETE FROM barang WHERE id_barang = '$id'");
    header("location:../../stok_barang.php");
    exit;
}

// 2. JIKA MENERIMA PERINTAH EDIT (POST)
if (isset($_POST['edit'])) {
    $id    = $_POST['id_barang'];
    $nama  = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $harga = $_POST['harga'];
    $stok  = $_POST['stok'];
    
    mysqli_query($conn, "UPDATE barang SET nama_barang = '$nama', harga = '$harga', stok = '$stok' WHERE id_barang = '$id'");
    header("location:../../stok_barang.php");
    exit;
}
?>