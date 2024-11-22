function searchMusic() {
    const searchType = document.getElementById('search-type').value;
    const query = document.getElementById('search-query').value;

    if (query.trim() === '') {
        alert("Masukkan kata kunci pencarian!");
        return;
    }

    fetch(`search_music.php?type=${encodeURIComponent(searchType)}&query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            let resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = '';

            if (data.error) {
                resultsDiv.innerHTML = `<p>Terjadi kesalahan: ${data.message}</p>`;
                return;
            }

            // Menampilkan hasil berdasarkan jenis data
            let resultContent = `<h2>Hasil untuk: ${query}</h2>`;
            if (data.results && data.results.length > 0) {
                data.results.forEach(item => {
                    resultContent += `
                        <div>
                            <h3>${item.title}</h3>
                            <p><strong>Artis:</strong> ${item.artist}</p>
                            ${item.album ? `<p><strong>Album:</strong> ${item.album}</p>` : ''}
                            <a href="${item.url}" target="_blank">Lihat di Genius</a>
                        </div><hr>
                    `;
                });
            } else {
                resultContent += `<p>Tidak ada hasil ditemukan.</p>`;
            }
            resultsDiv.innerHTML = resultContent;
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
