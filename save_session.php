<?php
session_start();

// Menangkap data nama dan meja yang diketik pelanggan di HP
if (isset($_POST['nama']) && isset($_POST['meja'])) {
    $_SESSION['nama_pemesan'] = $_POST['nama'];
    $_SESSION['nomor_meja'] = $_POST['meja'];
    echo "Sesi Berhasil Disimpan";
}
?>