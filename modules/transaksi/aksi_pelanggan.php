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
$query_transaksi = "INSERT INTO transaksi 
                    (nama_pemesan, nomor_meja, metode_pembayaran, total_bayar, status_pesanan) 
                    VALUES 
                    ('$nama_pemesan', '$nomor_meja', '$metode_pembayaran', '$total_bayar', 'Pending')";

if (mysqli_query($conn, $query_transaksi)) {
    // Ambil ID Transaksi yang baru saja tersimpan secara otomatis
    $id_transaksi_baru = mysqli_insert_id($conn);
    
    // 4. PROSES SIMPAN KE TABEL DETAIL (MENYELARASKAN DENGAN KATALOG.PHP)
    // Membaca array id_barang_array dan jumlah_array yang dikirim dari form katalog
    if (isset($_POST['id_barang_array']) && is_array($_POST['id_barang_array'])) {
        
        $array_id_barang = $_POST['id_barang_array'];
        $array_jumlah    = $_POST['jumlah_array'];

        // Looping berdasarkan indeks data barang yang dibeli
        foreach ($array_id_barang as $index => $id_b) {
            $id_barang = mysqli_real_escape_string($conn, $id_b);
            $jumlah    = mysqli_real_escape_string($conn, $array_jumlah[$index]);

            // Ambil harga asli barang dari database untuk menghitung subtotal asli di sistem
            $query_harga = mysqli_query($conn, "SELECT harga FROM barang WHERE id_barang = '$id_barang'");
            $data_harga  = mysqli_fetch_assoc($query_harga);
            $harga_asli  = isset($data_harga['harga']) ? $data_harga['harga'] : 0;
            
            // Hitung subtotal tiap barang
            $subtotal = $harga_asli * $jumlah;

            // Masukkan data barang satu per satu ke tabel detail_transaksi
            $query_detail = "INSERT INTO detail_transaksi 
                             (id_transaksi, id_barang, jumlah, subtotal) 
                             VALUES 
                             ('$id_transaksi_baru', '$id_barang', '$jumlah', '$subtotal')";
            mysqli_query($conn, $query_detail);
        }
    }
    
    // 5. REDIRECT JIKA SUKSES
    echo "<script>
            alert('Pesanan berhasil dikirim! Silakan tunggu di meja nomor $nomor_meja.'); 
            window.location.href = '../../katalog.php'; 
          </script>";
    exit;

} else {
    echo "Gagal menyimpan transaksi ke database: " . mysqli_error($conn);
}
?>