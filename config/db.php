// proof of concept
<?php 
// config/db.php

$host = 'localhost';
$db   = 'restaurant_db'; // Tên cơ sở dữ liệu quản lý nhà hàng[cite: 3]
$user = 'root';          // Tài khoản kết nối MySQL mặc định
$pass = '';              // Mật khẩu kết nối MySQL
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Bật chế độ báo lỗi ngoại lệ
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Mặc định trả về mảng kết hợp
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Vắt kiệt sức mạnh bảo mật prepared statements
];

try {
    // Khởi tạo kết nối PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
    // echo "Kết nối cơ sở dữ liệu thành công!";
} catch (\PDOException $e) {
    // Xử lý lỗi kết nối nếu có
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>