// proof of concept
<?php
// controllers/OrderController.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../dao/OrderDAO.php';
require_once __DIR__ . '/../dao/MenuDAO.php';
require_once __DIR__ . '/../service/OrderService.php';

session_start();

class OrderController {
    private $orderService;

    public function __construct($pdo) {
        $orderDAO = new OrderDAO($pdo);
        $menuDAO = new MenuDAO($pdo);
        $this->orderService = new OrderService($orderDAO, $menuDAO);
    }

    /**
     * Xử lý hành động xác nhận đơn gọi món từ khách hàng hoặc nhân viên phục vụ (UI-03)[cite: 16, 18]
     */
    public function createOrderAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tableId = $_POST['table_id'] ?? null;
            $customerId = $_SESSION['user']['id'] ?? null;
            $createdByUserId = $_SESSION['user']['id'] ?? null;
            $items = $_POST['items'] ?? []; // Mảng chứa các món ăn và số lượng

            try {
                // 1. Tạo đơn hàng chung trước (orders)
                // (Giả định gọi OrderDAO trực tiếp hoặc qua Service, ở đây ta gọi logic đơn giản)
                $orderDAO = new OrderDAO($GLOBALS['pdo']);
                $orderId = $orderDAO->createOrder($tableId, $customerId, $createdByUserId, 'CONFIRMED');

                // 2. Sử dụng OrderService để kiểm tra quy tắc (BR02, BR03) và thêm chi tiết món[cite: 2]
                $this->orderService->addItemsToOrder($orderId, $items);

                // Trả về kết quả JSON thành công cho phía giao diện (JavaScript Fetch API)
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Đặt món thành công!',
                    'order_id' => $orderId
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
        }
    }
}
?>