// proof of concept
<?php
// index.php (File gốc đặt ngoài cùng thư mục dự án)
session_start();

// Nhúng kết nối CSDL chung nếu cần thiết cho các xử lý nhanh
require_once __DIR__ . '/config/db.php';

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['user'])) {
    // Nếu chưa đăng nhập, chuyển hướng ngay về trang Đăng nhập (UI-01)[cite: 16]
    header('Location: views/login.php');
    exit();
}

// Nếu đã đăng nhập, lấy vai trò (role) để phân phối hướng đi
$userRole = $_SESSION['user']['role'] ?? '';

switch ($userRole) {
    case 'CUSTOMER':
        // Khách hàng -> Chuyển đến màn hình Đặt bàn & Gọi món (UI-02 / UI-03)[cite: 16]
        header('Location: views/customer_order.php');
        break;

    case 'CHEF':
        // Đầu bếp -> Chuyển đến màn hình theo dõi chế biến (UI-04)[cite: 16]
        header('Location: views/kitchen.php');
        break;

    case 'CASHIER':
        // Thu ngân -> Chuyển đến màn hình hóa đơn và thanh toán (UI-05)[cite: 16]
        header('Location: views/cashier.php');
        break;

    case 'ADMIN':
    case 'MANAGER':
    case 'INVENTORY_STAFF':
        // Quản lý, Kho, Admin -> Chuyển đến màn hình quản lý nhà hàng (UI-06)[cite: 16]
        header('Location: views/admin.php');
        break;

    default:
        // Nếu role không hợp lệ, hủy session và bắt đăng nhập lại
        session_destroy();
        header('Location: views/login.php');
        break;
}
exit();
?>