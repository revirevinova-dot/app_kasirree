document.addEventListener('DOMContentLoaded', function() {
    // --- FUNGSI HELPER GLOBAL ---
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

    // Mendeteksi halaman saat ini dari URL
    const currentPage = new URLSearchParams(window.location.search).get('page') || 'dashboard';

    // =================================================================================
    // LOGIKA UNTUK HALAMAN DASHBOARD
    // =================================================================================
       // =================================================================================
    // LOGIKA UNTUK HALAMAN DASHBOARD
    // =================================================================================
    if (currentPage === 'dashboard') {
        // --- Fungsi untuk memuat data KPI ---
        async function fetchDashboardData() {
            try {
                const response = await fetch('api/dashboard.php');
                const data = await response.json();

                document.getElementById('totalProduk').textContent = data.totalProduk;
                document.getElementById('totalPelanggan').textContent = data.totalPelanggan;
                document.getElementById('totalPenjualanHariIni').textContent = data.totalPenjualanHariIni;
                document.getElementById('totalPendapatanHariIni').textContent = formatRupiah(data.totalPendapatanHariIni);

            } catch (error) {
                showNotification('Gagal memuat data dashboard', 'error');
                console.error('Error fetching dashboard data:', error);
            }
        }

        // --- Fungsi untuk memuat dan merender grafik penjualan ---
        async function loadSalesChart() {
            try {
                const response = await fetch('api/laporan.php?type=sales-chart');
                const chartData = await response.json();

                const ctx = document.getElementById('salesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line', // Tipe grafik garis
                    data: {
                        labels: chartData.labels, // Label di sumbu X (tanggal)
                        datasets: [{
                            label: 'Total Penjualan',
                            data: chartData.data,   // Data di sumbu Y (total)
                            borderColor: 'rgba(46, 125, 50, 1)', // Warna garis (hijau tema)
                            backgroundColor: 'rgba(46, 125, 50, 0.1)', // Warna area di bawah garis
                            borderWidth: 2,
                            fill: true, // Isi area di bawah garis
                            tension: 0.3 // Buat garis sedikit melengkung
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += formatRupiah(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error loading sales chart:', error);
            }
        }
        
        // --- Fungsi untuk memuat transaksi terakhir ---
        async function loadRecentTransactions() {
            try {
                const response = await fetch('api/laporan.php?type=recent-transactions');
                const transactions = await response.json();
                
                const tableBody = document.getElementById('recentTransactionsTableBody');
                tableBody.innerHTML = '';

                if (transactions.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="3" class="text-center">Belum ada transaksi</td></tr>';
                    return;
                }

                transactions.forEach(t => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>#${t.PenjualanID}</td>
                        <td>${t.NamaPelanggan || 'Umum'}</td>
                        <td>${formatRupiah(t.TotalHarga)}</td>
                    `;
                    tableBody.appendChild(row);
                });

            } catch (error) {
                console.error('Error loading recent transactions:', error);
            }
        }

        // --- Inisialisasi Dashboard ---
        fetchDashboardData();
        loadSalesChart();
        loadRecentTransactions();
    }
    // =================================================================================
    // LOGIKA UNTUK HALAMAN KASIR (Kode dari jawaban sebelumnya)
    // =================================================================================
    if (currentPage === 'kasir') {
        // ... (tempatkan seluruh logika kasir dari jawaban sebelumnya di sini) ...
        // State, fungsi fetchProducts, renderProducts, addToCart, dan semua event listener untuk kasir
    }

    // =================================================================================
    // LOGIKA UNTUK HALAMAN MANAJEMEN STOK
    // =================================================================================
    document.addEventListener('DOMContentLoaded', function() {
    // ... (fungsi helper seperti formatRupiah, showNotification)

    // Mendeteksi halaman saat ini
    const currentPage = new URLSearchParams(window.location.search).get('page') || 'dashboard';

    // =================================================================================
    // LOGIKA UNTUK HALAMAN MANAJEMEN STOK
    // =================================================================================
    if (currentPage === 'manajemen-stok') {
        const stockTableBody = document.getElementById('stockTableBody');
        const addProductForm = document.getElementById('addProductForm');

        // --- FUNGSI UNTUK MEMUAT ULANG TABEL PRODUK ---
        async function loadStockTable() {
            try {
                const response = await fetch('api/produk.php');
                const products = await response.json();
                
                // Kosongkan tabel terlebih dahulu
                stockTableBody.innerHTML = '';

                // Tampilkan produk dalam bentuk baris tabel
                products.forEach(p => {
                    stockTableBody.innerHTML += `
                        <tr>
                            <td>${p.NamaProduk}</td>
                            <td>${formatRupiah(p.Harga)}</td>
                            <td>${p.Stok}</td>
                            <td><input type="number" class="form-control stock-input" data-id='${p.ProdukID}' value="${p.Stok}" min="0"></td>
                            <td><button class="btn btn-sm btn-warning update-stock-btn" data-id='${p.ProdukID}'>Update</button></td>
                        </tr>
                    `;
                });
            } catch (error) {
                showNotification('Gagal memuat data stok', 'error');
                console.error('Error loading stock table:', error);
            }
        }

        // --- EVENT LISTENER UNTUK FORM TAMBAH PRODUK ---
        addProductForm.addEventListener('submit', async (e) => {
            e.preventDefault(); // Mencegah form melakukan reload halaman

            // Ambil data dari form
            const formData = new FormData(addProductForm);
            const data = Object.fromEntries(formData.entries());

            // Tampilkan di console untuk debugging
            console.log('Data yang akan dikirim:', data);

            try {
                // Kirim data ke backend API dengan method POST
                const response = await fetch('api/produk.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                console.log('Respons dari server:', result);

                // Tampilkan notifikasi berdasarkan hasil
                showNotification(result.message, response.ok ? 'success' : 'error');

                // Jika berhasil, reset form dan muat ulang tabel
                if (response.ok) {
                    addProductForm.reset();
                    loadStockTable();
                }
            } catch (error) {
                showNotification('Gagal menambah produk', 'error');
                console.error('Error adding product:', error);
            }
        });

        // ... (event listener untuk update stok bisa ditambahkan di sini)

        // Muat tabel produk saat halaman pertama kali dibuka
        loadStockTable();
    }

    // ... (logika untuk halaman lain)
});

    // =================================================================================
    // LOGIKA UNTUK HALAMAN MANAJEMEN PELANGGAN
    // =================================================================================
    if (currentPage === 'manajemen-pelanggan') {
        const customerTableBody = document.getElementById('customerTableBody');
        const addCustomerForm = document.getElementById('addCustomerForm');

        async function loadCustomerTable() {
            try {
                const response = await fetch('api/pelanggan.php');
                const customers = await response.json();
                customerTableBody.innerHTML = customers.map(c => `
                    <tr>
                        <td>${c.NamaPelanggan}</td>
                        <td>${c.Alamat}</td>
                        <td>${c.NomorTelepon}</td>
                        <td><button class="btn btn-sm btn-danger delete-customer-btn" data-id='${c.PelangganID}'>Hapus</button></td>
                    </tr>
                `).join('');
            } catch (error) {
                showNotification('Gagal memuat data pelanggan', 'error');
            }
        }
        
        addCustomerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(addCustomerForm);
            const data = Object.fromEntries(formData.entries());
            try {
                const response = await fetch('api/pelanggan.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                showNotification(result.message, response.ok ? 'success' : 'error');
                if (response.ok) {
                    addCustomerForm.reset();
                    loadCustomerTable();
                }
            } catch (error) {
                showNotification('Gagal menambah pelanggan', 'error');
            }
        });

        customerTableBody.addEventListener('click', async (e) => {
            if (e.target.classList.contains('delete-customer-btn')) {
                const customerId = e.target.dataset.id;
                const confirm = await Swal.fire({
                    title: 'Yakin?', text: "Data akan dihapus!", icon: 'warning', showCancelButton: true
                });
                if (confirm.isConfirmed) {
                    try {
                        const response = await fetch('api/pelanggan.php', {
                            method: 'DELETE',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ PelangganID: customerId })
                        });
                        const result = await response.json();
                        showNotification(result.message, response.ok ? 'success' : 'error');
                        if (response.ok) loadCustomerTable();
                    } catch (error) {
                        showNotification('Gagal menghapus pelanggan', 'error');
                    }
                }
            }
        });

        loadCustomerTable();
    }
});