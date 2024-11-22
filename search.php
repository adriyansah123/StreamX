<?php
require 'functions.php'; // Pastikan untuk menyertakan koneksi ke database
global $conn;
$judul = $_GET['keyword'] ?? null;
$jenis = $_GET['jenis'] ?? null;
$type = $_GET['type'];
$data = $_GET['data'] ?? null;

if ($type == "movie") {
    if ($judul != null) {
        $daftarfilm = mysqli_query($conn, "SELECT * FROM `movie` WHERE `judul` LIKE '%$judul%' ORDER BY `tahun` DESC");
    }
    if ($jenis == "genre") {
        $daftarfilm = mysqli_query($conn, "SELECT * FROM `movie` WHERE `genre` LIKE '%$data%' ORDER BY `tahun` DESC");
    } else if ($jenis == "pemeran") {
        $daftarfilm = mysqli_query($conn, "SELECT * FROM `movie` WHERE `pemeran` LIKE '%$data%' ORDER BY `tahun` DESC");
    } else if ($jenis == "tahun") {
        $daftarfilm = mysqli_query($conn, "SELECT * FROM `movie` WHERE `tahun` = '$data' ORDER BY `tahun` DESC");
    }
}
?>
<?php if ($type == "movie") : ?>
<div style="margin-top: 100px;">
    <div class="row justify-content-center">
        <?php foreach ($daftarfilm as $list): ?>
            <div class="col-lg-2 col-sm-6 mb-3">
                <div class="card" style="height: 450px; position: relative;">
                    <div class="ribbon">
                        <?= $list['jenis']; ?>
                    </div>
                    <img src="admin/<?= $list['poster']; ?>" class="card-img-top" alt="..." width="" height="200px">
                    <div class="card-body">
                        <h5 class="card-title"><?= $list['judul']; ?> (<?= $list['tahun']; ?>)</h5>
                        <p class="card-text">
                            <?php
                            // Membuat array nama
                            $genre = $list['genre'];
                            $arraygenre = explode(", ", $genre);
                            // URL base untuk link (contoh: Google Search atau halaman profil dinamis)
                            $base_urlgenre = "../search.php?jenis=genre&data=";
                            ?>
                            <?php foreach ($arraygenre as $a): ?>
                                <a href="<?= $base_urlgenre . urlencode($a); ?>"><?= $a; ?></a>
                            <?php endforeach; ?>
                        </p>
                        <a href="watch.php/?id=<?= $list['id']; ?>" class="btn btn-primary">Nonton Film</a>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php else : ?>
<?php endif; ?>