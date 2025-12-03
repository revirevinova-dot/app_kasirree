<h2 class="mb-4"><i class="fas fa-users me-2"></i>Manajemen Pelanggan</h2>

<!-- Form Tambah Pelanggan Baru -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Tambah Pelanggan Baru</h5>
    </div>
    <div class="card-body">
        <form id="addCustomerForm">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="namaPelanggan" class="form-label">Nama</label>
                    <input type="text" name="NamaPelanggan" class="form-control" id="namaPelanggan" placeholder="Masukkan nama" required>
                </div>
                <div class="col-md-4">
                    <label for="alamatPelanggan" class="form-label">Alamat</label>
                    <input type="text" name="Alamat" class="form-control" id="alamatPelanggan" placeholder="Masukkan alamat">
                </div>
                <div class="col-md-3">
                    <label for="teleponPelanggan" class="form-label">Nomor Telepon</label>
                    <input type="text" name="NomorTelepon" class="form-control" id="teleponPelanggan" placeholder="Masukkan nomor telepon">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save me-1"></i>Tambah</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Daftar Pelanggan -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Pelanggan</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Pelanggan</th>
                        <th>Alamat</th>
                        <th>Nomor Telepon</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="customerTableBody">
                    <tr><td colspan="4" class="text-center">Memuat data pelanggan...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit Pelanggan -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCustomerModalLabel">Edit Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCustomerForm">
                    <input type="hidden" id="editPelangganID" name="PelangganID">
                    <div class="mb-3">
                        <label for="editNamaPelanggan" class="form-label">Nama Pelanggan</label>
                        <input type="text" name="NamaPelanggan" class="form-control" id="editNamaPelanggan" required>
                    </div>
                    <div class="mb-3">
                        <label for="editAlamatPelanggan" class="form-label">Alamat</label>
                        <input type="text" name="Alamat" class="form-control" id="editAlamatPelanggan">
                    </div>
                    <div class="mb-3">
                        <label for="editTeleponPelanggan" class="form-label">Nomor Telepon</label>
                        <input type="text" name="NomorTelepon" class="form-control" id="editTeleponPelanggan">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="editCustomerForm" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================== -->
<!-- SCRIPT KHUSUS UNTUK HALAMAN PELANGGAN -->
<!-- =========================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log("Halaman Manajemen Pelanggan dimuat.");

    const customerTableBody = document.getElementById('customerTableBody');
    const addCustomerForm = document.getElementById('addCustomerForm');
    const editCustomerForm = document.getElementById('editCustomerForm');
    const editCustomerModal = new bootstrap.Modal(document.getElementById('editCustomerModal'));

    function showNotification(message, icon = 'success') { 
        Swal.fire({ position: 'top-end', icon, title: message, showConfirmButton: false, timer: 1500 }); 
    }

    async function loadCustomerTable() {
        try {
            const response = await fetch('api/pelanggan.php');
            const customers = await response.json();
            customerTableBody.innerHTML = customers.map(c => `
                <tr>
                    <td>${c.NamaPelanggan}</td>
                    <td>${c.Alamat}</td>
                    <td>${c.NomorTelepon}</td>
                    <td>
                        <button class="btn btn-sm btn-warning edit-customer-btn" data-id='${c.PelangganID}' data-bs-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger delete-customer-btn" data-id='${c.PelangganID}' data-bs-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `).join('');
        } catch (error) { showNotification('Gagal memuat data pelanggan', 'error'); }
    }
    
    addCustomerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(addCustomerForm).entries());
        try {
            const response = await fetch('api/pelanggan.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
            const result = await response.json();
            showNotification(result.message, response.ok ? 'success' : 'error');
            if (response.ok) { addCustomerForm.reset(); loadCustomerTable(); }
        } catch (error) { showNotification('Gagal menambah pelanggan', 'error'); }
    });

    customerTableBody.addEventListener('click', async (e) => {
        // --- LOGIKA UNTUK TOMBOL EDIT ---
        if (e.target.closest('.edit-customer-btn')) {
            const customerId = e.target.closest('.edit-customer-btn').dataset.id;
            const row = e.target.closest('tr');
            
            // Ambil data dari sel di baris yang sama
            const customerName = row.cells[0].textContent;
            const customerAddress = row.cells[1].textContent;
            const customerPhone = row.cells[2].textContent;

            // Isi form modal dengan data pelanggan
            document.getElementById('editPelangganID').value = customerId;
            document.getElementById('editNamaPelanggan').value = customerName;
            document.getElementById('editAlamatPelanggan').value = customerAddress;
            document.getElementById('editTeleponPelanggan').value = customerPhone;
            
            // Tampilkan modal
            editCustomerModal.show();
        }

        // --- LOGIKA UNTUK TOMBOL HAPUS ---
        if (e.target.closest('.delete-customer-btn')) {
            const customerId = e.target.closest('.delete-customer-btn').dataset.id;
            const customerName = e.target.closest('tr').cells[0].textContent;

            const confirmResult = await Swal.fire({
                title: 'Yakin ingin menghapus?',
                html: `Pelanggan "<strong>${customerName}</strong>" akan dihapus secara permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            });

            if (confirmResult.isConfirmed) {
                try {
                    const response = await fetch(`api/pelanggan.php?id=${customerId}`, { method: 'DELETE' });
                    const result = await response.json();
                    showNotification(result.message, response.ok ? 'success' : 'error');
                    if (response.ok) loadCustomerTable();
                } catch (error) { showNotification('Gagal menghapus pelanggan', 'error'); }
            }
        }
    });

    // --- EVENT LISTENER UNTUK FORM EDIT PELANGGAN ---
    editCustomerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(editCustomerForm).entries());
        
        try {
            const response = await fetch('api/pelanggan.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
            const result = await response.json();
            showNotification(result.message, response.ok ? 'success' : 'error');
            
            if (response.ok) {
                editCustomerModal.hide(); // Sembunyikan modal
                loadCustomerTable();      // Muat ulang tabel
            }
        } catch (error) { showNotification('Gagal update pelanggan', 'error'); }
    });

    loadCustomerTable();
});
</script>