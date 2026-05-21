<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
Daftarkan Kepala DISKOMINFO
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Page -->
    <div class="flex items-center gap-3">
        <a href="/admin/users" class="h-9 w-9 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-700 transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-800">Daftarkan Kepala DISKOMINFO</h1>
            <p class="text-sm text-gray-500 mt-1">Daftarkan akun Kepala Dinas Komunikasi dan Informatika yang akan memproses persetujuan akhir usulan pengadaan ASET TIK.</p>
        </div>
    </div>

    <!-- Form Card -->
    <form action="/admin/register-kadin" method="POST" class="glass-card rounded-3xl p-6 md:p-8 space-y-6">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Account Info -->
            <div class="space-y-4">
                <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-key text-purple-500"></i> Kredensial Akun Kadin
                </h3>
                
                <div>
                    <label for="username" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Username <span class="text-red-500">*</span></label>
                    <input type="text" id="username" name="username" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 outline-none transition-all" placeholder="username_kadin" value="<?= old('username') ?>" required>
                    <?php if (isset(session('errors')['username'])): ?>
                        <p class="text-xs text-red-500 mt-1 font-medium"><?= session('errors')['username'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 outline-none transition-all" placeholder="kadin@mail.com" value="<?= old('email') ?>" required>
                    <?php if (isset(session('errors')['email'])): ?>
                        <p class="text-xs text-red-500 mt-1 font-medium"><?= session('errors')['email'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Password <span class="text-red-500">*</span></label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 outline-none transition-all" placeholder="Min. 6 Karakter" required>
                    <?php if (isset(session('errors')['password'])): ?>
                        <p class="text-xs text-red-500 mt-1 font-medium"><?= session('errors')['password'] ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="space-y-4">
                <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-address-card text-purple-500"></i> Informasi Profil Kadin
                </h3>

                <div>
                    <label for="nama_lengkap" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 outline-none transition-all" placeholder="Nama Lengkap Beserta Gelar" value="<?= old('nama_lengkap') ?>" required>
                    <?php if (isset(session('errors')['nama_lengkap'])): ?>
                        <p class="text-xs text-red-500 mt-1 font-medium"><?= session('errors')['nama_lengkap'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="nip" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">NIP</label>
                    <input type="text" id="nip" name="nip" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 outline-none transition-all" placeholder="NIP Kepala Dinas" value="<?= old('nip') ?>">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">OPD / Instansi Terikat</label>
                    <input type="text" class="w-full px-4 py-2.5 rounded-xl border border-gray-100 bg-gray-50 text-gray-500 outline-none cursor-not-allowed font-medium" value="Dinas Komunikasi dan Informatika (DISKOMINFO)" readonly disabled>
                    <p class="text-xs text-gray-400 mt-1">Akun Kepala DISKOMINFO secara otomatis terikat dengan OPD DISKOMINFO.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6 mt-6">
            <a href="/admin/users" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm shadow-lg shadow-purple-500/20 hover:shadow-purple-500/35 hover:-translate-y-0.5 transition-all">
                Daftarkan Kepala Dinas
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
