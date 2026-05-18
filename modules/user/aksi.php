<?php
session_start();
include "../../config/db.php";
/** @var mysqli $conn */

// PROTEKSI KETAT: Hanya boleh diakses oleh Admin
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:../../login.php");
    exit;
}
$role = strtolower($_SESSION['role']);
if ($role !== 'admin') {
    header("location:../../index.php");
    exit;
}

// 1. LOGIKA TAMBAH USER (POST)
if (isset($_POST['tambah_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); // Menggunakan MD5 sesuai standard login biasa
    $role_user = strtolower($_SESSION['role']); // Ambil role pilihan dari form

    mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role_user')");
    header("location:../../users.php");
    exit;
}

// 2. LOGIKA EDIT USER (POST)
if (isset($_POST['edit_user'])) {
    $id_user  = $_POST['id_user'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role_user = $_POST['role'];

    // Jika password diisi, ganti password lama. Jika kosong, biarkan password lama.
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        mysqli_query($conn, "UPDATE users SET username = '$username', password = '$password', role = '$role_user' WHERE id_user = '$id_user'");
    } else {
        mysqli_query($conn, "UPDATE users SET username = '$username', role = '$role_user' WHERE id_user = '$id_user'");
    }

    header("location:../../users.php");
    exit;
}

// 3. LOGIKA HAPUS USER (GET)
if (isset($_GET['hapus'])) {
    $id_user = mysqli_real_escape_string($conn, $_GET['hapus']);

    // Cegah admin menghapus dirinya sendiri yang sedang login
    $user_sekarang = $_SESSION['username'];
    $cek_user = mysqli_query($conn, "SELECT username FROM users WHERE id_user = '$id_user'");
    $data_user = mysqli_fetch_assoc($cek_user);

    if ($data_user['username'] == $user_sekarang) {
        echo "<script>alert('Anda tidak bisa menghapus akun Anda sendiri yang sedang aktif!'); window.location='../../users.php';</script>";
        exit;
    }

    mysqli_query($conn, "DELETE FROM users WHERE id_user = '$id_user'");
    header("location:../../users.php");
    exit;
}
?>