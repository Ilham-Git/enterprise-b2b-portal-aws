# Dokumen 001: Latar Belakang & Masalah Bisnis (Business Problem)

## Konteks Bisnis
Perusahaan manufaktur berskala besar seringkali menghadapi tantangan dalam mendistribusikan produk komoditas (seperti semen, besi beton, dan bata) ke ratusan agen distributor di berbagai daerah. Proses pemesanan yang masih semi-manual atau menggunakan infrastruktur server lokal (*on-premise*) tunggal seringkali menjadi leher botol (*bottleneck*).

## Tantangan yang Dihadapi (Pain Points)
1. **Server Overload:** Sistem pemesanan sering mengalami *down* atau sangat lambat pada akhir bulan ketika ratusan agen melakukan pemesanan stok secara bersamaan (keterbatasan skalabilitas).
2. **Beban Penyimpanan:** Katalog produk yang berisi ribuan gambar beresolusi tinggi sangat membebani kapasitas penyimpanan dan *bandwidth web server* utama.
3. **Keamanan Data Transaksi:** Database agen dan transaksi rahasia masih tergabung dalam jaringan yang sama dengan *web server* publik, sehingga rentan terhadap serangan siber dari luar.

## Objektif Proyek
Membangun *Proof of Concept* (PoC) portal B2B Wholesale Wholesale berbasis arsitektur *cloud* yang *scalable*, aman, dan memisahkan beban kerja komputasi dengan penyimpanan statis.