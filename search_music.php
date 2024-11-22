<?php
if (!isset($_GET['type']) || !isset($_GET['query'])) {
    echo json_encode(["error" => true, "message" => "Parameter pencarian tidak lengkap."]);
    exit;
}

$searchType = $_GET['type'];
$query = $_GET['query'];
$api_key = 'UdU5y57lxvTPZcWYRMBnwKqgml6cAqaRx3srPVefJDAYkPLLZECGnd8jZO5hZF97'; // Gantilah dengan Client Access Token Anda
$api_url = "https://api.genius.com/search?q=" . urlencode($query);

// Inisialisasi cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $api_key"
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["error" => true, "message" => "cURL Error: " . curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);
$data = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["error" => true, "message" => "Genius API memberikan respons tidak valid."]);
    exit;
}

// Cek apakah ada hasil dalam data yang diterima
if (!isset($data['response']['hits'])) {
    echo json_encode(["error" => true, "message" => "Tidak ada hasil ditemukan."]);
    exit;
}

// Menyaring hasil berdasarkan jenis pencarian yang dipilih
$results = [];
foreach ($data['response']['hits'] as $hit) {
    $item = $hit['result'];
    if ($searchType === 'artist' && stripos($item['primary_artist']['name'], $query) !== false) {
        $results[] = [
            "title" => $item['title'],
            "artist" => $item['primary_artist']['name'],
            "url" => $item['url'],
            "album" => $item['album']['name'] ?? null
        ];
    } elseif ($searchType === 'album' && isset($item['album']['name']) && stripos($item['album']['name'], $query) !== false) {
        $results[] = [
            "title" => $item['title'],
            "artist" => $item['primary_artist']['name'],
            "url" => $item['url'],
            "album" => $item['album']['name']
        ];
    } elseif ($searchType === 'title' && stripos($item['title'], $query) !== false) {
        $results[] = [
            "title" => $item['title'],
            "artist" => $item['primary_artist']['name'],
            "url" => $item['url'],
            "album" => $item['album']['name'] ?? null
        ];
    }
}

// Mengirimkan data JSON
echo json_encode([
    "results" => $results
]);
