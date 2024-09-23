document.getElementById('crawl-btn').addEventListener('click', function() {
    fetch('http://localhost/backend/index.php?api_key=your-secret-api-key')
        .then(response => response.json())
        .then(data => {
            const resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = ''; // Eemalda eelmised tulemused
            data.forEach(store => {
                const storeDiv = document.createElement('div');
                storeDiv.innerHTML = `<h2>${store.url}</h2><p>Kategooriad: ${store.categories.join(', ')}</p>`;
                resultsDiv.appendChild(storeDiv);
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
});
