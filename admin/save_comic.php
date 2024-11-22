<?php
require '../functions.php'; // Pastikan untuk menyertakan koneksi ke database

if (isset($_POST['tambahkomik'])) {
    // Ambil data dari form
    $title = $_POST['title'];
    $year = $_POST['year'];
    $genre = $_POST['genre'];
    $author = $_POST['author'];
    $type = $_POST['type'];
    // $posterFile = $_POST['cover']; // Ambil URL poster dari input

    // Validasi input
    if (empty($title) || empty($year) || empty($genre)) {
        echo "Judul, tahun, dan genre harus diisi.";
        exit;
    }

    // Tentukan direktori penyimpanan untuk poster
    $judul = strtolower(str_replace(' ', '_', $title));
    $folderPath = "komik/$year/$judul/";
    // Buat direktori jika belum ada
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true); // Buat folder dengan izin yang sesuai
    }
    $temp = explode('.', $_FILES['cover']['name']);
    $posterExtension = end($temp);
    $posterData = $_FILES['cover']['tmp_name'];
    $filePath = $folderPath . 'cover.' . $posterExtension; // Tentukan path lengkap
    move_uploaded_file($posterData, $filePath);


    // Query untuk menyimpan data film
    $sql = "INSERT INTO `comic` (`title`, `author`, `type`, `year`, `genre`, `cover`) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $title, $author, $type, $year, $genre, $filePath); // Gunakan path gambar yang baru diunduh

    if ($stmt->execute()) {
        echo "
        <script>
            alert('Komik berhasil ditambahkan!');
        </script>
        ";
        // Redirect atau tampilkan pesan sukses
        header("Location: comic.php");
    } else {
        echo "Terjadi kesalahan saat menambahkan Komik: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} elseif (isset($_POST['ubahkomik'])) {
    // Ambil data dari form
    $id = $_POST['id'];
    $title = $_POST['title'];
    $year = $_POST['year'];
    $genre = $_POST['genre'];
    $author = $_POST['author'];
    $type = $_POST['type'];
    $posterFile = $_POST['cover']; // Ambil URL poster dari input

    // Validasi input
    if (empty($title) || empty($year) || empty($genre)) {
        echo "Judul, tahun, dan genre harus diisi.";
        exit;
    }

    // Tentukan direktori penyimpanan untuk poster
    $judul = strtolower(str_replace(' ', '_', $title));
    $folderPath = "komik/$year/$judul/";
    // Buat direktori jika belum ada
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true); // Buat folder dengan izin yang sesuai
    }
    $temp = explode('.', $_FILES['cover']['name']);
    $posterExtension = end($temp);
    $posterData = $_FILES['cover']['tmp_name'];
    $filePath = $folderPath . 'cover.' . $posterExtension; // Tentukan path lengkap
    move_uploaded_file($posterData, $filePath);


    // Query untuk menyimpan data film
    $sql = "UPDATE `comic` SET `title`=?, `author`=?, `type`=?, `year`=?, `genre`=?, `cover`=? WHERE `id`=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $title, $author, $type, $year, $genre, $filePath, $id); // Gunakan path gambar yang baru diunduh

    if ($stmt->execute()) {
        echo "
        <script>
            alert('Komik berhasil diubah!');
        </script>
        ";
        // Redirect atau tampilkan pesan sukses
        header("Location: comic.php");
    } else {
        echo "Terjadi kesalahan saat menambahkan Komik: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} elseif (isset($_POST['hapuskomik'])) {

} elseif (isset($_POST['tambahchapter'])) {
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Koneksi database gagal: " . $e->getMessage());
    }

    $comic_id = $_POST['id'];
    $file = $_FILES['file'];

    // Validasi input
    if (empty($comic_id) || empty($file)) {
        die("ID dan file zip wajib diisi.");
    }

    // Validasi ID komik
    $stmt = $pdo->prepare("SELECT * FROM comic WHERE id = :id");
    $stmt->execute(['id' => $comic_id]);
    $comic = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$comic) {
        die("Komik tidak ditemukan.");
    }

    $year = $comic['year'];
    $title = strtolower(str_replace(' ', '_', $comic['title']));
    $uploadDir = __DIR__ . "/komik/$year/$title";
    $cacheDir = __DIR__ . "/komik/cache"; // Folder sementara untuk ekstraksi

    // Buat folder tujuan dan folder cache jika belum ada
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0777, true);
    }

    // Pindahkan file ZIP yang diunggah ke folder sementara
    $zipPath = $cacheDir . '/' . basename($file['name']);
    move_uploaded_file($file['tmp_name'], $zipPath);

    // Ekstrak file ZIP ke folder cache
    $zip = new ZipArchive();
    if ($zip->open($zipPath) === TRUE) {
        $zip->extractTo($cacheDir);
        $zip->close();
        unlink($zipPath); // Hapus file ZIP setelah diekstrak
    } else {
        die("Gagal membuka file ZIP.");
    }

    // Cari folder yang telah diekstrak di dalam cache
    $folders = array_filter(glob($cacheDir . '/*'), 'is_dir');

    foreach ($folders as $folder) {
        $chapterName = basename($folder);
        $chapterPath = "komik/$year/$title/$chapterName";

        // Periksa apakah lokasi sudah ada di database
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM chapter WHERE location = :location");
        $stmt->execute(['location' => $chapterPath]);
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            // Pindahkan folder ke lokasi akhir
            $finalPath = $uploadDir . '/' . $chapterName;
            rename($folder, $finalPath);

            // Simpan data chapter ke tabel
            $stmt = $pdo->prepare("
            INSERT INTO chapter (comic_id, title, location)
            VALUES (:comic_id, :title, :location)
        ");
            $stmt->execute([
                'comic_id' => $comic_id,
                'title' => $chapterName,
                'location' => $chapterPath,
            ]);
        } else {
            // Hapus folder dari cache jika sudah ada di database
            array_map('unlink', glob("$folder/*"));
            rmdir($folder);
            echo "Folder $chapterName sudah ada di database, dilewati.\n";
        }
    }


    echo "
        <script>
            alert('Chapter berhasil ditambahkan!');
        </script>
        ";
    // Redirect atau tampilkan pesan sukses
    header("Location: comic.php");
}