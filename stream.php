<?php
require 'functions.php'; // Koneksi database
global $conn;

if (isset($_GET['song'])) {
    $song_id = intval($_GET['song']);

    // Ambil informasi lagu dari database
    $stmt = $conn->prepare("SELECT `file` FROM `song` WHERE `id` = ?");
    $stmt->bind_param("i", $song_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $song = $result->fetch_assoc();
    
    if ($song && file_exists('admin/'.$song['file'])) {
        $file_path = 'admin/'.$song['file'];

        // Header untuk streaming audio
        header('Content-Type: audio/mpeg');
        header('Content-Length: ' . filesize($file_path));
        header('Accept-Ranges: bytes');

        // Baca file dan stream ke klien
        readfile($file_path);
        exit;
    } else {
        http_response_code(404);
        echo "File tidak ditemukan.";
        exit;
    }
} else {
    http_response_code(400);
    echo "ID lagu tidak valid.";
    exit;
}
