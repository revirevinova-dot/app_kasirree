<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Kasir</title>
    <!-- CSS Khusus Kasir -->
    <link rel="stylesheet" href="assets/css/kasir.css">
</head>
<body>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0"><i class="fas fa-cash-register me-2"></i>Halaman Kasir</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="searchProduct" class="form-control" placeholder="Cari produk...">
                        </div>
                    </div>
                    <div id="productList" class="row g-3">
                        <!-- Produk akan dimuat di sini oleh JavaScript -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Pemilihan Pelanggan -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-friends me-2"></i>Pilih Pelanggan</h5>
                </div>
                <div class="card-body">
                    <select id="customerSelect" class="form-select">
                        <option value="">-- Pilih Pelanggan --</option>
                    </select>
                </div>
            </div>
            <!-- Keranjang Belanja -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Keranjang</h5>
                    <button id="clearCart" class="btn btn-sm btn-danger">Kosongkan</button>
                </div>
                <div class="card-body">
                    <div id="cartItems" style="min-height: 150px;">
                        <p class="text-muted text-center">Keranjang kosong</p>
                    </div>
                    <hr>
                    <h4>Total: <span id="totalPrice">Rp 0</span></h4>
                </div>
            </div>
            <!-- Pembayaran -->
            <div class="card">
                <div class="card-body">
                    <button id="checkoutBtn" class="btn btn-success btn-lg w-100"><i class="fas fa-credit-card me-2"></i>Bayar Sekarang</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================== -->
<!-- MODAL DETAIL TRANSAKSI -->
<!-- =========================================================== -->
<div class="modal fade" id="transactionDetailModal" tabindex="-1" aria-labelledby="transactionDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionDetailModalLabel"><i class="fas fa-receipt me-2"></i>Detail Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Pelanggan:</strong> <span id="modalCustomerName">-</span>
                    </div>
                    <div class="col-md-6 text-end">
                        <strong>Tanggal:</strong> <span id="modalTransactionDate">-</span>
                    </div>
                </div>
                <hr>
                <h6 class="mb-2">Rincian Produk:</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="modalTransactionItems">
                            <!-- Isi akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label for="totalBelanja" class="form-label"><strong>Total Belanja:</strong></label>
                            <input type="text" class="form-control" id="totalBelanja" readonly>
                        </div>
                        <div class="mb-2">
                            <label for="paymentAmount" class="form-label"><strong>Uang yang Dibayarkan:</strong></label>
                            <input type="number" class="form-control" id="paymentAmount" placeholder="Masukkan uang">
                        </div>
                        <div>
                            <label for="changeAmount" class="form-label"><strong>Kembalian:</strong></label>
                            <input type="text" class="form-control" id="changeAmount" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex flex-column justify-content-end">
                        <button class="btn btn-sm btn-outline-primary mb-2" id="copyDetailsBtn">
                            <i class="fas fa-copy me-1"></i> Salin Detail
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="confirmPaymentBtn"><i class="fas fa-check me-1"></i> Konfirmasi & Bayar</button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================== -->
<!-- MODAL NOTA/STRUK BELANJA -->
<!-- =========================================================== -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body" id="receiptContent">
                <!-- Konten Nota akan diisi oleh JavaScript -->
            </div>
            <div class="modal-footer d-print-none">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <!-- TOMBOL CETAK DENGAN ID UNTUK EVENT LISTENER -->
                <button type="button" class="btn btn-primary" id="printReceiptBtn">
                    <i class="fas fa-print me-1"></i> Cetak Nota
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS & Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- =========================================================== -->
<!-- SCRIPT KHUSUS UNTUK HALAMAN KASIR -->
<!-- =========================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log("Halaman Kasir dimuat.");

    // --- STATE & ELEMEN ---
    const state = { products: [], customers: [], cart: [], selectedCustomerId: null };
    const productListEl = document.getElementById('productList');
    const cartItemsEl = document.getElementById('cartItems');
    const totalPriceEl = document.getElementById('totalPrice');
    const customerSelectEl = document.getElementById('customerSelect');
    const searchInput = document.getElementById('searchProduct');
    const clearCartBtn = document.getElementById('clearCart');
    const checkoutBtn = document.getElementById('checkoutBtn');

    // --- ELEMEN MODAL ---
    const transactionDetailModal = new bootstrap.Modal(document.getElementById('transactionDetailModal'));
    const modalCustomerNameEl = document.getElementById('modalCustomerName');
    const modalTransactionDateEl = document.getElementById('modalTransactionDate');
    const modalTransactionItemsEl = document.getElementById('modalTransactionItems');
    const confirmPaymentBtn = document.getElementById('confirmPaymentBtn');
    const copyDetailsBtn = document.getElementById('copyDetailsBtn');
    
    // --- ELEMEN MODAL NOTA ---
    const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
    const receiptContentEl = document.getElementById('receiptContent');
    const printReceiptBtn = document.getElementById('printReceiptBtn'); // Tambahkan ini
    
    // --- ELEMEN DETAIL PEMBAYARAN ---
    const totalBelanjaEl = document.getElementById('totalBelanja');
    const paymentAmountEl = document.getElementById('paymentAmount');
    const changeAmountEl = document.getElementById('changeAmount');

    // --- FUNGSI HELPER ---
    function formatRupiah(amount) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount); }
    function showNotification(message, icon = 'success') { Swal.fire({ position: 'top-end', icon, title: message, showConfirmButton: false, timer: 1500 }); }

    // --- FUNGSI PEMANGGILAN DATA ---
    async function fetchProducts() {
        try {
            const response = await fetch('api/produk.php');
            const productsFromApi = await response.json();
            state.products = productsFromApi.map(p => ({
                ...p,
                Harga: parseInt(p.Harga.toString().replace(/[^0-9]/g, ''))
            }));
            renderProducts();
        } catch (error) { showNotification('Gagal memuat produk', 'error'); }
    }
    async function fetchCustomers() {
        try {
            const response = await fetch('api/pelanggan.php');
            state.customers = await response.json();
            renderCustomers();
        } catch (error) { showNotification('Gagal memuat pelanggan', 'error'); }
    }

    // --- FUNGSI RENDER ---
    function renderProducts(productsToRender = state.products) {
        productListEl.innerHTML = productsToRender.map(p => `
            <div class="col-md-6 col-lg-4">
                <div class="card product-card-item h-100 ${p.Stok <= 0 ? 'disabled' : ''}" data-id='${p.ProdukID}'>
                    <div class="card-body">
                        <span class="add-icon"><i class="fas fa-plus"></i></span>
                        <h5>${p.NamaProduk}</h5>
                        <p>Harga: <strong>${formatRupiah(p.Harga)}</strong><br>Stok: <span class="${p.Stok <= 5 ? 'text-danger' : ''}">${p.Stok}</span></p>
                    </div>
                </div>
            </div>
        `).join('');
    }
    function renderCustomers() {
        customerSelectEl.innerHTML = 
            '<option value="">-- Pilih Pelanggan --</option>' + 
            '<option value="0">Pelanggan Umum</option>' +
            state.customers.map(c => `<option value="${c.PelangganID}">${c.NamaPelanggan}</option>`).join('');
    }
    function renderCart() {
        if (state.cart.length === 0) {
            cartItemsEl.innerHTML = '<p class="text-muted text-center">Keranjang kosong</p>';
            totalPriceEl.textContent = 'Rp 0';
            return;
        }
        cartItemsEl.innerHTML = state.cart.map(item => `
            <div class="cart-item d-flex justify-content-between align-items-center mb-2 p-2 border rounded" data-id='${item.ProdukID}'>
                <div class="me-2">
                    <div>${item.NamaProduk}</div>
                    <small>${formatRupiah(item.Harga)} x ${item.JumlahProduk}</small>
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-secondary decrease-qty">-</button>
                    <span class="mx-2 qty-display">${item.JumlahProduk}</span>
                    <button class="btn btn-sm btn-secondary increase-qty">+</button>
                    <button class="btn btn-sm btn-danger ms-2 remove-item">Hapus</button>
                </div>
            </div>
        `).join('');
        const total = state.cart.reduce((sum, item) => sum + item.Subtotal, 0);
        totalPriceEl.textContent = formatRupiah(total);
    }

    // --- EVENT LISTENER ---
    productListEl.addEventListener('click', (e) => {
        const card = e.target.closest('.product-card-item');
        if (!card || card.classList.contains('disabled')) return;
        const productId = parseInt(card.dataset.id);
        const product = state.products.find(p => p.ProdukID === productId);
        const existingItem = state.cart.find(i => i.ProdukID === productId);
        if (existingItem) { 
            if (existingItem.JumlahProduk >= product.Stok) { showNotification('Stok tidak mencukupi', 'error'); return; }
            existingItem.JumlahProduk++; existingItem.Subtotal = existingItem.JumlahProduk * existingItem.Harga; 
        } else { 
            state.cart.push({ ...product, JumlahProduk: 1, Subtotal: product.Harga }); 
        } 
        renderCart();
        showNotification(`${product.NamaProduk} ditambahkan`, 'success');
    });

    cartItemsEl.addEventListener('click', (e) => {
        const cartItem = e.target.closest('.cart-item');
        if (!cartItem) return;
        const productId = parseInt(cartItem.dataset.id);
        const product = state.products.find(p => p.ProdukID === productId);
        const cartItemIndex = state.cart.findIndex(i => i.ProdukID === productId);
        if (e.target.classList.contains('increase-qty')) {
            if (state.cart[cartItemIndex].JumlahProduk >= product.Stok) { showNotification('Stok tidak mencukupi', 'error'); return; }
            state.cart[cartItemIndex].JumlahProduk++; state.cart[cartItemIndex].Subtotal = state.cart[cartItemIndex].JumlahProduk * state.cart[cartItemIndex].Harga;
        } else if (e.target.classList.contains('decrease-qty')) {
            if (state.cart[cartItemIndex].JumlahProduk > 1) { state.cart[cartItemIndex].JumlahProduk--; state.cart[cartItemIndex].Subtotal = state.cart[cartItemIndex].JumlahProduk * state.cart[cartItemIndex].Harga; }
        } else if (e.target.classList.contains('remove-item')) { state.cart.splice(cartItemIndex, 1); }
        renderCart();
    });

    customerSelectEl.addEventListener('change', (e) => { state.selectedCustomerId = e.target.value ? parseInt(e.target.value) : null; });
    searchInput.addEventListener('input', (e) => { const filtered = state.products.filter(p => p.NamaNamaProduk.toLowerCase().includes(e.target.value.toLowerCase())); renderProducts(filtered); });
    clearCartBtn.addEventListener('click', () => { if (state.cart.length > 0) { Swal.fire({ title: 'Yakin?', text: "Keranjang akan dikosongkan!", icon: 'warning', showCancelButton: true }).then((result) => { if (result.isConfirmed) { state.cart = []; renderCart(); } }); } });

    checkoutBtn.addEventListener('click', () => {
        if (state.cart.length === 0) { showNotification('Keranjang masih kosong', 'error'); return; }
        if (state.selectedCustomerId === null) { showNotification('Silakan pilih pelanggan terlebih dahulu', 'error'); return; }
        populateTransactionDetailModal();
        transactionDetailModal.show();
    });

    // --- FUNGSI MODAL DETAIL ---
    function populateTransactionDetailModal() {
        if (state.cart.length === 0) { showNotification('Keranjang masih kosong', 'error'); return; }
        const selectedCustomer = state.customers.find(c => c.PelangganID == state.selectedCustomerId);
        modalCustomerNameEl.textContent = selectedCustomer ? selectedCustomer.NamaPelanggan : 'Pelanggan Umum';
        modalTransactionDateEl.textContent = new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        let itemsHtml = ''; let grandTotal = 0;
        state.cart.forEach((item, index) => {
            itemsHtml += `<tr><td>${index + 1}</td><td>${item.NamaProduk}</td><td class="text-center">${item.JumlahProduk}</td><td class="text-end">${formatRupiah(item.Harga)}</td><td class="text-end">${formatRupiah(item.Subtotal)}</td></tr>`;
            grandTotal += item.Subtotal;
        });
        modalTransactionItemsEl.innerHTML = itemsHtml;
        totalBelanjaEl.value = formatRupiah(grandTotal);
        paymentAmountEl.value = ''; changeAmountEl.value = '';
    }

    paymentAmountEl.addEventListener('input', () => {
        const total = state.cart.reduce((sum, item) => sum + item.Subtotal, 0);
        const paid = parseInt(paymentAmountEl.value) || 0;
        const change = paid - total;
        changeAmountEl.value = change >= 0 ? formatRupiah(change) : 'Uang tidak cukup';
    });

    copyDetailsBtn.addEventListener('click', () => { /* ... (kode salin detail tetap sama) ... */ });

    // --- FUNGSI NOTA ---
    function populateAndShowReceipt(receiptData) {
        const selectedCustomer = state.customers.find(c => c.PelangganID == state.selectedCustomerId);
        const customerName = selectedCustomer ? selectedCustomer.NamaPelanggan : 'Pelanggan Umum';
        let itemsHtml = '';
        receiptData.items.forEach(item => {
            itemsHtml += `<div class="d-flex justify-content-between"><span>${item.NamaProduk} (x${item.JumlahProduk})</span><span>${formatRupiah(item.Subtotal)}</span></div>`;
        });
        receiptContentEl.innerHTML = `
            <div class="text-center mb-3"><h4><strong>NOTA BELANJA</strong></h4><p class="mb-1">TErE Coffee House</p><p class="mb-1">Jln.Tralala 210</p><hr></div>
            <div class="mb-2"><p><strong>No. Transaksi:</strong> #${receiptData.transaksiId || Math.floor(Math.random() * 100000)}</p><p><strong>Tanggal:</strong> ${new Date().toLocaleString('id-ID')}</p><p><strong>Pelanggan:</strong> ${customerName}</p></div><hr>
            <div class="mb-2"><h6>Detail Belanja:</h6>${itemsHtml}</div><hr>
            <div class="d-flex justify-content-between"><strong>Total:</strong><strong>${formatRupiah(receiptData.totalHarga)}</strong></div>
            <div class="d-flex justify-content-between"><strong>Tunai:</strong><strong>${formatRupiah(receiptData.uangDibayar)}</strong></div>
            <div class="d-flex justify-content-between"><strong>Kembalian:</strong><strong>${formatRupiah(receiptData.kembalian)}</strong></div><hr>
            <p class="text-center mt-3">-- Terima Kasih --</p>
        `;
        transactionDetailModal.hide(); receiptModal.show();
    }

    // --- EVENT LISTENER UNTUK TOMBOL CETAK (PERBAIKAN) ---
    printReceiptBtn.addEventListener('click', () => {
        window.print();
    });

    // --- EVENT LISTENER PEMBAYARAN ---
    confirmPaymentBtn.addEventListener('click', async () => {
        const totalHarga = state.cart.reduce((sum, item) => sum + item.Subtotal, 0);
        const uangDibayar = parseInt(paymentAmountEl.value) || 0;
        const kembalian = uangDibayar - totalHarga;
        if (uangDibayar < totalHarga) { showNotification('Uang yang dibayarkan tidak mencukupi!', 'error'); return; }
        const transactionData = { pelangganId: state.selectedCustomerId, items: state.cart, totalHarga, uangDibayar, kembalian };
        try {
            confirmPaymentBtn.disabled = true; confirmPaymentBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
            const response = await fetch('api/transaksi.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(transactionData) });
            const result = await response.json();
            if (response.ok) {
                showNotification('Pembayaran berhasil!', 'success');
                const receiptData = { transaksiId: result.transaksiId, ...transactionData };
                populateAndShowReceipt(receiptData);
                state.cart = []; renderCart(); fetchProducts();
            } else { showNotification(result.message, 'error'); }
        } catch (error) { showNotification('Gagal menghubungi server', 'error'); }
        finally { confirmPaymentBtn.disabled = false; confirmPaymentBtn.innerHTML = '<i class="fas fa-check me-1"></i> Konfirmasi & Bayar'; }
    });

    // --- INISIALISASI ---
    fetchProducts(); fetchCustomers();
});
</script>

</body>
</html>