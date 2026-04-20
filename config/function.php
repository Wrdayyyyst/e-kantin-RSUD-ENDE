<?php
// Fungsi untuk memformat angka ke Rupiah
function rupiah($angka) {
    $hasil_rupiah = "Rp " . number_format($angka, 0, ',', '.');
    return $hasil_rupiah;
}

// Fungsi untuk mengamankan input dari karakter aneh (Security)
function input($data) {
    global $conn;
    $data = mysqli_real_escape_string($conn, $data);
    $data = htmlspecialchars($data);
    return $data;
}
?>