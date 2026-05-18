<?php
session_start();

// PROTEKSI HALAMAN: Hanya boleh dibuka jika role-nya murni ADMIN
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php");
    exit;
}

$role = strtolower($_SESSION['role']);
if ($role !== 'admin') {
    header("location:index.php");
    exit;
}

include "config/db.php";
/** @var mysqli $conn */
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User | e-Kantin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 flex min-h-screen">

    <?php include "layout/sidebar.php"; ?>

    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 uppercase">Kelola Pengguna / User</h2>
                <p class="text-slate-500 text-xs">Manajemen hak akses akun aplikasi e-Kantin RSUD Ende</p>
            </div>
            
            <button onclick="document.getElementById('modalTambahUser').classList.remove('hidden')" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-bold hover:bg-blue-700 transition flex items-center gap-2 shadow-md">
                <i class="fas fa-user-plus"></i> Tambah User
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase">No</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase">Username</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase">Hak Akses / Role</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php 
                    $no = 1;
                    $query = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, username ASC"); 
                    while($row = mysqli_fetch_array($query)) {
                    ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-4 text-sm text-slate-600"><?= $no++; ?></td>
                        <td class="p-4 text-sm font-bold text-slate-800 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 text-xs font-bold">
                                <?= strtoupper(substr($row['username'], 0, 2)); ?>
                            </div>
                            <?= $row['username']; ?>
                        </td>
                        <td class="p-4 text-sm">
                            <span class="px-2.5 py-1 text-xs font-bold rounded-md uppercase 
                                <?= $row['role'] == 'admin' ? 'bg-red-100 text-red-700' : ($row['role'] == 'pengelola' || $row['role'] == 'pemilik' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'); ?>">
                                <?= $row['role']; ?>
                            </span>
                        </td>
                        <td class="p-4 text-center flex justify-center gap-3">
                            <button onclick="bukaModalEditUser('<?= $row['id_user']; ?>', '<?= addslashes($row['username']); ?>', '<?= $row['role']; ?>')" class="text-blue-500 hover:text-blue-700 transition">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="modules/user/aksi.php?hapus=<?= $row['id_user']; ?>" onclick="return confirm('Yakin ingin menghapus pengguna <?= addslashes($row['username']); ?>?')" class="text-red-500 hover:text-red-700 transition">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="modalTambahUser" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white p-6 rounded-xl w-full max-w-md shadow-2xl">
            <h3 class="text-xl font-bold mb-4">Tambah Pengguna Baru</h3>
            <form action="modules/user/aksi.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Username</label>
                    <input type="text" name="username" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required autocomplete="off">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Role / Hak Akses</label>
                    <select name="role" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white" required>
                        <option value="kasir">Kasir</option>
                        <option value="pengelola">Pengelola</option>
                        <option value="pemilik">Pemilik</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modalTambahUser').classList.add('hidden')" class="px-4 py-2 text-slate-500 font-bold">Batal</button>
                    <button type="submit" name="tambah_user" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700">Simpan User</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEditUser" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white p-6 rounded-xl w-full max-w-md shadow-2xl">
            <h3 class="text-xl font-bold mb-4">Edit Data Pengguna</h3>
            <form action="modules/user/aksi.php" method="POST" class="space-y-4">
                <input type="hidden" name="id_user" id="edit_id_user">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Username</label>
                    <input type="text" name="username" id="edit_username" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Password Baru <span class="text-xs font-normal text-slate-400">(Kosongkan jika tidak diganti)</span></label>
                    <input type="password" name="password" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Role / Hak Akses</label>
                    <select name="role" id="edit_role_user" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white" required>
                        <option value="kasir">Kasir</option>
                        <option value="pengelola">Pengelola</option>
                        <option value="pemilik">Pemilik</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modalEditUser').classList.add('hidden')" class="px-4 py-2 text-slate-500 font-bold">Batal</button>
                    <button type="submit" name="edit_user" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bukaModalEditUser(id, username, role) {
            document.getElementById('edit_id_user').value = id;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_role_user').value = role;
            document.getElementById('modalEditUser').classList.remove('hidden');
        }
    </script>
</body>
</html>