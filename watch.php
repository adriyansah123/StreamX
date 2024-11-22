<?php
require 'functions.php';
$id = $_GET['id'];
$season_watch = $_GET['season'] ?? null;
$episode_watch = $_GET['episode'] ?? null;
$title = '';
if ($episode_watch != null) {
    $episode_watch = urldecode($_GET['episode']);
}
$sql = "SELECT * FROM `movie` WHERE `id` = $id";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) === 1) {
    $movie = mysqli_fetch_assoc($result);
    $title = $movie['judul'];
} else {
    echo "Data Tidak Ditemukan";
}
if (($season_watch != null) && ($episode_watch != null)) {
    $episode_movie = $conn->prepare("SELECT `episodes`.`id` AS `id`, `episodes`.`id_movie`, `episodes`.`season`, `episodes`.`episode`, `episodes`.`file`, `episodes`.`subtitle`, `movie`.`judul` FROM `episodes` LEFT JOIN `movie` ON `episodes`.`id_movie` = `movie`.`id` WHERE `id_movie` = ? AND `season` = ? AND `episode` = ?");
    $episode_movie->bind_param("iss", $id, $season_watch, $episode_watch);
    $episode_movie->execute();

    // Mendapatkan hasil dari query
    $result = $episode_movie->get_result();
    $episode_streaming = $result->fetch_assoc();

    $judul = $episode_streaming['judul'];
    $season = $episode_streaming['season'];
    $episode = $episode_streaming['episode'];
    $file = $episode_streaming['file'];
    $title = "$judul - Season $season - Episode $episode";
    $episode_id = $episode_streaming['id'];

    $id_prev = $episode_id - 1;
    $episode_sebelumnya = mysqli_query($conn, "SELECT `episodes`.`id` AS `id`, `episodes`.`id_movie`, `episodes`.`season`, `episodes`.`episode`, `episodes`.`file`, `movie`.`judul` FROM `episodes` LEFT JOIN `movie` ON `episodes`.`id_movie` = `movie`.`id` WHERE `episodes`.`id` = $id_prev");
    $episode_sebelum = mysqli_fetch_assoc($episode_sebelumnya);
    $episode_prev = $episode_sebelum['episode'] ?? null;
    $season_prev = $episode_sebelum['season'] ?? null;

    $id_next = $episode_id + 1;
    $episode_sesudahnya = mysqli_query($conn, "SELECT `episodes`.`id` AS `id`, `episodes`.`id_movie`, `episodes`.`season`, `episodes`.`episode`, `episodes`.`file`, `movie`.`judul` FROM `episodes` LEFT JOIN `movie` ON `episodes`.`id_movie` = `movie`.`id` WHERE `episodes`.`id` = $id_next");
    $episode_sesudah = mysqli_fetch_assoc($episode_sesudahnya);
    $episode_next = $episode_sesudah['episode'] ?? null;
    $season_next = $episode_sesudah['season'] ?? null;
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .curved-navbar {
            background-color: #333;
            border-radius: 0 0 25px 25px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .navbar-brand {
            font-size: 1.5rem;
            color: white;
            font-weight: bold;
        }

        .navbar-nav .nav-link {
            color: white;
        }

        .video-card {
            margin-top: 100px;
        }

        .card-body h3 {
            margin-bottom: 10px;
            text-align: center;
        }

        .video-player {
            position: relative;
            padding-bottom: 50%;
            height: 0;
            overflow: hidden;
        }

        .video-player video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .poster {
            width: 100%;
            max-width: 300px;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-top: 40px;
        }

        .description {
            margin-top: 20px;
        }

        h2 {
            text-align: center;
            margin-top: 100px;
        }

        .episode-list ul {
            list-style: none;
            padding: 0;
        }

        .episode-list li {
            padding: 10px;
            background-color: #333;
            margin-bottom: 10px;
            border-radius: 8px;
        }

        .episode-list a {
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <?php include 'navbar.php'; ?>
        <div class="video-card">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center"><?= $movie['judul'] ?></h3>
                    <div class="card video-player mb-3">
                        <video id="player" controls>
                            <?php if ($movie['jenis'] == 'film'): ?>
                                <source src="admin/<?= $movie['file']; ?>" type="video/mp4" />
                                <track label="English" kind="subtitles" srclang="en"
                                    src="admin/<?= $movie['subtitle']; ?>" default />
                            <?php elseif ($movie['jenis'] == 'tv_series'): ?>
                                <source src="admin/<?= $episode_streaming['file']; ?>" type="video/mp4" />
                                <track label="English" kind="subtitles" srclang="en"
                                    src="admin/<?= $episode_streaming['subtitle']; ?>" default />
                            <?php endif; ?>
                        </video>
                    </div>
                    <?php if ($movie['jenis'] == 'tv_series'): ?>
                        <?php if (($season_watch != null) && ($episode_watch != null)): ?>
                            <div class="d-flex mb-3 mt-3">
                                <?php if ($episode_sebelum['id_movie'] == $id): ?>
                                    <a href="watch.php?id=<?= $id; ?>&season=<?= $season_prev; ?>&episode=<?= $episode_prev; ?>"
                                        class="btn btn-primary flex-fill">Season <?= $season_prev; ?> : Episode
                                        <?= $episode_prev; ?></a>
                                <?php endif; ?>
                                <a href="watch.php?id=<?= $id; ?>&season=<?= $season; ?>&episode=<?= $episode; ?>"
                                    class="btn btn-primary flex-fill">Season <?= $season; ?> : Episode <?= $episode; ?></a>
                                <?php if ($episode_sesudah['id_movie'] == $id): ?>
                                    <a href="watch.php?id=<?= $id; ?>&season=<?= $season_next; ?>&episode=<?= $episode_next; ?>"
                                        class="btn btn-primary flex-fill">Season <?= $season_next; ?> : Episode
                                        <?= $episode_next; ?></a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Movie Details -->
                    <div class="row">
                        <div class="col-md-4">
                            <img src="admin/<?= $movie['poster']; ?>" alt="<?= $movie['judul']; ?> Poster"
                                class="poster">
                        </div>
                        <div class="col-md-8">
                            <p><strong>Genre:</strong>
                                <?php foreach (explode(", ", $movie['genre']) as $a): ?>
                                    <a href="search.php?jenis=genre&data=<?= urlencode($a); ?>"><?= $a; ?></a>
                                <?php endforeach; ?>
                            </p>
                            <p><strong>Actors:</strong>
                                <?php foreach (explode(", ", $movie['pemeran']) as $b): ?>
                                    <a href="search.php?jenis=pemeran&data=<?= urlencode($b); ?>"><?= $b; ?></a>
                                <?php endforeach; ?>
                            </p>
                            <p><strong>Year:</strong> <a
                                    href="search.php?jenis=tahun&data=<?= $movie['tahun'] ?>"><?= $movie['tahun'] ?></a>
                            </p>
                            <p><strong>Description:</strong> <?= $movie['deskripsi'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Episode List for TV Series -->
            <?php if ($movie['jenis'] == "tv_series"): ?>
                <div class="episode-list mt-4">
                    <h4>Episode List</h4>
                    <?php
                    $episode_sql = "SELECT * FROM `episodes` WHERE `id_movie` = $id ORDER BY `season` ASC, `episode` ASC";
                    $episode = mysqli_query($conn, $episode_sql);
                    if (mysqli_num_rows($episode) === 0): ?>
                        <h5 style="text-align: center;">No Episodes Available</h5>
                    <?php else: ?>
                        <table class="table" id="example">
                            <thead>
                                <tr>
                                    <td>Episode</td>
                                    <td>Nonton</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($episode as $eps): ?>
                                    <tr>
                                        <td>
                                            Season <?= $eps['season']; ?> : Episode <?= $eps['episode']; ?>
                                        </td>
                                        <td>
                                            <a href="watch.php?id=<?= $id; ?>&season=<?= $eps['season']; ?>&episode=<?= $eps['episode']; ?>"
                                                data-id="<?= $id; ?>" data-season="<?= $eps['season']; ?>"
                                                data-episode="<?= $eps['episode']; ?>"
                                                class="seriesepisode btn btn-sm btn-primary">Watch</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <script>
        new DataTable('#example');
        $(document).ready(function () {
            const video = document.getElementById('player');
            let audioContext, gainNode, source;

            if (!video) {
                console.error("Elemen video dengan ID 'player' tidak ditemukan.");
                return; // Hentikan eksekusi jika video tidak ditemukan
            }

            // Inisialisasi Web Audio API hanya sekali
            function initializeAudio() {
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
                gainNode = audioContext.createGain();
                source = audioContext.createMediaElementSource(video);
                source.connect(gainNode);
                gainNode.connect(audioContext.destination);
            }

            // Fungsi untuk memundurkan waktu video 10 detik
            function skipBackward() {
                video.currentTime = Math.max(video.currentTime - 10, 0);
            }

            // Fungsi untuk memajukan waktu video 10 detik
            function skipForward() {
                video.currentTime = Math.min(video.currentTime + 10, video.duration);
            }

            // Fungsi untuk menjeda atau memutar video
            function togglePlayPause() {
                if (video.paused) {
                    video.play();
                } else {
                    video.pause();
                }
            }

            // Fungsi untuk memperbesar layar (fullscreen)
            function toggleFullscreen() {
                if (!document.fullscreenElement) {
                    video.requestFullscreen().catch(err => {
                        console.error(`Gagal masuk ke layar penuh: ${err.message}`);
                    });
                } else {
                    document.exitFullscreen();
                }
            }

            // Fungsi untuk mengatur gain (volume)
            function setVolumeLevel(level) {
                if (!audioContext) {
                    initializeAudio(); // Inisialisasi jika belum dilakukan
                }
                gainNode.gain.value = level; // Set volume sesuai level
            }

            // Fungsi untuk mengubah volume
            function changeVolume(amount) {
                if (!audioContext) {
                    initializeAudio(); // Inisialisasi jika belum dilakukan
                }
                gainNode.gain.value = Math.min(Math.max(gainNode.gain.value + amount, 0), 3.0); // Pembatasan volume
            }

            // Event keydown untuk kontrol keyboard
            document.addEventListener('keydown', (event) => {
                switch (event.code) {
                    case 'ArrowRight': // Panah kanan untuk maju 10 detik
                        skipForward();
                        break;
                    case 'ArrowLeft': // Panah kiri untuk mundur 10 detik
                        skipBackward();
                        break;
                    case 'Space': // Spasi untuk toggle play/pause
                        event.preventDefault(); // Mencegah halaman bergulir saat menekan spasi
                        togglePlayPause();
                        break;
                    case 'KeyF': // Tombol 'F' untuk masuk/keluar layar penuh
                        toggleFullscreen();
                        break;
                    case 'ArrowUp': // Panah atas untuk menaikkan volume
                        changeVolume(0.1);
                        break;
                    case 'ArrowDown': // Panah bawah untuk menurunkan volume
                        changeVolume(-0.1);
                        break;
                }
            });

            // Inisialisasi volume pada 100% saat halaman dimuat
            setVolumeLevel(1.0);

        });
    </script>

</body>

</html>