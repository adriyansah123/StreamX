<?php
$servername = "";
$username = "";
$password = "";
$dbname = "";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$apiOMDB = '';