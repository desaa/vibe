<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
Welcome to Vibe CI4
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-lg shadow-lg p-8 mb-4">
    <h1 class="text-3xl font-bold text-blue-600 mb-4">CodeIgniter 4 + Bootstrap 5 + Tailwind CSS</h1>
    
    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading">Success!</h4>
        <p>If you see this alert, Bootstrap 5 is working properly.</p>
        <hr>
        <p class="mb-0">And the blue, bold heading above means Tailwind CSS is also working!</p>
    </div>

    <div class="mt-6 flex gap-4">
        <button class="btn btn-primary">Bootstrap Button</button>
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded border-0">
            Tailwind Button
        </button>
    </div>
</div>
<?= $this->endSection() ?>
