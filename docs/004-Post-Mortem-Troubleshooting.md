# Dokumen 004: Post-Mortem & Log Penyelesaian Masalah (Troubleshooting)

Dalam proses *deployment* arsitektur 3-Tier ini, beberapa kendala teknis ditemukan dan diselesaikan dengan pendekatan *Cloud Engineering* yang sistematis. Catatan ini berfungsi sebagai *Knowledge Base* untuk *deployment* di masa depan.

## Insiden 1: HTTP Error 500 dari Web Server (PHP)
* **Gejala:** Saat mengakses *Public IP* EC2, *browser* menampilkan status `HTTP 500 Internal Server Error` alih-alih merender halaman.
* **Investigasi:** Amazon Linux 2023 menggunakan PHP 8.2 yang melemparkan *Fatal Exception* ketika koneksi ke database gagal, sehingga menyebabkan *server crash*.
* **Resolusi:** Alih-alih melakukan perbaikan langsung di dalam server yang cacat, kami menerapkan prinsip **Immutable Infrastructure**. Kode PHP di GitHub diperbarui dengan mengimplementasikan blok `try...catch` untuk menangkap (*catch*) kesalahan dari MySQL dan mengembalikannya dalam format JSON yang bersih. Setelah kode di-*push*, instans EC2 yang lama di-*terminate*, dan instans baru diluncurkan menggunakan *User Data provisioning* untuk menarik kode terbaru secara otomatis.

## Insiden 2: "Unknown Database" pada Amazon RDS
* **Gejala:** Setelah insiden 1 diselesaikan, aplikasi berhasil merender JSON namun mengembalikan pesan *error* MySQL: `Unknown database 'b2b_portal'`. Infrastruktur jaringan (Security Group dan Subnet) dipastikan valid karena EC2 berhasil melakukan otentikasi.
* **Penyebab Akar (Root Cause):** Pada saat *provisioning* RDS via AWS Console (opsi *Standard Create/Full Configuration*), parameter `Initial database name` pada menu *Additional Configuration* terlewat, sehingga AWS hanya melakukan *provisioning* mesin basis data tanpa membuat skema *logical database* di dalamnya.
* **Resolusi (Bastion Host Approach):** Untuk menghindari pembuatan ulang RDS yang memakan waktu, EC2 di *Public Subnet* dimanfaatkan sebagai *Jump Server / Bastion Host*. 
    1. Melakukan *remote login* ke EC2 via AWS EC2 Instance Connect.
    2. Menginstal *client* basis data secara manual (`sudo yum install -y mariadb105`).
    3. Terhubung ke *endpoint* RDS Private secara langsung lewat terminal (`mysql -h [ENDPOINT] -u admin -p`).
    4. Menjalankan *query* DDL `CREATE DATABASE b2b_portal;` secara manual di dalam RDS. Aplikasi langsung berjalan normal seketika.

## Insiden 3: Gambar S3 Menampilkan Placeholder (Access Denied)

* **Gejala:** Gambar produk tidak muncul dan terjadi *flickering* UI (jatuh kembali ke gambar *placeholder* dari atribut `onerror`). Mode *Inspect Element* pada *browser* menunjukkan URL S3 merespons dengan XML `AccessDenied`.
* **Investigasi:** Konfigurasi AWS S3 modern menggunakan sistem "Gembok Ganda". Meskipun fitur *Block Public Access* sudah dimatikan di tingkat *bucket*, objek di dalamnya tetap berstatus privat secara bawaan (*ACLs disabled*).
* **Resolusi:** Mengimplementasikan **Bucket Policy** (JSON) untuk memberikan izin `s3:GetObject` kepada *Principal* `*` (publik) pada *Resource* `arn:aws:s3:::NAMA_BUCKET/*`. Ini memastikan prinsip *Least Privilege* tetap terjaga, di mana publik hanya bisa membaca gambar, namun tidak dapat memodifikasi isi *bucket*.