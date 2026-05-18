<?php
session_start();
include "config/db.php";
/** @var mysqli $conn */

// Proteksi Halaman: Hanya Admin dan Kasir yang boleh melakukan transaksi
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php");
    exit;
}

$role = strtolower($_SESSION['role']);
if ($role !== 'admin' && $role !== 'kasir') {
    header("location:index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Baru | e-Kantin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex min-h-screen">

    <?php include "layout/sidebar.php"; ?>

    <main class="flex-1 p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-800 uppercase">Transaksi Baru</h2>
            <p class="text-slate-500 text-xs">Pencatatan penjualan makanan dan minuman kantin</p>
        </div>

        <form action="modules/transaksi/aksi.php" method="POST">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                        <h3 class="text-sm font-bold text-slate-700 uppercase mb-4 flex items-center gap-2">
                            <i class="fas fa-search text-blue-500"></i> Pilih Menu Makanan / Minuman
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Menu</label>
                                <select id="pilih_barang" class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white text-sm">
                                    <option value="">-- Pilih Menu Kantin --</option>
                                    <?php 
                                    $produk = mysqli_query($conn, "SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang ASC");
                                    while($p = mysqli_fetch_array($produk)) {
                                        echo "<option value='".$p['id_barang']."' data-harga='".$p['harga']."' data-stok='".$p['stok']."'>".$p['nama_barang']." (Stok: ".$p['stok'].") - Rp ".number_format($p['harga'])."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jumlah</label>
                                <input type="number" id="jumlah_beli" min="1" value="1" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                            </div>
                        </div>
                        <button type="button" onclick="tambahKeKeranjang()" class="mt-4 w-full bg-slate-800 text-white p-2 rounded-lg font-bold hover:bg-slate-900 transition text-sm">
                            <i class="fas fa-plus mr-1"></i> Masukkan Keranjang
                        </button>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-4 bg-slate-100 border-b border-slate-200 font-bold text-xs text-slate-600 uppercase">
                            Daftar Belanja Sementara
                        </div>
                        <table class="w-full text-left border-collapse" id="tabel_keranjang">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="p-3 text-xs font-bold text-slate-500 uppercase">Menu</th>
                                    <th class="p-3 text-xs font-bold text-slate-500 uppercase text-right">Harga</th>
                                    <th class="p-3 text-xs font-bold text-slate-500 uppercase text-center">Jumlah</th>
                                    <th class="p-3 text-xs font-bold text-slate-500 uppercase text-right">Subtotal</th>
                                    <th class="p-3 text-xs font-bold text-slate-500 uppercase text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100" id="item_keranjang">
                                <tr id="baris_kosong">
                                    <td colspan="5" class="p-8 text-center text-sm text-slate-400">Belum ada item di keranjang.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 space-y-4">
                        
                        <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl text-center">
                            <span class="block text-xs font-bold text-blue-500 uppercase tracking-wider mb-1">Total yang Harus Dibayar</span>
                            <h1 class="text-3xl font-black text-blue-700" id="label_total">Rp 0</h1>
                            <input type="hidden" name="total_bayar" id="input_total" value="0">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Uang Tunai / Cash (Rp)</label>
                            <input type="number" name="jumlah_uang" id="jumlah_uang" oninput="hitungKembalian()" required class="w-full p-3 border rounded-lg text-lg font-bold focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex justify-between items-center">
                            <span class="text-sm font-bold text-slate-500">Kembalian:</span>
                            <span class="text-lg font-black text-slate-800" id="label_kembalian">Rp 0</span>
                            <input type="hidden" name="kembalian" id="input_kembalian" value="0">
                        </div>

                        <button type="submit" name="simpan_transaksi" class="w-full bg-blue-600 text-white p-3 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/20">
                            <i class="fas fa-save mr-1"></i> Simpan Transaksi
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </main>

    <script>
        let keranjang = [];
        let totalBelanja = 0;

        function tambahKeKeranjang() {
            const select = document.getElementById('pilih_barang');
            const id_barang = select.value;
            if(!id_barang) return alert('Silakan pilih menu terlebih dahulu!');

            const option = select.options[select.selectedIndex];
            const nama = option.text.split(' (Stok:')[0];
            const harga = parseInt(option.getAttribute('data-harga'));
            const stokMax = parseInt(option.getAttribute('data-stok'));
            const qty = parseInt(document.getElementById('jumlah_beli').value);

            if(qty > stokMax) return alert('Stok menu tidak mencukupi! Sisa stok: ' + stokMax);
            if(qty <= 0) return alert('Jumlah minimal pembelian adalah 1!');

            const indeksAda = keranjang.findIndex(item => item.id_barang === id_barang);
            if(indeksAda > -1) {
                if((keranjang[indeksAda].qty + qty) > stokMax) return alert('Total belanja di keranjang melampaui sisa stok!');
                keranjang[indeksAda].qty += qty;
                keranjang[indeksAda].subtotal = keranjang[indeksAda].qty * harga;
            } else {
                keranjang.push({ id_barang, nama, harga, qty, subtotal: qty * harga });
            }

            renderKeranjang();
        }

        function hapusItem(indeks) {
            keranjang.splice(indeks, 1);
            renderKeranjang();
        }

        function renderKeranjang() {
            const tbody = document.getElementById('item_keranjang');
            tbody.innerHTML = '';
            totalBelanja = 0;

            if(keranjang.length === 0) {
                tbody.innerHTML = `<tr id="baris_kosong"><td colspan="5" class="p-8 text-center text-sm text-slate-400">Belum ada item di keranjang.</td></tr>`;
                document.getElementById('label_total').innerText = 'Rp 0';
                document.getElementById('input_total').value = 0;
                hitungKembalian();
                return;
            }

            keranjang.forEach((item, indeks) => {
                totalBelanja += item.subtotal;
                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50 text-sm text-slate-700">
                        <td class="p-3 font-medium text-slate-800">
                            ${item.nama}
                            <input type="hidden" name="id_barang_array[]" value="${item.id_barang}">
                        </td>
                        <td class="p-3 text-right">Rp ${item.harga.toLocaleString()}</td>
                        <td class="p-3 text-center">
                            ${item.qty}
                            <input type="hidden" name="jumlah_array[]" value="${item.qty}">
                        </td>
                        <td class="p-3 text-right font-bold text-slate-900">Rp ${item.subtotal.toLocaleString()}</td>
                        <td class="p-3 text-center">
                            <button type="button" onclick="hapusItem(${indeks})" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });

            document.getElementById('label_total').innerText = 'Rp ' + totalBelanja.toLocaleString('id-ID');
            document.getElementById('input_total').value = totalBelanja;
            hitungKembalian();
        }

        function hitungKembalian() {
            const uangTunai = parseInt(document.getElementById('jumlah_uang').value) || 0;
            const kembalian = uangTunai - totalBelanja;

            if(kembalian >= 0) {
                document.getElementById('label_kembalian').innerText = 'Rp ' + kembalian.toLocaleString('id-ID');
                document.getElementById('label_kembalian').className = "text-lg font-black text-emerald-600";
                document.getElementById('input_kembalian').value = kembalian;
            } else {
                document.getElementById('label_kembalian').innerText = 'Rp 0';
                document.getElementById('label_kembalian').className = "text-lg font-black text-red-500";
                document.getElementById('input_kembalian').value = 0;
            }
        }
    </script>
</body>
</html>