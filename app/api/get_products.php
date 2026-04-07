<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config.php';

// Menyiapkan struktur balikan data
$response = [
    'db_status' => 'Menunggu koneksi...',
    'products' => []
];

// Mematikan error bawaan PHP agar tidak merusak format JSON jika terjadi gagal koneksi
error_reporting(0); 

// Uji koneksi ke Amazon RDS
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    $response['db_status'] = 'Gagal: Tidak dapat menembus RDS Private Subnet.';
} else {
    $response['db_status'] = 'Sukses: Terhubung ke RDS di Private Subnet!';
}

// Data Dummy PoC (Dalam skenario asli, data ini di-query dari tabel MySQL).
// Kita menggunakan komoditas grosir B2B general (Bahan Bangunan) agar relevan.
$response['products'] = [
    [
        'id' => 'PRD-001',
        'name' => 'Semen Konstruksi Tipe 1 (Zak 50kg)',
        'stock' => 5000,
        'price' => 55000,
        's3_image_url' => 'https://GANTI_DENGAN_LINK_S3_KAMU_NANTI.s3.ap-southeast-3.amazonaws.com/produk1.jpg'
    ],
    [
        'id' => 'PRD-002',
        'name' => 'Besi Beton Ulir Standar SNI 12mm',
        'stock' => 1200,
        'price' => 95000,
        's3_image_url' => 'https://GANTI_DENGAN_LINK_S3_KAMU_NANTI.s3.ap-southeast-3.amazonaws.com/produk2.jpg'
    ],
    [
        'id' => 'PRD-003',
        'name' => 'Batu Bata Merah Oven (Per 1000 Pcs)',
        'stock' => 850,
        'price' => 850000,
        's3_image_url' => 'https://GANTI_DENGAN_LINK_S3_KAMU_NANTI.s3.ap-southeast-3.amazonaws.com/produk3.jpg'
    ]
];

echo json_encode($response);
if (!$conn->connect_error) {
    $conn->close();
}
?>