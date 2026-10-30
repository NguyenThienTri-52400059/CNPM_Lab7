// proof of concept
<!-- views/login.php -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - Hệ thống Quản lý Nhà hàng</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>HỆ THỐNG QUẢN LÝ NHÀ HÀNG</h2>
        
        <!-- Khu vực hiển thị thông báo lỗi nếu có từ Controller -->
        <?php if (isset($errorMsg)): ?>
            <div class="alert-error" style="color: red; margin-bottom: 15px;">
                <?php echo htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>

        <form action="../controllers/AuthController.php" method="POST">
            <div class="form-group">
                <label for="email">Email / Tên Đăng nhập</label>
                <input type="text" id="email" name="email" placeholder="Nhập email hoặc tên đăng nhập" required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-submit">ĐĂNG NHẬP</button>
        </form>
        
        <div class="notification-area">
            <p>Thông báo sẽ hiển thị ở đây</p>
        </div>
    </div>
</body>
</html>