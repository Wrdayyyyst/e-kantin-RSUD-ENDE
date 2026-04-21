<?php
session_start();
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
    <title>Dashboard | e-Kantin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-100 flex">

    <aside class="w-64 bg-blue-900 h-screen sticky top-0 text-white flex flex-col shadow-xl">
        <div class="p-6 text-center border-b border-blue-800">
            <h1 class="text-2xl font-bold tracking-widest">e-KANTIN</h1>
            <p class="text-xs text-blue-300">RSUD ENDE</p>
        </div>

        <nav class="flex-1 mt-6 px-4 space-y-2">
            <a href="index.php" class="flex items-center gap-3 p-3 bg-blue-800 rounded-lg transition">
                <i class="fas fa-home w-5"></i> Dashboard
            </a>
            
            <a href="transaksi.php" class="flex items-center gap-3 p-3 hover:bg-blue-800 rounded-lg transition">
                <i class="fas fa-shopping-cart w-5"></i> Transaksi
            </a>

            <?php if ($_SESSION['role'] == 'admin') : ?>
            <div class="pt-4 pb-2 text-xs font-semibold text-blue-400 uppercase tracking-wider">
                Manajemen Data
            </div>
            <a href="stok_barang.php" class="flex items-center gap-3 p-3 hover:bg-blue-800 rounded-lg transition">
                <i class="fas fa-box w-5"></i> Stok Barang
            </a>
            <a href="laporan.php" class="flex items-center gap-3 p-3 hover:bg-blue-800 rounded-lg transition">
                <i class="fas fa-chart-line w-5"></i> Laporan
            </a>
            <a href="manajemen_user.php" class="flex items-center gap-3 p-3 hover:bg-blue-800 rounded-lg transition">
                <i class="fas fa-users w-5"></i> Kelola User
            </a>
            <?php endif; ?>
        </nav>

        <div class="p-4 border-t border-blue-800">
            <a href="logout.php" class="flex items-center gap-3 p-3 bg-red-600 hover:bg-red-700 rounded-lg transition text-center justify-center font-semibold">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>

    <main class="flex-1 p-8">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Dashboard Utama</h2>
                <p class="text-slate-500 text-sm">Selamat datang kembali, <?= $_SESSION['username']; ?>.</p>
            </div>
            <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-200 flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs text-slate-500 leading-none">Login sebagai:</p>
                    <p class="text-sm font-bold text-slate-700 leading-tight"><?= ucfirst($_SESSION['role']); ?></p>
                </div>
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold">
                    <?= strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-600">
                <p class="text-slate-500 text-sm font-medium">Penjualan Hari Ini</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">Rp 0</h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-600">
                <p class="text-slate-500 text-sm font-medium">Total Menu</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">12 Menu</h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-orange-600">
                <p class="text-slate-500 text-sm font-medium">Stok Menipis</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">3 Item</h3>
            </div>
        </div>

        <div class="mt-8 bg-white p-8 rounded-xl shadow-sm border border-slate-200">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Informasi Sistem</h3>
            <p class="text-slate-600 leading-relaxed">
                Anda telah masuk ke sistem manajemen e-Kantin RSUD Ende. Gunakan menu di sebelah kiri untuk melakukan transaksi atau mengelola data operasional kantin.
            </p>
        </div>
    </main>

</body>
</html>