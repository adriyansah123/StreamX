<?php
// Pastikan getID3 sudah di-include melalui Composer atau manual.
require 'vendor/autoload.php'; // Jika Anda menggunakan Composer

// Inisialisasi getID3
$getID3 = new getID3;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['musicFile'])) {
    $file = $_FILES['musicFile'];

    // Periksa apakah file berhasil diunggah tanpa error
    if ($file['error'] == UPLOAD_ERR_OK) {
        $filePath = $file['tmp_name'];
        
        // Baca metadata dari file
        $metadata = $getID3->analyze($filePath);

        // Opsional: Handle Error
        if (isset($metadata['error'])) {
            echo "Error membaca metadata: " . implode(', ', $metadata['error']);
            exit;
        }

        // Menampilkan beberapa metadata utama
        echo "<h2>Metadata Musik</h2>";
        echo "<p><strong>Judul:</strong> " . ($metadata['tags']['id3v2']['title'][0] ?? 'Tidak tersedia') . "</p>";
        echo "<p><strong>Artis:</strong> " . ($metadata['tags']['id3v2']['artist'][0] ?? 'Tidak tersedia') . "</p>";
        echo "<p><strong>Album:</strong> " . ($metadata['tags']['id3v2']['album'][0] ?? 'Tidak tersedia') . "</p>";
        echo "<p><strong>Tahun:</strong> " . ($metadata['tags']['id3v2']['year'][0] ?? 'Tidak tersedia') . "</p>";
    } else {
        echo "Gagal mengunggah file.";
    }
} else {
    echo "Tidak ada file yang diunggah.";
}