// proof of concept
<?php
// controllers/AuthController.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../dao/UserDAO.php';
require_once __DIR__ . '/../service/AuthService.php';

session_start();

class AuthController {
    private $authService;

    public function __construct($pdo) {
        $userDAO = new UserDAO($pdo);
        $this->authService = new AuthService($userDAO);
    }

    /**
     * Xử lý yêu cầu đăng nhập từ người dùng (UI-01)
     */
    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            try {
                // Gọi tầng Service để xác thực
                $user = $this->authService->login($email, $password);

                // Lưu thông tin vào Session phiên làm việc
                $_SESSION['user'] = $user;

                // Điều hướng dựa trên vai trò (role) của người dùng
                switch ($user['role']) {
                    case 'CUSTOMER':
                        header('Location: ../views/customer_order.php');
                        break;
                    case 'CHEF':
                        header('Location: ../views/kitchen.php');
                        break;
                    case 'CASHIER':
                        header('Location: ../views/cashier.php');
                        break;
                    default:
                        header('Location: ../views/admin.php');
                        break;
                }
                exit();
            } catch (Exception $e) {
                // Trả về lỗi hiển thị trên giao diện đăng nhập
                $errorMsg = $e->getMessage();
                include __DIR__ . '/../views/login.php';
            }
        }
    }
}

// Khởi chạy bộ điều khiển nếu có request POST
if (isset($pdo)) {
    $controller = new AuthController($pdo);
    $controller->handleLogin();
}
?>