<?php
// --- LANGKAH PENTING: CEK SESSION DI AWAL ---
session_start();

// Jika user belum login, redirect ke halaman login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit(); // Hentikan eksekusi script
}

// Menentukan halaman yang akan dimuat, default ke 'dashboard'
 $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Validasi halaman yang diizinkan
// Saya menambahkan 'manajemen-user' kembali ke daftar
 $allowed_pages = ['dashboard', 'kasir', 'manajemen-stok', 'manajemen-pelanggan', 'laporan', 'manajemen-user'];
if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard'; // Kembali ke default jika halaman tidak valid
}

// Sertakan file yang diperlukan
require_once 'includes/header.php';
require_once 'pages/' . $page . '.php';
require_once 'includes/footer.php';
?>