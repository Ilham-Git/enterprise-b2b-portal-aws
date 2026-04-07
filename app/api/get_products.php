<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'config.php';

$response = [
    'db_status' => 'Memeriksa koneksi...',
    'products' => []
];

try {
    // Mencoba mengetuk pintu RDS
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    $response['db_status'] = 'Sukses: Terhubung ke RDS di Private Subnet!';
    
    // Data produk B2B
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
        ]
    ];
    
    $conn->close();

} catch (Exception $e) {
    // Jika gagal terhubung, tangkap dan tampilkan pesan error asli dari AWS/MySQL!
    $response['db_status'] = 'Gagal: ' . $e->getMessage();
}

echo json_encode($response);
?>