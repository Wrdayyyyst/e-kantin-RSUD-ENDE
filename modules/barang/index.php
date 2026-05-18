<?php 
session_start();
include "../../config/db.php";

// 1. Definisikan variabel yang dibutuhkan sidebar
$role = strtolower($_SESSION['role']);
$username = $_SESSION['username'];

// 2. Set base path untuk link di sidebar
$base_path = "./"; 
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

    <?php include "../../layout/sidebar.php"; ?>

    <main class="flex-1 p-8">
        <h2 class="text-2xl font-bold text-slate-800 mb-6 uppercase">Manajemen Stok Barang</h2>
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
             </div>
    </main>

</body>
</html>