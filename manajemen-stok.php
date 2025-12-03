<h2 class="mb-4"><i class="fas fa-boxes me-2"></i>Manajemen Stok Produk</h2>

<!-- Form Tambah Produk Baru -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Tambah Produk Baru</h5>
    </div>
    <div class="card-body">
        <form id="addProductForm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="namaProduk" class="form-label">Nama Produk</label>
                    <input type="text" name="NamaProduk" class="form-control" id="namaProduk" placeholder="Masukkan nama produk" required>
                </div>
                <div class="col-md-3">
                    <label for="hargaProduk" class="form-label">Harga</label>
                    <input type="number" name="Harga" class="form-control" id="hargaProduk" placeholder="0" required>
                </div>
                <div class="col-md-3">
                    <label for="stokProduk" class="form-label">Stok Awal</label>
                    <input type="number" name="Stok" class="form-control" id="stokProduk" placeholder="0" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save me-1"></i>Tambah</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Daftar Produk -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Produk</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok Saat Ini</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="stockTableBody">
                    <tr><td colspan="4" class="text-center">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- =========================================================== -->
<!-- SCRIPT KHUSUS UNTUK HALAMAN INI -->
<!-- =========================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM siap. Menjalankan script Manajemen Stok.");

    // --- FUNGSI HELPER ---
    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
    }

    function showNotification(message, icon = 'success') {
        Swal.fire({
            position: 'top-end',
            icon: icon,
            title: message,
            showConfirmButton: false,
            timer: 1500
        });
    }

    // --- ELEMEN DOM ---
    const addProductForm = document.getElementById('addProductForm');
    const stockTableBody = document.getElementById('stockTableBody');

    if (!addProductForm) {
        console.error("ERROR: Form dengan ID 'addProductForm' tidak ditemukan!");
        return;
    }

    // --- FUNGSI UNTUK MEMUAT DATA PRODUK ---
    async function loadStockTable() {
        console.log("Memuat data produk...");
        try {
            const response = await fetch('api/produk.php');
            if (!response.ok) throw new Error('Gagal mengambil data produk');
            const products = await response.json();
            console.log("Data produk diterima:", products);

            stockTableBody.innerHTML = products.map(p => `
                <tr>
                    <td>${p.NamaProduk}</td>
                    <td><input type="number" class="form-control price-input" data-id='${p.ProdukID}' value="${p.Harga}" min="0"></td>
                    <td><input type="number" class="form-control stock-input" data-id='${p.ProdukID}' value="${p.Stok}" min="0"></td>
                    <td>
                        <button class="btn btn-sm btn-success save-product-btn" data-id='${p.ProdukID}'>Simpan</button>
                        <button class="btn btn-sm btn-danger delete-product-btn" data-id='${p.ProdukID}' data-bs-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `).join('');

        } catch (error) {
            console.error("Gagal memuat data produk:", error);
            stockTableBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Gagal memuat data.</td></tr>';
        }
    }

    // --- EVENT LISTENER UNTUK FORM TAMBAH PRODUK ---
    addProductForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log("Form disubmit. Mencegah reload halaman.");

        const formData = new FormData(addProductForm);
        const data = Object.fromEntries(formData.entries());
        console.log("Data yang akan dikirim:", data);

        try {
            const response = await fetch('api/produk.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            console.log("Respons dari server:", result);

            showNotification(result.message, response.ok ? 'success' : 'error');

            if (response.ok) {
                addProductForm.reset();
                loadStockTable();
            }
        } catch (error) {
            console.error("Terjadi kesalahan:", error);
            showNotification('Gagal menghubungi server', 'error');
        }
    });

    // --- EVENT LISTENER UNTUK TOMBOL SIMPAN & HAPUS ---
    stockTableBody.addEventListener('click', async (e) => {
        // --- LOGIKA UNTUK TOMBOL SIMPAN (EDIT INLINE) ---
        if (e.target.classList.contains('save-product-btn')) {
            const productId = e.target.dataset.id;
            const row = e.target.closest('tr');
            
            const productName = row.cells[0].textContent;
            const newPrice = row.querySelector('.price-input').value;
            const newStock = row.querySelector('.stock-input').value;

            const data = {
                ProdukID: productId,
                NamaProduk: productName, // Kirim nama juga untuk konsistensi
                Harga: newPrice,
                Stok: newStock
            };

            // Tampilkan indikator loading pada tombol
            const originalText = e.target.innerHTML;
            e.target.disabled = true;
            e.target.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';

            try {
                const response = await fetch('api/produk.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                showNotification(result.message, response.ok ? 'success' : 'error');

                // Kembalikan tombol ke keadaan semula
                e.target.disabled = false;
                e.target.innerHTML = originalText;

                // Jika berhasil, muat ulang tabel untuk memastikan data sinkron
                if (response.ok) {
                    loadStockTable();
                }
            } catch (error) {
                console.error("Terjadi kesalahan saat menyimpan:", error);
                showNotification('Gagal menghubungi server', 'error');
                e.target.disabled = false;
                e.target.innerHTML = originalText;
            }
        }

        // --- LOGIKA UNTUK TOMBOL HAPUS ---
        if (e.target.classList.contains('delete-product-btn')) {
            const productId = e.target.dataset.id;
            const productName = e.target.closest('tr').querySelector('td:first-child').textContent;

            const confirmResult = await Swal.fire({
                title: 'Yakin ingin menghapus?',
                html: `Produk "<strong>${productName}</strong>" akan dihapus secara permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            });

            if (confirmResult.isConfirmed) {
                try {
                    const response = await fetch(`api/produk.php?id=${productId}`, {
                        method: 'DELETE'
                    });

                    const result = await response.json();
                    showNotification(result.message, response.ok ? 'success' : 'error');

                    if (response.ok) {
                        loadStockTable(); // Muat ulang tabel setelah hapus
                    }
                } catch (error) {
                    console.error("Terjadi kesalahan saat menghapus:", error);
                    showNotification('Gagal menghubungi server', 'error');
                }
            }
        }
    });

    // --- INISIALISASI PERTAMA KALI ---
    loadStockTable();
});
</script>