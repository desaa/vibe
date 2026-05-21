<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?? 'Vibe CodeIgniter App' ?></title>
    <!-- Bootstrap CSS -->
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <!-- Tailwind CSS -->
    <link href="<?= base_url('assets/css/tailwind.css') ?>" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal text-gray-900">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Vibe CI4</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="target" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="container mx-auto px-4">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
