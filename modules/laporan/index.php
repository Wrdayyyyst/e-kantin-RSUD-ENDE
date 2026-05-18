<?php
session_start();
include "../../config/db.php";

// 1. Ambil data session & set base_path
$role = strtolower($_SESSION['role']);
$username = $_SESSION['username'];
$base_path = "../../"; 

// 2. Proteksi Halaman: Hanya Admin dan Pengelola/Pemilik
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:../../login.php");
    exit;
}

if ($role != 'admin' && $role != 'pengelola' && $role != 'pemilik') {
    header("location:../../index.php");
    exit;
}

// 3. Logika Filter Tanggal
$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : date('Y-m-01'); 
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : date('Y-m-d'); 

// Query mengambil data transaksi (sesuaikan jika nama tabel user kamu adalah 'users' atau 'user')
$query_laporan = mysqli_query($conn, "SELECT t.*, u.username FROM transaksi t 
                                      JOIN users u ON t.id_user = u.id_user 
                                      WHERE DATE(t.tgl_transaksi) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
                                      ORDER BY t.tgl_transaksi DESC");

// Query hitung total pendapatan
$query_total = mysqli_query($conn, "SELECT SUM(total_bayar) as total_pendapatan FROM transaksi 
                                    WHERE DATE(tgl_transaksi) BETWEEN '$tgl_mulai' AND '$tgl_selesai'");
$data_total = mysqli_fetch_assoc($query_total);
$total_pendapatan = $data_total['total_pendapatan'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendapatan | e-Kantin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex min-h-screen">

    <?php include "../../layout/sidebar.php"; ?>

    <main class="flex-1 p-8">
        <header class="mb-8">
            <h2 class="text-2xl font-bold text-slate-800 uppercase">Laporan Pendapatan</h2>
            <p class="text-slate-500 text-xs">Pantau total penjualan dan transaksi e-Kantin RSUD Ende</p>
        </header>

        <div class="mb-8 max-w-md">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 rounded-2xl shadow-md text-white">
                <div class="flex justify-between items-center opacity-80 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Total Pendapatan</span>
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <h3 class="text-3xl font-black">Rp <?= number_format($total_pendapatan); ?></h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm mb-6">
            <form method="GET" action="" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" value="<?= $tgl_mulai; ?>" class="p-2 border rounded-lg text-sm text-slate-700 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" value="<?= $tgl_selesai; ?>" class="p-2 border rounded-lg text-sm text-slate-700 outline-none">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition">
                    Filter Data
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100 border-b border-slate-200 text-slate-600">
                    <tr>
                        <th class="p-4 font-bold uppercase text-xs">No</th>
                        <th class="p-4 font-bold uppercase text-xs">ID Transaksi</th>
                        <th class="p-4 font-bold uppercase text-xs">Waktu</th>
                        <th class="p-4 font-bold uppercase text-xs">Kasir</th>
                        <th class="p-4 font-bold uppercase text-xs text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php 
                    $no = 1;
                    if (mysqli_num_rows($query_laporan) > 0) :
                        while($row = mysqli_fetch_array($query_laporan)) :
                    ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-4"><?= $no++; ?></td>
                        <td class="p-4 font-mono font-bold text-blue-600">#TRX-<?= $row['id_transaksi']; ?></td>
                        <td class="p-4 text-xs"><?= date('d M Y, H:i', strtotime($row['tgl_transaksi'])); ?></td>
                        <td class="p-4 text-xs uppercase font-medium"><?= $row['username']; ?></td>
                        <td class="p-4 text-right font-black">Rp <?= number_format($row['total_harga']); ?></td>
                    </tr>
                    <?php 
                        endwhile;
                    else : 
                    ?>
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 italic">Tidak ada data transaksi.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>