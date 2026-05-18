<?php
session_start();
include "../../config/db.php";
/** @var mysqli $conn */

// Proteksi Halaman Aksi
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:../../login.php");
    exit;
}

if (isset($_POST['simpan_transaksi'])) {
    $total_bayar  = $_POST['total_bayar'];
    $jumlah_uang  = $_POST['jumlah_uang']; // Sesuai nama kolom di database
    $kembalian    = $_POST['kembalian'];   // Sesuai nama kolom di database
    $id_user      = $_SESSION['id_user'];  // Mengambil ID user yang sedang login

    // Menangkap array belanja dari keranjang JavaScript
    $id_barang_array = $_POST['id_barang_array'] ?? [];
    $jumlah_array    = $_POST['jumlah_array'] ?? []; // Menggunakan nama array jumlah

    if (empty($id_barang_array)) {
        echo "<script>alert('Gagal! Keranjang belanja sementara masih kosong.'); window.location='../../transaksi.php';</script>";
        exit;
    }

    if ($jumlah_uang < $total_bayar) {
        echo "<script>alert('Gagal! Jumlah uang tunai kurang dari total bayar.'); window.location='../../transaksi.php';</script>";
        exit;
    }

    // 1. INPUT DATA KE TABEL UTAMA: transaksi (Tanpa no_nota karena pakai id_transaksi AUTO_INCREMENT)
    $query_transaksi = "INSERT INTO transaksi (id_user, total_bayar, jumlah_uang, kembalian) VALUES ('$id_user', '$total_bayar', '$jumlah_uang', '$kembalian')";
    
    if (mysqli_query($conn, $query_transaksi)) {
        // Ambil ID Transaksi yang barusan otomatis ter-generate di database
        $id_transaksi = mysqli_insert_id($conn);

        // 2. INPUT DATA KE TABEL RELASI: detail_transaksi & POTONG STOK
        for ($i = 0; $i < count($id_barang_array); $i++) {
            $id_barang = mysqli_real_escape_string($conn, $id_barang_array[$i]);
            $jumlah    = (int)$jumlah_array[$i]; // Menggunakan variabel $jumlah sesuai database

            // Ambil data harga barang terbaru dari tabel barang
            $cek_barang = mysqli_query($conn, "SELECT harga FROM barang WHERE id_barang = '$id_barang'");
            $b = mysqli_fetch_assoc($cek_barang);
            $harga_satuan = $b['harga'];
            $subtotal = $jumlah * $harga_satuan;

            // Masukkan data ke detail_transaksi sesuai nama kolom database asli
            mysqli_query($conn, "INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah, subtotal) VALUES ('$id_transaksi', '$id_barang', '$jumlah', '$subtotal')");

            // Otomatis mengurangi stok di tabel barang
            mysqli_query($conn, "UPDATE barang SET stok = stok - $jumlah WHERE id_barang = '$id_barang'");
        }

        // Jika berhasil, munculkan notifikasi sukses
        echo "<script>alert('Berhasil! Transaksi kasir telah disimpan ke database.'); window.location='../../transaksi.php';</script>";
        exit;
    } else {
        echo "Aplikasi mengalami kendala database: " . mysqli_error($conn);
    }
}
?>