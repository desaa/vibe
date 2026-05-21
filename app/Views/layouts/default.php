<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?? 'ASET TIK - Sistem Rekomendasi' ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <!-- Tailwind CSS -->
    <link href="<?= base_url('assets/css/tailwind.css') ?>" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f3f4f6;
        }
        .sidebar {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            box-shadow: 4px 0 24px rgba(0,0,0,0.15);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        }
        .nav-link-custom {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-link-custom:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
        }
        .nav-link-custom.active {
            background: linear-gradient(90deg, #3b82f6 0%, #1d4ed8 100%);
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);
        }
        .badge-role {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
</head>
<body class="bg-gray-50 flex min-h-screen text-gray-800">

    <?php 
    $user = auth()->user(); 
    $db = \Config\Database::connect();
    $profile = $db->table('user_profiles')
        ->select('user_profiles.*, opd.nama_opd, bidang.nama_bidang')
        ->join('opd', 'opd.id = user_profiles.opd_id', 'left')
        ->join('bidang', 'bidang.id = user_profiles.bidang_id', 'left')
        ->where('user_id', $user->id)
        ->get()
        ->getRowArray();

    $roleName = 'User';
    $roleClass = 'bg-gray-100 text-gray-700';
    if ($user->inGroup('superadmin')) {
        $roleName = 'Super Admin';
        $roleClass = 'bg-red-500 text-white';
    } elseif ($user->inGroup('kepala_diskominfo')) {
        $roleName = 'Kepala DISKOMINFO';
        $roleClass = 'bg-purple-500 text-white';
    } elseif ($user->inGroup('admin_opd')) {
        $roleName = 'Admin OPD';
        $roleClass = 'bg-blue-500 text-white';
    } elseif ($user->inGroup('admin_bidang')) {
        $roleName = 'Admin Bidang / UPTD';
        $roleClass = 'bg-emerald-500 text-white';
    }
    ?>

    <!-- Sidebar -->
    <aside class="sidebar w-64 text-white flex-shrink-0 flex flex-col justify-between hidden md:flex border-r border-slate-800">
        <div>
            <!-- Brand Logo -->
            <div class="p-6 flex items-center gap-3 border-b border-slate-800">
                <div class="h-10 w-10 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <i class="fa-solid fa-laptop-code text-lg"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold leading-tight tracking-wide">ASET TIK</h1>
                    <span class="text-xs text-slate-400">Sistem Rekomendasi</span>
                </div>
            </div>

            <!-- Profile Widget -->
            <div class="p-6 border-b border-slate-800 bg-slate-900/50">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-full bg-slate-700 border-2 border-blue-500 flex items-center justify-center font-bold text-lg text-white">
                        <?= strtoupper(substr($user->username, 0, 2)) ?>
                    </div>
                    <div class="overflow-hidden">
                        <h4 class="font-semibold text-sm truncate"><?= $profile['nama_lengkap'] ?? $user->username ?></h4>
                        <span class="badge-role px-2 py-0.5 rounded text-[10px] <?= $roleClass ?> inline-block mt-1"><?= $roleName ?></span>
                    </div>
                </div>
                <div class="mt-3 text-xs text-slate-400 truncate">
                    <i class="fa-solid fa-building text-[10px] mr-1"></i> <?= $profile['nama_opd'] ?? 'Pusat' ?>
                    <?php if (!empty($profile['nama_bidang'])): ?>
                        <br><i class="fa-solid fa-sitemap text-[10px] mr-1"></i> <?= $profile['nama_bidang'] ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="p-4 space-y-1">
                <a href="/dashboard" class="nav-link-custom flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white font-medium <?= service('router')->getMatchedRoute()[0] === 'dashboard' ? 'active text-white' : '' ?>">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i> Dashboard
                </a>

                <?php if ($user->inGroup('admin_bidang') || $user->inGroup('superadmin')): ?>
                    <div class="pt-4 pb-1 px-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Menu Bidang</div>
                    <a href="/bidang/pengajuan" class="nav-link-custom flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white font-medium <?= strpos(service('router')->getMatchedRoute()[0], 'bidang/pengajuan') === 0 ? 'active text-white' : '' ?>">
                        <i class="fa-solid fa-file-invoice w-5 text-center"></i> Usulan Pengadaan
                    </a>
                <?php endif; ?>

                <?php if ($user->inGroup('admin_opd') || $user->inGroup('superadmin')): ?>
                    <div class="pt-4 pb-1 px-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Menu OPD</div>
                    <a href="/opd/verifikasi" class="nav-link-custom flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white font-medium <?= strpos(service('router')->getMatchedRoute()[0], 'opd/verifikasi') === 0 ? 'active text-white' : '' ?>">
                        <i class="fa-solid fa-clipboard-check w-5 text-center"></i> Verifikasi Bidang
                    </a>
                    <a href="/opd/konsolidasi" class="nav-link-custom flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white font-medium <?= strpos(service('router')->getMatchedRoute()[0], 'opd/konsolidasi') === 0 ? 'active text-white' : '' ?>">
                        <i class="fa-solid fa-boxes-packing w-5 text-center"></i> Konsolidasi Usulan
                    </a>
                <?php endif; ?>

                <?php if ($user->inGroup('kepala_diskominfo') || $user->inGroup('superadmin')): ?>
                    <div class="pt-4 pb-1 px-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Menu DISKOMINFO</div>
                    <a href="/kominfo/persetujuan" class="nav-link-custom flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white font-medium <?= strpos(service('router')->getMatchedRoute()[0], 'kominfo/persetujuan') === 0 ? 'active text-white' : '' ?>">
                        <i class="fa-solid fa-stamp w-5 text-center"></i> Persetujuan Kadin
                    </a>
                <?php endif; ?>

                <?php if ($user->inGroup('superadmin')): ?>
                    <div class="pt-4 pb-1 px-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Super Admin</div>
                    <a href="/admin/users" class="nav-link-custom flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white font-medium <?= strpos(service('router')->getMatchedRoute()[0], 'admin/users') === 0 ? 'active text-white' : '' ?>">
                        <i class="fa-solid fa-users w-5 text-center"></i> Manajemen Pengguna
                    </a>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Sidebar Footer / Logout -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/20">
            <a href="/logout" class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-400 hover:text-red-300 hover:bg-red-500/10 font-medium transition-all">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Keluar
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Navbar (Mobile support + Notification area) -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-10">
            <div class="flex items-center gap-3 md:hidden">
                <div class="h-9 w-9 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-laptop-code"></i>
                </div>
                <h1 class="text-md font-bold tracking-wide">ASET TIK</h1>
            </div>
            
            <div class="hidden md:block">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">
                    <?= $this->renderSection('title') ?? 'Sistem Rekomendasi Pengadaan TIK' ?>
                </h2>
            </div>

            <!-- Mobile Logout & Mini Avatar -->
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-xs text-gray-400">Selamat datang,</p>
                    <h5 class="text-sm font-bold text-gray-700"><?= $profile['nama_lengkap'] ?? $user->username ?></h5>
                </div>
                <a href="/logout" class="md:hidden h-9 w-9 rounded-lg bg-red-50 hover:bg-red-100 flex items-center justify-center text-red-500 transition-colors">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </header>

        <!-- Dynamic Content Container -->
        <main class="flex-1 overflow-y-auto p-6 md:p-8">
            <!-- Toast Notifications -->
            <?php if (session()->has('message')): ?>
                <div class="mb-6 flex items-center gap-3 px-4 py-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-sm shadow-emerald-500/5 transition-all">
                    <i class="fa-solid fa-circle-check text-lg text-emerald-500"></i>
                    <p class="text-sm font-medium"><?= session('message') ?></p>
                </div>
            <?php endif; ?>

            <?php if (session()->has('error')): ?>
                <div class="mb-6 flex items-center gap-3 px-4 py-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm shadow-rose-500/5 transition-all">
                    <i class="fa-solid fa-circle-exclamation text-lg text-rose-500"></i>
                    <p class="text-sm font-medium"><?= session('error') ?></p>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
