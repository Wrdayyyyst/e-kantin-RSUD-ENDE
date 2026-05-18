<?php
include "config/db.php";
/** @var mysqli $conn */
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Mandiri | e-Kantin RSUD Ende</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen pb-32">

    <header class="bg-gradient-to-r from-blue-700 to-indigo-800 text-white shadow-md sticky top-0 z-50 px-4 py-5 text-center rounded-b-2xl">
        <h1 class="text-2xl font-black tracking-wide flex items-center justify-center gap-2 uppercase">
            <i class="fas fa-utensils"></i> e-Kantin RSUD Ende
        </h1>
        <p class="text-xs text-blue-200 mt-1">Sistem Pemesanan Mandiri & Meja Digital</p>
    </header>

    <div class="mb-6 px-4 max-w-md mx-auto mt-6">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                <i class="fas fa-search"></i>
            </span>
            <input 
                type="text" 
                id="searchMenu" 
                placeholder="Cari makanan atau minuman..." 
                class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm text-slate-700 transition-all"
                onkeyup="cariMenu()"
            >
        </div>
    </div>

    <main class="max-w-md mx-auto px-4">
        
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-6 space-y-3">
            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider"><i class="fas fa-user-edit text-blue-600"></i> Informasi Pelanggan</h3>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nama Pemesan</label>
                    <input type="text" id="nama_pemesan" placeholder="Contoh: Budi" class="w-full p-2 border rounded-lg text-sm bg-slate-50 outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nomor Meja</label>
                    <input type="number" id="nomor_meja" placeholder="Contoh: 05" class="w-full p-2 border rounded-lg text-sm bg-slate-50 outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h2 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Silakan Pilih & Tekan Menu :</h2>
        </div>

        <div class="space-y-4">
            <?php
            $query = mysqli_query($conn, "SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang ASC");
            
            if (mysqli_num_rows($query) == 0) {
                echo '
                <div class="bg-white rounded-xl p-8 text-center border border-slate-200 shadow-sm">
                    <i class="fas fa-cookie-bite text-slate-300 text-4xl mb-3"></i>
                    <p class="text-slate-500 font-medium text-sm">Mohon maaf, semua menu makanan saat ini sedang habis.</p>
                </div>';
            }

            while ($row = mysqli_fetch_array($query)) {
            ?>
            <div class="card-menu bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex items-center justify-between hover:border-blue-300 transition duration-300">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                        <i class="fas fa-hamburger text-sm"></i>
                    </div>
                    <div>
                        <h3 class="nama-menu font-bold text-slate-800 text-base capitalize"><?= $row['nama_barang']; ?></h3>
                        <p class="text-xs text-slate-400 mt-0.5">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
                    </div>
                </div>
                
                <div class="text-right">
                    <button type="button" onclick="pilihMenu('<?= $row['id_barang']; ?>', '<?= $row['nama_barang']; ?>', <?= $row['harga']; ?>, <?= $row['stok']; ?>)" class="bg-blue-600 text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm">
                        <i class="fas fa-plus"></i> Pesan
                    </button>
                </div>
            </div>
            <?php } ?>
        </div>
    </main>

    <form id="form_order" action="modules/transaksi/aksi_pelanggan.php" method="POST">
        <input type="hidden" name="nama_pemesan" id="form_nama">
        <input type="hidden" name="nomor_meja" id="form_meja">
        <input type="hidden" name="total_bayar" id="form_total">
        <input type="hidden" name="metode_pembayaran" id="form_metode">
        <div id="wrapper_input_barang"></div>

        <div id="floating_cart" class="hidden fixed bottom-0 left-0 right-0 max-w-md mx-auto bg-white border-t border-slate-200 shadow-[0_-5px_20px_rgba(0,0,0,0.05)] p-4 rounded-t-2xl z-50">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <span class="text-xs text-slate-400 block">Total Pesanan Kamu:</span>
                    <span class="text-lg font-black text-blue-700" id="label_total_pelanggan">Rp 0</span>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="setMetode('TUNAI')" class="bg-slate-800 text-white text-xs font-bold px-3 py-2.5 rounded-lg hover:bg-slate-900 transition flex items-center gap-1">
                        <i class="fas fa-money-bill-wave"></i> Bayar Tunai
                    </button>
                    <button type="button" onclick="setMetode('QRIS')" class="bg-emerald-600 text-white text-xs font-bold px-3 py-2.5 rounded-lg hover:bg-emerald-700 transition flex items-center gap-1">
                        <i class="fas fa-qrcode"></i> Scan QRIS
                    </button>
                </div>
            </div>
            <div class="text-[10px] text-slate-400 border-t pt-2 text-center" id="instruksi_pembayaran">
                Silakan pilih metode pembayaran untuk mengirimkan pesanan ke dapur.
            </div>
        </div>
    </form>

    <script>
        let keranjangPelanggan = [];
        let totalBelanja = 0;

        function pilihMenu(id_barang, nama, harga, stokMax) {
            const namaInput = document.getElementById('nama_pemesan').value.trim();
            const mejaInput = document.getElementById('nomor_meja').value.trim();

            if(!namaInput || !mejaInput) {
                alert('Mohon isi Nama Pemesan dan Nomor Meja terlebih dahulu di bagian atas!');
                return;
            }

            const indeksAda = keranjangPelanggan.findIndex(item => item.id_barang === id_barang);
            if(indeksAda > -1) {
                if(keranjangPelanggan[indeksAda].jumlah >= stokMax) {
                    alert('Maaf, sisa porsi yang ada di dapur sudah maksimal!');
                    return;
                }
                keranjangPelanggan[indeksAda].jumlah += 1;
                keranjangPelanggan[indeksAda].subtotal = keranjangPelanggan[indeksAda].jumlah * harga;
            } else {
                keranjangPelanggan.push({ id_barang, nama, harga, jumlah: 1, subtotal: harga });
            }

            hitungTotalPelanggan();
        }

        function hitungTotalPelanggan() {
            const cartBar = document.getElementById('floating_cart');
            const wrapper = document.getElementById('wrapper_input_barang');
            
            totalBelanja = 0;
            wrapper.innerHTML = '';

            if(keranjangPelanggan.length === 0) {
                cartBar.classList.add('hidden');
                return;
            }

            cartBar.classList.remove('hidden');

            keranjangPelanggan.forEach(item => {
                totalBelanja += item.subtotal;
                wrapper.innerHTML += `
                    <input type="hidden" name="id_barang_array[]" value="${item.id_barang}">
                    <input type="hidden" name="jumlah_array[]" value="${item.jumlah}">
                `;
            });

            document.getElementById('label_total_pelanggan').innerText = 'Rp ' + totalBelanja.toLocaleString('id-ID');
            document.getElementById('form_total').value = totalBelanja;
        }

        function setMetode(metode) {
            const namaInput = document.getElementById('nama_pemesan').value.trim();
            const mejaInput = document.getElementById('nomor_meja').value.trim();

            document.getElementById('form_nama').value = namaInput;
            document.getElementById('form_meja').value = mejaInput;
            document.getElementById('form_metode').value = metode;

            if (metode === 'QRIS') {
                if(confirm('Pesanan Anda akan dikirim ke dapur. Silakan lakukan scan pada stiker QRIS FISIK yang berada di meja/kasir sebesar Rp ' + totalBelanja.toLocaleString('id-ID') + '. Apakah Anda sudah paham?')) {
                    document.getElementById('form_order').submit();
                }
            } else {
                if(confirm('Pesanan Anda akan dikirim ke dapur. Silakan bayar tunai sebesar Rp ' + totalBelanja.toLocaleString('id-ID') + ' ke loket kasir saat mengambil makanan. Kirim pesanan sekarang?')) {
                    document.getElementById('form_order').submit();
                }
            }
        }

        // FUNGSI PENCARIAN REAL-TIME
        function cariMenu() {
            let kataKunci = document.getElementById('searchMenu').value.toLowerCase();
            let daftarKartu = document.getElementsByClassName('card-menu');
            
            for (let i = 0; i < daftarKartu.length; i++) {
                let namaMakanan = daftarKartu[i].getElementsByClassName('nama-menu')[0].innerText.toLowerCase();
                
                if (namaMakanan.includes(kataKunci)) {
                    daftarKartu[i].style.display = ""; 
                } else {
                    daftarKartu[i].style.display = "none"; 
                }
            }
        }
    </script>
</body>
</html>