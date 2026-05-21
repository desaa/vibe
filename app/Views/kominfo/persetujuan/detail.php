<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
Reviu Usulan Pengadaan OPD
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in">
    <!-- Header Back Panel -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="/kominfo/persetujuan" class="h-9 w-9 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-700 shadow-sm transition-colors">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-gray-800">Reviu Usulan #<?= esc($konsolidasi['nomor_surat_opd']) ?></h1>
                <p class="text-xs text-gray-500 mt-0.5">Asal Instansi: <strong class="text-slate-700"><?= esc($konsolidasi['nama_opd']) ?></strong> (Pengirim: <?= esc($konsolidasi['pengirim']) ?>)</p>
            </div>
        </div>
        
        <div>
            <?php
            $statusLabel = $konsolidasi['status'];
            $statusClass = 'bg-gray-100 text-gray-700';
            
            switch($konsolidasi['status']) {
                case 'DIAJUKAN':
                    $statusLabel = 'Perlu Reviu Kadin';
                    $statusClass = 'bg-yellow-50 text-yellow-800 border border-yellow-200';
                    break;
                case 'DISETUJUI':
                    $statusLabel = 'Disetujui / Selesai';
                    $statusClass = 'bg-emerald-50 text-emerald-800 border border-emerald-200';
                    break;
                case 'DITOLAK':
                    $statusLabel = 'Ditolak';
                    $statusClass = 'bg-rose-50 text-rose-800 border border-rose-200';
                    break;
                case 'DIKEMBALIKAN':
                    $statusLabel = 'Dikembalikan ke OPD';
                    $statusClass = 'bg-purple-50 text-purple-800 border border-purple-200';
                    break;
            }
            ?>
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold <?= $statusClass ?>">
                <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5 animate-pulse"></span>
                <?= esc($statusLabel) ?>
            </span>
        </div>
    </div>

    <!-- Recommendation Alert / Info Card -->
    <?php if ($konsolidasi['status'] === 'DISETUJUI'): ?>
        <div class="p-6 rounded-3xl bg-emerald-50 border border-emerald-100 flex items-start gap-4 text-emerald-800 shadow-sm">
            <div class="h-10 w-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-circle-check text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-sm text-emerald-950">Surat Rekomendasi Resmi Telah Diterbitkan</h4>
                <div class="text-xs text-emerald-800/90 mt-1 space-y-1">
                    <p>Nomor Rekomendasi: <strong class="text-emerald-950"><?= esc($konsolidasi['nomor_rekomendasi']) ?></strong></p>
                    <p>Tanggal Rekomendasi: <strong><?= date('d M Y', strtotime($konsolidasi['tanggal_rekomendasi'])) ?></strong></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Two Column Layout: Submissions (Left) and Action/Timeline (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Consolidated Submissions & Items List -->
        <div class="lg:col-span-2 space-y-6">
            <h3 class="font-bold text-gray-800 flex items-center gap-2 text-lg">
                <i class="fa-solid fa-folder-tree text-blue-500"></i> Lampiran Usulan Bidang / UPTD
            </h3>
            
            <?php foreach ($submissions as $sub): ?>
                <div class="glass-card rounded-3xl overflow-hidden shadow-sm border border-gray-100/50">
                    <div class="p-5 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h4 class="font-bold text-sm text-gray-800"><?= esc($sub['nama_bidang']) ?></h4>
                            <span class="text-xs text-gray-400"><?= esc($sub['nomor_pengajuan']) ?> | Pengusul: <?= esc($sub['pengusul']) ?></span>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-white/50 text-gray-500 font-semibold uppercase tracking-wider border-b border-gray-100">
                                    <th class="p-4">Nama Barang</th>
                                    <th class="p-4">Spesifikasi Detail</th>
                                    <th class="p-4 text-center">Jumlah</th>
                                    <th class="p-4 text-right">Harga Satuan</th>
                                    <th class="p-4 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-600">
                                <?php 
                                $subTotal = 0;
                                foreach ($sub['items'] as $item): 
                                    $itemSubtotal = $item['jumlah'] * $item['estimasi_harga'];
                                    $subTotal += $itemSubtotal;
                                ?>
                                    <tr class="hover:bg-gray-50/20">
                                        <td class="p-4 font-semibold text-slate-800"><?= esc($item['nama_aset']) ?></td>
                                        <td class="p-4 whitespace-pre-line"><?= esc($item['spesifikasi']) ?></td>
                                        <td class="p-4 text-center font-medium"><?= esc($item['jumlah']) ?> <?= esc($item['satuan']) ?></td>
                                        <td class="p-4 text-right">Rp <?= number_format($item['estimasi_harga'], 0, ',', '.') ?></td>
                                        <td class="p-4 text-right font-bold text-slate-800">Rp <?= number_format($itemSubtotal, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-4 bg-slate-50/60 border-t border-gray-100 flex items-center justify-between text-xs text-slate-700">
                        <span>Total Usulan Bidang:</span>
                        <span class="font-bold text-blue-700">Rp <?= number_format($subTotal, 0, ',', '.') ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Verification Action & History Panel -->
        <div class="space-y-6">
            <?php if ($konsolidasi['status'] === 'DIAJUKAN'): ?>
                <!-- Verification Form Card -->
                <div class="glass-card rounded-3xl p-6 md:p-8 space-y-6">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-2 border-b border-gray-100 pb-4">
                        <i class="fa-solid fa-stamp text-blue-500"></i> Panel Persetujuan Kadin
                    </h3>
                    
                    <form action="/kominfo/persetujuan/aksi/<?= $konsolidasi['id'] ?>" method="POST" id="formAksi" class="space-y-4">
                        <?= csrf_field() ?>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Keputusan</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="flex flex-col items-center justify-center p-3 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer transition-all relative select-btn">
                                    <input type="radio" name="action" value="setuju" class="sr-only" checked onclick="togglePanel('setuju')">
                                    <i class="fa-solid fa-circle-check text-emerald-500 text-lg mb-1"></i>
                                    <span class="text-[11px] font-bold text-gray-700">Setujui</span>
                                </label>
                                <label class="flex flex-col items-center justify-center p-3 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer transition-all relative select-btn">
                                    <input type="radio" name="action" value="kembalikan" class="sr-only" onclick="togglePanel('kembalikan')">
                                    <i class="fa-solid fa-arrows-spin text-purple-500 text-lg mb-1"></i>
                                    <span class="text-[11px] font-bold text-gray-700">Kembalikan</span>
                                </label>
                                <label class="flex flex-col items-center justify-center p-3 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer transition-all relative select-btn">
                                    <input type="radio" name="action" value="tolak" class="sr-only" onclick="togglePanel('tolak')">
                                    <i class="fa-solid fa-circle-xmark text-rose-500 text-lg mb-1"></i>
                                    <span class="text-[11px] font-bold text-gray-700">Tolak</span>
                                </label>
                            </div>
                        </div>

                        <!-- Setuju Fields -->
                        <div id="panelSetuju" class="space-y-4 animate-fade-in">
                            <div>
                                <label for="nomor_rekomendasi" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nomor Surat Rekomendasi <span class="text-red-500">*</span></label>
                                <input type="text" id="nomor_rekomendasi" name="nomor_rekomendasi" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all font-semibold" placeholder="misal: 555/456/DISKOMINFO/2026" required>
                            </div>
                            <div>
                                <label for="tanggal_rekomendasi" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tanggal Rekomendasi <span class="text-red-500">*</span></label>
                                <input type="date" id="tanggal_rekomendasi" name="tanggal_rekomendasi" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all font-semibold" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>

                        <!-- Tolak / Kembalikan Catatan -->
                        <div id="panelCatatan" class="hidden animate-fade-in">
                            <label for="catatan" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Alasan & Catatan Revisi/Tolak <span class="text-red-500">*</span></label>
                            <textarea id="catatan" name="catatan" rows="4" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all resize-none" placeholder="Berikan catatan detail alasan mengapa pengajuan dikembalikan atau ditolak..."></textarea>
                        </div>

                        <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-lg shadow-blue-500/20 hover:shadow-blue-500/35 transition-all">
                            Proses Keputusan
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
    function togglePanel(action) {
        const panelSetuju = document.getElementById('panelSetuju');
        const panelCatatan = document.getElementById('panelCatatan');
        
        const nomRekomendasi = document.getElementById('nomor_rekomendasi');
        const tglRekomendasi = document.getElementById('tanggal_rekomendasi');
        const catatan = document.getElementById('catatan');
        
        if (action === 'setuju') {
            panelSetuju.classList.remove('hidden');
            panelCatatan.classList.add('hidden');
            
            nomRekomendasi.setAttribute('required', 'required');
            tglRekomendasi.setAttribute('required', 'required');
            catatan.removeAttribute('required');
            catatan.value = '';
        } else {
            panelSetuju.classList.add('hidden');
            panelCatatan.classList.remove('hidden');
            
            nomRekomendasi.removeAttribute('required');
            tglRekomendasi.removeAttribute('required');
            catatan.setAttribute('required', 'required');
            nomRekomendasi.value = '';
        }
    }

    // Radio button UI highlighting
    document.querySelectorAll('#formAksi input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.select-btn').forEach(btn => {
                btn.classList.remove('border-blue-500', 'bg-blue-50/20');
            });
            if (this.checked) {
                this.closest('.select-btn').classList.add('border-blue-500', 'bg-blue-50/20');
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
