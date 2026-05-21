<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>Sign Up | ASET TIK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="ASET TIK Registration" name="description" />
    <link rel="shortcut icon" href="<?= base_url('assets/velzone/assets/images/favicon.ico') ?>">
    <script src="<?= base_url('assets/velzone/assets/js/layout.js') ?>"></script>
    <link href="<?= base_url('assets/velzone/assets/css/bootstrap.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/velzone/assets/css/icons.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/velzone/assets/css/app.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/velzone/assets/css/custom.min.css') ?>" rel="stylesheet" type="text/css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif !important;
        }
    </style>
</head>

<body>
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden m-0 card-bg-fill galaxy-border-none">
                            <div class="row justify-content-center g-0">
                                <div class="col-lg-5">
                                    <div class="p-lg-5 p-4 auth-one-bg h-100">
                                        <div class="bg-overlay"></div>
                                        <div class="position-relative h-100 d-flex flex-column">
                                            <div class="mb-4">
                                                <a href="<?= base_url('/') ?>" class="d-block">
                                                    <h3 class="text-white font-bold tracking-wide"><i class="ri-computer-line align-middle me-2"></i>ASET TIK</h3>
                                                </a>
                                            </div>
                                            <div class="mt-auto">
                                                <div class="mb-3">
                                                    <i class="ri-double-quotes-l display-4 text-success"></i>
                                                </div>
                                                <div id="qoutescarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                                                    <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                                    </div>
                                                    <div class="carousel-inner text-center text-white-50 pb-5">
                                                        <div class="carousel-item active">
                                                            <p class="fs-15 fst-italic">" Sistem informasi pengusulan pengadaan aset TIK yang terintegrasi dan akuntabel. "</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Mempermudah proses usulan rekomendasi teknis pengadaan sarana TIK instansi Anda. "</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Menghubungkan Bidang, OPD, dan Dinas Komunikasi & Informatika secara digital. "</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-7">
                                    <div class="p-lg-5 p-4">
                                        <div>
                                            <h5 class="text-primary">Daftar Akun Pengguna</h5>
                                            <p class="text-muted">Buat akun untuk mengajukan atau melakukan verifikasi usulan pengadaan ASET TIK.</p>
                                        </div>

                                        <!-- Flash Messages -->
                                        <?php if (session()->has('error')): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?= session('error') ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (session()->has('errors')): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <ul class="mb-0">
                                                <?php foreach (session('errors') as $error): ?>
                                                <li><?= esc($error) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                        <?php endif; ?>

                                        <div class="mt-4">
                                            <form class="needs-validation" method="post" action="<?= url_to('register') ?>">
                                                <?= csrf_field() ?>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Nama lengkap beserta gelar" value="<?= old('nama_lengkap') ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="nip" class="form-label">NIP</label>
                                                        <input type="text" class="form-control" id="nip" name="nip" placeholder="NIP (opsional)" value="<?= old('nip') ?>">
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                        <input type="email" class="form-control" id="email" name="email" placeholder="contoh@mail.com" value="<?= old('email') ?>" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" value="<?= old('username') ?>" required>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                                                        <div class="position-relative auth-pass-inputgroup">
                                                            <input type="password" class="form-control pe-5 password-input" placeholder="Min. 6 Karakter" id="password" name="password" required>
                                                            <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" type="button" id="password-addon">
                                                                <i class="ri-eye-fill align-middle"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label" for="password_confirm">Konfirmasi Password <span class="text-danger">*</span></label>
                                                        <div class="position-relative auth-pass-inputgroup">
                                                            <input type="password" class="form-control pe-5 password-input" placeholder="Konfirmasi password" id="password_confirm" name="password_confirm" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="role" class="form-label">Peran / Role Pengguna <span class="text-danger">*</span></label>
                                                    <select class="form-select font-semibold" id="role" name="role" required onchange="handleRoleChange(this.value)">
                                                        <option value="">-- Pilih Peran --</option>
                                                        <option value="admin_opd" <?= old('role') === 'admin_opd' ? 'selected' : '' ?>>Admin OPD</option>
                                                        <option value="admin_bidang" <?= old('role') === 'admin_bidang' ? 'selected' : '' ?>>Admin Bidang / UPTD</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3 d-none" id="containerOpd">
                                                    <label for="kd_opd" class="form-label">OPD / Dinas Terkait <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="kd_opd" name="kd_opd">
                                                        <option value="">-- Pilih OPD / Dinas --</option>
                                                        <?php foreach ($opd_list as $opd): ?>
                                                            <option value="<?= $opd['kode_opd'] ?>" data-id="<?= $opd['id'] ?>" <?= old('kd_opd') === $opd['kode_opd'] ? 'selected' : '' ?>><?= esc($opd['nama_opd']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3 d-none" id="containerBidang">
                                                    <label for="kd_bidang" class="form-label">Bidang / Seksi / UPTD <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="kd_bidang" name="kd_bidang">
                                                        <option value="">-- Pilih Bidang / UPTD --</option>
                                                        <?php foreach ($bidang_list as $bidang): ?>
                                                            <option value="<?= $bidang['kode_bidang'] ?>" data-opd-id="<?= $bidang['opd_id'] ?>" <?= old('kd_bidang') === $bidang['kode_bidang'] ? 'selected' : '' ?>><?= esc($bidang['nama_bidang']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="submit" id="register-btn">Daftar Akun Baru</button>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <p class="mb-0">Sudah memiliki akun? <a href="<?= url_to('login') ?>" class="fw-semibold text-primary text-decoration-underline"> Masuk ke Aplikasi</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer galaxy-border-none">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0">&copy; <?= date('Y') ?> ASET TIK. All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="<?= base_url('assets/velzone/assets/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/velzone/assets/libs/simplebar/simplebar.min.js') ?>"></script>
    <script src="<?= base_url('assets/velzone/assets/libs/node-waves/waves.min.js') ?>"></script>
    <script src="<?= base_url('assets/velzone/assets/libs/feather-icons/feather.min.js') ?>"></script>
    <script src="<?= base_url('assets/velzone/assets/js/plugins.js') ?>"></script>
    <script src="<?= base_url('assets/velzone/assets/js/pages/password-addon.init.js') ?>"></script>

    <script>
        function handleRoleChange(role) {
            const containerOpd = document.getElementById('containerOpd');
            const containerBidang = document.getElementById('containerBidang');
            const opdSelect = document.getElementById('kd_opd');
            const bidangSelect = document.getElementById('kd_bidang');

            if (role === 'admin_opd') {
                containerOpd.classList.remove('d-none');
                containerBidang.classList.add('d-none');
                opdSelect.setAttribute('required', 'required');
                bidangSelect.removeAttribute('required');
                bidangSelect.value = '';
            } else if (role === 'admin_bidang') {
                containerOpd.classList.remove('d-none');
                containerBidang.classList.remove('d-none');
                opdSelect.setAttribute('required', 'required');
                bidangSelect.setAttribute('required', 'required');
            } else {
                containerOpd.classList.add('d-none');
                containerBidang.classList.add('d-none');
                opdSelect.removeAttribute('required');
                bidangSelect.removeAttribute('required');
                opdSelect.value = '';
                bidangSelect.value = '';
            }
        }

        // Dynamic Bidang filtering based on selected OPD
        document.getElementById('kd_opd').addEventListener('change', function() {
            const opdSelect = this;
            const selectedOption = opdSelect.options[opdSelect.selectedIndex];
            const opdId = selectedOption.getAttribute('data-id');
            const bidangSelect = document.getElementById('kd_bidang');
            const options = bidangSelect.querySelectorAll('option');

            bidangSelect.value = '';
            options.forEach(opt => {
                const optOpdId = opt.getAttribute('data-opd-id');
                if (!optOpdId || optOpdId === opdId) {
                    opt.style.display = 'block';
                } else {
                    opt.style.display = 'none';
                }
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const roleSelect = document.getElementById('role');
            if (roleSelect && roleSelect.value) {
                handleRoleChange(roleSelect.value);
            }
        });
    </script>
</body>

</html>
