<?php
session_start();

if (!isset($_SESSION['status'])) { 
    header("location:login.php"); 
    exit; 
}

include "config/db.php";
/** @var mysqli $conn */


$username = $_SESSION['username'];
$base_path = "./"; 

$role = $_SESSION['role']; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | e-Kantin RSUD Ende</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex min-h-screen">
    <?php include "layout/sidebar.php"; ?>
    
    <main class="flex-1 p-8">
        <header class="flex justify-between items-center mb-8 bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <div>
                <h2 class="text-xl font-bold text-slate-800 uppercase tracking-tight">Dashboard Utama</h2>
                <p class="text-slate-500 text-xs mt-1"><?= date('l, d F Y'); ?></p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-bold text-slate-800 leading-none"><?= $username; ?></p>
                    <p class="text-[10px] text-blue-600 font-bold uppercase mt-1 tracking-tighter">
                        Role: <?= $role; ?>
                    </p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 font-bold border border-blue-200 shadow-sm">
                    <?= strtoupper(substr($username, 0, 1)); ?>
                </div>
            </div>
        </header>

        <div class="bg-gradient-to-r from-blue-700 to-blue-800 p-8 rounded-2xl text-white shadow-lg mb-8">
            <h3 class="text-2xl font-bold">Halo, <?= $username; ?>!</h3>
            <p class="text-blue-100 mt-2 text-sm max-w-xl leading-relaxed">
                Anda masuk sebagai <strong class="uppercase"><?= $role; ?></strong>. 
                <?php 
                if ($role == 'admin') {
                    echo "Anda memiliki otoritas penuh untuk mengelola data barang, laporan, dan hak akses pengguna sistem.";
                } elseif ($role == 'pengelola' || $role == 'pemilik') {
                    echo "Anda bertugas untuk memantau ketersediaan stok menu dan mengawasi laporan pendapatan kantin secara berkala.";
                } else {
                    echo "Anda bertugas untuk melayani transaksi penjualan pelanggan di meja kasir secara cepat dan akurat.";
                }
                ?>
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Koneksi Database</p>
                <p class="text-slate-800 font-bold mt-2 flex items-center gap-2 text-lg">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Stabil
                </p>
            </div>
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm text-center md:text-left">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Waktu Lokal</p>
                <p class="text-slate-800 font-bold mt-2 text-lg"><?= date('H:i'); ?> WITA</p>
            </div>
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm text-center md:text-left">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Lokasi Operasional</p>
                <p class="text-slate-800 font-bold mt-2 text-lg">RSUD Ende</p>
            </div>
        </div>
    </main>

</body>
</html>