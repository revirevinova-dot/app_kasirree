<?php
// Izinkan akses dari berbagai sumber (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Sertakan file koneksi database
require_once '../config/db.php';

try {
    // --- 1. Query untuk KPIs ---
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM kasir_produk");
    $totalProduk = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM kasir_pelanggan");
    $totalPelanggan = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM kasir_penjualan WHERE DATE(TanggalPenjualan) = CURDATE()");
    $totalPenjualanHariIni = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $pdo->query("SELECT SUM(TotalHarga) as total FROM kasir_penjualan WHERE DATE(TanggalPenjualan) = CURDATE()");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalPendapatanHariIni = $result['total'] ?? 0;

    // --- 2. Query untuk Grafik Penjualan 7 Hari Terakhir ---
    $chartStmt = $pdo->query("
        SELECT DATE(TanggalPenjualan) as date, SUM(TotalHarga) as total 
        FROM kasir_penjualan 
        WHERE TanggalPenjualan >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
        GROUP BY DATE(TanggalPenjualan) 
        ORDER BY date ASC
    ");
    $chartData = $chartStmt->fetchAll(PDO::FETCH_ASSOC);

    // --- 3. Query untuk Transaksi Terakhir ---
    // Asumsi: ada kolom PelangganID di tabel kasir_penjualan yang terhubung ke kasir_pelanggan
    $transactionsStmt = $pdo->query("
        SELECT p.PenjualanID, pl.NamaPelanggan, p.TotalHarga 
        FROM kasir_penjualan p 
        JOIN kasir_pelanggan pl ON p.PelangganID = pl.PelangganID 
        ORDER BY p.TanggalPenjualan DESC 
        LIMIT 5
    ");
    $recentTransactions = $transactionsStmt->fetchAll(PDO::FETCH_ASSOC);

    // --- 4. Kumpulkan semua data dalam struktur JSON yang diharapkan ---
    $response = [
        'kpis' => [
            'totalProduk' => (int)$totalProduk,
            'totalPelanggan' => (int)$totalPelanggan,
            'totalPenjualanHariIni' => (int)$totalPenjualanHariIni,
            'totalPendapatanHariIni' => (float)$totalPendapatanHariIni
        ],
        'chart' => $chartData,
        'recentTransactions' => $recentTransactions
    ];

    // Ubah array menjadi JSON dan kirim ke browser
    echo json_encode($response);

} catch (PDOException $e) {
    // Jika terjadi error koneksi atau query, kirim pesan error
    http_response_code(500); // Internal Server Error
    echo json_encode(['message' => 'Error di database: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Jika terjadi error lain
    http_response_code(500);
    echo json_encode(['message' => 'Error umum: ' . $e->getMessage()]);
}
?>