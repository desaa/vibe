<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
Buat Konsolidasi Usulan Baru
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Page -->
    <div class="flex items-center gap-3">
        <a href="/opd/konsolidasi" class="h-9 w-9 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-700 transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-800">Buat Konsolidasi Usulan</h1>
            <p class="text-sm text-gray-500 mt-1">Pilih usulan bidang yang sudah disetujui untuk disatukan dalam satu surat pengantar ke DISKOMINFO.</p>
        </div>
    </div>

    <!-- Form Container -->
    <form action="/opd/konsolidasi/store" method="POST" class="space-y-6" id="formKonsolidasi">
        <?= csrf_field() ?>

        <!-- General Letter Info Card -->
        <div class="glass-card rounded-3xl p-6 md:p-8 space-y-6">
            <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-2 border-b border-gray-100 pb-4">
                <i class="fa-solid fa-file-signature text-blue-500"></i> Informasi Surat Pengantar OPD
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nomor_surat_opd" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nomor Surat Pengantar OPD <span class="text-red-500">*</span></label>
                    <input type="text" id="nomor_surat_opd" name="nomor_surat_opd" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all font-semibold" placeholder="misal: 050/123/OPD/2026" value="<?= old('nomor_surat_opd') ?>" required>
                    <?php if (isset(session('errors')['nomor_surat_opd'])): ?>
                        <p class="text-xs text-red-500 mt-1 font-medium"><?= session('errors')['nomor_surat_opd'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="tahun_anggaran" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tahun Anggaran <span class="text-red-500">*</span></label>
                    <input type="number" id="tahun_anggaran" name="tahun_anggaran" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all font-semibold" value="<?= old('tahun_anggaran', date('Y') + 1) ?>" min="2020" max="2100" required>
                    <?php if (isset(session('errors')['tahun_anggaran'])): ?>
                        <p class="text-xs text-red-500 mt-1 font-medium"><?= session('errors')['tahun_anggaran'] ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Submissions Checklist Card -->
        <div class="glass-card rounded-3xl p-6 md:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-blue-500"></i> Pilih Usulan Bidang / UPTD
                </h3>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="selectAll(true)" class="text-xs font-semibold text-blue-600 hover:underline">Pilih Semua</button>
                    <span class="text-gray-300">|</span>
                    <button type="button" onclick="selectAll(false)" class="text-xs font-semibold text-gray-500 hover:underline">Hapus Pilihan</button>
                </div>
            </div>

            <?php if (empty($approvedSubmissions)): ?>
                <div class="p-10 text-center text-gray-400">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <i class="fa-solid fa-face-smile text-4xl text-gray-300"></i>
                        <p class="font-medium text-sm">Tidak ada usulan bidang yang berstatus 'Disetujui OPD' dan siap dikonsolidasikan.</p>
                        <a href="/opd/verifikasi" class="text-xs text-blue-600 hover:underline">Verifikasi Usulan Bidang Masuk &rarr;</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($approvedSubmissions as $sub): ?>
                        <label class="flex items-start gap-4 p-4 rounded-2xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50/10 cursor-pointer transition-all checkbox-container">
                            <input type="checkbox" name="submissions[]" value="<?= $sub['id'] ?>" class="mt-1 h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 submission-checkbox" onclick="calculateSelectedTotal()">
                            
                            <div class="flex-grow">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                    <h4 class="font-bold text-sm text-gray-800"><?= esc($sub['nama_bidang']) ?></h4>
                                    <span class="text-xs font-medium text-slate-500"><?= esc($sub['nomor_pengajuan']) ?></span>
                                </div>
                                <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                                    <span><i class="fa-regular fa-user mr-1"></i> <?= esc($sub['pengusul']) ?></span>
                                    <span><i class="fa-regular fa-clock mr-1"></i> <?= date('d M Y', strtotime($sub['updated_at'])) ?></span>
                                </div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Aggregation summary panel -->
            <div class="mt-6 pt-6 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-xs text-gray-500 font-medium" id="selectedCount">
                    0 usulan dipilih untuk dikonsolidasikan.
                </div>
                <button type="submit" class="inline-flex items-center gap-1.5 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/35 hover:-translate-y-0.5 transition-all">
                    <i class="fa-solid fa-paper-plane text-xs"></i> Kirim Konsolidasi ke Kominfo
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function selectAll(checked) {
        const checkboxes = document.querySelectorAll('.submission-checkbox');
        checkboxes.forEach(chk => {
            chk.checked = checked;
            // trigger custom highlight classes
            const container = chk.closest('.checkbox-container');
            if (checked) {
                container.classList.add('border-blue-400', 'bg-blue-50/10');
            } else {
                container.classList.remove('border-blue-400', 'bg-blue-50/10');
            }
        });
        calculateSelectedTotal();
    }

    function calculateSelectedTotal() {
        const checkedBoxes = document.querySelectorAll('.submission-checkbox:checked');
        document.getElementById('selectedCount').innerText = checkedBoxes.length + ' usulan dipilih untuk dikonsolidasikan.';
        
        // Highlight active checkboxes
        document.querySelectorAll('.submission-checkbox').forEach(chk => {
            const container = chk.closest('.checkbox-container');
            if (chk.checked) {
                container.classList.add('border-blue-400', 'bg-blue-50/10');
            } else {
                container.classList.remove('border-blue-400', 'bg-blue-50/10');
            }
        });
    }

    document.getElementById('formKonsolidasi').addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.submission-checkbox:checked');
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal satu usulan bidang untuk dikonsolidasikan.');
        }
    });
</script>
<?= $this->endSection() ?>
