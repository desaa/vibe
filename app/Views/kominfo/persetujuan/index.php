<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
Persetujuan Surat Rekomendasi Pengadaan TIK
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header Page -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-800">Reviu Usulan Konsolidasi OPD</h1>
        <p class="text-sm text-gray-500 mt-1">Reviu usulan pengadaan Aset TIK konsolidasi dari setiap OPD dan berikan surat rekomendasi resmi.</p>
    </div>

    <!-- Main Table Card -->
    <div class="glass-card rounded-3xl overflow-hidden shadow-sm">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Daftar Usulan Masuk</h3>
            <span class="px-2.5 py-1 text-xs font-medium bg-blue-50 text-blue-700 rounded-lg">
                Total: <?= count($konsolidasis) ?> Surat Usulan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="p-5">Instansi OPD Pengirim</th>
                        <th class="p-5">Nomor Surat Pengantar</th>
                        <th class="p-5">Tahun Anggaran</th>
                        <th class="p-5">Status</th>
                        <th class="p-5">Tanggal Masuk</th>
                        <th class="p-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php if (empty($konsolidasis)): ?>
                        <tr>
                            <td colspan="6" class="p-10 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i class="fa-solid fa-inbox text-4xl text-gray-300"></i>
                                    <p class="font-medium">Belum ada usulan konsolidasi masuk dari OPD yang perlu direviu.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($konsolidasis as $k): ?>
                            <?php
                            $statusLabel = $k['status'];
                            $statusClass = 'bg-gray-100 text-gray-700';
                            
                            switch($k['status']) {
                                case 'DIAJUKAN':
                                    $statusLabel = 'Perlu Reviu Kadin';
                                    $statusClass = 'bg-yellow-50 text-yellow-800 border border-yellow-200 animate-pulse';
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
                            <tr class="hover:bg-gray-50/40 transition-colors">
                                <td class="p-5">
                                    <div class="font-semibold text-slate-800"><?= esc($k['nama_opd']) ?></div>
                                    <div class="text-xs text-gray-400 mt-0.5">Pengirim: <?= esc($k['pengirim'] ?? '-') ?></div>
                                </td>
                                <td class="p-5 font-medium text-gray-600">
                                    <?= esc($k['nomor_surat_opd']) ?>
                                </td>
                                <td class="p-5 text-gray-600">
                                    TA <?= esc($k['tahun_anggaran']) ?>
                                </td>
                                <td class="p-5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
                                        <?= esc($statusLabel) ?>
                                    </span>
                                </td>
                                <td class="p-5 text-gray-500">
                                    <?= date('d M Y, H:i', strtotime($k['updated_at'])) ?>
                                </td>
                                <td class="p-5 text-right whitespace-nowrap">
                                    <a href="/kominfo/persetujuan/detail/<?= $k['id'] ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 <?= $k['status'] === 'DIAJUKAN' ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/10' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' ?> text-xs font-semibold rounded-xl transition-all">
                                        <i class="fa-solid <?= $k['status'] === 'DIAJUKAN' ? 'fa-file-signature' : 'fa-eye' ?> text-[10px]"></i> 
                                        <?= $k['status'] === 'DIAJUKAN' ? 'Reviu & Proses' : 'Detail' ?>
                                    </a>
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
