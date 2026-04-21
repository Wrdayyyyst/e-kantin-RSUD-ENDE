<?php
session_start();

// Cek apakah sudah login, jika belum balikkan ke halaman login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php");
    exit;
}

include "config/db.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | e-Kantin RSUD Ende</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans">

    <nav class="bg-blue-800 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">e-Kantin RSUD Ende</h1>
            <div class="flex items-center gap-4">
                <span class="text-sm">Halo, <strong><?= $_SESSION['username']; ?></strong> (<?= $_SESSION['role']; ?>)</span>
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-sm transition">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-8 px-4">
        <div class="bg-white p-6 rounded-xl shadow-sm mb-6">
            <h2 class="text-2xl font-semibold text-slate-800">Selamat Datang di Sistem e-Kantin</h2>
            <p class="text-slate-600 mt-1">Gunakan menu di bawah untuk mengelola operasional kantin hari ini.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <a href="transaksi.php" class="bg-white p-6 rounded-xl shadow-sm border-b-4 border-blue-500 hover:shadow-md transition">
                <h3 class="font-bold text-lg text-blue-700">🛒 Transaksi Baru</h3>
                <p class="text-sm text-slate-500 mt-2">Mulai input pesanan pembeli dan cetak struk.</p>
            </a>

            <?php if ($_SESSION['role'] == 'admin') : ?>
            <a href="stok_barang.php" class="bg-white p-6 rounded-xl shadow-sm border-b-4 border-green-500 hover:shadow-md transition">
                <h3 class="font-bold text-lg text-green-700">📦 Stok Barang</h3>
                <p class="text-sm text-slate-500 mt-2">Tambah menu baru atau update ketersediaan stok.</p>
            </a>

            <a href="laporan.php" class="bg-white p-6 rounded-xl shadow-sm border-b-4 border-purple-500 hover:shadow-md transition">
                <h3 class="font-bold text-lg text-purple-700">📊 Laporan Penjualan</h3>
                <p class="text-sm text-slate-500 mt-2">Lihat rekapitulasi pendapatan harian dan bulanan.</p>
            </a>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>