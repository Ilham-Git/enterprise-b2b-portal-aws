# Dokumen 003: Architectural Decision Records (Justifikasi Pemilihan Layanan)

Dokumen ini menguraikan keputusan arsitektural di balik pemilihan setiap layanan AWS, dengan membandingkannya terhadap alternatif yang tersedia untuk memastikan solusi yang paling optimal dari segi biaya, keamanan, dan performa.

## 1. Topologi Jaringan: Mengapa 1 Public Subnet & 2 Private Subnets?

* **Keputusan:** Kami menggunakan 1 VPC dengan 1 Public Subnet (Zone A) dan 2 Private Subnets (Zone A & Zone B).
* **Alasan:** * **Public Subnet:** Didedikasikan khusus untuk Amazon EC2 yang bertindak sebagai *Web Server* berhadapan langsung dengan internet (dilengkapi Internet Gateway).
    * **2 Private Subnets:** Amazon RDS mewajibkan pembuatan *DB Subnet Group* yang membentang di minimal dua *Availability Zone* (AZ) yang berbeda. Meskipun pada PoC ini kita menggunakan *Single-DB Instance* untuk menghemat biaya, menyiapkan dua subnet privat sejak awal memastikan arsitektur ini sudah **High Availability (HA) Ready**. Jika kelak perusahaan butuh performa lebih, kita hanya perlu menghidupkan fitur *Multi-AZ Deployment* tanpa merombak jaringan dasar.

## 2. Lapisan Komputasi: Mengapa Amazon EC2? (Bukan ECS/Docker atau AWS Lambda)
* **Keputusan:** Menggunakan Amazon EC2 (Amazon Linux 2023) dengan metode *bootstrapping* via *User Data*.
* **Alasan:** Aplikasi *legacy* berbasis PHP Monolitik membutuhkan kontrol penuh terhadap sistem operasi untuk menginstal modul spesifik (seperti `httpd`, `php-mysqli`, dan `mariadb105`). 
* **Trade-off:** AWS Lambda (Serverless) akan terlalu kompleks karena membutuhkan perombakan ulang kode (*refactoring*) menjadi *microservices*. Amazon ECS (Container) adalah opsi yang bagus, namun untuk tahap *Proof of Concept*, EC2 menawarkan *time-to-market* yang paling cepat dan visibilitas terbaik untuk *troubleshooting* jaringan secara langsung.

## 3. Lapisan Database: Mengapa Amazon RDS MySQL? (Bukan Amazon DynamoDB)
* **Keputusan:** Menggunakan Amazon RDS (*Relational Database Service*) dengan *engine* MySQL.
* **Alasan:** Data portal B2B Grosir (seperti Relasi Agen, Transaksi Pesanan, dan Katalog Inventaris) bersifat sangat terstruktur dan membutuhkan relasi antar tabel (*JOIN operations*). Sistem transaksi finansial/stok juga mewajibkan kepatuhan **ACID** (*Atomicity, Consistency, Isolation, Durability*) yang mutlak.
* **Trade-off:** Amazon DynamoDB (NoSQL) menawarkan kecepatan baca/tulis satu digit milidetik yang luar biasa dan skalabilitas otomatis. Namun, NoSQL tidak cocok untuk data transaksional yang saling berelasi kompleks. DynamoDB lebih cocok digunakan jika sistem ini nantinya membutuhkan fitur *log* aktivitas *user* atau *shopping cart* berskala masif.

## 4. Penyimpanan Objek: Mengapa Amazon S3? (Bukan EBS atau EFS)
* **Keputusan:** Menyimpan aset gambar beresolusi tinggi di Amazon S3.
* **Alasan:** Amazon S3 mengalihkan (*offload*) beban transfer data dari *Web Server* EC2. S3 memiliki skalabilitas tak terbatas dan skema biaya *pay-as-you-go*.
* **Trade-off:** Menyimpan gambar di dalam Amazon EBS (*Block Storage* yang menempel di EC2) akan membuat *web server* cepat penuh dan membebani memori saat terjadi lonjakan trafik (*spike*). EBS juga tidak bisa dibagi antar beberapa EC2 jika nanti kita menggunakan *Auto Scaling*.