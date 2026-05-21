<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
Verifikasi Usulan Bidang
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header Page -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-800">Verifikasi Usulan Bidang / UPTD</h1>
        <p class="text-sm text-gray-500 mt-1">Reviu dan verifikasi usulan pengadaan aset TIK yang diajukan oleh berbagai Bidang atau UPTD di bawah instansi Anda.</p>
    </div>

    <!-- Main Table Card -->
    <div class="glass-card rounded-3xl overflow-hidden shadow-sm">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Usulan Masuk</h3>
            <span class="px-2.5 py-1 text-xs font-medium bg-blue-50 text-blue-700 rounded-lg">
                Total: <?= count($pengajuans) ?> Permohonan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="p-5">Asal Bidang / UPTD</th>
                        <th class="p-5">Nomor Pengajuan</th>
                        <th class="p-5">Pengusul</th>
                        <th class="p-5">Tahun Anggaran</th>
                        <th class="p-5">Status</th>
                        <th class="p-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php if (empty($pengajuans)): ?>
                        <tr>
                            <td colspan="6" class="p-10 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i class="fa-solid fa-inbox text-4xl text-gray-300"></i>
                                    <p class="font-medium">Belum ada usulan masuk dari bidang yang perlu diverifikasi.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pengajuans as $p): ?>
                            <?php
                            $statusLabel = $p['status'];
                            $statusClass = 'bg-gray-100 text-gray-700';
                            
                            switch($p['status']) {
                                case 'DIAJUKAN':
                                    $statusLabel = 'Menunggu Verifikasi';
                                    $statusClass = 'bg-yellow-50 text-yellow-800 border border-yellow-200 animate-pulse';
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
                            <tr class="hover:bg-gray-50/40 transition-colors">
                                <td class="p-5 font-semibold text-slate-800">
                                    <?= esc($p['nama_bidang']) ?>
                                </td>
                                <td class="p-5 text-gray-600 font-medium">
                                    <?= esc($p['nomor_pengajuan']) ?>
                                </td>
                                <td class="p-5 text-gray-600">
                                    <?= esc($p['pengusul'] ?? '-') ?>
                                </td>
                                <td class="p-5 text-gray-600">
                                    TA <?= esc($p['tahun_anggaran']) ?>
                                </td>
                                <td class="p-5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
                                        <?= esc($statusLabel) ?>
                                    </span>
                                </td>
                                <td class="p-5 text-right whitespace-nowrap">
                                    <a href="/opd/verifikasi/detail/<?= $p['id'] ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 <?= $p['status'] === 'DIAJUKAN' ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/10' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' ?> text-xs font-semibold rounded-xl transition-all">
                                        <i class="fa-solid <?= $p['status'] === 'DIAJUKAN' ? 'fa-clipboard-check' : 'fa-eye' ?> text-[10px]"></i> 
                                        <?= $p['status'] === 'DIAJUKAN' ? 'Verifikasi' : 'Detail' ?>
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
