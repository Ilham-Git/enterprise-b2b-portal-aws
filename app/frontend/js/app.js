document.addEventListener('DOMContentLoaded', () => {
    const statusBadge = document.getElementById('db-status-badge');
    const productGrid = document.getElementById('product-grid');

    // Mengambil data dari backend PHP
    fetch('../api/get_products.php')
        .then(response => response.json())
        .then(data => {
            // 1. Update Status Badge
            statusBadge.textContent = data.db_status;
            statusBadge.className = 'badge'; 
            if (data.db_status.includes('Sukses')) {
                statusBadge.classList.add('success');
            } else {
                statusBadge.classList.add('error');
            }

            // 2. Render Produk
            if (data.products && data.products.length > 0) {
                data.products.forEach(product => {
                    const formatRupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(product.price);
                    
                    const card = document.createElement('div');
                    card.className = 'card';
                    card.innerHTML = `
                        <img src="${product.s3_image_url}" alt="${product.name}" onerror="this.src='https://via.placeholder.com/300x200?text=Gambar+S3+Belum+Siap'">
                        <div class="card-body">
                            <h3 class="card-title">${product.name}</h3>
                            <p class="card-price">${formatRupiah}</p>
                            <p class="card-stock">Stok Tersedia: ${product.stock} Unit</p>
                        </div>
                    `;
                    productGrid.appendChild(card);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            statusBadge.textContent = 'Gagal: API Backend tidak merespon.';
            statusBadge.className = 'badge error';
        });
});