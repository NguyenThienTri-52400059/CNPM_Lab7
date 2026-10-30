// proof of concept
<?php
// views/admin.php
session_start();

// Kiểm tra quyền truy cập: Chỉ các vai trò quản lý/vận hành phù hợp mới được vào (BR09)[cite: 2, 16]
$allowedRoles = ['ADMIN', 'MANAGER', 'INVENTORY_STAFF'];
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], $allowedRoles)) {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Nhà Hàng (UI-06)</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h2>QUẢN LÝ NHÀ HÀNG</h2>
            <div class="user-info">
                <span>Xin chào, <strong><?php echo htmlspecialchars($user['full_name'] ?? 'Quản lý'); ?></strong> (<?php echo htmlspecialchars($user['role']); ?>)</span>
                <a href="../controllers/AuthController.php?action=logout" class="btn-logout">Đăng xuất</a>
            </div>
        </header>

        <div class="admin-layout">
            <!-- Điều hướng các chức năng quản lý bên trái -->
            <aside class="sidebar-menu">
                <button type="button" class="btn-menu-tab active" onclick="showSection('menu-mgmt')">Thực đơn<br><small>Thêm / Sửa / Ân món</small></button>
                <button type="button" class="btn-menu-tab" onclick="showSection('table-mgmt')">Quản lý Bàn<br><small>Thêm / Cập nhật bàn</small></button>
                <button type="button" class="btn-menu-tab" onclick="showSection('inventory-mgmt')">Kho Nguyên Liệu<br><small>Nhập / Xuất kho</small></button>
                <button type="button" class="btn-menu-tab" onclick="showSection('user-mgmt')">Tài khoản & Nhân sự<br><small>Phân quyền người dùng</small></button>
            </aside>

            <!-- Khu vực hiển thị nội dung và form quản lý -->
            <main class="content-section">
                <!-- 1. Quản lý Thực đơn -->
                <div id="menu-mgmt" class="tab-content active">
                    <h3>Danh sách thực đơn</h3>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Mã món</th>
                                    <th>Tên đối tượng</th>
                                    <th>Giá bán</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>M01</td>
                                    <td>Cơm chiên hải sản</td>
                                    <td>50.000đ</td>
                                    <td><span class="badge active">Đang hoạt động</span></td>
                                    <td><button class="btn-sm">Chỉnh sửa</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <form class="admin-form">
                        <h4>Thêm / Cập nhật món</h4>
                        <div class="form-row">
                            <input type="text" placeholder="Tên món ăn" required>
                            <input type="number" placeholder="Giá bán (VNĐ)" required>
                            <select>
                                <option value="AVAILABLE">Phục vụ (AVAILABLE)</option>
                                <option value="OUT_OF_STOCK">Hết hàng (OUT_OF_STOCK)</option>
                            </select>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-primary">Thêm mới</button>
                            <button type="button" class="btn-secondary">Cập nhật</button>
                        </div>
                    </form>
                </div>

                <!-- 2. Quản lý Bàn (Mặc định ẩn) -->
                <div id="table-mgmt" class="tab-content" style="display: none;">
                    <h3>Danh sách Bàn ăn</h3>
                    <p>Khu vực quản lý mã bàn, sức chứa và trạng thái hoạt động của bàn.</p>
                </div>

                <!-- 3. Quản lý Kho (Mặc định ẩn) -->
                <div id="inventory-mgmt" class="tab-content" style="display: none;">
                    <h3>Quản lý Kho Nguyên liệu</h3>
                    <p>Theo dõi tồn kho nguyên liệu hiện tại và lịch sử nhập/xuất kho.</p>
                </div>
            </main>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            // Ẩn tất cả nội dung tab
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.style.display = 'none');

            // Bỏ class active ở các nút tab
            const buttons = document.querySelectorAll('.btn-menu-tab');
            buttons.forEach(btn => btn.classList.remove('active'));

            // Hiển thị tab được chọn
            document.getElementById(sectionId).style.display = 'block';
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>