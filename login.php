<?php
// Cek apakah user sudah login. Jika ya, redirect ke dashboard.
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Kasir</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- CSS Kustom (Tema Hijau Soft) -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* Gaya khusus untuk halaman login agar berada di tengah */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
        }
        /* Menggunakan variabel CSS untuk warna header tombol */
        .card-header {
            background-color: var(--bs-primary);
            color: white;
            text-align: center;
            font-weight: bold;
            border-bottom: none; /* Hapus border default */
        }
        .btn-success {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }
        .btn-success:hover, .btn-success:focus {
            background-color: var(--bs-primary-hover);
            border-color: var(--bs-primary-hover);
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="card shadow-lg">
        <div class="card-header">
            <h4 class="mb-0"><i class="fas fa-cash-register me-2"></i>Aplikasi Kasir</h4>
        </div>
        <div class="card-body p-4">
            <form id="loginForm">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-sign-in-alt me-1"></i> Login
                </button>
            </form>
            <div id="loginError" class="alert alert-danger mt-3" style="display: none;"></div>
        </div>
    </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
 $(document).ready(function() {
    $('#loginForm').on('submit', async function(e) {
        e.preventDefault(); // Mencegah form submit default

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        // Tampilkan indikator loading
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Mengecek...');

        try {
            const response = await fetch('api/auth.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                // Login berhasil
                Swal.fire({
                    icon: 'success',
                    title: 'Login Berhasil!',
                    text: 'Mengalihkan ke dashboard...',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'index.php';
                });
            } else {
                // Login gagal
                $('#loginError').text(result.message || 'Username atau password salah.').show();
                submitBtn.prop('disabled', false).html(originalText);
            }
        } catch (error) {
            console.error('Login error:', error);
            $('#loginError').text('Terjadi kesalahan server. Coba lagi nanti.').show();
            submitBtn.prop('disabled', false).html(originalText);
        }
    });
});
</script>

</body>
</html>