<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
Konsolidasi Usulan Pengadaan OPD
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-800">Konsolidasi Usulan OPD</h1>
            <p class="text-sm text-gray-500 mt-1">Gabungkan usulan-usulan dari berbagai bidang yang telah disetujui menjadi satu surat pengantar ke DISKOMINFO.</p>
        </div>
        <div>
            <a href="/opd/konsolidasi/create" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/35 hover:-translate-y-0.5 transition-all">
                <i class="fa-solid fa-boxes-packing text-xs"></i> Buat Konsolidasi Baru
            </a>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="glass-card rounded-3xl overflow-hidden shadow-sm">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Surat Konsolidasi</h3>
            <span class="px-2.5 py-1 text-xs font-medium bg-blue-50 text-blue-700 rounded-lg">
                Total: <?= count($konsolidasis) ?> Surat
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="p-5">Nomor Surat OPD</th>
                        <th class="p-5">Tahun Anggaran</th>
                        <th class="p-5">Nomor Rekomendasi Kominfo</th>
                        <th class="p-5">Status</th>
                        <th class="p-5">Tanggal Dibuat</th>
                        <th class="p-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php if (empty($konsolidasis)): ?>
                        <tr>
                            <td colspan="6" class="p-10 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i class="fa-solid fa-folder-open text-4xl text-gray-300"></i>
                                    <p class="font-medium">Belum ada surat konsolidasi yang dibuat.</p>
                                    <a href="/opd/konsolidasi/create" class="text-xs text-blue-600 hover:underline">Mulai Buat Sekarang &rarr;</a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($konsolidasis as $k): ?>
                            <?php
                            $statusLabel = $k['status'];
                            $statusClass = 'bg-gray-100 text-gray-700';
                            
                            switch($k['status']) {
                                case 'DRAFT':
                                    $statusLabel = 'Draft';
                                    $statusClass = 'bg-slate-100 text-slate-700 border border-slate-200';
                                    break;
                                case 'DIAJUKAN':
                                    $statusLabel = 'Diajukan ke Kominfo';
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
                                    $statusLabel = 'Dikembalikan';
                                    $statusClass = 'bg-purple-50 text-purple-800 border border-purple-200';
                                    break;
                            }
                            ?>
                            <tr class="hover:bg-gray-50/40 transition-colors">
                                <td class="p-5 font-semibold text-slate-800">
                                    <?= esc($k['nomor_surat_opd']) ?>
                                </td>
                                <td class="p-5 text-gray-600">
                                    TA <?= esc($k['tahun_anggaran']) ?>
                                </td>
                                <td class="p-5 text-gray-600 font-medium">
                                    <?= esc($k['nomor_rekomendasi'] ?: '-') ?>
                                    <?php if ($k['tanggal_rekomendasi']): ?>
                                        <div class="text-[10px] text-gray-400 font-normal mt-0.5">Tgl: <?= date('d M Y', strtotime($k['tanggal_rekomendasi'])) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="p-5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
                                        <?= esc($statusLabel) ?>
                                    </span>
                                </td>
                                <td class="p-5 text-gray-500">
                                    <?= date('d M Y, H:i', strtotime($k['created_at'])) ?>
                                </td>
                                <td class="p-5 text-right space-x-1.5 whitespace-nowrap">
                                    <a href="/opd/konsolidasi/detail/<?= $k['id'] ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-all" title="Detail Konsolidasi">
                                        <i class="fa-solid fa-eye text-[10px]"></i> Detail
                                    </a>
                                    
                                    <?php if ($k['status'] === 'DISETUJUI'): ?>
                                        <a href="/opd/rekomendasi/cetak/<?= $k['id'] ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg transition-all" title="Cetak Rekomendasi">
                                            <i class="fa-solid fa-print text-[10px]"></i> Cetak Rekomendasi
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
