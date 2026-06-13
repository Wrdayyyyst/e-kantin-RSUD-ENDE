<?php
session_start();
// Jalur database sudah benar masuk ke folder config
include "config/db.php"; 

// Tangkap nama dan meja pembeli yang aktif dari session
$nama = $_SESSION['nama_pemesan'] ?? '';
$meja = $_SESSION['nomor_meja'] ?? '';

if (!empty($nama) && !empty($meja)) {
    
    // KODE YANG SUDAH DISELARASKAN DENGAN PHPMYADMIN KAMU:
    // Tabel: transaksi | Kolom Status: status_pesanan | ID: id_transaksi
    $query = mysqli_query($conn, "SELECT status_pesanan FROM transaksi WHERE nama_pemesan = '$nama' AND nomor_meja = '$meja' ORDER BY id_transaksi DESC LIMIT 1");

    if (!$query) {
        echo "Error Query: " . mysqli_error($conn);
        exit;
    }

    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        echo $data['status_pesanan']; // Mencetak isi status: Pending / Diproses / Selesai
    } else {
        echo "Belum Ada";
    }
} else {
    echo "No Session";
}
?>