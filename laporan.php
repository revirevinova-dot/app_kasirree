<?php
// Izinkan akses dari berbagai sumber (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Sertakan file koneksi database
require_once '../config/db.php';

try {
    // Query untuk mengambil data ringkasan setiap transaksi
    // Data diambil dari tabel kasir_penjualan dan kasir_pelanggan
    $sql = "SELECT 
                p.PenjualanID, 
                p.TanggalPenjualan, 
                pl.NamaPelanggan, 
                p.TotalHarga 
            FROM 
                kasir_penjualan p
            LEFT JOIN 
                kasir_pelanggan pl ON p.PelangganID = pl.PelangganID
            ORDER BY 
                p.TanggalPenjualan DESC";

    $stmt = $pdo->query($sql);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Kembalikan data dalam format JSON array
    echo json_encode($reports);

} catch (PDOException $e) {
    // Jika terjadi error, kirim response error
    http_response_code(500);
    echo json_encode(['message' => 'Error di database: ' . $e->getMessage()]);
}
?>