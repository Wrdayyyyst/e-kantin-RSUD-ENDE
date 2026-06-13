<?php
include "config/db.php";
$cek = mysqli_query($conn, "SELECT * FROM pesanan");
if (!$cek) {
    echo "Error Database: " . mysqli_error($conn);
} else {
    echo "Tabel 'pesanan' ditemukan. Jumlah data: " . mysqli_num_rows($cek);
}
?>