<?php
require '../functions.php'; // Pastikan untuk menyertakan koneksi ke database
global $conn;
$daftarartist = mysqli_query($conn, "SELECT * FROM `artist` ORDER BY `name` ASC");
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
    <div class="modal fade" id="modal-menuMusic" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modaltitle-menuMusic">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalbody-menuMusic">
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
                            <button type="button" class="btn btn-sm btn-outline-primary modalMusic" data-modal="tambahmusic"
                                data-judul="tambah Artist">Tambah</button>
                        </div>
                        <div class="col">
                            <h2 class="text-center mb-4">Daftar Film</h2>
                        </div>
                        <div class="col"></div>
                    </div>
                    <table class="table" id="example">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Artist</th>
                                <th>Deskripsi</th>
                                <th>Album</th>
                                <th>Menu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($daftarartist as $list): ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= $list['name']; ?></td>
                                    <td><?= $list['description']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-warning modalMusic"
                                            data-id="<?= $list['id']; ?>" data-modal="daftaralbum">List</button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary modalMusic"
                                            data-id="<?= $list["id"]; ?>" data-modal="ubahmusic">Ubah</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger modalMusic"
                                            data-id="<?= $list["id"]; ?>" data-modal="hapusmusic">Hapus</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
        new DataTable('#example');
        $(document).ready(function () {
            $(document).off('click', '.modalMusic');
            $(document).on('click', '.modalMusic', function(e) {
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
                        $('#modalbody-menuMusic').html(response);
                        $('#modaltitle-menuMusic').html(judul);
                        // Display Modal
                        $('#modal-menuMusic').modal('show');
                    }
                });
            });
        });
    </script>
</body>

</html>