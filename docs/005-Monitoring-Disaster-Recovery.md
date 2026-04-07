# Dokumen 005: Monitoring, Alarms & Disaster Recovery (Operasional)

Untuk memenuhi standar operasional perusahaan (*Enterprise-grade*), arsitektur ini tidak hanya berfokus pada *deployment*, tetapi juga mencakup observabilitas dan keberlangsungan bisnis (*Business Continuity*).

## 1. Monitoring & Alarming (Amazon CloudWatch & Amazon SNS)
* **Konsep:** Kami mengimplementasikan pemantauan metrik infrastruktur secara proaktif untuk mencegah *downtime* aplikasi.
* **Implementasi:**
  * **Amazon CloudWatch:** Memantau metrik `CPUUtilization` pada instans EC2 (Web Server).
  * **Alarm Threshold:** CloudWatch Alarm dikonfigurasi untuk memicu peringatan jika pemakaian CPU melampaui 80% selama 5 menit berturut-turut.
  * **Amazon SNS (Simple Notification Service):** Terintegrasi dengan CloudWatch Alarm. Jika ambang batas terlewati, SNS akan langsung mengirimkan notifikasi *email* kepada tim IT/Infrastruktur agar dapat segera melakukan investigasi atau *scaling* sebelum *server* benar-benar *down*.

## 2. Disaster Recovery & Backup (Amazon Machine Image - AMI)
* **Konsep:** Mengantisipasi kegagalan perangkat keras, serangan *ransomware*, atau kesalahan konfigurasi fatal yang menyebabkan *server* tidak dapat dipulihkan.
* **Implementasi:**
  * **AMI (Amazon Machine Image) Creation:** Mengambil *snapshot* atau cetakan (*image*) dari instans EC2 setelah semua konfigurasi dan kode terinstal dengan sempurna.
  * **Nilai Pemulihan (RTO - Recovery Time Objective):** Jika *server* utama hancur, tim infrastruktur dapat meluncurkan EC2 baru dari *backup* AMI ini dalam waktu kurang dari 3 menit. Server baru tersebut akan langsung menyala dalam keadaan persis sama seperti saat di-*backup*, tanpa perlu menjalankan skrip *provisioning* ulang dari awal.