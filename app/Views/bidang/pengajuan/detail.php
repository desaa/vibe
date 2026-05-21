<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
Detail Usulan Pengadaan TIK
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in">
    <!-- Header Back Panel -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="/bidang/pengajuan" class="h-9 w-9 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-700 shadow-sm transition-colors">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-gray-800">Detail Usulan #<?= esc($pengajuan['nomor_pengajuan']) ?></h1>
                <p class="text-xs text-gray-500 mt-0.5">Tahun Anggaran <?= esc($pengajuan['tahun_anggaran']) ?></p>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            <?php if (in_array($pengajuan['status'], ['DRAFT', 'DIKEMBALIKAN_OPD', 'DIKEMBALIKAN_KOMINFO'])): ?>
                <a href="/bidang/pengajuan/edit/<?= $pengajuan['id'] ?>" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 text-sm font-semibold rounded-xl transition-all">
                    <i class="fa-solid fa-pen text-xs"></i> Edit Usulan
                </a>
                <form action="/bidang/pengajuan/kirim/<?= $pengajuan['id'] ?>" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin mengirim usulan ini ke Admin OPD?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-500/20 transition-all">
                        <i class="fa-solid fa-paper-plane text-xs"></i> Kirim ke OPD
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Alert Catatan Revisi / Penolakan if exist -->
    <?php if (in_array($pengajuan['status'], ['DIKEMBALIKAN_OPD', 'DIKEMBALIKAN_KOMINFO', 'DITOLAK_OPD', 'DITOLAK_KOMINFO']) && (!empty($pengajuan['catatan_revisi']) || !empty($pengajuan['catatan_kominfo']))): ?>
        <div class="p-6 rounded-3xl bg-rose-50 border border-rose-100 flex items-start gap-4 text-rose-800 shadow-sm shadow-rose-500/5">
            <div class="h-10 w-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-600 flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-circle-exclamation text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-sm text-rose-900">Catatan Reviu / Revisi:</h4>
                <p class="text-xs leading-relaxed text-rose-700/90 mt-1">
                    <?= esc($pengajuan['catatan_revisi'] ?: $pengajuan['catatan_kominfo']) ?>
                </p>
                <?php if (in_array($pengajuan['status'], ['DIKEMBALIKAN_OPD', 'DIKEMBALIKAN_KOMINFO'])): ?>
                    <a href="/bidang/pengajuan/edit/<?= $pengajuan['id'] ?>" class="inline-flex items-center gap-1 mt-3 text-xs font-semibold text-rose-900 hover:underline">
                        Perbaiki usulan sekarang &rarr;
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Two Column Layout: Items (Left) and Timeline (Right) -->
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

        <!-- Timeline Log Audit Column -->
        <div class="space-y-6">
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
<?= $this->endSection() ?>
