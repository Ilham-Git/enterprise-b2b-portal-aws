# Dokumen 002: Solusi Arsitektur (Architecture Solution)

Untuk menjawab tantangan bisnis, proyek ini mengimplementasikan **AWS 3-Tier Architecture** (Arsitektur Tiga Lapis) yang memisahkan aplikasi menjadi komponen-komponen independen.

## Desain Arsitektur Lapis Tiga

1. **Tier 1: Presentation & Static Storage (Amazon S3)**
   - **Fungsi:** Menyimpan seluruh aset statis berupa gambar katalog produk.
   - **Solusi:** Membebaskan *web server* dari beban menyajikan gambar yang memakan *bandwidth* besar.

2. **Tier 2: Application / Compute (Amazon EC2 di Public Subnet)**
   - **Fungsi:** Menjalankan logika bisnis (PHP) dan merender antarmuka pengguna (HTML/CSS/JS).
   - **Solusi:** Ditempatkan di *Public Subnet* dengan Security Group ketat yang hanya membuka port HTTP (80) dan SSH (22). Server ini dikonfigurasi menggunakan metode *Immutable Infrastructure* melalui skrip instalasi otomatis (User Data).

3. **Tier 3: Database (Amazon RDS di Private Subnet)**
   - **Fungsi:** Menyimpan data terstruktur (informasi produk, harga, stok).
   - **Solusi:** Ditempatkan di *Private Subnet* (terisolasi dari akses internet langsung). Hanya dapat diakses oleh EC2 di Tier 2 melalui jalur port MySQL (3306) yang diizinkan oleh Security Group khusus (*DB-SG*).