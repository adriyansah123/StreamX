<?php
require '../functions.php'; // Pastikan untuk menyertakan koneksi ke database
$modal = $_POST['modal'];
$array = ['tambah', 'tambahmusic', 'tambahkomik'];
$array2 = ['ubah', 'hapus', 'tambahepisode'];
$array3 = ['daftaralbum', 'ubahmusic', 'hapusmusic'];
$array4 = ['daftarchapter', 'ubahkomik', 'hapuskomik'];
if (!in_array($modal, $array)) {
    if (in_array($modal, $array2)) {
        $id = $_POST['id'];
        $sql = "SELECT * FROM `movie` WHERE `id` = $id";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) {
            $movie = mysqli_fetch_assoc($result);
        } else {
            echo "Data Tidak Ditemukan";
        }
    } elseif (in_array($modal, $array3)) {
        $id = $_POST['id'];

        $sql = "SELECT * FROM `album` WHERE `artist_id` = $id";
        $album = mysqli_query($conn, $sql);

        $sql2 = "SELECT * FROM `artist` WHERE `id` = $id";
        $data = mysqli_query($conn, $sql2);
        $artist = mysqli_fetch_assoc($data);
    } elseif (in_array($modal, $array4)) {
        $id = $_POST['id'];

        $sql = "SELECT * FROM `chapter` WHERE `comic_id` = $id";
        $chapter = mysqli_query($conn, $sql);

        $sql2 = "SELECT * FROM `comic` WHERE `id` = $id";
        $data = mysqli_query($conn, $sql2);
        $comic = mysqli_fetch_assoc($data);
    }
}


if ($modal == "ubah"): ?>

    <form id="cariFilmForm">
        <div class="mb-3">
            <label for="keyword" class="form-label">Movie Title</label>
            <input type="text" class="form-control" id="keyword" placeholder="Enter movie title"
                value="<?= $movie['judul']; ?>">
        </div>
        <div class="mb-3">
            <label for="keyword" class="form-label">Imdb ID</label>
            <input type="text" class="form-control" id="imdb_id" placeholder="Enter movie title" value="">
        </div>
        <button type="submit" class="btn btn-primary w-100">Search</button>
    </form>

    <form action="save_movie.php" enctype="multipart/form-data" method="POST">
        <input type="hidden" name="id" value="<?= $id; ?>">
        <div id="hasilpencarian">
            <div class="form-group">
                <label for="">Jenis</label>
                <select name="jenis" id="jenis" class="form-control">
                    <option value="<?= $movie['jenis']; ?>"><?= $movie['jenis']; ?></option>
                    <option value="film">Film</option>
                    <option value="tv_series">Serial TV</option>
                </select>
            </div>
            <div class="form-group">
                <label for="">Judul Film</label>
                <input type="text" name="judul" id="judul" class="form-control" value="<?= $movie['judul']; ?>" required>
            </div>
            <div class="form-group">
                <label for="">Tahun</label>
                <input type="text" name="tahun" id="tahun" class="form-control" value="<?= $movie['tahun']; ?>" required>
            </div>
            <div class="form-group">
                <label for="">Pemeran</label>
                <input type="text" name="pemeran" id="pemeran" class="form-control" value="<?= $movie['pemeran']; ?>">
            </div>
            <div class="form-group">
                <label for="">Genre</label>
                <input type="text" name="genre" id="genre" class="form-control" value="<?= $movie['genre']; ?>" required>
            </div>
            <div class="form-group">
                <label for="">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control"><?= $movie['deskripsi']; ?></textarea>
            </div>
            <img src="<?= $movie['poster']; ?>" alt="" width="100px" height="200px">
            <div class="form-group">
                <label for="">poster</label>
                <input type="text" name="poster" id="poster" class="form-control" value="<?= $movie['poster']; ?>">
            </div>
            <div class="form-group">
                <label for="">file</label>
                <input type="file" name="file" id="file" class="form-control">
            </div>
            <div class="form-group">
                <label for="">subtitle</label>
                <input type="file" name="subtitle" id="subtitle" class="form-control">
            </div>
            <div class="mt-3">
                <button type="submit" name="ubahfilm" class="btn btn-primary">Ubah</button>
            </div>
        </div>
    </form>

    <script>
        $('#cariFilmForm').submit(function (e) {
            e.preventDefault(); // Prevent the form from refreshing the page

            let keyword = $('#keyword').val(); // Get the search query
            let apiKey = '<?= $apiOMDB; ?>'; // Ganti dengan API Key dari OMDb API
            let imdb_id = $('#imdb_id').val(); // Get the search query
            let url = '';
            if (keyword.trim() === '') {
                url = `https://www.omdbapi.com/?i=${imdb_id}&plot=full&apikey=${apiKey}`;
            } else {
                url = `https://www.omdbapi.com/?t=${keyword}&plot=full&apikey=${apiKey}`;
            }

            $.ajax({
                url: url,
                method: 'GET',
                success: function (data) {
                    if (data.Response === 'True') {
                        let movie = data; // Tidak perlu mengambil movie.Search, cukup data
                        const tanggalString = movie.Released; // format YYYY-MM-DD
                        const tanggal = new Date(tanggalString);

                        // Mendapatkan tahun
                        const tahun = tanggal.getFullYear();

                        let output = `
                                                                <div class="form-group">
                                                                    <label for="jenis">Jenis</label>
                                                                    <select name="jenis" id="jenis" class="form-control">
                                                                        <option value="film">Film</option>
                                                                        <option value="tv_series">Serial TV</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="judul">Judul Film</label>
                                                                    <input type="text" name="judul" id="judul" class="form-control" value="${movie.Title}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="tahun">Tahun</label>
                                                                    <input type="text" name="tahun" id="tahun" class="form-control" value="${tahun}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="">Pemeran</label>
                                                                    <input type="text" name="pemeran" id="pemeran" class="form-control" value="${movie.Actors}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="genre">Genre</label>
                                                                    <input type="text" name="genre" id="genre" class="form-control" value="${movie.Genre}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="deskripsi">Deskripsi</label>
                                                                    <textarea name="deskripsi" id="deskripsi" class="form-control">${movie.Plot}</textarea>
                                                                </div>
                                                                <img src="${movie.Poster}" alt="" width="100px" height="200px">
                                                                <div class="form-group">
                                                                    <label for="poster">Poster</label>
                                                                    <input type="text" name="poster" id="poster" class="form-control" value="${movie.Poster}">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="file">File</label>
                                                                    <input type="file" name="file" id="file" class="form-control">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="">subtitle</label>
                                                                    <input type="file" name="subtitle" id="subtitle" class="form-control">
                                                                </div>
                                                                <div class="mt-3">
                                                                    <button type="submit" name="ubahfilm" class="btn btn-primary">Ubah</button>
                                                                </div>
                                                            `;
                        $('#hasilpencarian').html(output); // Show the movie details in the form
                    } else {
                        $('#hasilpencarian').html(`<div class="alert alert-danger">${data.Error}</div>`);
                    }
                },
                error: function () {
                    $('#hasilpencarian').html('<div class="alert alert-danger">An error occurred while fetching data from OMDb API</div>');
                }
            });
        });
    </script>
<?php elseif ($modal == "hapus"): ?>
    <form action="save_movie.php" method="POST">
        <input type="hidden" name="id" value="<?= $id; ?>">
        <p>Apakah anda yakin ingin menghapus film "<b><?= $movie['judul']; ?></b>" ?</p>
        <button class="btn btn-danger mt-3" type="submit" name="hapusfilm">Hapus</button>
    </form>
<?php elseif ($modal == "tambahepisode"): ?>

    <style>
        #importepisode {
            display: none;
        }

        #tambahepisode {
            display: none;
        }
    </style>
    <div class="row mb-3">
        <div class="col">
            <button class="btn btn-primary aksiepisode" data-menu="tambahepisode">Tambah Episode</button>
            <button class="btn btn-primary aksiepisode" data-menu="importepisode">Tambah Episode Batch</button>
        </div>
    </div>
    <div id="tambahepisode">
        <form action="save_movie.php" enctype="multipart/form-data" method="POST" class="mb-3">
            <input type="hidden" name="id" value="<?= $id; ?>">
            <div id="movieResults">
                <div class="form-group">
                    <label for="">Season</label>
                    <input type="text" name="season" id="season" class="form-control" value="" required>
                </div>
                <div class="form-group">
                    <label for="">Episode</label>
                    <input type="text" name="episode" id="episode" class="form-control" value="" required>
                </div>
                <div class="form-group">
                    <label for="episode_type">Episode Type</label>
                    <select name="episode_type" id="episode_type" class="form-control" required>
                        <option value="Movie">Movie</option>
                        <option value="OVA">OVA</option>
                        <option value="Special">Special</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="">File</label>
                    <input type="file" name="file" id="file" class="form-control" value="" required>
                </div>
                <div class="mt-3">
                    <button type="submit" name="tambahepisode" class="btn btn-primary">Tambah</button>
                </div>
            </div>
        </form>
    </div>

    <div id="importepisode">
        <form action="save_movie.php" enctype="multipart/form-data" method="POST" class="mb-3">
            <input type="hidden" name="id" value="<?= $id; ?>">
            <div id="movieResults">
                <div class="form-group">
                    <label for="">File</label>
                    <input type="file" name="file" id="file" class="form-control" value="" required>
                </div>
                <div class="mt-3">
                    <button type="submit" name="importepisode" class="btn btn-primary">Import</button>
                </div>
            </div>
        </form>
    </div>

    <?php
    $episode_sql = "SELECT * FROM `episodes` WHERE `id_movie` = $id ORDER BY `season` ASC, `episode` ASC";
    $episode = mysqli_query($conn, $episode_sql);
    if (mysqli_num_rows($episode) === 0): ?>

        <h3 style="text-align: center;">Tidak Ada Episode</h3>

    <?php else: ?>
        <table class="table" id="example" style="width: 100%;">
            <thead>
                <tr>
                    <th>Season</th>
                    <th>Episode</th>
                    <th>menu</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($episode as $eps): ?>
                    <tr>
                        <td><?= $eps['season']; ?></td>
                        <td><?= $eps['episode']; ?></td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>
    <script>
        new DataTable('#example');
        $(document).ready(function () {
            $(document).off('click', '.aksiepisode');
            $(document).on('click', '.aksiepisode', function (e) {
                var menu = $(this).data('menu');

                // Hide both forms first
                $('#tambahepisode, #importepisode').hide();

                // Show the selected form
                $('#' + menu).show();
            });
        });
    </script>

<?php elseif ($modal == "tambah"): ?>
    <form id="cariFilmForm">
        <div class="mb-3">
            <label for="keyword" class="form-label">Movie Title</label>
            <input type="text" class="form-control" id="keyword" placeholder="Enter movie title" value="">
        </div>
        <div class="mb-3">
            <label for="keyword" class="form-label">Imdb ID</label>
            <input type="text" class="form-control" id="imdb_id" placeholder="Enter movie title" value="">
        </div>
        <button type="submit" class="btn btn-primary w-100">Search</button>
    </form>

    <form action="save_movie.php" enctype="multipart/form-data" method="POST">
        <input type="hidden" name="id" value="<?= $id; ?>">
        <div id="hasilpencarian">
            <div class="form-group">
                <label for="">Jenis</label>
                <select name="jenis" id="jenis" class="form-control">
                    <option value="film">Film</option>
                    <option value="tv_series">Serial TV</option>
                </select>
            </div>
            <div class="form-group">
                <label for="">Judul Film</label>
                <input type="text" name="judul" id="judul" class="form-control" value="" required>
            </div>
            <div class="form-group">
                <label for="">Tahun</label>
                <input type="text" name="tahun" id="tahun" class="form-control" value="" required>
            </div>
            <div class="form-group">
                <label for="">Pemeran</label>
                <input type="text" name="pemeran" id="pemeran" class="form-control" value="">
            </div>
            <div class="form-group">
                <label for="">Genre</label>
                <input type="text" name="genre" id="genre" class="form-control" value="" required>
            </div>
            <div class="form-group">
                <label for="">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label for="">poster</label>
                <input type="text" name="poster" id="poster" class="form-control" value="">
            </div>
            <div class="form-group">
                <label for="">file</label>
                <input type="file" name="file" id="file" class="form-control">
            </div>
            <div class="form-group">
                <label for="">subtitle</label>
                <input type="file" name="subtitle" id="subtitle" class="form-control">
            </div>
            <div class="mt-3">
                <button type="submit" name="tambahfilm" class="btn btn-primary">Tambah</button>
            </div>
        </div>
    </form>

    <script>
        $('#cariFilmForm').submit(function (e) {
            e.preventDefault(); // Prevent the form from refreshing the page

            let keyword = $('#keyword').val(); // Get the search query
            let apiKey = '<?= $apiOMDB; ?>'; // Ganti dengan API Key dari OMDb API
            let imdb_id = $('#imdb_id').val(); // Get the search query
            let url = '';
            if (keyword.trim() === '') {
                url = `https://www.omdbapi.com/?i=${imdb_id}&plot=full&apikey=${apiKey}`;
            } else {
                url = `https://www.omdbapi.com/?t=${keyword}&plot=full&apikey=${apiKey}`;
            }

            $.ajax({
                url: url,
                method: 'GET',
                success: function (data) {
                    if (data.Response === 'True') {
                        let movie = data; // Tidak perlu mengambil movie.Search, cukup data
                        const tanggalString = movie.Released; // format YYYY-MM-DD
                        const tanggal = new Date(tanggalString);

                        // Mendapatkan tahun
                        const tahun = tanggal.getFullYear();

                        let output = `
                                                                <div class="form-group">
                                                                    <label for="jenis">Jenis</label>
                                                                    <select name="jenis" id="jenis" class="form-control">
                                                                        <option value="film">Film</option>
                                                                        <option value="tv_series">Serial TV</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="judul">Judul Film</label>
                                                                    <input type="text" name="judul" id="judul" class="form-control" value="${movie.Title}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="tahun">Tahun</label>
                                                                    <input type="text" name="tahun" id="tahun" class="form-control" value="${tahun}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="">Pemeran</label>
                                                                    <input type="text" name="pemeran" id="pemeran" class="form-control" value="${movie.Actors}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="genre">Genre</label>
                                                                    <input type="text" name="genre" id="genre" class="form-control" value="${movie.Genre}" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="deskripsi">Deskripsi</label>
                                                                    <textarea name="deskripsi" id="deskripsi" class="form-control">${movie.Plot}</textarea>
                                                                </div>
                                                                <img src="${movie.Poster}" alt="" width="100px" height="200px">
                                                                <div class="form-group">
                                                                    <label for="poster">Poster</label>
                                                                    <input type="text" name="poster" id="poster" class="form-control" value="${movie.Poster}">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="file">File</label>
                                                                    <input type="file" name="file" id="file" class="form-control">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="">subtitle</label>
                                                                    <input type="file" name="subtitle" id="subtitle" class="form-control">
                                                                </div>
                                                                <div class="mt-3">
                                                                    <button type="submit" name="tambahfilm" class="btn btn-primary">Tambah</button>
                                                                </div>
                                                            `;
                        $('#hasilpencarian').html(output); // Show the movie details in the form
                    } else {
                        $('#hasilpencarian').html(`<div class="alert alert-danger">${data.Error}</div>`);
                    }
                },
                error: function () {
                    $('#hasilpencarian').html('<div class="alert alert-danger">An error occurred while fetching data from OMDb API</div>');
                }
            });
        });
    </script>
<?php elseif ($modal == "tambahmusic"): ?>
    <form action="save_music.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="">Nama Artis</label>
            <input type="text" name="name" id="name" class="form-control">
        </div>
        <div class="mb-3">
            <label for="">Foto</label>
            <input type="file" name="photo" id="photo" class="form-control">
        </div>
        <div class="mb-3">
            <label for="description">Deskripsi</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-outline-primary" name="tambahartist">Simpan</button>
    </form>
<?php elseif ($modal == "daftaralbum"): ?>
    <form class="mb-3" action="save_music.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="file">Upload File</label>
            <input type="file" name="files[]" class="form-control" multiple>
        </div>
        <button type="submit" class="form-control" name="tambahmusic">Tambah</button>
    </form>

    <div class="container my-4">
        <div class="row">
            <?php foreach ($album as $data): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header text-center bg-dark text-light">
                            <h4 class="card-title m-0"><?= htmlspecialchars($data['title']); ?></h4>
                        </div>
                        <div class="card-body text-center">
                            <img src="<?= htmlspecialchars($data['cover']); ?>" alt="Cover Album" class="img-fluid rounded mb-3"
                                style="width: 100%; height: 200px; object-fit: cover;">
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php
                            $album_id = $data['id'];
                            $song = mysqli_query($conn, "SELECT * FROM `song` WHERE `album_id` = $album_id");
                            $no = 1;
                            foreach ($song as $s): ?>
                                <li class="list-group-item">
                                    <?= $no++; ?>. <?= htmlspecialchars($s['title']); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php elseif ($modal == "ubahmusic"): ?>
    <form action="save_music.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $artist['id'] ?>">
        <div class="mb-3">
            <label for="">Nama Artis</label>
            <input type="text" name="name" id="name" value="<?= $artist['name'] ?>" class="form-control">
        </div>
        <div class="mb-3">
            <label for="">Foto</label>
            <input type="file" name="photo" id="photo" class="form-control">
        </div>
        <div class="mb-3">
            <label for="description">Deskripsi</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-outline-primary" name="ubahartist">Simpan</button>
    </form>
<?php elseif ($modal == "tambahkomik"): ?>
    <form action="save_comic.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="">Judul Komik</label>
            <input type="text" name="title" id="title" class="form-control">
        </div>
        <div class="mb-3">
            <label for="">Pembuat</label>
            <input type="text" name="author" id="author" class="form-control">
        </div>
        <div class="mb-3">
            <label for="">Genre</label>
            <input type="text" name="genre" id="genre" class="form-control">
        </div>
        <div class="mb-3">
            <label for="">Tahun</label>
            <input type="text" name="year" id="year" class="form-control">
        </div>
        <div class="mb-3">
            <label for="">Jenis</label>
            <select name="type" id="type" class="form-control">
                <option value="Komik">Komik</option>
                <option value="Manga">Manga</option>
                <option value="Manhwa">Manhwa</option>
                <option value="Manhua">Manhua</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="">Cover</label>
            <input type="file" name="cover" id="cover" class="form-control">
        </div>
        <button type="submit" class="btn btn-outline-primary" name="tambahkomik">Simpan</button>
    </form>
<?php elseif ($modal == "ubahkomik"): ?>
    <form action="save_comic.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $comic['id']; ?>">
        <div class="mb-3">
            <label for="">Judul Komik</label>
            <input type="text" name="title" value="<?= $comic['title']; ?>" id="title" class="form-control">
        </div>
        <div class="mb-3">
            <label for="">Pembuat</label>
            <input type="text" name="author" value="<?= $comic['author']; ?>" id="author" class="form-control">
        </div>
        <div class="mb-3">
            <label for="">Genre</label>
            <input type="text" name="genre" value="<?= $comic['genre']; ?>" id="genre" class="form-control">
        </div>
        <div class="mb-3">
            <label for="">Tahun</label>
            <input type="text" name="year" value="<?= $comic['year']; ?>" id="year" class="form-control">
        </div>
        <div class="mb-3">
            <label for="">Jenis</label>
            <select name="type" id="type" class="form-control">
                <option value="<?= $comic['type']; ?>"><?= $comic['type']; ?></option>
                <option value="Komik">Komik</option>
                <option value="Manga">Manga</option>
                <option value="Manhwa">Manhwa</option>
                <option value="Manhua">Manhua</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="">Cover</label>
            <input type="file" name="cover" id="cover" class="form-control">
        </div>
        <button type="submit" class="btn btn-outline-primary" name="ubahkomik">Simpan</button>
    </form>
<?php elseif ($modal == "daftarchapter"): ?>
    <form class="mb-3" action="save_comic.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id; ?>">
        <div class="mb-3">
            <label for="file">Upload File</label>
            <input type="file" name="file" class="form-control" multiple>
        </div>
        <button type="submit" class="form-control" name="tambahchapter">Tambah</button>
    </form>

    <div class="container my-4">
        <table class="table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Title</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($chapter as $data): ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= $data['title']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>