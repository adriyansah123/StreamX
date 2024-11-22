<?php
require '../functions.php'; // Pastikan untuk menyertakan koneksi ke database

if (isset($_POST['tambahfilm'])) {
    // Ambil data dari form
    $jenis = $_POST['jenis'];
    $judul = $_POST['judul'];
    $tahun = $_POST['tahun'];
    $genre = $_POST['genre'];
    $pemeran = $_POST['pemeran'];
    $deskripsi = $_POST['deskripsi'];
    $posterUrl = $_POST['poster']; // Ambil URL poster dari input
    $posterFile = $_POST['fileposter']; // Ambil URL poster dari input
    
    // Validasi input
    if (empty($judul) || empty($tahun) || empty($genre)) {
        echo "Judul, tahun, dan genre harus diisi.";
        exit;
    }

    // Tentukan direktori penyimpanan untuk poster
    $folderPath = "poster/$tahun/";
    $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $judul) . " ($tahun)"; // Sanitasi nama file
    $fileExtension = pathinfo($posterUrl, PATHINFO_EXTENSION); // Dapatkan ekstensi gambar
    $filePath = $folderPath . $fileName . '.' . $fileExtension; // Tentukan path lengkap

    // Buat direktori jika belum ada
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true); // Buat folder dengan izin yang sesuai
    }

    if (is_null($posterUrl)) {
        $temp = explode('.', $_FILES['fileposter']['name']);
        $posterExtension = end($temp);
        $posterData = $_FILES['fileposter']['tmp_name'];
        $filePath = $folderPath . $fileName . '.' . $posterExtension; // Tentukan path lengkap
        move_uploaded_file($posterData, $filePath);
    } else {
        // Unduh dan simpan gambar dari URL
        if (file_put_contents($filePath, file_get_contents($posterUrl))) {
            echo "Poster berhasil diunduh dan disimpan.";
        } else {
            echo "Gagal menyimpan poster.";
            exit;
        }
    }


    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];

        // Mendapatkan ekstensi file
        $temp = explode('.', $fileName);
        $fileExtension = end($temp);
        $newFileName = preg_replace('/[^a-zA-Z0-9_-]/', ' ', $judul) . " ($tahun).$fileExtension"; // Sanitasi nama file

        // Tentukan direktori penyimpanan
        $uploadFileDir = "movie/{$tahun}/";
        if (!file_exists($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true); // Buat folder dengan izin yang sesuai
        }
        $dest_path = $uploadFileDir . $newFileName;

        // Pindahkan file yang diupload
        if (move_uploaded_file($fileTmpPath, $dest_path)) {

        } else {
            echo "Error uploading file.";
            exit; // Hentikan eksekusi jika ada kesalahan
        }
    }

    if (isset($_FILES['subtitle']) && $_FILES['subtitle']['error'] === UPLOAD_ERR_OK) {
        $subTmpPath = $_FILES['subtitle']['tmp_name'];
        $subName = $_FILES['subtitle']['name'];
        $subSize = $_FILES['subtitle']['size'];
        $subType = $_FILES['subtitle']['type'];

        // Mendapatkan ekstensi file
        $temp = explode('.', $subName);
        $subExtension = end($temp);
        $newFileName = preg_replace('/[^a-zA-Z0-9_-]/', ' ', $judul) . " ($tahun).$subExtension"; // Sanitasi nama file

        // Tentukan direktori penyimpanan
        $uploadFileDir = "movie/{$tahun}/";
        if (!file_exists($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true); // Buat folder dengan izin yang sesuai
        }
        $sub_path = $uploadFileDir . $newFileName;

        // Pindahkan file yang diupload
        if (move_uploaded_file($subTmpPath, $sub_path)) {

        } else {
            echo "Error uploading file.";
            exit; // Hentikan eksekusi jika ada kesalahan
        }
    }

    // Query untuk menyimpan data film
    $sql = "INSERT INTO `movie` (`jenis`, `pemeran`, `judul`, `tahun`, `genre`, `deskripsi`, `poster`, `file`, `subtitle`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $jenis, $pemeran, $judul, $tahun, $genre, $deskripsi, $filePath, $dest_path, $sub_path); // Gunakan path gambar yang baru diunduh

    if ($stmt->execute()) {
        echo "
        <script>
            alert('Film berhasil ditambahkan!');
        </script>
        ";
        // Redirect atau tampilkan pesan sukses
        header("Location: list.php");
    } else {
        echo "Terjadi kesalahan saat menambahkan film: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} elseif (isset($_POST['ubahfilm'])) {
    // Ambil data dari form
    $id = $_POST['id'];
    $jenis = $_POST['jenis'];
    $judul = $_POST['judul'];
    $tahun = $_POST['tahun'];
    $genre = $_POST['genre'];
    $pemeran = $_POST['pemeran'];
    $deskripsi = $_POST['deskripsi'];
    $posterUrl = $_POST['poster']; // Ambil URL poster dari input

    $movie = mysqli_query($conn, "SELECT * FROM `movie` WHERE `id` = $id");

    if (mysqli_num_rows($movie) === 1) {
        $movie = mysqli_fetch_assoc($movie);
        $dest_path = $movie['file'];
        // Tentukan direktori penyimpanan untuk poster
        $folderPath = "poster/$tahun/";
        $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $judul) . " ($tahun)"; // Sanitasi nama file
        $fileExtension = pathinfo($posterUrl, PATHINFO_EXTENSION); // Dapatkan ekstensi gambar
        $filePath = $folderPath . $fileName . '.' . $fileExtension; // Tentukan path lengkap

        // Buat direktori jika belum ada
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true); // Buat folder dengan izin yang sesuai
        }

        // Unduh dan simpan gambar dari URL
        if (file_put_contents($filePath, file_get_contents($posterUrl))) {
            echo "Poster berhasil diunduh dan disimpan.";
        } else {
            echo "Gagal menyimpan poster.";
            exit;
        }

        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $fileSize = $_FILES['file']['size'];
            $fileType = $_FILES['file']['type'];

            // Mendapatkan ekstensi file
            $temp = explode('.', $fileName);
            $fileExtension = end($temp);
            $newFileName = preg_replace('/[^a-zA-Z0-9_-]/', ' ', $judul) . " ($tahun).$fileExtension"; // Sanitasi nama file

            // Tentukan direktori penyimpanan
            $uploadFileDir = "movie/{$tahun}/";
            if (!file_exists($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true); // Buat folder dengan izin yang sesuai
            }
            $dest_path = $uploadFileDir . $newFileName;

            // Pindahkan file yang diupload
            if (move_uploaded_file($fileTmpPath, $dest_path)) {

            } else {
                echo "Error uploading file.";
                exit; // Hentikan eksekusi jika ada kesalahan
            }
        }

        if (isset($_FILES['subtitle']) && $_FILES['subtitle']['error'] === UPLOAD_ERR_OK) {
            $subTmpPath = $_FILES['subtitle']['tmp_name'];
            $subName = $_FILES['subtitle']['name'];
            $subSize = $_FILES['subtitle']['size'];
            $subType = $_FILES['subtitle']['type'];
    
            // Mendapatkan ekstensi file
            $temp = explode('.', $subName);
            $subExtension = end($temp);
            $newFileName = preg_replace('/[^a-zA-Z0-9_-]/', ' ', $judul) . " ($tahun).$subExtension"; // Sanitasi nama file
    
            // Tentukan direktori penyimpanan
            $uploadFileDir = "movie/{$tahun}/";
            if (!file_exists($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true); // Buat folder dengan izin yang sesuai
            }
            $sub_path = $uploadFileDir . $newFileName;
    
            // Pindahkan file yang diupload
            if (move_uploaded_file($subTmpPath, $sub_path)) {
    
            } else {
                echo "Error uploading file.";
                exit; // Hentikan eksekusi jika ada kesalahan
            }
        }

        // Query untuk menyimpan data film
        $sql = "UPDATE `movie` SET `jenis`=?, `judul`=?, `pemeran`=?, `tahun`=?, `genre`=?, `deskripsi`=?, `poster`=?, `file`=?, `subtitle`=? WHERE id=$id";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssss", $jenis, $judul, $pemeran, $tahun, $genre, $deskripsi, $filePath, $dest_path, $sub_path); // Gunakan path gambar yang baru diunduh
        
        if ($stmt->execute()) {
            echo "
            <script>
                alert('Film berhasil ditambahkan!');
            </script>
            ";
            // Redirect atau tampilkan pesan sukses
            header("Location: list.php");
        } else {
            echo "
            <script>
                alert('Terjadi kesalahan saat menambahkan film: $stmt->error');
            </script>
            ";
            header("Location: list.php");
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "
            <script>
                alert('Data Tidak Ditemukan');
            </script>
            ";
        echo "";
        header("Location: list.php");
    }
} elseif (isset($_POST['tambahepisode'])) {
    // Ambil data dari form
    $id = $_POST['id'];
    $season = $_POST['season'];
    $episode = $_POST['episode'];
    $episode_type = $_POST['episode_type'];

    $movie = mysqli_query($conn, "SELECT * FROM `movie` WHERE `id` = $id");

    if (mysqli_num_rows($movie) === 1) {
        $movie = mysqli_fetch_assoc($movie);

        $judul = preg_replace('/[^a-zA-Z0-9_-]/', '_', $movie['judul']);
        $tahun = $movie['tahun'];

        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];

        // Mendapatkan ekstensi file
        $temp = explode('.', $fileName);
        $fileExtension = end($temp);
        $newFileName = "$episode.$fileExtension"; // Sanitasi nama file

        // Tentukan direktori penyimpanan
        $uploadFileDir = "tv_series/{$tahun}/{$judul}/{$season}/";
        if (!file_exists($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true); // Buat folder dengan izin yang sesuai
        }
        $dest_path = $uploadFileDir . $newFileName;

        // Pindahkan file yang diupload
        if (move_uploaded_file($fileTmpPath, $dest_path)) {

            $sql = "INSERT INTO `episodes` (`id_movie`, `season`, `episode`, `file`) 
            VALUES ('$id', '$season', '$episode', '$dest_path')";

            if (mysqli_query($conn, $sql)) {
                echo "
                <script>
                    alert('Episode Berhasil ditambahkan');
                </script>
                ";
                header("Location: list.php");
            } else {
                echo "
                <script>
                    alert('Episode Gagal ditambahkan');
                </script>
                ";
                header("Location: list.php");
            }
        } else {
            echo "Error uploading file.";
            exit; // Hentikan eksekusi jika ada kesalahan
        }

    } else {
        echo "
            <script>
                alert('Data Tidak Ditemukan');
            </script>
            ";
        echo "";
        header("Location: list.php");
    }
} elseif (isset($_POST['importepisode'])) {
    $id = $_POST['id'];

    // Ambil informasi film dari database
    $data = mysqli_query($conn, "SELECT * FROM `movie` WHERE `id` = $id");
    $movie = mysqli_fetch_assoc($data);
    $judul = preg_replace('/[^a-zA-Z0-9_-]/', '_', $movie['judul']);
    $tahun = $movie['tahun'];

    // Penanganan file ZIP
    $zipFile = $_FILES['file']['tmp_name'];
    $zip = new ZipArchive;

    if ($zip->open($zipFile) === TRUE) {
        // Ekstrak semua file dalam ZIP ke direktori sementara
        $extractPath = "temp_extracted/";
        createDirectory($extractPath);
        $zip->extractTo($extractPath);
        $zip->close();

        // Urutkan folder berdasarkan nama
        $folders = array_diff(scandir($extractPath), ['.', '..']);
        natsort($folders); // Mengurutkan folder secara alami

        foreach ($folders as $folder) {
            // Anggap nama folder adalah nama musim (season)
            $season = $folder;
            $baseDir = "tv_series/{$tahun}/{$judul}/{$season}/";
            createDirectory($baseDir);

            // Proses dan urutkan setiap file dalam folder musim (season)
            $episodeFiles = array_diff(scandir($extractPath . $folder), ['.', '..', 'vtt']);
            natsort($episodeFiles); // Mengurutkan file secara alami

            foreach ($episodeFiles as $episodeFile) {
                // Anggap nama file adalah nama episode (tanpa ekstensi)
                $episodeNumber = pathinfo($episodeFile, PATHINFO_FILENAME);
                $fileExtension = pathinfo($episodeFile, PATHINFO_EXTENSION);

                // Cek apakah episode sudah ada di database
                $query = "SELECT * FROM `episodes` WHERE `id_movie` = ? AND `season` = ? AND `episode` = ?";
                $caridata = $conn->prepare($query);
                $caridata->bind_param("iss", $id, $season, $episodeNumber);
                $caridata->execute();

                $result = $caridata->get_result();
                $jumlahData = $result->num_rows;

                if ($jumlahData === 0) {
                    // Jika episode belum ada, pindahkan file ke direktori tujuan
                    $filePath = $baseDir . basename($episodeFile);
                    rename($extractPath . $folder . '/' . $episodeFile, $filePath);

                    // Masukkan informasi episode ke database
                    $sql = "INSERT INTO `episodes` (`id_movie`, `file`, `season`, `episode`) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("isss", $id, $filePath, $season, $episodeNumber);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            // Cek keberadaan folder vtt untuk subtitle
            $vttFolderPath = $extractPath . $folder . '/vtt';
            if (is_dir($vttFolderPath)) {
                $vttFiles = array_diff(scandir($vttFolderPath), ['.', '..']);
                natsort($vttFiles);

                foreach ($vttFiles as $vttFile) {
                    $vttEpisodeNumber = pathinfo($vttFile, PATHINFO_FILENAME);
                    $subtitlePath = $baseDir . basename($vttFile); // Simpan subtitle di folder yang sama dengan episode

                    // Cari episode yang cocok dan update kolom subtitle
                    $updateQuery = "UPDATE `episodes` SET `subtitle` = ? WHERE `id_movie` = ? AND `season` = ? AND `episode` = ?";
                    echo $updateQuery."<br>";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bind_param("siss", $subtitlePath, $id, $season, $vttEpisodeNumber);
                    $updateStmt->execute();
                    $updateStmt->close();

                    // Pindahkan file subtitle ke direktori episode
                    rename($vttFolderPath . '/' . $vttFile, $subtitlePath);
                }
            }

        }

        // Hapus direktori sementara setelah selesai
        deleteDirectory($extractPath);
        echo "Files sorted, uploaded, and saved to the database successfully!";
    } else {
        echo "Failed to open the ZIP file.";
    }
} else if (isset($_POST['hapusfilm'])) {
    $id = $_POST['id'];
    $cari = mysqli_query($conn, "SELECT * FROM `movie` WHERE `id` = $id");
    $movie = mysqli_fetch_assoc($cari);
    $poster = $movie['poster'];
    $file = $movie['file'];
    $jenis = $movie['jenis'];

    // Hapus file jika ada
    if (file_exists($poster)) {
        unlink($poster); // Hapus file poster dari server
    }
    if (file_exists($file)) {
        unlink($file); // Hapus file movie dari server
    }

    if ($jenis == "tv_series") {
        $list_episode = mysqli_query($conn, "SELECT * FROM `episodes` WHERE `id_movie` = $id");
        if (mysqli_num_rows($list_episode) > 0) {
            foreach ($list_episode as $list) {
                $episode_file = $list['file'];
                // Hapus file episode jika ada
                if (file_exists($episode_file)) {
                    unlink($episode_file); // Hapus file episode dari server
                }
            }
        }
        $delete_episodes = "DELETE FROM `episodes` WHERE `id_movie` = $id";
        mysqli_query($conn, $delete_episodes); // Hapus semua episode terkait movie ini
    }
    // Hapus movie dari database
    $sql = "DELETE FROM `movie` WHERE `id` = $id"; // Hapus movie dengan id yang spesifik
    if (mysqli_query($conn, $sql)) {
        echo "Movie dan file terkait berhasil dihapus.";
    } else {
        echo "Error menghapus movie: " . mysqli_error($conn);
    }
}

function createDirectory($path)
{
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
}

function deleteDirectory($dir)
{
    if (!is_dir($dir)) {
        return;
    }
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            deleteDirectory($path);
        } else {
            unlink($path);
        }
    }
    rmdir($dir);
}