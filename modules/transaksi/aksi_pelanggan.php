<?php
session_start();

// 1. HUBUNGKAN KONEKSI DATABASE (Mundur 2 folder agar tepat ke root/config)
include "../../config/db.php"; 
/** @var mysqli $conn */

// Pengecekan apakah koneksi berhasil, jika gagal langsung stop
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// 2. TANGKAP DATA KIRIMAN FORM DARI HP KATALOG PEMBELI
// Menggunakan mysqli_real_escape_string agar aman dari SQL Injection
$nama_pemesan      = mysqli_real_escape_string($conn, $_POST['nama_pemesan']);
$nomor_meja        = mysqli_real_escape_string($conn, $_POST['nomor_meja']);
$metode_pembayaran = mysqli_real_escape_string($conn, $_POST['metode_pembayaran']);
$total_bayar       = mysqli_real_escape_string($conn, $_POST['total_bayar']); 

// Validasi sederhana, jika form dikirim dalam keadaan kosong, kembalikan ke katalog
if (empty($nama_pemesan) || empty($nomor_meja) || empty($total_bayar)) {
    echo "<script>alert('Data pesanan tidak lengkap!'); window.history.back();</script>";
    exit;
}

// 3. PROSES SIMPAN KE TABEL UTAMA (transaksi)
// Kolom nama_pemesan, nomor_meja, dan metode_pembayaran wajib diisi agar tidak error default value
$query_transaksi = "INSERT INTO transaksi 
                    (nama_pemesan, nomor_meja, metode_pembayaran, total_bayar, status_pesanan) 
                    VALUES 
                    ('$nama_pemesan', '$nomor_meja', '$metode_pembayaran', '$total_bayar', 'Pending')";

if (mysqli_query($conn, $query_transaksi)) {
    // Ambil ID Transaksi yang baru saja tersimpan secara otomatis
    $id_transaksi_baru = mysqli_insert_id($conn);
    
    // 4. PROSES SIMPAN KE TABEL DETAIL (detail_transaksi)
    // Di sini sistem membaca array item menu yang dibeli pembeli dari keranjang katalog
    if (isset($_POST['items']) && is_array($_POST['items'])) {
        foreach ($_POST['items'] as $item) {
            $id_barang = mysqli_real_escape_string($conn, $item['id_barang']);
            $jumlah    = mysqli_real_escape_string($conn, $item['jumlah']);
            $subtotal  = mysqli_real_escape_string($conn, $item['subtotal']);
            
            // Masukkan tiap item makanan/minuman ke dalam detail_transaksi
            $query_detail = "INSERT INTO detail_transaksi 
                             (id_transaksi, id_barang, jumlah, subtotal) 
                             VALUES 
                             ('$id_transaksi_baru', '$id_barang', '$jumlah', '$subtotal')";
            mysqli_query($conn, $query_detail);
        }
    }
    
    // 5. REDIRECT JIKA SUKSES
    // Pembeli diarahkan ke halaman sukses/notifikasi kalau pesanan sudah terkirim ke dapur/kasir
    echo "<script>
            alert('Pesanan berhasil dikirim! Silakan tunggu di meja nomor $nomor_meja.'); 
            window.location.href = '../../katalog.php'; 
          </script>";
    exit;

} else {
    // Jika query utama gagal, tampilkan pesan error SQL-nya
    echo "Gagal menyimpan transaksi ke database: " . mysqli_error($conn);
}
?>