<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
<?= isset($pengajuan) ? 'Edit Usulan Pengadaan' : 'Buat Usulan Baru' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php 
$isEdit = isset($pengajuan); 
$actionUrl = $isEdit ? "/bidang/pengajuan/update/{$pengajuan['id']}" : "/bidang/pengajuan/store";
?>
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header Page -->
    <div class="flex items-center gap-3">
        <a href="/bidang/pengajuan" class="h-9 w-9 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-700 transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-800"><?= $isEdit ? 'Revisi / Edit Usulan Pengadaan' : 'Buat Usulan Baru' ?></h1>
            <p class="text-sm text-gray-500 mt-1">Masukkan rencana pengadaan aset TIK beserta rincian spesifikasi teknis dan estimasi harganya.</p>
        </div>
    </div>

    <!-- Form Container -->
    <form action="<?= $actionUrl ?>" method="POST" class="space-y-6" id="formPengajuan">
        <?= csrf_field() ?>
        
        <!-- General Info Card -->
        <div class="glass-card rounded-3xl p-6 md:p-8 space-y-4">
            <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-2">
                <i class="fa-solid fa-circle-info text-blue-500"></i> Informasi Umum
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Bidang / UPTD</label>
                    <input type="text" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-500 font-medium" value="<?= esc($profile['nama_bidang']) ?>" readonly disabled>
                </div>
                <div>
                    <label for="tahun_anggaran" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tahun Anggaran <span class="text-red-500">*</span></label>
                    <input type="number" id="tahun_anggaran" name="tahun_anggaran" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all font-semibold" value="<?= old('tahun_anggaran', $pengajuan['tahun_anggaran'] ?? date('Y') + 1) ?>" min="2020" max="2100" required>
                    <?php if (isset(session('errors')['tahun_anggaran'])): ?>
                        <p class="text-xs text-red-500 mt-1 font-medium"><?= session('errors')['tahun_anggaran'] ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Dynamic Items Card -->
        <div class="glass-card rounded-3xl p-6 md:p-8 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-blue-500"></i> Rincian Kebutuhan Aset TIK
                </h3>
                <button type="button" onclick="addRow()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg transition-all">
                    <i class="fa-solid fa-plus text-[10px]"></i> Tambah Baris
                </button>
            </div>

            <!-- Items Table Layout (Responsive Card for mobile, Table for desktop) -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[900px]" id="itemsTable">
                    <thead>
                        <tr class="text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-200 pb-3">
                            <th class="pb-3 w-1/4">Nama Aset TIK</th>
                            <th class="pb-3 w-1/4">Spesifikasi Detail</th>
                            <th class="pb-3 w-12 text-center">Jumlah</th>
                            <th class="pb-3 w-24">Satuan</th>
                            <th class="pb-3 w-36">Estimasi Harga Satuan</th>
                            <th class="pb-3 w-1/4">Kegunaan / Alasan</th>
                            <th class="pb-3 text-right w-12">Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- Items Rows container -->
                        <?php 
                        $itemsData = old('items', $items ?? []);
                        $rowCount = max(count($itemsData), 1);
                        for ($i = 0; $i < $rowCount; $i++):
                            $item = $itemsData[$i] ?? null;
                        ?>
                            <tr class="item-row" data-index="<?= $i ?>">
                                <td class="py-4 pr-3 align-top">
                                    <input type="text" name="items[<?= $i ?>][nama_aset]" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="misal: Laptop Administrasi" value="<?= esc($item['nama_aset'] ?? '') ?>" required>
                                </td>
                                <td class="py-4 pr-3 align-top">
                                    <textarea name="items[<?= $i ?>][spesifikasi]" rows="2" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all resize-none" placeholder="misal: Core i5, RAM 16GB, SSD 512GB, Windows 11" required><?= esc($item['spesifikasi'] ?? '') ?></textarea>
                                </td>
                                <td class="py-4 pr-3 align-top">
                                    <input type="number" name="items[<?= $i ?>][jumlah]" class="qty-input w-full px-2 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-center" placeholder="0" value="<?= esc($item['jumlah'] ?? '') ?>" min="1" required>
                                </td>
                                <td class="py-4 pr-3 align-top">
                                    <select name="items[<?= $i ?>][satuan]" class="w-full px-2 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                                        <option value="Unit" <?= ($item['satuan'] ?? '') === 'Unit' ? 'selected' : '' ?>>Unit</option>
                                        <option value="Buah" <?= ($item['satuan'] ?? '') === 'Buah' ? 'selected' : '' ?>>Buah</option>
                                        <option value="Paket" <?= ($item['satuan'] ?? '') === 'Paket' ? 'selected' : '' ?>>Paket</option>
                                        <option value="Lisensi" <?= ($item['satuan'] ?? '') === 'Lisensi' ? 'selected' : '' ?>>Lisensi</option>
                                    </select>
                                </td>
                                <td class="py-4 pr-3 align-top">
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-xs text-gray-400 font-semibold">Rp</span>
                                        <input type="number" name="items[<?= $i ?>][estimasi_harga]" class="price-input w-full pl-8 pr-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="0" value="<?= esc($item['estimasi_harga'] ?? '') ?>" min="0" required>
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-1 font-medium pl-1 subtotal-display">Subtotal: Rp 0</div>
                                </td>
                                <td class="py-4 pr-3 align-top">
                                    <input type="text" name="items[<?= $i ?>][kegunaan]" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="misal: Penunjang pelayanan publik" value="<?= esc($item['kegunaan'] ?? '') ?>" required>
                                </td>
                                <td class="py-4 text-right align-top">
                                    <button type="button" onclick="removeRow(this)" class="h-8 w-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-500 flex items-center justify-center transition-colors btn-delete-row">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <!-- Budget Aggregation Display -->
            <div class="mt-6 pt-6 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-sm text-gray-500">
                    * Pastikan harga estimasi sudah mencakup pajak / ongkir jika ada.
                </div>
                <div class="p-4 rounded-2xl bg-slate-900 text-white flex items-center gap-8 shadow-md">
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Total Anggaran Diusulkan</p>
                        <h2 class="text-xl font-bold text-yellow-400 mt-0.5" id="totalBudget">Rp 0</h2>
                    </div>
                    <i class="fa-solid fa-wallet text-2xl text-slate-600"></i>
                </div>
            </div>
        </div>

        <!-- Submit Panel -->
        <div class="flex items-center justify-end gap-3">
            <a href="/bidang/pengajuan" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-lg shadow-blue-500/20 hover:shadow-blue-500/35 hover:-translate-y-0.5 transition-all">
                <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan ke Draf
            </button>
        </div>
    </form>
</div>

<!-- Dynamic Row Adder JS Script -->
<script>
    let rowIndex = <?= $rowCount ?>;

    function addRow() {
        const tbody = document.querySelector('#itemsTable tbody');
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.setAttribute('data-index', rowIndex);
        
        tr.innerHTML = `
            <td class="py-4 pr-3 align-top">
                <input type="text" name="items[\${rowIndex}][nama_aset]" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="misal: Laptop Administrasi" required>
            </td>
            <td class="py-4 pr-3 align-top">
                <textarea name="items[\${rowIndex}][spesifikasi]" rows="2" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all resize-none" placeholder="misal: Core i5, RAM 16GB, SSD 512GB, Windows 11" required></textarea>
            </td>
            <td class="py-4 pr-3 align-top">
                <input type="number" name="items[\${rowIndex}][jumlah]" class="qty-input w-full px-2 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-center" placeholder="0" min="1" required>
            </td>
            <td class="py-4 pr-3 align-top">
                <select name="items[\${rowIndex}][satuan]" class="w-full px-2 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                    <option value="Unit">Unit</option>
                    <option value="Buah">Buah</option>
                    <option value="Paket">Paket</option>
                    <option value="Lisensi">Lisensi</option>
                </select>
            </td>
            <td class="py-4 pr-3 align-top">
                <div class="relative">
                    <span class="absolute left-3 top-2 text-xs text-gray-400 font-semibold">Rp</span>
                    <input type="number" name="items[\${rowIndex}][estimasi_harga]" class="price-input w-full pl-8 pr-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="0" min="0" required>
                </div>
                <div class="text-[10px] text-gray-400 mt-1 font-medium pl-1 subtotal-display">Subtotal: Rp 0</div>
            </td>
            <td class="py-4 pr-3 align-top">
                <input type="text" name="items[\${rowIndex}][kegunaan]" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="misal: Penunjang pelayanan publik" required>
            </td>
            <td class="py-4 text-right align-top">
                <button type="button" onclick="removeRow(this)" class="h-8 w-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-500 flex items-center justify-center transition-colors btn-delete-row">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(tr);
        rowIndex++;
        attachCalculators();
        updateDeleteButtons();
        calculateTotals();
    }

    function removeRow(btn) {
        const row = btn.closest('.item-row');
        row.remove();
        updateDeleteButtons();
        calculateTotals();
    }

    function updateDeleteButtons() {
        const rows = document.querySelectorAll('.item-row');
        const deleteButtons = document.querySelectorAll('.btn-delete-row');
        
        // Always keep at least 1 row
        if (rows.length === 1) {
            deleteButtons[0].style.display = 'none';
        } else {
            deleteButtons.forEach(btn => btn.style.display = 'flex');
        }
    }

    function attachCalculators() {
        const qtyInputs = document.querySelectorAll('.qty-input');
        const priceInputs = document.querySelectorAll('.price-input');
        
        qtyInputs.forEach(input => {
            input.removeEventListener('input', calculateTotals);
            input.addEventListener('input', calculateTotals);
        });
        
        priceInputs.forEach(input => {
            input.removeEventListener('input', calculateTotals);
            input.addEventListener('input', calculateTotals);
        });
    }

    function calculateTotals() {
        const rows = document.querySelectorAll('.item-row');
        let grandTotal = 0;
        
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const subtotal = qty * price;
            grandTotal += subtotal;
            
            row.querySelector('.subtotal-display').innerText = 'Subtotal: Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
        });
        
        document.getElementById('totalBudget').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);
    }

    // Initial setup
    document.addEventListener('DOMContentLoaded', () => {
        attachCalculators();
        updateDeleteButtons();
        calculateTotals();
    });
</script>
<?= $this->endSection() ?>
