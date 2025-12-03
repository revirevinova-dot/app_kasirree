<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Kasir UMKM</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 untuk Notifikasi -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- CSS Kustom (Tema Hijau Soft) -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Tambahkan Library Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color: var(--bs-primary);">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-store"></i> Aplikasi Kasir
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            
            <!-- MENU NAVIGASI UTAMA (DI KIRI) -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'dashboard') ? 'active' : ''; ?>" href="index.php?page=dashboard">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'kasir') ? 'active' : ''; ?>" href="index.php?page=kasir">
                        <i class="fas fa-cash-register me-1"></i> Kasir
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'manajemen-stok') ? 'active' : ''; ?>" href="index.php?page=manajemen-stok">
                        <i class="fas fa-boxes me-1"></i> Manajemen Stok
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'manajemen-pelanggan') ? 'active' : ''; ?>" href="index.php?page=manajemen-pelanggan">
                        <i class="fas fa-users me-1"></i> Manajemen Pelanggan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'laporan') ? 'active' : ''; ?>" href="index.php?page=laporan">
                        <i class="fas fa-file-invoice me-1"></i> Laporan Penjualan
                    </a>
                </li>
                
                <!-- MENU MANAJEMEN USER (HANYA UNTUK ADMIN) -->
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($page == 'manajemen-user') ? 'active' : ''; ?>" href="index.php?page=manajemen-user">
                        <i class="fas fa-users-cog me-1"></i> Manajemen User
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            <!-- MENU USER (DI KANAN) -->
            <?php if (isset($_SESSION['user_id'])): ?>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item d-flex align-items-center">
                    <span class="navbar-text me-3">
                        <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username']); ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-danger btn-sm" href="api/logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
            <?php endif; ?>

        </div>
    </div>
</nav>

<main class="container-fluid mt-4">