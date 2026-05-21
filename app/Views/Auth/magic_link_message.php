<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>Check Your Email | Vibe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Vibe Application" name="description" />
    <link rel="shortcut icon" href="<?= base_url('assets/velzone/assets/images/favicon.ico') ?>">
    <script src="<?= base_url('assets/velzone/assets/js/layout.js') ?>"></script>
    <link href="<?= base_url('assets/velzone/assets/css/bootstrap.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/velzone/assets/css/icons.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/velzone/assets/css/app.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('assets/velzone/assets/css/custom.min.css') ?>" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden card-bg-fill galaxy-border-none">
                            <div class="row justify-content-center g-0">
                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4 auth-one-bg h-100">
                                        <div class="bg-overlay"></div>
                                        <div class="position-relative h-100 d-flex flex-column">
                                            <div class="mb-4">
                                                <a href="<?= base_url('/') ?>" class="d-block">
                                                    <img src="<?= base_url('assets/velzone/assets/images/logo-light.png') ?>" alt="" height="18">
                                                </a>
                                            </div>
                                            <div class="mt-auto">
                                                <div class="mb-3">
                                                    <i class="ri-double-quotes-l display-4 text-success"></i>
                                                </div>
                                                <div id="qoutescarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                                                    <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                                    </div>
                                                    <div class="carousel-inner text-center text-white-50 pb-5">
                                                        <div class="carousel-item active">
                                                            <p class="fs-15 fst-italic">" Check your email! "</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">
                                        <div class="text-center">
                                            <div class="avatar-lg mx-auto mt-2">
                                                <div class="avatar-title bg-light text-success display-3 rounded-circle">
                                                    <i class="ri-mail-send-line"></i>
                                                </div>
                                            </div>
                                            <div class="mt-4 pt-2">
                                                <h4>Success !</h4>
                                                <p class="text-muted mx-4">A reset link has been sent to your email address if it exists in our system.</p>
                                                <div class="mt-4">
                                                    <a href="<?= url_to('login') ?>" class="btn btn-success w-100">Back to Login</a>
                                                </div>
                                            </div>
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
                            <p class="mb-0">&copy; <?= date('Y') ?> Vibe. All rights reserved.</p>
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
</body>

</html>
