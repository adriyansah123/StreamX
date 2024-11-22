<?php
require 'functions.php';
$id = $_GET['id'];
$sql = "SELECT * FROM `comic` WHERE `id` = $id";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) === 1) {
    $comic = mysqli_fetch_assoc($result);
    $title = $comic['title'];
} else {
    echo 'Data Tidak Ditemukan';
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
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
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
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="exampleModal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <?php include 'navbar.php'; ?>
        <div class="video-card">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center"><?= $comic['title'] ?></h3>
                    <!-- comic Details -->
                    <div class="row">
                        <div class="col-md-4">
                            <img src="admin/<?= $comic['cover'] ?>" alt="<?= $comic['title'] ?> Poster" class="poster">
                        </div>
                        <div class="col-md-8">
                            <table class="table">
                                <tr>
                                    <td>Type</td>
                                    <td>
                                        <a
                                            href="search.php?jenis=genre&data=<?= $comic['type']; ?>"><?= $comic['type']; ?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Genre</td>
                                    <td>
                                        <?php foreach (explode(", ", $comic['genre']) as $a): ?>
                                            <a href="search.php?jenis=genre&data=<?= urlencode($a) ?>"><?= $a ?></a>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Author</td>
                                    <td>
                                        <?php foreach (explode(", ", $comic['author']) as $b): ?>
                                            <a href="search.php?jenis=pemeran&data=<?= urlencode($b) ?>"><?= $b ?></a>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Year</td>
                                    <td>
                                        <a
                                            href="search.php?jenis=tahun&data=<?= $comic['year'] ?>"><?= $comic['year'] ?></a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Episode List for TV Series -->
            <div class="episode-list mt-4">
                <h4>Chapter List</h4>
                <?php
                $sql = "SELECT * FROM `chapter` 
        WHERE `comic_id` = $id 
        ORDER BY CAST(SUBSTRING_INDEX(`title`, ' ', -1) AS UNSIGNED) ASC";
                $chapter = mysqli_query($conn, $sql);
                if (mysqli_num_rows($chapter) === 0): ?>
                    <h5 style="text-align: center;">No Chapters Available</h5>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($chapter as $chp): ?>
                            <li class="list-group-item">
                                <?= $chp['title'] ?>
                                <a data-id="<?= $id ?>" data-location="<?= $chp['location'] ?>"
                                    data-type="<?= $comic['type']; ?>" data-title="<?= $comic['title']; ?>"
                                    class="seriesepisode float-end btn btn-sm btn-primary">Baca</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function () {
            $(document).off('click', '.seriesepisode');
            $(document).on('click', '.seriesepisode', function (e) {
                var id = $(this).data('id');
                var location = $(this).data('location');
                var type = $(this).data('type');
                var title = $(this).data('title');

                $.ajax({
                    url: 'read.php',
                    type: 'GET',
                    data: {
                        id: id,
                        location: location,
                        type: type,
                        title: title,
                    },
                    success: function (response) {
                        // Add response in Modal body
                        $('#exampleModal-body').html(response);
                        $('#exampleModalLabel').html(title);
                        // Display Modal
                        $('#exampleModal').modal('show');
                    }
                });
            });
        });
    </script>
</body>

</html>