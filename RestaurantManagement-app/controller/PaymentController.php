// proof of concept
<?php
// controllers/PaymentController.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../dao/InvoiceDAO.php';
require_once __DIR__ . '/../service/BillingService.php';

session_start();

class PaymentController {
    private $billingService;
    private $invoiceDAO;

    public function __construct($pdo) {
        $this->invoiceDAO = new InvoiceDAO($pdo);
        $this->billingService = new BillingService($this->invoiceDAO);
    }

    /**
     * Xử lý yêu cầu thanh toán hóa đơn (UI-05)[cite: 16, 21]
     */
    public function handlePayment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoiceId = $_POST['invoice_id'] ?? null;
            $method = $_POST['method'] ?? 'CASH'; // 'CASH' (Tiền mặt) hoặc 'ONLINE' (Trực tuyến)[cite: 11, 21]
            $amount = $_POST['amount'] ?? 0;
            $transactionCode = $_POST['transaction_code'] ?? null; // Mã giao dịch từ cổng thanh toán[cite: 11]

            try {
                // Kiểm tra tính hợp lệ cơ bản
                if (!$invoiceId || $amount <= 0) {
                    throw new Exception("Thông tin hóa đơn hoặc số tiền thanh toán không hợp lệ.");
                }

                // Xác định trạng thái thành công dựa trên phương thức thanh toán
                // Theo quy tắc BR08: Thanh toán trực tuyến chỉ thành công khi nhận được kết quả hợp lệ[cite: 2]
                $isSuccess = true; 
                if ($method === 'ONLINE') {
                    // Nếu là thanh toán trực tuyến, bắt buộc phải có mã giao dịch trả về từ cổng thanh toán
                    if (empty($transactionCode)) {
                        $isSuccess = false;
                        throw new Exception("Không nhận được mã xác thực giao dịch từ cổng thanh toán trực tuyến (BR08).");
                    }
                }

                // Gọi tầng Service để ghi nhận giao dịch và cập nhật hóa đơn
                $result = $this->billingService->processPayment($invoiceId, $method, $amount, $isSuccess, $transactionCode);

                // Trả về kết quả dạng JSON cho giao diện xử lý (AJAX / Fetch API)
                echo json_encode([
                    'status' => 'success',
                    'message' => $result['message'],
                    'invoice_id' => $invoiceId
                ]);
            } catch (Exception $e) {
                // Nếu giao dịch thất bại, giữ nguyên trạng thái hóa đơn và trả về thông báo lỗi (BR08)[cite: 2]
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
        }
    }
}

// Khởi chạy Controller khi có yêu cầu POST từ phía giao diện thanh toán
if (isset($pdo) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new PaymentController($pdo);
    $controller->handlePayment();
}
?>