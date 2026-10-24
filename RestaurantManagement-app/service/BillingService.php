// proof of concept
<?php
// service/BillingService.php

class BillingService {
    private $invoiceDAO;

    public function __construct($invoiceDAO) {
        $this->invoiceDAO = $invoiceDAO;
    }

    /**
     * Xử lý lập hóa đơn cho đơn hàng
     */
    public function createInvoice($orderId, $totalAmount) {
        if ($totalAmount < 0) {
            throw new Exception("Tổng tiền hóa đơn không được âm (C07).");
        }

        // Trạng thái ban đầu của hóa đơn là UNPAID[cite: 10]
        return $this->invoiceDAO->createInvoice($orderId, $totalAmount, 'UNPAID');
    }

    /**
     * Xử lý giao dịch thanh toán (Tiền mặt hoặc Trực tuyến)
     */
    public function processPayment($invoiceId, $method, $amount, $isSuccess, $transactionCode = null) {
        // Xác định trạng thái thanh toán dựa trên kết quả thực tế (SUCCESS / FAILED)[cite: 11]
        $paymentStatus = $isSuccess ? 'SUCCESS' : 'FAILED';

        // Ghi nhận lịch sử giao dịch vào bảng payments (kể cả lần thất bại theo BR08)[cite: 2, 5, 11]
        $result = $this->invoiceDAO->recordPayment($invoiceId, $method, $amount, $paymentStatus, $transactionCode);

        if (!$result) {
            throw new Exception("Không thể ghi nhận giao dịch thanh toán vào hệ thống.");
        }

        if (!$isSuccess) {
            throw new Exception("Giao dịch thanh toán thất bại. Hóa đơn vẫn ở trạng thái chưa thanh toán (BR08).");
        }

        return [
            'status' => 'SUCCESS',
            'message' => 'Thanh toán thành công, biên nhận đã được tạo.'
        ];
    }
}
?>