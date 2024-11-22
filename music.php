<?php
require 'functions.php'; // Koneksi ke database
global $conn;

// API Key Last.fm
$apiKey = 'a33a0272b83b0ad501da184d558778f1';

$keyword = $_GET['keyword'] ?? null;
if (is_null($keyword)) {
    $daftarmusik = mysqli_query($conn, "SELECT * FROM `artist` ORDER BY `name` ASC");
} else {
    $daftarmusik = mysqli_query($conn, "SELECT * FROM `artist` WHERE `name` LIKE '%$keyword%' ORDER BY `name` ASC");
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Musik</title>
    <link href="css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</head>

<body>
    <div class="container-fluid">
        <?php include 'navbar.php'; ?>

        <div id="halamanweb">
            <div style="margin-top: 100px;">
                <div class="row justify-content-center">
                    <?php foreach ($daftarmusik as $list): ?>
                        <div class="col-lg-2 col-sm-6 mb-3">
                            <div class="card" style="height: 300px; position: relative;">
                                <div class="ribbon"></div>
                                <img src="admin/<?= $list['photo'] ?>" class="card-img-top" alt="Foto Artis" width="" height="200px">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $list['name']; ?></h5>
                                    <a class="moviepage btn btn-primary" href="album.php?id=<?= $list['id']; ?>" data-id="<?= $list['id']; ?>">Daftar Album</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function () {
            $("#formpencarian").submit(function (e) {
                e.preventDefault(); // Mencegah pengiriman form secara default

                var keyword = $("#datakeyword").val();
                var halaman = $("#datatype").val();

                if (!keyword.trim()) {
                    alert("Keyword tidak boleh kosong!");
                    return; // Hentikan proses jika keyword kosong
                }

                // Kirim data menggunakan AJAX
                $.ajax({
                    url: halaman,
                    type: "GET",
                    data: { keyword: keyword }, // Kirim parameter keyword ke server
                    beforeSend: function () {
                        console.log("Mengirim pencarian...");
                    },
                    success: function (response) {
                        // Jika berhasil, arahkan ke halaman sesuai dengan value datatype
                        window.location.href = halaman + "?keyword=" + encodeURIComponent(keyword);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", status, error);
                        alert("Terjadi kesalahan saat memuat halaman. Silakan coba lagi.");
                    }
                });
            });
        });
    </script>
</body>

</html>
