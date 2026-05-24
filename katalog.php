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
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    <div class="flex-grow">
        <header class="bg-gradient-to-r from-blue-700 to-indigo-800 text-white shadow-md sticky top-0 z-50 px-4 py-5 text-center rounded-b-2xl max-w-7xl mx-auto w-full">
            <h1 class="text-2xl font-black tracking-wide flex items-center justify-center gap-2 uppercase">
                <i class="fas fa-utensils"></i> e-Kantin RSUD Ende
            </h1>
            <p class="text-xs text-blue-200 mt-1">Sistem Pemesanan Mandiri & Meja Digital</p>
        </header>

        <div class="mb-6 px-4 max-w-2xl mx-auto mt-6">
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

        <main class="max-w-7xl mx-auto px-4 mb-32">
            
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-6 space-y-3 max-w-lg mx-auto">
                <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider"><i class="fas fa-user-edit text-blue-600"></i> Informasi Pelanggan</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nama Pemesan</label>
                        <input type="text" id="nama_pemesan" placeholder="Contoh: Budi" class="w-full p-2 border rounded-lg text-sm bg-slate-50 outline-none focus:ring-2 focus:ring-blue-500" autocomplete="off">
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

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                <?php
                $query = mysqli_query($conn, "SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang ASC");
                
                if (mysqli_num_rows($query) == 0) {
                    echo '
                    <div class="col-span-full bg-white rounded-xl p-8 text-center border border-slate-200 shadow-sm">
                        <i class="fas fa-cookie-bite text-slate-300 text-4xl mb-3"></i>
                        <p class="text-slate-500 font-medium text-sm">Mohon maaf, semua menu makanan saat ini sedang habis.</p>
                    </div>';
                }

                while ($row = mysqli_fetch_array($query)) {
                    
                    // LOGIKA LINK GOOGLE/INTERNET:
                    // Jika kolom foto di database ada isinya, langsung ambil link internet tersebut.
                    // Jika kosong, pakai gambar salad default dari Unsplash.
                    if (!empty($row['foto'])) {
                        $url_gambar = $row['foto'];
                    } else {
                        $url_gambar = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&auto=format&fit=crop&q=60';
                    }
                ?>
                
                <div class="card-menu bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between hover:border-blue-400 hover:shadow-md transition duration-300">
                    
                    <div class="w-full h-36 bg-slate-100 relative overflow-hidden border-b border-slate-100 flex items-center justify-center">
                        <img src="<?= $url_gambar; ?>" class="w-full h-full object-cover" alt="<?= $row['nama_barang']; ?>" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&auto=format&fit=crop&q=60';">
                        
                        <span class="absolute top-2 right-2 bg-slate-900/70 backdrop-blur-xs text-[10px] font-bold text-white px-2 py-0.5 rounded-md">
                            Stok: <?= $row['stok']; ?>
                        </span>
                    </div>

                    <div class="p-3 flex flex-col justify-between flex-grow space-y-3">
                        <div>
                            <h3 class="nama-menu font-bold text-slate-800 text-sm capitalize line-clamp-2 min-h-[40px] leading-tight">
                                <?= $row['nama_barang']; ?>
                            </h3>
                        </div>
                        
                        <div class="space-y-2 mt-auto">
                            <div class="text-sm font-black text-blue-700 block">
                                Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                            </div>
                            
                            <div id="btn-container-<?= $row['id_barang']; ?>" class="w-full">
                                <button type="button" onclick="tambahPorsi('<?= $row['id_barang']; ?>', '<?= $row['nama_barang']; ?>', <?= $row['harga']; ?>, <?= $row['stok']; ?>)" class="w-full bg-blue-600 text-white text-xs font-bold py-2 rounded-xl hover:bg-blue-700 transition shadow-sm flex items-center justify-center gap-1">
                                    <i class="fas fa-plus text-[9px]"></i> Pesan
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                <?php } ?>
            </div>
        </main>
    </div>

    <form id="form_order" action="modules/transaksi/aksi_pelanggan.php" method="POST">
        <input type="hidden" name="nama_pemesan" id="form_nama">
        <input type="hidden" name="nomor_meja" id="form_meja">
        <input type="hidden" name="total_bayar" id="form_total">
        <input type="hidden" name="metode_pembayaran" id="form_metode">
        <div id="wrapper_input_barang"></div>

        <div id="floating_cart" class="hidden fixed bottom-14 left-0 right-0 max-w-md mx-auto bg-white border border-slate-200 shadow-[0_-8px_30px_rgba(0,0,0,0.15)] p-4 rounded-t-3xl z-50 transition-all duration-300">
            <div class="flex items-center gap-2 text-slate-700 font-bold text-xs uppercase tracking-wider mb-2 pb-2 border-b">
                <i class="fas fa-shopping-basket text-blue-600"></i> Rincian Pesanan Anda
            </div>

            <div id="detail_keranjang_list" class="max-h-32 overflow-y-auto space-y-2 mb-4 pr-1 text-xs text-slate-600"></div>

            <div class="flex justify-between items-center pt-1">
                <div>
                    <span class="text-[10px] text-slate-400 block font-semibold uppercase">Total Bayar:</span>
                    <span class="text-lg font-black text-blue-700" id="label_total_pelanggan">Rp 0</span>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="setMetode('TUNAI')" class="bg-slate-800 text-white text-xs font-bold px-3 py-2.5 rounded-xl hover:bg-slate-900 transition flex items-center gap-1 shadow-sm">
                        <i class="fas fa-money-bill-wave"></i> Tunai
                    </button>
                    <button type="button" onclick="setMetode('QRIS')" class="bg-emerald-600 text-white text-xs font-bold px-3 py-2.5 rounded-xl hover:bg-emerald-700 transition flex items-center gap-1 shadow-sm">
                        <i class="fas fa-qrcode"></i> QRIS
                    </button>
                </div>
            </div>
        </div>
    </form>

    <footer class="bg-white border-t border-slate-200 py-5 text-center w-full">
        <div class="max-w-7xl mx-auto px-4">
            <p class="text-slate-500 font-semibold text-xs tracking-wide">
                &copy; 2026 e-Kantin RSUD Ende. All Rights Reserved.
            </p>
            <p class="text-slate-400 text-[10px] mt-1">
                Dikembangkan oleh <span class="text-blue-600 font-bold">MAKN Ende</span> (PPLG)
            </p>
        </div>
    </footer>

    <script>
        let keranjangPelanggan = [];
        let totalBelanja = 0;

        function tambahPorsi(id_barang, nama, harga, stokMax) {
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
                keranjangPelanggan.push({ id_barang, nama, harga, jumlah: 1, subtotal: harga, stokMax: stokMax });
            }

            perbaruiSemuaUI(id_barang, nama, harga, stokMax);
        }

        function kurangPorsi(id_barang, nama, harga, stokMax) {
            const indeksAda = keranjangPelanggan.findIndex(item => item.id_barang === id_barang);
            
            if(indeksAda > -1) {
                keranjangPelanggan[indeksAda].jumlah -= 1;
                keranjangPelanggan[indeksAda].subtotal = keranjangPelanggan[indeksAda].jumlah * harga;
                
                if(keranjangPelanggan[indeksAda].jumlah <= 0) {
                    keranjangPelanggan.splice(indeksAda, 1);
                }
            }

            perbaruiSemuaUI(id_barang, nama, harga, stokMax);
        }

        function batalkanMenu(id_barang, nama, harga, stokMax) {
            const indeksAda = keranjangPelanggan.findIndex(item => item.id_barang === id_barang);
            
            if(indeksAda > -1) {
                if(confirm(`Apakah Anda yakin ingin membatalkan pesanan ${nama}?`)) {
                    keranjangPelanggan.splice(indeksAda, 1);
                    perbaruiSemuaUI(id_barang, nama, harga, stokMax);
                }
            }
        }

        function perbaruiSemuaUI(id_barang, nama, harga, stokMax) {
            const container = document.getElementById(`btn-container-${id_barang}`);
            const item = keranjangPelanggan.find(item => item.id_barang === id_barang);

            if(container) {
                if(item && item.jumlah > 0) {
                    container.innerHTML = `
                        <div class="flex items-center border border-blue-200 bg-blue-50 rounded-lg overflow-hidden shadow-sm w-full justify-between">
                            <button type="button" onclick="kurangPorsi('${id_barang}', '${nama}', ${harga}, ${stokMax})" class="px-2.5 py-1 bg-white text-blue-600 hover:bg-slate-100 font-black transition text-xs">-</button>
                            <span class="px-3 font-bold text-blue-700 text-xs">${item.jumlah}</span>
                            <button type="button" onclick="tambahPorsi('${id_barang}', '${nama}', ${harga}, ${stokMax})" class="px-2.5 py-1 bg-white text-blue-600 hover:bg-slate-100 font-black transition text-xs">+</button>
                        </div>
                    `;
                } else {
                    container.innerHTML = `
                        <button type="button" onclick="tambahPorsi('${id_barang}', '${nama}', ${harga}, ${stokMax})" class="w-full bg-blue-600 text-white text-xs font-bold px-4 py-1.5 rounded-lg hover:bg-blue-700 transition shadow-sm flex items-center justify-center gap-1">
                            <i class="fas fa-plus text-[10px]"></i> Pesan
                        </button>
                    `;
                }
            }

            hitungTotalDanRenderKeranjang();
        }

        function hitungTotalDanRenderKeranjang() {
            const cartBar = document.getElementById('floating_cart');
            const wrapper = document.getElementById('wrapper_input_barang');
            const listDetail = document.getElementById('detail_keranjang_list');
            
            totalBelanja = 0;
            wrapper.innerHTML = '';
            listDetail.innerHTML = '';

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

                listDetail.innerHTML += `
                    <div class="flex justify-between items-center bg-slate-50 p-2 rounded-xl border border-slate-200 shadow-sm gap-2">
                        <div class="flex flex-col flex-grow">
                            <span class="font-bold capitalize text-slate-800 text-sm">${item.nama}</span>
                            <span class="text-blue-600 font-extrabold text-[11px] mt-0.5">Rp ${item.subtotal.toLocaleString('id-ID')}</span>
                        </div>
                        
                        <div class="flex items-center border border-slate-300 bg-white rounded-lg overflow-hidden shadow-xs flex-shrink-0">
                            <button type="button" onclick="kurangPorsi('${item.id_barang}', '${item.nama}', ${item.harga}, ${item.stokMax})" class="px-2 py-0.5 bg-slate-100 text-slate-700 hover:bg-slate-200 font-black text-xs transition">-</button>
                            <span class="px-2.5 font-bold text-slate-800 text-xs">${item.jumlah}</span>
                            <button type="button" onclick="tambahPorsi('${item.id_barang}', '${item.nama}', ${item.harga}, ${item.stokMax})" class="px-2 py-0.5 bg-slate-100 text-slate-700 hover:bg-slate-200 font-black text-xs transition">+</button>
                        </div>

                        <button type="button" onclick="batalkanMenu('${item.id_barang}', '${item.nama}', ${item.harga}, ${item.stokMax})" class="w-7 h-7 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition flex items-center justify-center text-xs flex-shrink-0" title="Batalkan Menu">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
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

            let ringkasanTeks = "";
            keranjangPelanggan.forEach(item => {
                ringkasanTeks += `\n- ${item.nama} (x${item.jumlah}) : Rp ${item.subtotal.toLocaleString('id-ID')}`;
            });

            if (metode === 'QRIS') {
                if(confirm(`Rincian Pesanan Kamu:${ringkasanTeks}\n\nTotal: Rp ${totalBelanja.toLocaleString('id-ID')}\n\nPesanan akan dikirim ke dapur. Silakan lakukan scan pada stiker QRIS FISIK yang berada di meja/kasir. Apakah Anda sudah paham?`)) {
                    document.getElementById('form_order').submit();
                }
            } else {
                if(confirm(`Rincian Pesanan Kamu:${ringkasanTeks}\n\nTotal: Rp ${totalBelanja.toLocaleString('id-ID')}\n\nPesanan akan dikirim ke dapur. Silakan lakukan pembayaran tunai ke loket kasir saat mengambil makanan. Kirim pesanan sekarang?`)) {
                    document.getElementById('form_order').submit();
                }
            }
        }

        function cariMenu() {
            let kataKunci = document.getElementById('searchMenu').value.toLowerCase();
            let daftarKartu = document.getElementsByClassName('card-menu');
            
            for (let i = 0; i < daftarKartu.length; i++) {
                let namaMakanan = daftarKartu[i].getElementsByClassName('nama-menu')[0].innerText.toLowerCase();
                
                if (namaMakanan.includes(kataKunci)) {
                    daftarKartu[i].style.display = "flex"; 
                } else {
                    daftarKartu[i].style.display = "none"; 
                }
            }
        }
    </script>
</body>
</html>