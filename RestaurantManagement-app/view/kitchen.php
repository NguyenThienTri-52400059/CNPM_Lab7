// proof of concept
<?php
// views/kitchen.php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'CHEF') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Màn Hình Bếp - Danh Sách Món (UI-04)</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="kitchen-container">
        <h2>MÀN HÌNH BẾP - DANH SÁCH MÓN</h2>

        <!-- Khối danh sách các món cần chế biến (Áp dụng wireframe mục 4.4) -->
        <div class="kitchen-order-list">
            <div class="order-card">
                <h3>ĐƠN #001 - Bàn: B05</h3>
                <p><strong>Cơm chiên</strong> - Số lượng: 1</p>
                <div class="status-indicator">Trạng thái: <span class="badge waiting">Chờ chế biến</span></div>
                <div class="action-buttons">
                    <button type="button" class="btn-action" onclick="updateItemStatus(1, 'PREPARING')">Bắt đầu chế biến</button>
                    <button type="button" class="btn-action" onclick="updateItemStatus(1, 'READY')">Đánh dấu hoàn thành</button>
                </div>
            </div>

            <div class="order-card">
                <h3>ĐƠN #002 - Bàn: B02</h3>
                <p><strong>Mì xào</strong> - Số lượng: 1</p>
                <div class="status-indicator">Trạng thái: <span class="badge preparing">Đang chế biến</span></div>
                <div class="action-buttons">
                    <button type="button" class="btn-action" onclick="updateItemStatus(2, 'PREPARING')">Bắt đầu chế biến</button>
                    <button type="button" class="btn-action" onclick="updateItemStatus(2, 'READY')">Đánh dấu hoàn thành</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateItemStatus(orderItemId, newStatus) {
            // Gửi yêu cầu cập nhật trạng thái món ăn qua Fetch API tới Controller xử lý của bếp
            alert('Đã cập nhật trạng thái món ' + orderItemId + ' thành: ' + newStatus);
        }
    </script>
</body>
</html>