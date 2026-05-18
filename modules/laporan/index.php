<?php
session_start();
include "../../config/db.php";
/** @var mysqli $conn */

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

// Query mengambil data transaksi
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
    
    <style>
        @media print {
            /* Sembunyikan Sidebar, Form Filter, dan Tombol Cetak saat diprint */
            aside, .no-print, form, button, .btn-action {
                display: none !important;
            }
            /* Atur ukuran halaman dokumen PDF */
            @page {
                size: A4 portrait;
                margin: 20mm 15mm;
            }
            body {
                background-color: #ffffff;
                color: #000000;
                font-size: 12pt;
            }
            main {
                padding: 0 !important;
                margin: 0 !important;
            }
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 25px;
                border-bottom: 3px double #000;
                padding-bottom: 10px;
            }
            /* Pastikan tabel terlihat jelas saat dicetak */
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid #cbd5e1 !important;
                padding: 8px !important;
            }
        }
        .print-header { display: none; }
    </style>
</head>
<body class="bg-slate-50 flex min-h-screen">

    <?php include "../../layout/sidebar.php"; ?>

    <main class="flex-1 p-8">
        
        <div class="print-header">
            <h1 class="text-2xl font-black uppercase tracking-wide text-slate-900">E-KANTIN RSUD ENDE</h1>
            <p class="text-sm text-slate-600">Jl. Prof. Dr. W.Z. Johannes, Ende, Nusa Tenggara Timur</p>
            <h2 class="text-md font-bold uppercase mt-4 text-slate-800 tracking-wider">LAPORAN PENDAPATAN KANTIN</h2>
            <p class="text-xs text-slate-500 font-medium">Periode: <?= date('d M Y', strtotime($tgl_mulai)); ?> s/d <?= date('d M Y', strtotime($tgl_selesai)); ?></p>
        </div>

        <header class="mb-8 flex justify-between items-center no-print">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 uppercase">Laporan Pendapatan</h2>
                <p class="text-slate-500 text-xs">Pantau total penjualan dan transaksi e-Kantin RSUD Ende</p>
            </div>
            
            <button onclick="window.print()" class="bg-emerald-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-emerald-700 transition flex items-center gap-2 shadow-lg shadow-emerald-100">
                <i class="fas fa-print"></i> Cetak / Print PDF
            </button>
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

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm mb-6 no-print">
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
                        <td class="p-4 text-right font-black">Rp <?= number_format($row['total_bayar']); ?></td>
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