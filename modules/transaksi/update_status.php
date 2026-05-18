<?php
session_start();
include "../../config/db.php";
/** @var mysqli $conn */

// Proteksi hak akses login kasir/admin
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:../../login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id_trx = mysqli_real_escape_string($conn, $_GET['id']);
    $status = mysqli_real_escape_string($conn, $_GET['status']);

    // Validasi status agar hanya menerima nilai yang diizinkan sistem
    if (in_array($status, ['Diproses', 'Selesai'])) {
        
        // Update data status di database phpMyAdmin
        $query = "UPDATE transaksi SET status_pesanan = '$status' WHERE id_transaksi = '$id_trx'";
        
        if (mysqli_query($conn, $query)) {
            header("location:../../pesanan_masuk.php");
            exit;
        } else {
            echo "Gagal mengubah status pesanan: " . mysqli_error($conn);
        }
    }
} else {
    header("location:../../pesanan_masuk.php");
    exit;
}
?>