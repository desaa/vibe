<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
Manajemen Pengguna
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-800">Manajemen Pengguna</h1>
            <p class="text-sm text-gray-500 mt-1">Daftarkan dan petakan akun pengguna beserta hak akses (role), OPD, dan Bidang masing-masing.</p>
        </div>
        <div>
            <a href="/admin/users/create" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/35 hover:-translate-y-0.5 transition-all">
                <i class="fa-solid fa-user-plus text-xs"></i> Tambah Pengguna Baru
            </a>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="glass-card rounded-3xl overflow-hidden shadow-sm">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Pengguna Terdaftar</h3>
            <span class="px-2.5 py-1 text-xs font-medium bg-blue-50 text-blue-700 rounded-lg">
                Total: <?= count($users) ?> Akun
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-gray-500 font-semibold text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="p-5">Nama Lengkap</th>
                        <th class="p-5">Username</th>
                        <th class="p-5">NIP</th>
                        <th class="p-5">Hak Akses / Group</th>
                        <th class="p-5">OPD / Instansi</th>
                        <th class="p-5">Bidang / UPTD</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="p-10 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <i class="fa-solid fa-users-slash text-4xl text-gray-300"></i>
                                    <p class="font-medium">Belum ada akun pengguna yang terdaftar.</p>
                                    <a href="/admin/users/create" class="text-xs text-blue-600 hover:underline">Tambah Sekarang &rarr;</a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <?php
                            $roleLabel = esc($u['group'] ?? 'User');
                            $roleClass = 'bg-gray-100 text-gray-700';
                            
                            switch($u['group']) {
                                case 'superadmin':
                                    $roleLabel = 'Super Admin';
                                    $roleClass = 'bg-red-50 text-red-700 border border-red-200';
                                    break;
                                case 'kepala_diskominfo':
                                    $roleLabel = 'Kepala DISKOMINFO';
                                    $roleClass = 'bg-purple-50 text-purple-700 border border-purple-200';
                                    break;
                                case 'admin_opd':
                                    $roleLabel = 'Admin OPD';
                                    $roleClass = 'bg-blue-50 text-blue-700 border border-blue-200';
                                    break;
                                case 'admin_bidang':
                                    $roleLabel = 'Admin Bidang / UPTD';
                                    $roleClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                                    break;
                            }
                            ?>
                            <tr class="hover:bg-gray-50/40 transition-colors">
                                <td class="p-5 font-semibold text-slate-800">
                                    <?= esc($u['nama_lengkap']) ?>
                                </td>
                                <td class="p-5 text-gray-600 font-medium">
                                    <?= esc($u['username']) ?>
                                </td>
                                <td class="p-5 text-gray-500">
                                    <?= esc($u['nip'] ?: '-') ?>
                                </td>
                                <td class="p-5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?= $roleClass ?>">
                                        <?= esc($roleLabel) ?>
                                    </span>
                                </td>
                                <td class="p-5 text-gray-600 font-medium">
                                    <?= esc($u['nama_opd'] ?: 'Pusat') ?>
                                </td>
                                <td class="p-5 text-gray-500">
                                    <?= esc($u['nama_bidang'] ?: '-') ?>
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
