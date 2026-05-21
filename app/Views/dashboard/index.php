<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
Dashboard
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-8 animate-fade-in">
    <!-- Header Welcome Card -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-800 p-8 md:p-10 shadow-xl shadow-blue-500/10 text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_107%,rgba(255,255,255,0.08)_0%,rgba(255,255,255,0.05)_5%,rgba(255,255,255,0)_45%)]"></div>
        <div class="relative z-10 md:flex items-center justify-between">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-xs font-semibold uppercase tracking-wider backdrop-blur-md mb-4 text-blue-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                    Sistem Rekomendasi Aset TIK
                </span>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight leading-tight">
                    Selamat Datang Kembali, <br>
                    <span class="text-yellow-300"><?= esc($profile['nama_lengkap'] ?? $user->username) ?></span>
                </h1>
                <p class="mt-3 text-blue-100/90 text-sm md:text-base leading-relaxed">
                    Kelola dan reviu usulan pengadaan Aset Teknologi Informasi dan Komunikasi secara transparan, akurat, dan terintegrasi di lingkungan Pemerintah Daerah.
                </p>
            </div>
            <div class="hidden lg:block">
                <div class="h-28 w-28 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-xl border border-white/20 shadow-2xl">
                    <i class="fa-solid fa-laptop-code text-5xl text-blue-200"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Role-Specific Action Banner / Quick Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php if ($role === 'superadmin'): ?>
            <!-- Super Admin Stats -->
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-red-500">
                <div class="p-4 rounded-xl bg-red-500/10 text-red-500">
                    <i class="fa-solid fa-building text-2xl w-8 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Total OPD</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['total_opd'] ?? 0) ?></h3>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-blue-500">
                <div class="p-4 rounded-xl bg-blue-500/10 text-blue-500">
                    <i class="fa-solid fa-users text-2xl w-8 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Total Pengguna</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['total_users'] ?? 0) ?></h3>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-emerald-500">
                <div class="p-4 rounded-xl bg-emerald-50/10 border border-emerald-100 text-emerald-500">
                    <i class="fa-solid fa-file-lines text-2xl w-8 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Usulan Bidang</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['total_pengajuan_bidang'] ?? 0) ?></h3>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-purple-500">
                <div class="p-4 rounded-xl bg-purple-500/10 text-purple-500">
                    <i class="fa-solid fa-folder-tree text-2xl w-8 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Usulan OPD</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['total_pengajuan_opd'] ?? 0) ?></h3>
                </div>
            </div>

        <?php elseif ($role === 'admin_bidang'): ?>
            <!-- Admin Bidang Stats -->
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-slate-400">
                <div class="p-4 rounded-xl bg-slate-500/10 text-slate-600">
                    <i class="fa-solid fa-pen-to-square text-2xl w-8 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Draf</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['draft'] ?? 0) ?></h3>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-yellow-500">
                <div class="p-4 rounded-xl bg-yellow-500/10 text-yellow-600">
                    <i class="fa-solid fa-paper-plane text-2xl w-8 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Diajukan ke OPD</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['diajukan'] ?? 0) ?></h3>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-blue-500">
                <div class="p-4 rounded-xl bg-blue-500/10 text-blue-500">
                    <i class="fa-solid fa-spinner text-2xl w-8 text-center animate-spin"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Proses Kominfo</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['proses_kominfo'] ?? 0) ?></h3>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-emerald-500">
                <div class="p-4 rounded-xl bg-emerald-500/10 text-emerald-500">
                    <i class="fa-solid fa-circle-check text-2xl w-8 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Disetujui</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['disetujui'] ?? 0) ?></h3>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-purple-500">
                <div class="p-4 rounded-xl bg-purple-500/10 text-purple-500">
                    <i class="fa-solid fa-arrows-spin text-2xl w-8 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Perlu Revisi</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['revisi'] ?? 0) ?></h3>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-rose-500">
                <div class="p-4 rounded-xl bg-rose-500/10 text-rose-500">
                    <i class="fa-solid fa-circle-xmark text-2xl w-8 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Ditolak</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['ditolak'] ?? 0) ?></h3>
                </div>
            </div>

        <?php elseif ($role === 'admin_opd'): ?>
            <!-- Admin OPD Stats -->
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-yellow-500">
                <div class="p-4 rounded-xl bg-yellow-500/10 text-yellow-600">
                    <i class="fa-solid fa-inbox text-2xl w-8 text-center animate-bounce"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Usulan Bidang Masuk</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['bidang_masuk'] ?? 0) ?></h3>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-blue-500">
                <div class="p-4 rounded-xl bg-blue-500/10 text-blue-500">
                    <i class="fa-solid fa-clipboard-check text-2xl w-8 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Usulan Disetujui OPD</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['bidang_disetujui'] ?? 0) ?></h3>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-purple-500">
                <div class="p-4 rounded-xl bg-purple-500/10 text-purple-500">
                    <i class="fa-solid fa-paper-plane text-2xl w-8 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Konsolidasi Diajukan</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['konsolidasi_diajukan'] ?? 0) ?></h3>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-emerald-500">
                <div class="p-4 rounded-xl bg-emerald-500/10 text-emerald-500">
                    <i class="fa-solid fa-stamp text-2xl w-8 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Konsolidasi Disetujui</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['konsolidasi_disetujui'] ?? 0) ?></h3>
                </div>
            </div>

        <?php elseif ($role === 'kepala_diskominfo'): ?>
            <!-- Kepala DISKOMINFO Stats -->
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-yellow-500">
                <div class="p-4 rounded-xl bg-yellow-500/10 text-yellow-600">
                    <i class="fa-solid fa-file-signature text-2xl w-8 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Perlu Reviu</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['masuk'] ?? 0) ?></h3>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-emerald-500">
                <div class="p-4 rounded-xl bg-emerald-500/10 text-emerald-500">
                    <i class="fa-solid fa-circle-check text-2xl w-8 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Disetujui / Terbit Rekomendasi</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['disetujui'] ?? 0) ?></h3>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-purple-500">
                <div class="p-4 rounded-xl bg-purple-500/10 text-purple-500">
                    <i class="fa-solid fa-arrows-spin text-2xl w-8 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Dikembalikan ke OPD</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['revisi'] ?? 0) ?></h3>
                </div>
            </div>
            <div class="glass-card rounded-2xl p-6 flex items-center gap-5 hover:shadow-lg transition-all duration-300 border-l-4 border-rose-500">
                <div class="p-4 rounded-xl bg-rose-500/10 text-rose-500">
                    <i class="fa-solid fa-circle-xmark text-2xl w-8 text-center"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Ditolak</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-800"><?= esc($stats['ditolak'] ?? 0) ?></h3>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Links & System Help Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 glass-card rounded-3xl p-8">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-6">
                <i class="fa-solid fa-compass text-blue-500"></i> Pintasan Menu Cepat
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <?php if ($role === 'admin_bidang'): ?>
                    <a href="/bidang/pengajuan" class="flex items-center gap-4 p-4 rounded-2xl border border-gray-100 hover:border-blue-300 hover:bg-blue-50/50 transition-all group">
                        <div class="p-3 rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-100 transition-colors">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <div>
                            <h5 class="font-semibold text-sm text-gray-800">Daftar Usulan</h5>
                            <p class="text-xs text-gray-500 mt-0.5">Lihat status & tanggapan usulan</p>
                        </div>
                    </a>
                    <a href="/bidang/pengajuan/create" class="flex items-center gap-4 p-4 rounded-2xl border border-gray-100 hover:border-emerald-300 hover:bg-emerald-50/50 transition-all group">
                        <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100 transition-colors">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <div>
                            <h5 class="font-semibold text-sm text-gray-800">Buat Usulan Baru</h5>
                            <p class="text-xs text-gray-500 mt-0.5">Ajukan pengadaan Aset TIK</p>
                        </div>
                    </a>
                <?php elseif ($role === 'admin_opd'): ?>
                    <a href="/opd/verifikasi" class="flex items-center gap-4 p-4 rounded-2xl border border-gray-100 hover:border-yellow-300 hover:bg-yellow-50/50 transition-all group">
                        <div class="p-3 rounded-xl bg-yellow-50 text-yellow-600 group-hover:bg-yellow-100 transition-colors">
                            <i class="fa-solid fa-clipboard-question"></i>
                        </div>
                        <div>
                            <h5 class="font-semibold text-sm text-gray-800">Verifikasi Usulan Bidang</h5>
                            <p class="text-xs text-gray-500 mt-0.5">Review, setujui, tolak atau kembalikan</p>
                        </div>
                    </a>
                    <a href="/opd/konsolidasi" class="flex items-center gap-4 p-4 rounded-2xl border border-gray-100 hover:border-blue-300 hover:bg-blue-50/50 transition-all group">
                        <div class="p-3 rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-100 transition-colors">
                            <i class="fa-solid fa-boxes-packing"></i>
                        </div>
                        <div>
                            <h5 class="font-semibold text-sm text-gray-800">Konsolidasikan Usulan</h5>
                            <p class="text-xs text-gray-500 mt-0.5">Satukan usulan bidang untuk dikirim</p>
                        </div>
                    </a>
                <?php elseif ($role === 'kepala_diskominfo'): ?>
                    <a href="/kominfo/persetujuan" class="flex items-center gap-4 p-4 rounded-2xl border border-gray-100 hover:border-purple-300 hover:bg-purple-50/50 transition-all group">
                        <div class="p-3 rounded-xl bg-purple-50 text-purple-600 group-hover:bg-purple-100 transition-colors">
                            <i class="fa-solid fa-signature"></i>
                        </div>
                        <div>
                            <h5 class="font-semibold text-sm text-gray-800">Review Usulan OPD</h5>
                            <p class="text-xs text-gray-500 mt-0.5">Beri persetujuan rekomendasi resmi</p>
                        </div>
                    </a>
                <?php elseif ($role === 'superadmin'): ?>
                    <a href="/admin/users" class="flex items-center gap-4 p-4 rounded-2xl border border-gray-100 hover:border-red-300 hover:bg-red-50/50 transition-all group">
                        <div class="p-3 rounded-xl bg-red-50 text-red-600 group-hover:bg-red-100 transition-colors">
                            <i class="fa-solid fa-user-gear"></i>
                        </div>
                        <div>
                            <h5 class="font-semibold text-sm text-gray-800">Manajemen Pengguna</h5>
                            <p class="text-xs text-gray-500 mt-0.5">Kelola akun, OPD, dan Bidang</p>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="glass-card rounded-3xl p-8 flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-circle-info text-blue-500"></i> Alur Kerja Sistem
                </h3>
                <ol class="relative border-l border-gray-200 ml-3 space-y-4">                  
                    <li class="ml-4">
                        <div class="absolute w-2 h-2 bg-emerald-500 rounded-full -left-1"></div>
                        <time class="text-[10px] font-semibold text-emerald-500 uppercase">Tahap 1</time>
                        <h4 class="text-xs font-semibold text-gray-800">Pengajuan Bidang / UPTD</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">Bidang mengajukan draf rencana aset TIK.</p>
                    </li>
                    <li class="ml-4">
                        <div class="absolute w-2 h-2 bg-blue-500 rounded-full -left-1"></div>
                        <time class="text-[10px] font-semibold text-blue-500 uppercase">Tahap 2</time>
                        <h4 class="text-xs font-semibold text-gray-800">Verifikasi & Konsolidasi OPD</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">Admin OPD mereviu usulan lalu mengonsolidasikan usulan yang disetujui.</p>
                    </li>
                    <li class="ml-4">
                        <div class="absolute w-2 h-2 bg-purple-500 rounded-full -left-1"></div>
                        <time class="text-[10px] font-semibold text-purple-500 uppercase">Tahap 3</time>
                        <h4 class="text-xs font-semibold text-gray-800">Persetujuan DISKOMINFO</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">Kepala DISKOMINFO menyetujui dan menerbitkan Surat Rekomendasi.</p>
                    </li>
                </ol>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                <span>Versi Aplikasi v1.0.0</span>
                <span><i class="fa-regular fa-clock"></i> TA: <?= date('Y') ?></span>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
