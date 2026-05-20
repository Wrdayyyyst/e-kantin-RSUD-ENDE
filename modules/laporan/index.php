<?php
session_start();
include "../../config/db.php";
/** @var mysqli $conn */

// 1. Ambil data session & set base_path
$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Kasir';
$base_path = "../../"; 

// Proteksi Halaman: Hanya Admin dan Pengelola/Pemilik
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:../../login.php");
    exit;
}

if ($role != 'admin' && $role != 'pengelola' && $role != 'pemilik') {
    header("location:../../index.php");
    exit;
}

// 2. Logika Filter Tanggal (WAJIB DI ATAS AGAR TIDAK KOSONG)
$tgl_mulai = (isset($_GET['tgl_mulai']) && $_GET['tgl_mulai'] != '') ? $_GET['tgl_mulai'] : date('Y-m-01'); 
$tgl_selesai = (isset($_GET['tgl_selesai']) && $_GET['tgl_selesai'] != '') ? $_GET['tgl_selesai'] : date('Y-m-d'); 

// 3. Logika Proses Hapus Manual (Sekarang aman karena variabel tanggal sudah siap)
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])) {
    $id_hapus = $_GET['id'];
    
    // Proses hapus data berdasarkan ID Transaksi yang dipilih
    $hapus = mysqli_query($conn, "DELETE FROM transaksi WHERE id_transaksi = '$id_hapus'");
    
    if ($hapus) {
        echo "<script>
                alert('Data transaksi berhasil dihapus!');
                window.location.href='index.php?tgl_mulai=$tgl_mulai&tgl_selesai=$tgl_selesai';
              </script>";
        exit;
    }
}

// 4. Query Ambil Riwayat Transaksi Sukses Berdasarkan Filter Tanggal & Status 'Selesai'
$query_laporan = mysqli_query($conn, "SELECT * FROM transaksi 
                                      WHERE status_pesanan = 'Selesai' 
                                      AND DATE(tgl_transaksi) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
                                      ORDER BY id_transaksi DESC");

// 5. Query Hitung Total Pendapatan Sukses Berdasarkan Filter Tanggal
$query_total = mysqli_query($conn, "SELECT SUM(total_bayar) as total_pendapatan FROM transaksi 
                                    WHERE status_pesanan = 'Selesai' 
                                    AND DATE(tgl_transaksi) BETWEEN '$tgl_mulai' AND '$tgl_selesai'");
$data_total = mysqli_fetch_assoc($query_total);
$total_pendapatan = $data_total['total_pendapatan'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendapatan | e-Kantin RSUD Ende</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        @media print {
            aside, .no-print, form, button, .btn-action {
                display: none !important;
            }
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

    <div class="no-print">
        <?php include "../../layout/sidebar.php"; ?>
    </div>

    <main class="flex-1 p-8">
        
        <div class="print-header">
            <h1 class="text-2xl font-black uppercase tracking-wide text-slate-900">E-KANTIN RSUD ENDE</h1>
            <p class="text-xs text-slate-600">Jl. Prof. Dr. W.Z. Johannes, Ende, Nusa Tenggara Timur</p>
            <h2 class="text-md font-bold uppercase mt-4 text-slate-800 tracking-wider">LAPORAN PENDAPATAN KANTIN</h2>
            <p class="text-xs text-slate-500 font-medium">Periode: <?= date('d M Y', strtotime($tgl_mulai)); ?> s/d <?= date('d M Y', strtotime($tgl_selesai)); ?></p>
        </div>

        <header class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print">
            <div>
                <h2 class="text-2xl font-black text-slate-800 uppercase tracking-wide">Laporan Pendapatan</h2>
                <p class="text-slate-500 text-xs">Pantau total penjualan dan transaksi e-Kantin RSUD Ende secara real-time</p>
            </div>
            
            <button onclick="window.print()" class="bg-emerald-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-emerald-700 transition flex items-center gap-2 shadow-lg shadow-emerald-100">
                <i class="fas fa-print"></i> Cetak / Print PDF
            </button>
        </header>

        <div class="mb-8 max-w-md">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 rounded-2xl shadow-md text-white relative overflow-hidden">
                <div class="flex justify-between items-center opacity-80 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Total Omset (Status: Selesai)</span>
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <h3 class="text-3xl font-black">Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                <span class="absolute right-2 bottom-2 text-[10px] opacity-40 uppercase font-bold no-print">Real-Time Live</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm mb-6 no-print">
            <form method="GET" action="" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" value="<?= $tgl_mulai; ?>" class="p-2 border rounded-lg text-sm text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" value="<?= $tgl_selesai; ?>" class="p-2 border rounded-lg text-sm text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm">
                    Filter Data
                </button>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100 border-b border-slate-200 text-slate-600 font-bold text-xs uppercase tracking-wider">
                    <tr>
                        <th class="p-4 w-12 text-center">No</th>
                        <th class="p-4">ID Transaksi</th>
                        <th class="p-4">Nama Pelanggan</th>
                        <th class="p-4 text-center">No Meja</th>
                        <th class="p-4">Metode</th>
                        <th class="p-4">Waktu Selesai</th>
                        <th class="p-4 text-right">Total Bayar</th>
                        <th class="p-4 text-center no-print w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                    <?php 
                    $no = 1;
                    if (mysqli_num_rows($query_laporan) > 0) :
                        while($row = mysqli_fetch_array($query_laporan)) :
                            $badge_metode = ($row['metode_pembayaran'] == 'QRIS') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-700 border-slate-200';
                    ?>
                    <tr class="hover:bg-slate-50/80 transition font-medium">
                        <td class="p-4 text-center text-slate-400 font-bold"><?= $no++; ?></td>
                        <td class="p-4 font-mono font-bold text-blue-600">#TRX-<?= $row['id_transaksi']; ?></td>
                        <td class="p-4 font-black text-slate-800 capitalize"><?= $row['nama_pemesan']; ?></td>
                        <td class="p-4 text-center"><span class="bg-blue-50 text-blue-700 font-bold px-2 py-0.5 rounded text-[10px]">Meja <?= $row['nomor_meja']; ?></span></td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded border <?= $badge_metode; ?>">
                                <?= $row['metode_pembayaran']; ?>
                            </span>
                        </td>
                        <td class="p-4 text-slate-400 font-bold"><?= date('d M Y, H:i', strtotime($row['tgl_transaksi'])); ?></td>
                        <td class="p-4 text-right font-black text-slate-900 text-sm">Rp <?= number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                        
                        <td class="p-4 text-center no-print">
                            <a href="index.php?aksi=hapus&id=<?= $row['id_transaksi']; ?>&tgl_mulai=<?= $tgl_mulai; ?>&tgl_selesai=<?= $tgl_selesai; ?>" 
                               onclick="return confirm('Apakah kamu yakin ingin menghapus permanen data transaksi #TRX-<?= $row['id_transaksi']; ?> ini, Jan?')" 
                               class="text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 p-2 rounded-lg transition inline-block">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else : 
                    ?>
                    <tr>
                        <td colspan="8" class="p-12 text-center text-slate-400 font-bold italic">
                            <i class="fas fa-receipt block text-3xl mb-2 text-slate-300 not-italic"></i>
                            Tidak ada data transaksi sukses pada tanggal terpilih.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        if (!window.matchMedia('print').matches) {
            setTimeout(function() {
                let urlParams = new URLSearchParams(window.location.search);
                let tglMulai = urlParams.get('tgl_mulai') || '<?= $tgl_mulai; ?>';
                let tglSelesai = urlParams.get('tgl_selesai') || '<?= $tgl_selesai; ?>';
                window.location.href = `index.php?tgl_mulai=${tglMulai}&tgl_selesai=${tglSelesai}`;
            }, 5000);
        }
    </script>
</body>
</html>