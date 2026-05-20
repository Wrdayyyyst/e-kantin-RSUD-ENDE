<?php
session_start();
include "config/db.php";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    $cek = mysqli_num_rows($query);

    if ($cek > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['id_user']  = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];
        $_SESSION['status']   = "login";
        
        header("location:index.php");
        exit; // Ditambahkan exit setelah redirect agar skrip di bawahnya berhenti diproses
    } else {
        $error = "Username atau Password tidak sesuai.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | e-Kantin RSUD Ende</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-blue-700 flex items-center justify-center gap-2">
                <i class="fas fa-hospital"></i> e-Kantin
            </h2>
            <p class="text-slate-500 mt-1">RSUD Ende - Digital Management System</p>
        </div>
        
        <?php if(isset($error)) : ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm text-center font-semibold">
                <?= $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-5">
                <label class="block text-slate-700 text-sm font-semibold mb-2">Username</label>
                <input type="text" name="username" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Masukkan username" required autocomplete="off">
            </div>
            <div class="mb-6">
                <label class="block text-slate-700 text-sm font-semibold mb-2">Password</label>
                <input type="password" name="password" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Masukkan password" required>
            </div>
            <button type="submit" name="login" class="w-full bg-blue-700 text-white font-bold py-2.5 rounded-lg hover:bg-blue-800 transition duration-300 shadow-md">
                Masuk ke Sistem
            </button>
        </form>
        
        <p class="text-center text-slate-400 text-xs mt-8">
            &copy; 2026 MAKN Ende.
        </p>
    </div>
</body>
</html>