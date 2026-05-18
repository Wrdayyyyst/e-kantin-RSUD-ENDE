<?php
// Logika otomatis mendeteksi posisi folder agar tidak 404
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
if ($current_dir == 'barang' || $current_dir == 'laporan' || $current_dir == 'transaksi') {
    $path = "../../"; // Jika di dalam subfolder modules
} else {
    $path = ""; // Jika di folder terluar (root)
}
?>

<aside class="w-64 bg-[#0f172a] text-white flex flex-col shadow-xl sticky top-0 h-screen no-print">
    <div class="p-6 text-center border-b border-slate-800">
        <h1 class="text-2xl font-bold tracking-widest text-blue-400">e-KANTIN</h1>
        <p class="text-[10px] text-slate-400 uppercase tracking-widest">RSUD Ende</p>
    </div>

    <nav class="flex-1 mt-6 px-4 space-y-1">
        <a href="<?= $path; ?>index.php" class="flex items-center gap-3 p-3 hover:bg-slate-800 rounded-lg transition text-sm font-medium text-slate-400 hover:text-white">
            <i class="fas fa-home w-5 text-center"></i> Dashboard
        </a>
        
        <?php if ($role == 'admin' || $role == 'kasir') : ?>
        <a href="<?= $path; ?>transaksi.php" class="flex items-center gap-3 p-3 hover:bg-slate-800 rounded-lg transition text-sm text-slate-400 hover:text-white">
            <i class="fas fa-shopping-cart w-5 text-center"></i> Transaksi Baru
        </a>
        <?php endif; ?>

        <?php if ($role == 'admin' || $role == 'pengelola' || $role == 'pemilik') : ?>
        <div class="pt-6 pb-2 text-[10px] font-bold text-slate-500 uppercase px-3 tracking-widest">Manajemen Data</div>
        
        <a href="<?= $path; ?>stok_barang.php" class="flex items-center gap-3 p-3 hover:bg-slate-800 rounded-lg text-sm text-slate-400 hover:text-white transition">
            <i class="fas fa-box w-5 text-center"></i> Stok Barang
        </a>
        
        <a href="<?= $path; ?>modules/laporan/index.php" class="flex items-center gap-3 p-3 hover:bg-slate-800 rounded-lg transition text-sm text-slate-400 hover:text-white">
            <i class="fas fa-chart-bar w-5 text-center"></i> Laporan
        </a>
        <?php endif; ?>

        <?php if ($role == 'admin') : ?>
        <div class="pt-6 pb-2 text-[10px] font-bold text-slate-500 uppercase px-3 tracking-widest">Sistem</div>
        <a href="<?= $path; ?>users.php" class="flex items-center gap-3 p-3 hover:bg-slate-800 rounded-lg transition text-sm text-slate-400 hover:text-white">
            <i class="fas fa-user-cog w-5 text-center"></i> Kelola User
        </a>
        <?php endif; ?>
    </nav>

    <div class="p-4 border-t border-slate-800">
        <a href="<?= $path; ?>logout.php" onclick="return confirm('Yakin ingin keluar?')" class="flex items-center gap-3 p-3 bg-red-500/10 text-red-500 hover:bg-red-600 hover:text-white rounded-lg transition text-sm font-bold justify-center">
            <i class="fas fa-power-off"></i> Logout
        </a>
    </div>
</aside>