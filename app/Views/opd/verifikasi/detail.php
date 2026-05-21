<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
Verifikasi Usulan Pengadaan TIK
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in">
    <!-- Header Back Panel -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="/opd/verifikasi" class="h-9 w-9 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-700 shadow-sm transition-colors">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-gray-800">Verifikasi Usulan #<?= esc($pengajuan['nomor_pengajuan']) ?></h1>
                <p class="text-xs text-gray-500 mt-0.5">Diajukan oleh: <strong class="text-slate-700"><?= esc($pengajuan['nama_bidang']) ?></strong> (Pengusul: <?= esc($pengajuan['pengusul']) ?>)</p>
            </div>
        </div>
        
        <div>
            <?php
            $statusLabel = $pengajuan['status'];
            $statusClass = 'bg-gray-100 text-gray-700';
            
            switch($pengajuan['status']) {
                case 'DIAJUKAN':
                    $statusLabel = 'Menunggu Verifikasi';
                    $statusClass = 'bg-yellow-50 text-yellow-800 border border-yellow-200';
                    break;
                case 'DISETUJUI_OPD':
                    $statusLabel = 'Disetujui OPD';
                    $statusClass = 'bg-emerald-50 text-emerald-800 border border-emerald-200';
                    break;
                case 'DITOLAK_OPD':
                    $statusLabel = 'Ditolak OPD';
                    $statusClass = 'bg-rose-50 text-rose-800 border border-rose-200';
                    break;
                case 'DIKEMBALIKAN_OPD':
                    $statusLabel = 'Dikembalikan ke Bidang';
                    $statusClass = 'bg-purple-50 text-purple-800 border border-purple-200';
                    break;
                case 'DIPROSES_KOMINFO':
                    $statusLabel = 'Diproses Kominfo';
                    $statusClass = 'bg-indigo-50 text-indigo-800 border border-indigo-200';
                    break;
            }
            ?>
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold <?= $statusClass ?>">
                <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5 animate-pulse"></span>
                <?= esc($statusLabel) ?>
            </span>
        </div>
    </div>

    <!-- Alert Catatan Revisi / Penolakan if exist -->
    <?php if (!empty($pengajuan['catatan_revisi'])): ?>
        <div class="p-6 rounded-3xl bg-amber-50 border border-amber-100 flex items-start gap-4 text-amber-800 shadow-sm">
            <div class="h-10 w-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-600 flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-circle-exclamation text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-sm text-amber-900">Catatan/Alasan Sebelumnya:</h4>
                <p class="text-xs leading-relaxed text-amber-700/90 mt-1">
                    <?= esc($pengajuan['catatan_revisi']) ?>
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Two Column Layout: Items (Left) and Verification Form / Timeline (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Items Table Column -->
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-card rounded-3xl overflow-hidden shadow-sm">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-blue-500"></i> Daftar Barang Diusulkan
                    </h3>
                    <span class="px-2.5 py-1 text-xs font-semibold bg-blue-50 text-blue-700 rounded-lg">
                        <?= count($items) ?> Item
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-100">
                                <th class="p-5">Barang & Spesifikasi</th>
                                <th class="p-5 text-center">Jumlah</th>
                                <th class="p-5">Estimasi Harga</th>
                                <th class="p-5">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php 
                            $grandTotal = 0;
                            foreach ($items as $item): 
                                $subtotal = $item['jumlah'] * $item['estimasi_harga'];
                                $grandTotal += $subtotal;
                            ?>
                                <tr class="hover:bg-gray-50/40">
                                    <td class="p-5">
                                        <div class="font-semibold text-slate-800"><?= esc($item['nama_aset']) ?></div>
                                        <div class="text-xs text-gray-500 mt-1 max-w-sm whitespace-pre-line leading-relaxed"><?= esc($item['spesifikasi']) ?></div>
                                        <div class="text-[11px] text-blue-600 mt-2 font-medium bg-blue-50/80 px-2 py-0.5 rounded inline-block">
                                            <i class="fa-solid fa-circle-info mr-1 text-[9px]"></i>Kegunaan: <?= esc($item['kegunaan']) ?>
                                        </div>
                                    </td>
                                    <td class="p-5 text-center font-medium text-gray-700">
                                        <?= esc($item['jumlah']) ?> <?= esc($item['satuan']) ?>
                                    </td>
                                    <td class="p-5 text-gray-600 font-medium whitespace-nowrap">
                                        Rp <?= number_format($item['estimasi_harga'], 0, ',', '.') ?>
                                    </td>
                                    <td class="p-5 font-bold text-slate-800 whitespace-nowrap">
                                        Rp <?= number_format($subtotal, 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-slate-50 border-t border-gray-100 flex items-center justify-between text-slate-800">
                    <span class="font-semibold text-sm">Total Estimasi Anggaran:</span>
                    <span class="text-lg font-bold text-blue-700">Rp <?= number_format($grandTotal, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>

        <!-- Verification Action & History Panel -->
        <div class="space-y-6">
            <?php if ($pengajuan['status'] === 'DIAJUKAN'): ?>
                <!-- Verification Form Card -->
                <div class="glass-card rounded-3xl p-6 md:p-8 space-y-6">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-2 border-b border-gray-100 pb-4">
                        <i class="fa-solid fa-user-shield text-blue-500"></i> Panel Verifikasi OPD
                    </h3>
                    
                    <form action="/opd/verifikasi/aksi/<?= $pengajuan['id'] ?>" method="POST" id="formAksi" class="space-y-4">
                        <?= csrf_field() ?>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tindakan</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="flex flex-col items-center justify-center p-3 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer transition-all peer-checked:border-blue-500 relative select-btn active-state">
                                    <input type="radio" name="action" value="setuju" class="sr-only" checked onclick="toggleCatatan(false)">
                                    <i class="fa-solid fa-circle-check text-emerald-500 text-lg mb-1"></i>
                                    <span class="text-[11px] font-bold text-gray-700">Setujui</span>
                                </label>
                                <label class="flex flex-col items-center justify-center p-3 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer transition-all relative select-btn">
                                    <input type="radio" name="action" value="kembalikan" class="sr-only" onclick="toggleCatatan(true)">
                                    <i class="fa-solid fa-arrows-spin text-purple-500 text-lg mb-1"></i>
                                    <span class="text-[11px] font-bold text-gray-700">Kembalikan</span>
                                </label>
                                <label class="flex flex-col items-center justify-center p-3 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer transition-all relative select-btn">
                                    <input type="radio" name="action" value="tolak" class="sr-only" onclick="toggleCatatan(true)">
                                    <i class="fa-solid fa-circle-xmark text-rose-500 text-lg mb-1"></i>
                                    <span class="text-[11px] font-bold text-gray-700">Tolak</span>
                                </label>
                            </div>
                        </div>

                        <div id="containerCatatan" class="hidden animate-fade-in">
                            <label for="catatan" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Catatan / Alasan Revisi/Tolak <span class="text-red-500">*</span></label>
                            <textarea id="catatan" name="catatan" rows="3" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all resize-none" placeholder="Tuliskan catatan detail mengapa ditolak / perlu revisi..."></textarea>
                        </div>

                        <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-lg shadow-blue-500/20 hover:shadow-blue-500/35 transition-all">
                            Proses Verifikasi
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Timeline Log Audit -->
            <div class="glass-card rounded-3xl p-6 md:p-8 space-y-6">
                <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-2 border-b border-gray-100 pb-4">
                    <i class="fa-solid fa-clock-rotate-left text-blue-500"></i> Rekam Jejak Alur Kerja
                </h3>

                <div class="relative pl-6 border-l-2 border-gray-100 space-y-8 ml-3 py-2">
                    <?php foreach ($histories as $history): ?>
                        <div class="relative">
                            <!-- Timeline Indicator point -->
                            <div class="absolute -left-[31px] top-1.5 h-4.5 w-4.5 rounded-full border-4 border-white bg-blue-500 shadow-sm flex items-center justify-center"></div>
                            
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 block uppercase tracking-wider">
                                    <?= date('d M Y, H:i', strtotime($history['created_at'])) ?>
                                </span>
                                
                                <h4 class="font-bold text-xs text-gray-800 mt-1">
                                    <?= esc($history['nama_lengkap'] ?? 'System') ?>
                                </h4>
                                
                                <p class="text-xs text-gray-500 leading-relaxed mt-1">
                                    <?= esc($history['catatan']) ?>
                                </p>
                                
                                <div class="mt-2.5 flex items-center gap-1.5 text-[9px] font-bold uppercase tracking-wider text-slate-400">
                                    <span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-600"><?= esc($history['status_awal']) ?></span>
                                    <i class="fa-solid fa-arrow-right-long text-gray-300"></i>
                                    <span class="bg-blue-50 px-1.5 py-0.5 rounded text-blue-700"><?= esc($history['status_akhir']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
    </div>
</div>

<script>
    function toggleCatatan(show) {
        const container = document.getElementById('containerCatatan');
        const catatanInput = document.getElementById('catatan');
        
        if (show) {
            container.classList.remove('hidden');
            catatanInput.setAttribute('required', 'required');
        } else {
            container.classList.add('hidden');
            catatanInput.removeAttribute('required');
            catatanInput.value = '';
        }
    }

    // Add visual classes to selected radio button container
    document.querySelectorAll('#formAksi input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove active classes from all containers
            document.querySelectorAll('.select-btn').forEach(btn => {
                btn.classList.remove('border-blue-500', 'bg-blue-50/20');
            });
            
            // Add to selected
            if (this.checked) {
                const parent = this.closest('.select-btn');
                parent.classList.add('border-blue-500', 'bg-blue-50/20');
            }
        });
    });
    
    // Set default setuju border style on load
    document.addEventListener('DOMContentLoaded', () => {
        const checkedRadio = document.querySelector('#formAksi input[type="radio"]:checked');
        if (checkedRadio) {
            checkedRadio.closest('.select-btn').classList.add('border-blue-500', 'bg-blue-50/20');
        }
    });
</script>
<?= $this->endSection() ?>
