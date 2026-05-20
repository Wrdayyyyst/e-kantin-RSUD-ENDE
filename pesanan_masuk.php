<?php
session_start();
include "config/db.php";
/** @var mysqli $conn */

// 1. Proteksi login kasir/admin
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    header("location:login.php");
    exit;
}

// 2. Definisikan variabel role agar bisa dibaca oleh file layout/sidebar.php
$role = $_SESSION['role']; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan Masuk | e-Kantin RSUD Ende</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta http-equiv="refresh" content="5"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex">

    <?php include "layout/sidebar.php"; ?>

    <main class="flex-1 p-6 md:p-8 ml-0">
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-wide uppercase">Pesanan Masuk (Real-Time)</h2>
                <p class="text-slate-500 text-xs">Pantau pesanan mandiri pelanggan via QR Code secara langsung</p>
            </div>
            <span class="text-xs bg-blue-100 text-blue-700 font-bold px-3 py-1.5 rounded-full flex items-center gap-1.5 animate-pulse">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span> Live Monitoring
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            // Mengambil semua data transaksi dari database yang statusnya belum 'Selesai'
            $query_transaksi = mysqli_query($conn, "SELECT * FROM transaksi WHERE status_pesanan != 'Selesai' ORDER BY id_transaksi DESC");
            
            if(mysqli_num_rows($query_transaksi) == 0) {
                echo '
                <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-slate-200 shadow-sm max-w-md mx-auto mt-10">
                    <i class="fas fa-inbox text-slate-300 text-5xl mb-4"></i>
                    <p class="text-slate-500 font-bold text-sm">Belum ada pesanan masuk.</p>
                    <p class="text-slate-400 text-xs mt-1">Layar akan otomatis ter-update begitu pembeli mengirim pesanan dari meja kantin.</p>
                </div>';
            }

            while($t = mysqli_fetch_assoc($query_transaksi)) {
                $id_trx = $t['id_transaksi'];
                $bg_status = ($t['status_pesanan'] == 'Pending') ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-blue-100 text-blue-700 border-blue-200';
            ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col justify-between hover:shadow-md transition duration-300">
                <div class="p-5 space-y-4">
                    <div class="flex justify-between items-start border-b pb-3">
                        <div>
                            <h3 class="font-black text-lg text-slate-800 capitalize tracking-wide"><?= $t['nama_pemesan']; ?></h3>
                            <span class="text-xs font-extrabold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md uppercase">Meja: <?= $t['nomor_meja']; ?></span>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full border <?= $bg_status; ?>">
                            <?= $t['status_pesanan']; ?>
                        </span>
                    </div>

                    <div class="space-y-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Menu Dipesan:</span>
                        <ul class="text-sm text-slate-600 space-y-2 max-h-36 overflow-y-auto pr-1">
                            <?php
                            // Cek opsi 1: Cari di tabel detail_transaksi terlebih dahulu
                            $query_detail = mysqli_query($conn, "SELECT d.*, b.nama_barang FROM detail_transaksi d JOIN barang b ON d.id_barang = b.id_barang WHERE d.id_transaksi = '$id_trx'");
                            
                            if(mysqli_num_rows($query_detail) > 0) {
                                // Jika tabel relasi detail berfungsi, tampilkan dari sini
                                while($d = mysqli_fetch_assoc($query_detail)) {
                                    echo "<li class='flex justify-between items-center bg-slate-50 p-2 rounded-lg border border-slate-100'>
                                            <span class='font-bold capitalize text-slate-700'>".$d['nama_barang']." <b class='text-blue-600 font-black ml-1 bg-blue-50 px-1.5 py-0.5 rounded text-xs'>x".$d['jumlah']."</b></span>
                                            <span class='text-slate-500 text-xs font-bold'>Rp ".number_format($d['subtotal'], 0, ',', '.')."</span>
                                          </li>";
                                }
                            } else if (isset($t['detail_order']) && !empty($t['detail_order'])) {
                                // Cek opsi 2: Jika tabel detail kosong, baca teks langsung dari kolom cadangan 'detail_order'
                                $items = explode(",", $t['detail_order']);
                                foreach ($items as $item) {
                                    echo "<li class='bg-slate-50 p-2 rounded-lg border border-slate-100 font-bold capitalize text-slate-700 flex justify-between items-center'>
                                            <span><i class='fas fa-check text-xs text-blue-500 mr-1.5'></i> ".trim($item)."</span>
                                          </li>";
                                }
                            } else {
                                // Jika dua-duanya kosong sama sekali
                                echo "<li class='text-xs text-red-500 italic bg-red-50 p-2.5 rounded-lg text-center font-medium'>
                                        <i class='fas fa-exclamation-triangle mr-1'></i> Data rincian menu kosong.
                                      </li>";
                            }
                            ?>
                        </ul>
                    </div>

                    <div class="bg-slate-900 text-white p-3 rounded-xl flex justify-between items-center text-xs">
                        <div>
                            <span class="text-[9px] text-slate-400 block uppercase font-bold">Metode:</span>
                            <span class="font-bold flex items-center gap-1">
                                <i class="fas <?= ($t['metode_pembayaran'] == 'QRIS') ? 'fa-qrcode text-emerald-400' : 'fa-money-bill-wave text-amber-400'; ?>"></i>
                                <?= $t['metode_pembayaran']; ?>
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] text-slate-400 block uppercase font-bold">Total Tagihan:</span>
                            <span class="font-black text-emerald-400 text-sm">Rp <?= number_format($t['total_bayar'], 0, ',', '.'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 px-5 py-3.5 border-t border-slate-100 flex gap-2">
                    <?php if($t['status_pesanan'] == 'Pending') : ?>
                        <button type="button" onclick="terimaPesanan(<?= $id_trx; ?>)" class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 rounded-xl transition shadow-sm flex items-center justify-center gap-1">
                            <i class="fas fa-cookie-bite"></i> Terima & Kerjakan
                        </button>
                    <?php else : ?>
                        <a href="modules/transaksi/update_status.php?id=<?= $id_trx; ?>&status=Selesai" class="w-full text-center bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2.5 rounded-xl transition shadow-sm flex items-center justify-center gap-1" onclick="return confirm('Pastikan pembayaran Tunai/QRIS fisik sudah divalidasi sukses sebelum menyelesaikan pesanan!')">
                            <i class="fas fa-check-circle"></i> Pesanan Selesai
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </main>

    <script>
        function terimaPesanan(idTransaksi) {
            fetch(`modules/transaksi/update_status.php?id=${idTransaksi}&status=Diproses`)
            .then(response => {
                if (response.ok) {
                    window.location.reload(); 
                } else {
                    alert('Gagal memproses pesanan, silakan coba lagi.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan jaringan.');
            });
        }
    </script>
</body>
</html>