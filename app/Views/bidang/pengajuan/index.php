<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
Daftar Usulan Pengadaan Aset TIK
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-800">Daftar Usulan Pengadaan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola draf, kirim usulan, dan pantau status permohonan rekomendasi aset TIK Anda.</p>
        </div>
        <div>
            <a href="/bidang/pengajuan/create" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/35 hover:-translate-y-0.5 transition-all">
                <i class="fa-solid fa-plus text-xs"></i> Buat Usulan Baru
            </a>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="glass-card rounded-3xl overflow-hidden shadow-sm">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Riwayat Usulan</h3>
            <span class="px-2.5 py-1 text-xs font-medium bg-blue-50 text-blue-700 rounded-lg">
                Total: <?= count($pengajuans) ?> Usulan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="p-5">Nomor Pengajuan</th>
                        <th class="p-5">Tahun Anggaran</th>
                        <th class="p-5">Status</th>
                        <th class="p-5">Tanggal Diperbarui</th>
                        <th class="p-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php if (empty($pengajuans)): ?>
                        <tr>
                            <td colspan="5" class="p-10 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i class="fa-solid fa-folder-open text-4xl text-gray-300"></i>
                                    <p class="font-medium">Belum ada usulan pengadaan yang dibuat.</p>
                                    <a href="/bidang/pengajuan/create" class="text-xs text-blue-600 hover:underline">Mulai Ajukan Sekarang &rarr;</a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pengajuans as $p): ?>
                            <?php
                            $statusLabel = $p['status'];
                            $statusClass = 'bg-gray-100 text-gray-700';
                            
                            switch($p['status']) {
                                case 'DRAFT':
                                    $statusLabel = 'Draft';
                                    $statusClass = 'bg-slate-100 text-slate-700 border border-slate-200';
                                    break;
                                case 'DIAJUKAN':
                                    $statusLabel = 'Diajukan';
                                    $statusClass = 'bg-yellow-50 text-yellow-800 border border-yellow-200';
                                    break;
                                case 'DISETUJUI_OPD':
                                    $statusLabel = 'Disetujui OPD';
                                    $statusClass = 'bg-blue-50 text-blue-800 border border-blue-200';
                                    break;
                                case 'DITOLAK_OPD':
                                    $statusLabel = 'Ditolak OPD';
                                    $statusClass = 'bg-rose-50 text-rose-800 border border-rose-200';
                                    break;
                                case 'DIKEMBALIKAN_OPD':
                                    $statusLabel = 'Perlu Revisi (OPD)';
                                    $statusClass = 'bg-purple-50 text-purple-800 border border-purple-200';
                                    break;
                                case 'DIPROSES_KOMINFO':
                                    $statusLabel = 'Diproses Kominfo';
                                    $statusClass = 'bg-indigo-50 text-indigo-800 border border-indigo-200';
                                    break;
                                case 'DISETUJUI_KOMINFO':
                                    $statusLabel = 'Disetujui Kominfo';
                                    $statusClass = 'bg-emerald-50 text-emerald-800 border border-emerald-200';
                                    break;
                                case 'DITOLAK_KOMINFO':
                                    $statusLabel = 'Ditolak Kominfo';
                                    $statusClass = 'bg-red-50 text-red-800 border border-red-200';
                                    break;
                                case 'DIKEMBALIKAN_KOMINFO':
                                    $statusLabel = 'Perlu Revisi (Kominfo)';
                                    $statusClass = 'bg-violet-50 text-violet-800 border border-violet-200';
                                    break;
                            }
                            ?>
                            <tr class="hover:bg-gray-50/40 transition-colors">
                                <td class="p-5 font-semibold text-slate-800">
                                    <?= esc($p['nomor_pengajuan']) ?>
                                </td>
                                <td class="p-5 text-gray-600">
                                    TA <?= esc($p['tahun_anggaran']) ?>
                                </td>
                                <td class="p-5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
                                        <?= esc($statusLabel) ?>
                                    </span>
                                </td>
                                <td class="p-5 text-gray-500">
                                    <?= date('d M Y, H:i', strtotime($p['updated_at'])) ?>
                                </td>
                                <td class="p-5 text-right space-x-1.5 whitespace-nowrap">
                                    <a href="/bidang/pengajuan/detail/<?= $p['id'] ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-all" title="Detail Pengajuan">
                                        <i class="fa-solid fa-eye text-[10px]"></i> Detail
                                    </a>
                                    
                                    <?php if (in_array($p['status'], ['DRAFT', 'DIKEMBALIKAN_OPD', 'DIKEMBALIKAN_KOMINFO'])): ?>
                                        <a href="/bidang/pengajuan/edit/<?= $p['id'] ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-semibold rounded-lg transition-all" title="Edit Usulan">
                                            <i class="fa-solid fa-pen text-[10px]"></i> Edit
                                        </a>
                                        
                                        <!-- Form Kirim Usulan -->
                                        <form action="/bidang/pengajuan/kirim/<?= $p['id'] ?>" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin mengirim usulan ini ke Admin OPD? Setelah dikirim, usulan tidak dapat diedit kembali sebelum ada reviu.');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-all" title="Kirim Usulan">
                                                <i class="fa-solid fa-paper-plane text-[10px]"></i> Kirim
                                            </button>
                                        </form>
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
