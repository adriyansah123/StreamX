<?php
require '../functions.php'; // Pastikan untuk menyertakan koneksi ke database
global $conn;
$daftarfilm = mysqli_query($conn, "SELECT * FROM `movie` ORDER BY `judul` ASC");
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</head>

<body>
    <!-- Modal -->
    <div class="modal fade" id="modal-menufilm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modaltitle-menufilm">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalbody-menufilm">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <?php include 'navbar.php'; ?>

    <div class="container" style="margin-top: 100px;">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12">
                <div class="movie-table">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn btn-sm btn-outline-primary modalfilm" data-modal="tambah"
                                data-judul="tambah film">Tambah</button>
                        </div>
                        <div class="col">
                            <h2 class="text-center mb-4">Daftar Film</h2>
                        </div>
                        <div class="col"></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="example2">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul Film</th>
                                    <th>Pemeran</th>
                                    <th>Genre</th>
                                    <th>Jenis</th>
                                    <th>Episode</th>
                                    <th>Tahun</th>
                                    <th>Menu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($daftarfilm as $list): ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td><?= $list['judul']; ?></td>
                                        <td><?= $list['pemeran']; ?></td>
                                        <td><?= $list['genre']; ?></td>
                                        <td><?= $list['jenis']; ?></td>
                                        <td>
                                            <?php
                                            $idfilm = $list['id'];
                                            $episodelist = mysqli_query($conn, "SELECT * FROM `episodes` WHERE `id_movie`=$idfilm");
                                            $jumlahepisode = mysqli_num_rows($episodelist);
                                            echo $jumlahepisode;
                                            ?>
                                        </td>
                                        <td><?= $list['tahun']; ?></td>
                                        <td>
                                            <?php if ($list['jenis'] == "tv_series"): ?>
                                                <button type="button" class="btn btn-sm btn-outline-warning modalfilm"
                                                    data-id="<?= $list['id']; ?>" data-modal="tambahepisode"
                                                    data-judul="Episode <?= $list['judul']; ?>">Episode</button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary modalfilm"
                                                data-id="<?= $list["id"]; ?>" data-modal="ubah"
                                                data-judul="Ubah <?= $list['judul']; ?>">Ubah</button>
                                            <button type="button" class="btn btn-sm btn-outline-danger modalfilm"
                                                data-id="<?= $list["id"]; ?>" data-modal="hapus"
                                                data-judul="Hapus <?= $list['judul']; ?>">Hapus</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; <?= date('Y'); ?> StreamX. All rights reserved.</p>
    </footer>

    <script src="../js/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <script>
        new DataTable('#example2');
        $(document).ready(function () {
            $(document).off('click', '.modalfilm');
            $(document).on('click', '.modalfilm', function(e) {
                var id = $(this).data('id');
                var modal = $(this).data('modal');
                var judul = $(this).data('judul');

                // AJAX request
                $.ajax({
                    url: 'aksimodal.php',
                    type: 'post',
                    data: {
                        id: id,
                        modal: modal,
                        judul: judul
                    },
                    success: function (response) {
                        // Add response in Modal body
                        $('#modalbody-menufilm').html(response);
                        $('#modaltitle-menufilm').html(judul);
                        // Display Modal
                        $('#modal-menufilm').modal('show');
                    }
                });
            });
        });
    </script>
</body>

</html>