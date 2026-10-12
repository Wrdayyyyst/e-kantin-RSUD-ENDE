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
    
    <!-- Animasi CSS Tambahan khusus Halaman Dashboard -->
    <style>
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(2deg); }
        }
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.08); }
        }
        .animate-float {
            animation: floatSlow 5s ease-in-out infinite;
        }
        .animate-glow {
            animation: pulseGlow 3s ease-in-out infinite;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="bg-slate-50 flex min-h-screen">
    <?php include "layout/sidebar.php"; ?>
    
    <main class="flex-1 p-6 md:p-8 overflow-y-auto">
        
        <!-- HEADER KANAN DENGAN HOVER EFEK -->
        <header class="flex justify-between items-center mb-8 bg-white p-4 md:p-5 rounded-2xl shadow-sm border border-slate-200/80 hover:shadow-md transition-all duration-300">
            <div>
                <h2 class="text-xl font-black text-slate-800 uppercase tracking-tight flex items-center gap-2">
                    <i class="fas fa-chart-line text-blue-600 text-lg"></i> Dashboard Utama
                </h2>
                <p class="text-slate-500 text-xs mt-1 font-medium flex items-center gap-1.5">
                    <i class="far fa-calendar-alt text-blue-500"></i> <?= date('l, d F Y'); ?>
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-slate-800 leading-none capitalize"><?= $username; ?></p>
                    <span class="inline-block text-[10px] text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full font-bold uppercase mt-1 tracking-wider border border-blue-200/60">
                        <?= $role; ?>
                    </span>
                </div>
                <div class="relative group cursor-pointer">
                    <div class="w-11 h-11 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white font-black border-2 border-white shadow-md group-hover:scale-105 group-hover:rotate-3 transition-all duration-300">
                        <?= strtoupper(substr($username, 0, 1)); ?>
                    </div>
                    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                </div>
            </div>
        </header>

        <!-- BANNER UCAPAN SELAMAT DATANG (EFEK INTERAKTIF & ANIMASI DEKORATIF) -->
        <div class="relative overflow-hidden bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-800 p-8 rounded-3xl text-white shadow-xl shadow-blue-900/10 mb-8 border border-blue-600/30 transition-all duration-500 hover:shadow-2xl hover:shadow-blue-700/20 group">
            
            <!-- Ornamen Lingkaran Glow Abstrak -->
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-400/20 rounded-full blur-3xl animate-glow pointer-events-none"></div>
            <div class="absolute right-32 -top-12 w-48 h-48 bg-indigo-400/20 rounded-full blur-2xl animate-glow pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-3 py-1 rounded-full text-xs font-semibold text-blue-100 mb-3 border border-white/10">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-ping"></span> 
                        Sistem Siap Digunakan
                    </div>
                    <h3 class="text-2xl md:text-3xl font-black tracking-tight leading-tight">
                        Halo, <span class="capitalize text-amber-300 underline decoration-amber-400/50 underline-offset-4"><?= $username; ?></span>! 👋
                    </h3>
                    <p class="text-blue-100 mt-3 text-sm leading-relaxed font-normal">
                        Anda masuk sebagai <strong class="uppercase bg-white/20 px-2 py-0.5 rounded text-white font-bold"><?= $role; ?></strong>. 
                        <?php 
                        $role_cek = strtolower($role);

                        if ($role_cek == 'admin') {
                            echo "Anda memiliki otoritas penuh untuk mengelola data barang, laporan, dan hak akses pengguna sistem.";
                        } elseif ($role_cek == 'pengelola' || $role_cek == 'pemilik') {
                            echo "Anda bertugas untuk memantau ketersediaan stok menu dan mengawasi laporan pendapatan kantin secara berkala.";
                        } else {
                            echo "Anda bertugas untuk melayani transaksi penjualan pelanggan di meja kasir secara cepat dan akurat.";
                        }
                        ?>
                    </p>
                </div>

                <!-- Ikon Maskot Animasi Sederhana -->
                <div class="hidden lg:flex items-center justify-center w-24 h-24 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 shadow-inner group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 animate-float">
                    <i class="fas fa-store text-4xl text-amber-300"></i>
                </div>
            </div>
        </div>

        <!-- STATS / METRIC CARDS (INTERAKTIF SAAT DI-HOVER) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Card 1: Koneksi -->
            <div class="glass-card p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-2 h-full bg-emerald-500 transition-all group-hover:w-3"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Koneksi Database</p>
                        <p class="text-slate-800 font-extrabold mt-2 flex items-center gap-2.5 text-xl">
                            <span class="relative flex h-3 w-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                            </span> 
                            Stabil
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-database"></i>
                    </div>
                </div>
            </div>

            <!-- Card 2: Waktu Realtime Digital -->
            <div class="glass-card p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-2 h-full bg-blue-500 transition-all group-hover:w-3"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Waktu Real-time</p>
                        <p class="text-slate-800 font-extrabold mt-2 text-xl font-mono tracking-tight" id="jamRealtime">
                            <?= date('H:i'); ?> WITA
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>

            <!-- Card 3: Lokasi -->
            <div class="glass-card p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-2 h-full bg-indigo-500 transition-all group-hover:w-3"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Lokasi Operasional</p>
                        <p class="text-slate-800 font-extrabold mt-2 text-xl">RSUD Ende</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                        <i class="fas fa-hospital-user"></i>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <!-- Script Jam Digital Real-time agar detik terus berdetak tanpa merusak PHP -->
    <script>
        function updateJam() {
            const sekarang = new Date();
            const jam = String(sekarang.getHours()).padStart(2, '0');
            const menit = String(sekarang.getMinutes()).padStart(2, '0');
            const detik = String(sekarang.getSeconds()).padStart(2, '0');
            
            const elemenJam = document.getElementById('jamRealtime');
            if (elemenJam) {
                elemenJam.innerHTML = `${jam}:${menit}:${detik} <span class="text-xs font-bold text-blue-600">WITA</span>`;
            }
        }
        setInterval(updateJam, 1000);
        updateJam();
    </script>
</body>
</html>