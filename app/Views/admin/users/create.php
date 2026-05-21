<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
Tambah Pengguna Baru
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Page -->
    <div class="flex items-center gap-3">
        <a href="/admin/users" class="h-9 w-9 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-700 transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-800">Tambah Pengguna Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Daftarkan akun pengguna baru beserta profil, NIP, OPD, dan Bidang penempatan.</p>
        </div>
    </div>

    <!-- Form Card -->
    <form action="/admin/users/store" method="POST" class="glass-card rounded-3xl p-6 md:p-8 space-y-6" id="formUser">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Account Info -->
            <div class="space-y-4">
                <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-key text-blue-500"></i> Kredensial Akun
                </h3>
                
                <div>
                    <label for="username" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Username <span class="text-red-500">*</span></label>
                    <input type="text" id="username" name="username" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="username_pengguna" value="<?= old('username') ?>" required>
                    <?php if (isset(session('errors')['username'])): ?>
                        <p class="text-xs text-red-500 mt-1 font-medium"><?= session('errors')['username'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="user@mail.com" value="<?= old('email') ?>" required>
                    <?php if (isset(session('errors')['email'])): ?>
                        <p class="text-xs text-red-500 mt-1 font-medium"><?= session('errors')['email'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Password <span class="text-red-500">*</span></label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="Min. 6 Karakter" required>
                    <?php if (isset(session('errors')['password'])): ?>
                        <p class="text-xs text-red-500 mt-1 font-medium"><?= session('errors')['password'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="group" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Hak Akses / Group <span class="text-red-500">*</span></label>
                    <select id="group" name="group" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all font-semibold" required onchange="handleGroupChange(this.value)">
                        <option value="">-- Pilih Group --</option>
                        <option value="superadmin" <?= old('group') === 'superadmin' ? 'selected' : '' ?>>Super Admin</option>
                        <option value="kepala_diskominfo" <?= old('group') === 'kepala_diskominfo' ? 'selected' : '' ?>>Kepala DISKOMINFO</option>
                        <option value="admin_opd" <?= old('group') === 'admin_opd' ? 'selected' : '' ?>>Admin OPD</option>
                        <option value="admin_bidang" <?= old('group') === 'admin_bidang' ? 'selected' : '' ?>>Admin Bidang / UPTD</option>
                    </select>
                    <?php if (isset(session('errors')['group'])): ?>
                        <p class="text-xs text-red-500 mt-1 font-medium"><?= session('errors')['group'] ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="space-y-4">
                <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-address-card text-blue-500"></i> Informasi Profil
                </h3>

                <div>
                    <label for="nama_lengkap" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="Nama Lengkap Beserta Gelar" value="<?= old('nama_lengkap') ?>" required>
                    <?php if (isset(session('errors')['nama_lengkap'])): ?>
                        <p class="text-xs text-red-500 mt-1 font-medium"><?= session('errors')['nama_lengkap'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="nip" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">NIP</label>
                    <input type="text" id="nip" name="nip" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="NIP Pengguna (opsional)" value="<?= old('nip') ?>">
                </div>

                <div id="containerOpd" class="hidden">
                    <label for="opd_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pilih OPD / Dinas</label>
                    <select id="opd_id" name="opd_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                        <option value="">-- Pilih OPD --</option>
                        <?php foreach ($opds as $opd): ?>
                            <option value="<?= $opd['id'] ?>" <?= old('opd_id') == $opd['id'] ? 'selected' : '' ?>><?= esc($opd['nama_opd']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="containerBidang" class="hidden">
                    <label for="bidang_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pilih Bidang / UPTD</label>
                    <select id="bidang_id" name="bidang_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                        <option value="">-- Pilih Bidang --</option>
                        <?php foreach ($bidangs as $bidang): ?>
                            <option value="<?= $bidang['id'] ?>" data-opd="<?= $bidang['opd_id'] ?>" <?= old('bidang_id') == $bidang['id'] ? 'selected' : '' ?>><?= esc($bidang['nama_bidang']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6 mt-6">
            <a href="/admin/users" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-lg shadow-blue-500/20 hover:shadow-blue-500/35 hover:-translate-y-0.5 transition-all">
                Daftarkan Pengguna
            </button>
        </div>
    </form>
</div>

<script>
    function handleGroupChange(role) {
        const containerOpd = document.getElementById('containerOpd');
        const containerBidang = document.getElementById('containerBidang');
        
        const opdSelect = document.getElementById('opd_id');
        const bidangSelect = document.getElementById('bidang_id');
        
        if (role === 'admin_opd') {
            containerOpd.classList.remove('hidden');
            containerBidang.classList.add('hidden');
            opdSelect.setAttribute('required', 'required');
            bidangSelect.removeAttribute('required');
            bidangSelect.value = '';
        } else if (role === 'admin_bidang') {
            containerOpd.classList.remove('hidden');
            containerBidang.classList.remove('hidden');
            opdSelect.setAttribute('required', 'required');
            bidangSelect.setAttribute('required', 'required');
        } else {
            containerOpd.classList.add('hidden');
            containerBidang.classList.add('hidden');
            opdSelect.removeAttribute('required');
            bidangSelect.removeAttribute('required');
            opdSelect.value = '';
            bidangSelect.value = '';
        }
    }

    // Dynamic Bidang filtering based on selected OPD
    document.getElementById('opd_id').addEventListener('change', function() {
        const opdId = this.value;
        const bidangSelect = document.getElementById('bidang_id');
        const options = bidangSelect.querySelectorAll('option');
        
        bidangSelect.value = '';
        options.forEach(opt => {
            const optOpd = opt.getAttribute('data-opd');
            if (!optOpd || optOpd === opdId) {
                opt.style.display = 'block';
            } else {
                opt.style.display = 'none';
            }
        });
    });

    // Run on load to restore state if validation fails
    document.addEventListener('DOMContentLoaded', () => {
        const groupSelect = document.getElementById('group');
        if (groupSelect.value) {
            handleGroupChange(groupSelect.value);
        }
    });
</script>
<?= $this->endSection() ?>
