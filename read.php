<?php
$location = $_GET['location'];
$type = $_GET['type'];
$chapterPath = 'admin/' . $location;
$images = glob($chapterPath . '/*.jpg'); // Sesuaikan ekstensi file
?>
<?php if ($type == "Manhwa" or $type == "Manhua"): ?>
    <?php if ($images): ?>
        <?php foreach ($images as $image): ?>
            <img src="<?= str_replace(__DIR__ . '/public', '', $image) ?>" class="img-fluid" alt="Page">
        <?php endforeach; ?>
    <?php else: ?>
        <p>Halaman tidak ditemukan.</p>
    <?php endif; ?>
<?php else: ?>
    <?php if ($images): ?>
        <?php foreach ($images as $image): ?>
            <img src="<?= str_replace(__DIR__ . '/public', '', $image) ?>" class="img-fluid mb-3" alt="Page">
        <?php endforeach; ?>
    <?php else: ?>
        <p>Halaman tidak ditemukan.</p>
    <?php endif; ?>
<?php endif; ?>