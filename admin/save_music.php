<?php
require '../functions.php'; // Pastikan untuk menyertakan koneksi ke database
require '../vendor/autoload.php'; // Load getID3 jika menggunakan Composer

if (isset($_POST['tambahartist'])) {
    // Ambil data dari form
    $name = $_POST['name'];
    $description = $_POST['description'];
    $photo = $_FILES['photo']['name'];

    // Tentukan direktori penyimpanan untuk poster
    $folderPath = "music/$name/";
    $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name); // Sanitasi nama file
    $posterExtension = pathinfo($photo, PATHINFO_EXTENSION); // Dapatkan ekstensi gambar dari nama file asli
    $filePath = $folderPath . $fileName . '.' . $posterExtension; // Tentukan path lengkap

    // Buat direktori jika belum ada
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true); // Buat folder dengan izin yang sesuai
    }

    // Proses unggah file
    $posterData = $_FILES['photo']['tmp_name'];
    if (move_uploaded_file($posterData, $filePath)) {
        // Query untuk menyimpan data musik
        $sql = "INSERT INTO `artist` (`name`, `photo`, `description`) 
                VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $filePath, $description); // Gunakan path gambar yang baru diunduh

        if ($stmt->execute()) {
            // Redirect setelah alert
            echo "
            <script>
                alert('Musik berhasil ditambahkan!');
                window.location.href = 'music.php';
            </script>
            ";
        } else {
            echo "Terjadi kesalahan saat menambahkan musik: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Gagal mengunggah file gambar.";
    }
} elseif (isset($_POST['ubahartist'])) {
    // Ambil data dari form
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $photo = $_FILES['photo']['name'];

    // Tentukan direktori penyimpanan untuk poster
    $folderPath = "music/$name/";
    $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name); // Sanitasi nama file
    $posterExtension = pathinfo($photo, PATHINFO_EXTENSION); // Dapatkan ekstensi gambar dari nama file asli
    $filePath = $folderPath . $fileName . '.' . $posterExtension; // Tentukan path lengkap

    // Buat direktori jika belum ada
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true); // Buat folder dengan izin yang sesuai
    }

    // Proses unggah file
    $posterData = $_FILES['photo']['tmp_name'];
    if (move_uploaded_file($posterData, $filePath)) {
        // Query untuk menyimpan data musik
        $sql = "UPDATE `artist` SET `name`=?, `photo`=?, `description`=? WHERE `id`=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $filePath, $description, $id); // Gunakan path gambar yang baru diunduh

        if ($stmt->execute()) {
            // Redirect setelah alert
            echo "
            <script>
                alert('Musik berhasil diubah!');
                window.location.href = 'music.php';
            </script>
            ";
        } else {
            echo "Terjadi kesalahan saat menambahkan musik: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Gagal mengunggah file gambar.";
    }
} elseif (isset($_POST['tambahmusic'])) {
    
    processUploadedFiles($_FILES['files'], $conn);
}

function processUploadedFiles($files, $conn) {
    $getID3 = new getID3;

    foreach ($files['tmp_name'] as $index => $filePath) {
        $metadata = $getID3->analyze($filePath);

        // Pastikan metadata memiliki informasi yang dibutuhkan
        if (isset($metadata['tags']['id3v2'])) {
            $title = $metadata['tags']['id3v2']['title'][0] ?? null;
            $artist = $metadata['tags']['id3v2']['artist'][0] ?? null;
            $album = $metadata['tags']['id3v2']['album'][0] ?? null;

            if (!$title || !$artist || !$album) {
                echo "File tidak memiliki metadata yang lengkap: $filePath<br>";
                continue;
            }

            // Periksa atau tambah artis
            $artistId = checkOrAddArtist($artist, $conn);

            // Periksa atau tambah album
            $albumId = checkOrAddAlbum($album, $artistId, $conn);

            
            // Tentukan path untuk menyimpan file
            $sanitizedArtist = sanitizeFileName($artist);
            $sanitizedAlbum = sanitizeFileName($album);
            $sanitizedTitle = sanitizeFileName($title);
            $destinationPath = "music/$sanitizedArtist/$sanitizedAlbum/$sanitizedTitle.mp3";
            
            // Periksa atau tambah lagu
            checkOrAddSong($title, $artistId, $albumId, $filePath, $destinationPath, $conn);

            saveAlbumCover($filePath, $artist, $album, $albumId, $conn);

            // Buat direktori jika belum ada
            if (!file_exists(dirname($destinationPath))) {
                mkdir(dirname($destinationPath), 0777, true);
            }

            // Simpan file
            if (move_uploaded_file($filePath, $destinationPath)) {
                echo "File $title berhasil disimpan di $destinationPath<br>";
            } else {
                echo "Gagal menyimpan file $title.<br>";
            }
        } else {
            echo "Metadata tidak ditemukan untuk file: $filePath<br>";
        }
    }
}

function checkOrAddArtist($artist, $conn) {
    $stmt = $conn->prepare("SELECT id FROM artist WHERE name = ?");
    $stmt->bind_param("s", $artist);
    $stmt->execute();
    $stmt->bind_result($artistId);
    $stmt->fetch();
    $stmt->close();

    if (!$artistId) {
        $stmt = $conn->prepare("INSERT INTO artist (name) VALUES (?)");
        $stmt->bind_param("s", $artist);
        $stmt->execute();
        $artistId = $stmt->insert_id;
        $stmt->close();
    }

    return $artistId;
}

function checkOrAddAlbum($album, $artistId, $conn) {
    $stmt = $conn->prepare("SELECT id FROM album WHERE title = ? AND artist_id = ?");
    $stmt->bind_param("si", $album, $artistId);
    $stmt->execute();
    $stmt->bind_result($albumId);
    $stmt->fetch();
    $stmt->close();

    if (!$albumId) {
        $stmt = $conn->prepare("INSERT INTO album (title, artist_id) VALUES (?, ?)");
        $stmt->bind_param("si", $album, $artistId);
        $stmt->execute();
        $albumId = $stmt->insert_id;
        $stmt->close();
    }

    return $albumId;
}

function checkOrAddSong($title, $artistId, $albumId, $filePath, $destinationPath, $conn) {
    $stmt = $conn->prepare("SELECT id FROM song WHERE title = ? AND artist_id = ? AND album_id = ?");
    $stmt->bind_param("sii", $title, $artistId, $albumId);
    $stmt->execute();
    $stmt->bind_result($songId);
    $stmt->fetch();
    $stmt->close();

    if (!$songId) {
        $stmt = $conn->prepare("INSERT INTO song (title, artist_id, album_id, file) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siis", $title, $artistId, $albumId, $destinationPath);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("UPDATE song SET title=?, artist_id=?, album_id=?, file=? WHERE id=?");
        $stmt->bind_param("siisi", $title, $artistId, $albumId, $destinationPath, $songId);
        $stmt->execute();
        $stmt->close();
    }
}

function sanitizeFileName($string) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '_', $string);
}

function saveAlbumCover($filePath, $artistName, $albumTitle, $albumId, $conn) {
    $getID3 = new getID3;
    $metadata = $getID3->analyze($filePath);

    // Pastikan metadata memiliki cover album
    if (isset($metadata['id3v2']['APIC'][0]['data'])) {
        $coverData = $metadata['id3v2']['APIC'][0]['data'];
        $mimeType = $metadata['id3v2']['APIC'][0]['image_mime'];
    } elseif (isset($metadata['quicktime']['moov']['subatoms']['trak']['subatoms'][0]['mdia']['subatoms']['minf']['subatoms']['stbl']['subatoms']['stsd']['subatoms'][0]['covr'][0]['data'])) {
        $coverData = $metadata['quicktime']['moov']['subatoms']['trak']['subatoms'][0]['mdia']['subatoms']['minf']['subatoms']['stbl']['subatoms']['stsd']['subatoms'][0]['covr'][0]['data'];
        $mimeType = 'image/jpeg'; // Biasanya dalam format JPEG
    } else {
        echo "Cover album tidak ditemukan dalam file: $filePath<br>";
        return;
    }

    // Sanitasi nama artis dan album untuk dijadikan folder
    $sanitizedArtist = sanitizeFileName($artistName);
    $sanitizedAlbum = sanitizeFileName($albumTitle);

    // Tentukan direktori dan path file untuk cover
    $folderPath = "music/$sanitizedArtist/$sanitizedAlbum";
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true);
    }

    // Tentukan ekstensi file berdasarkan MIME type
    $extension = match ($mimeType) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        default => 'jpg',
    };

    // Simpan gambar sebagai file
    $coverFilePath = "$folderPath/cover.$extension";
    file_put_contents($coverFilePath, $coverData);

    // Simpan path file cover dalam database
    $stmt = $conn->prepare("UPDATE album SET cover = ? WHERE id = ?");
    $stmt->bind_param("si", $coverFilePath, $albumId);

    if ($stmt->execute()) {
        echo "Cover album berhasil disimpan di $coverFilePath<br>";
    } else {
        echo "Gagal menyimpan cover album: " . $stmt->error . "<br>";
    }

    $stmt->close();
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