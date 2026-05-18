<?php
session_start();

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php");
    exit;
}

$role = strtolower($_SESSION['role']);
if ($role !== 'admin' && $role !== 'pengelola' && $role !== 'pemilik') {
    header("location:index.php");
    exit;
}

include "config/db.php";

if (isset($_POST['tambah'])) {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $harga = $_POST['harga'];
    $stok  = $_POST['stok'];
    
    mysqli_query($conn, "INSERT INTO barang (nama_barang, harga, stok) VALUES ('$nama', '$harga', '$stok')");
    header("location:stok_barang.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Stok Barang | e-Kantin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex min-h-screen">

    <?php include "layout/sidebar.php"; ?>

    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-slate-800 uppercase">Manajemen Stok Barang</h2>
            
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-bold hover:bg-blue-700 transition flex items-center gap-2 shadow-md">
                <i class="fas fa-plus"></i> Tambah Menu
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase">No</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase">Nama Produk</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase text-right">Harga</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase text-center">Stok</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php 
                    $no = 1;
                    $data = mysqli_query($conn, "SELECT * FROM barang"); 
                    while($row = mysqli_fetch_array($data)) {
                    ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-4 text-sm text-slate-600"><?= $no++; ?></td>
                        <td class="p-4 text-sm font-bold text-slate-800"><?= $row['nama_barang']; ?></td>
                        <td class="p-4 text-sm text-slate-800 text-right">Rp <?= number_format($row['harga']); ?></td>
                        <td class="p-4 text-sm text-center">
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-md font-bold"><?= $row['stok']; ?></span>
                        </td>
                        <td class="p-4 text-center flex justify-center gap-3">
                            <button onclick="bukaModalEdit('<?= $row['id_barang']; ?>', '<?= addslashes($row['nama_barang']); ?>', '<?= $row['harga']; ?>', '<?= $row['stok']; ?>')" class="text-blue-500 hover:text-blue-700 transition">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="modules/barang/aksi.php?hapus=<?= $row['id_barang']; ?>" onclick="return confirm('Yakin ingin menghapus menu <?= addslashes($row['nama_barang']); ?>?')" class="text-red-500 hover:text-red-700 transition">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="modalTambah" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-40">
        <div class="bg-white p-6 rounded-xl w-full max-w-md shadow-2xl">
            <h3 class="text-xl font-bold mb-4">Tambah Menu Baru</h3>
            <form action="" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700">Nama Produk</label>
                    <input type="text" name="nama_produk" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700">Harga (Rp)</label>
                    <input type="number" name="harga" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700">Jumlah Stok</label>
                    <input type="number" name="stok" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="px-4 py-2 text-slate-500 font-bold">Batal</button>
                    <button type="submit" name="tambah" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700">Simpan Menu</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEdit" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white p-6 rounded-xl w-full max-w-md shadow-2xl">
            <h3 class="text-xl font-bold mb-4">Edit Menu Barang</h3>
            <form action="modules/barang/aksi.php" method="POST" class="space-y-4">
                <input type="hidden" name="id_barang" id="edit_id">
                <div>
                    <label class="block text-sm font-bold text-slate-700">Nama Produk</label>
                    <input type="text" name="nama_produk" id="edit_nama" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700">Harga (Rp)</label>
                    <input type="number" name="harga" id="edit_harga" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700">Jumlah Stok</label>
                    <input type="number" name="stok" id="edit_stok" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" class="px-4 py-2 text-slate-500 font-bold">Batal</button>
                    <button type="submit" name="edit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bukaModalEdit(id, nama, harga, stok) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_harga').value = harga;
            document.getElementById('edit_stok').value = stok;
            document.getElementById('modalEdit').classList.remove('hidden');
        }
    </script>
</body>
</html>