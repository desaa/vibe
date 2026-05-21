<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
Detail Konsolidasi Usulan OPD
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in">
    <!-- Header Back Panel -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="/opd/konsolidasi" class="h-9 w-9 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-700 shadow-sm transition-colors">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-gray-800">Detail Konsolidasi #<?= esc($konsolidasi['nomor_surat_opd']) ?></h1>
                <p class="text-xs text-gray-500 mt-0.5">Tahun Anggaran <?= esc($konsolidasi['tahun_anggaran']) ?> | Dibuat oleh: <?= esc($konsolidasi['pengirim'] ?? 'Admin OPD') ?></p>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            <?php if ($konsolidasi['status'] === 'DISETUJUI'): ?>
                <a href="/opd/rekomendasi/cetak/<?= $konsolidasi['id'] ?>" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-emerald-500/20 transition-all">
                    <i class="fa-solid fa-print text-xs"></i> Cetak Rekomendasi
                </a>
            <?php endif; ?>
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
    <?php elseif ($konsolidasi['status'] === 'DIKEMBALIKAN' && !empty($konsolidasi['catatan_kominfo'])): ?>
        <div class="p-6 rounded-3xl bg-rose-50 border border-rose-100 flex items-start gap-4 text-rose-800 shadow-sm">
            <div class="h-10 w-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-600 flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-circle-exclamation text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-sm text-rose-950">Catatan Pengembalian / Revisi Kominfo:</h4>
                <p class="text-xs leading-relaxed text-rose-700/90 mt-1">
                    <?= esc($konsolidasi['catatan_kominfo']) ?>
                </p>
            </div>
        </div>
    <?php elseif ($konsolidasi['status'] === 'DITOLAK' && !empty($konsolidasi['catatan_kominfo'])): ?>
        <div class="p-6 rounded-3xl bg-rose-50 border border-rose-100 flex items-start gap-4 text-rose-800 shadow-sm">
            <div class="h-10 w-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-600 flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-circle-xmark text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-sm text-rose-950">Alasan Penolakan Kominfo:</h4>
                <p class="text-xs leading-relaxed text-rose-700/90 mt-1">
                    <?= esc($konsolidasi['catatan_kominfo']) ?>
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Two Column Layout: Submissions (Left) and Details & Timeline (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Consolidated Submissions & Items List -->
        <div class="lg:col-span-2 space-y-6">
            <h3 class="font-bold text-gray-800 flex items-center gap-2 text-lg">
                <i class="fa-solid fa-folder-tree text-blue-500"></i> Usulan Bidang Terlampir
            </h3>
            
            <?php foreach ($submissions as $sub): ?>
                <div class="glass-card rounded-3xl overflow-hidden shadow-sm border border-gray-100/50">
                    <div class="p-5 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h4 class="font-bold text-sm text-gray-800"><?= esc($sub['nama_bidang']) ?></h4>
                            <span class="text-xs text-gray-400"><?= esc($sub['nomor_pengajuan']) ?> | Pengusul: <?= esc($sub['pengusul']) ?></span>
                        </div>
                        <div>
                            <?php
                            $subStatusClass = 'bg-gray-100 text-gray-600';
                            if ($sub['status'] === 'DISETUJUI_KOMINFO') {
                                $subStatusClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                            } elseif ($sub['status'] === 'DITOLAK_KOMINFO') {
                                $subStatusClass = 'bg-rose-50 text-rose-700 border border-rose-200';
                            } elseif ($sub['status'] === 'DIKEMBALIKAN_KOMINFO') {
                                $subStatusClass = 'bg-purple-50 text-purple-700 border border-purple-200';
                            } else {
                                $subStatusClass = 'bg-blue-50 text-blue-700 border border-blue-200';
                            }
                            ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider <?= $subStatusClass ?>">
                                <?= esc($sub['status']) ?>
                            </span>
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

        <!-- Right Detail Box & Audit History -->
        <div class="space-y-6">
            <!-- Summary Info Card -->
            <div class="glass-card rounded-3xl p-6 md:p-8 space-y-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-2 border-b border-gray-100 pb-4">
                    <i class="fa-solid fa-circle-info text-blue-500"></i> Informasi Konsolidasi
                </h3>
                
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-gray-400 block font-semibold uppercase tracking-wider">Status Konsolidasi</span>
                        <?php
                        $statusColor = 'text-gray-700 bg-gray-100';
                        if ($konsolidasi['status'] === 'DISETUJUI') $statusColor = 'text-emerald-700 bg-emerald-50 border border-emerald-200';
                        elseif ($konsolidasi['status'] === 'DITOLAK') $statusColor = 'text-rose-700 bg-rose-50 border border-rose-200';
                        elseif ($konsolidasi['status'] === 'DIKEMBALIKAN') $statusColor = 'text-purple-700 bg-purple-50 border border-purple-200';
                        elseif ($konsolidasi['status'] === 'DIAJUKAN') $statusColor = 'text-yellow-700 bg-yellow-50 border border-yellow-200';
                        ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded font-bold uppercase tracking-wider mt-1 <?= $statusColor ?>">
                            <?= esc($konsolidasi['status']) ?>
                        </span>
                    </div>
                    
                    <div>
                        <span class="text-gray-400 block font-semibold uppercase tracking-wider">Nomor Surat Pengantar</span>
                        <p class="font-bold text-slate-800 mt-0.5"><?= esc($konsolidasi['nomor_surat_opd']) ?></p>
                    </div>
                    
                    <div>
                        <span class="text-gray-400 block font-semibold uppercase tracking-wider">Tahun Anggaran</span>
                        <p class="font-semibold text-slate-700 mt-0.5">TA <?= esc($konsolidasi['tahun_anggaran']) ?></p>
                    </div>
                </div>
            </div>

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
<?= $this->endSection() ?>
