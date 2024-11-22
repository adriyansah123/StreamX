<?php
require 'functions.php';
global $conn;

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM `artist` WHERE `id` = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$artist = $stmt->get_result();
$artis = $artist->fetch_assoc();

$stmt = $conn->prepare("SELECT * FROM `album` WHERE `artist_id` = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$album = $stmt->get_result();

if (!$artis) {
    die('Artis tidak ditemukan.');
}

if ($album->num_rows > 0) {
    foreach ($album as $data) {
        // Render HTML album
    }
} else {
    echo "<p>Album tidak ditemukan.</p>";
}

?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($artis['name']); ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <style>
        .list-group-flush {
            max-height: 100%;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .card {
            height: 100%;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <?php include 'navbar.php'; ?>

        <div id="halamanweb">
            <div class="container py-5">
                <!-- Informasi Artis -->
                <div class="text-center mb-5">
                    <h1 class="display-4"><?= htmlspecialchars($artis['name']); ?></h1>
                    <img src="admin/<?= htmlspecialchars($artis['photo']); ?>" alt="Foto Artis"
                        class="img-fluid rounded-circle shadow" style="width: 200px; height: 200px; object-fit: cover;">
                </div>

                <!-- Daftar Album -->
                <div class="row">
                    <?php foreach ($album as $data): ?>
                        <div class="col-md-12 mb-4">
                            <div class="card shadow-sm">
                                <div class="row g-0 align-items-stretch">
                                    <!-- Cover Album di sebelah kiri -->
                                    <div class="col-md-4">
                                        <div class="text-center bg-dark h-100 d-flex flex-column">
                                            <img src="admin/<?= htmlspecialchars($data['cover']); ?>" alt="Cover Album"
                                                class="img-fluid rounded mb-3"
                                                style="max-height: 100%; width: 100%; height: auto; object-fit: cover;">
                                            <div class="card-header text-center bg-dark text-light">
                                                <h5 class="card-title m-0"><?= htmlspecialchars($data['title']); ?></h5>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Daftar Lagu di sebelah kanan -->
                                    <div class="col-md-8">
                                        <ul class="list-group list-group-flush">
                                            <?php
                                            $album_id = $data['id'];
                                            $song = mysqli_query($conn, "SELECT * FROM `song` WHERE `album_id` = $album_id");
                                            $no = 1;
                                            foreach ($song as $s): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span><?= $no++; ?>. <?= htmlspecialchars($s['title']); ?></span>
                                                    <div>
                                                        <audio controls style="width: 180px;">
                                                            <source src="stream.php?song=<?= htmlspecialchars($s['id']); ?>"
                                                                type="audio/mpeg">
                                                            Browser Anda tidak mendukung pemutar audio.
                                                        </audio>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>

</html>