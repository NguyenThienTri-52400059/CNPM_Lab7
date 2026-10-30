// proof of concept
<?php
// views/customer_order.php
// Giả định trang này được truy cập sau khi đã đăng nhập (Session tồn tại)
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'CUSTOMER') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Gọi Món - Quản Lí Đơn Hàng (UI-03)</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="order-container">
        <h2>GỌI MÓN - QUẢN LÍ ĐƠN HÀNG</h2>

        <div class="order-layout">
            <!-- Cột danh mục và danh sách món -->
            <div class="menu-selection-section">
                <div class="category-box">
                    <h3>Danh mục</h3>
                    <ul>
                        <li>Món chính</li>
                        <li>Đồ uống</li>
                        <li>Món phụ</li>
                    </ul>
                </div>

                <div class="menu-items-box">
                    <h3>Danh sách món</h3>
                    <div class="item-row">
                        <span>Tên món A - Giá: 50.000đ</span>
                        <div class="quantity-control">
                            <button type="button">-</button>
                            <span>1</span>
                            <button type="button">+</button>
                        </div>
                        <button type="button" class="btn-add">Thêm món</button>
                    </div>
                </div>
            </div>

            <!-- Cột đơn hàng hiện tại -->
            <div class="current-order-section">
                <h3>Đơn hàng hiện tại</h3>
                <p>Bàn: <span id="selected-table">[Chọn bàn]</span> - Mã đơn: <span id="auto-order-id">[Tự động]</span></p>
                
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Các món đã chọn</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3">Chưa có món nào được chọn.</td>
                        </tr>
                    </tbody>
                </table>

                <div class="total-summary">
                    <strong>Tổng tạm tính: </strong> <span id="total-amount">0đ</span>
                </div>

                <button type="button" id="btn-confirm-order" class="btn-primary">XÁC NHẬN ĐƠN</button>
            </div>
        </div>
    </div>

    <!-- Script JavaScript xử lý gọi AJAX gửi dữ liệu tới OrderController -->
    <script>
        document.getElementById('btn-confirm-order').addEventListener('click', function() {
            // Logic gửi yêu cầu bằng Fetch API đến ../controllers/OrderController.php
            alert('Đã gửi yêu cầu xác nhận đơn hàng tới hệ thống!');
        });
    </script>
</body>
</html>