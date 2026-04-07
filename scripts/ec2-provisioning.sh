#!/bin/bash
# 1. Update sistem dan install komponen web, PHP, Git, dan MySQL Client
yum update -y
yum install -y httpd php php-mysqli git mariadb105

# 2. Nyalakan Apache (Web Server)
systemctl start httpd
systemctl enable httpd

# 3. Masuk ke direktori web utama
cd /var/www/html

# 4. Ambil kode B2B Portal dari GitHub
git clone https://github.com/Ilham-Git/enterprise-b2b-portal-aws.git .

# 5. Pindahkan file aplikasi ke root web server agar bisa diakses langsung
mv app/frontend/* .
mv app/api ./api

# 6. Restart Apache agar semua perubahan terbaca
systemctl restart httpd