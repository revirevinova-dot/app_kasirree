<?php
// ===============================================================
// API untuk Memproses Transaksi Kasir (Versi 2 Tabel)
// ===============================================================

// --- Atur Header untuk API ---
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// --- Tangani preflight request dari browser ---
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --- Hubungkan ke Database ---
require_once '../config/db.php';

// --- Pastikan Method adalah POST ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => 'Metode permintaan tidak diizinkan.']);
    exit;
}

// --- Ambil Data dari Frontend ---
 $data = json_decode(file_get_contents("php://input"));

// --- Validasi Data yang Diterima ---
if (
    !isset($data->pelangganId) ||
    !isset($data->items) || !is_array($data->items) ||
    !isset($data->totalHarga) ||
    !isset($data->uangDibayar) ||
    !isset($data->kembalian)
) {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'Data transaksi tidak lengkap.']);
    exit;
}

// --- Proses Transaksi ---
try {
    // 1. Mulai transaksi database
    $pdo->beginTransaction();

    // ==========================================================
    // 2. INSERT DATA UTAMA KE TABEL kasir_penjualan
    // ==========================================================
    $sqlPenjualan = "INSERT INTO kasir_penjualan (PelangganID, TanggalPenjualan, TotalHarga, UangDibayar, Kembalian) 
                      VALUES (?, NOW(), ?, ?, ?)";
    $stmtPenjualan = $pdo->prepare($sqlPenjualan);
    
    // Jika pelangganId adalah 0 (Pelanggan Umum), simpan sebagai NULL
    $pelangganId = ($data->pelangganId == 0) ? null : $data->pelangganId;

    $stmtPenjualan->execute([
        $pelangganId,
        $data->totalHarga,
        $data->uangDibayar,
        $data->kembalian
    ]);

    // Ambil ID penjualan yang baru saja dibuat (ini adalah 'penjualanid')
    $penjualanId = $pdo->lastInsertId();

    // ==========================================================
    // 3. INSERT DETAIL ITEM KE TABEL kasir_detail_penjualan
    // ==========================================================
    $sqlDetail = "INSERT INTO kasir_detailpenjualan (PenjualanID, ProdukID, JumlahProduk, Subtotal) 
                  VALUES (?, ?, ?, ?)";
    $stmtDetail = $pdo->prepare($sqlDetail);

    $sqlUpdateStok = "UPDATE kasir_produk SET Stok = Stok - ? WHERE ProdukID = ?";
    $stmtUpdateStok = $pdo->prepare($sqlUpdateStok);

    foreach ($data->items as $item) {
        // Insert detail item
        $stmtDetail->execute([
            $penjualanId, // Gunakan ID dari tabel utama
            $item->ProdukID,
            $item->JumlahProduk,
            $item->Subtotal
        ]);

        // Update stok produk
        $stmtUpdateStok->execute([
            $item->JumlahProduk,
            $item->ProdukID
        ]);
    }

    // 4. Jika semua berhasil, commit transaksi
    $pdo->commit();

    // 5. Kirim respons sukses
    http_response_code(201); // Created
    echo json_encode([
        'message' => 'Transaksi berhasil diproses!',
        'transaksiId' => $penjualanId // Kirim ID penjualan kembali ke frontend
    ]);

} catch (PDOException $e) {
    // Jika terjadi error, rollback transaksi
    $pdo->rollBack();

    // Kirim respons error
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'message' => 'Gagal memproses transaksi: ' . $e->getMessage()
    ]);
}
?>