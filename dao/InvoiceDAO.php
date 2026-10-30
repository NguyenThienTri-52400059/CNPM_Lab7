// proof of concept
<?php
// dao/InvoiceDAO.php

class InvoiceDAO {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lập hóa đơn từ một đơn hàng (invoices)
     */
    public function createInvoice($orderId, $totalAmount, $status) {
        $sql = "INSERT INTO invoices (order_id, total_amount, status, created_at) 
                VALUES (?, ?, ?, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$orderId, $totalAmount, $status]);
        
        return $this->pdo->lastInsertId();
    }

    /**
     * Ghi nhận lịch sử giao dịch thanh toán (payments), kể cả lần thất bại (BR08)[cite: 2, 5, 11]
     */
    public function recordPayment($invoiceId, $method, $amount, $status, $transactionCode) {
        $sql = "INSERT INTO payments (invoice_id, method, amount, status, transaction_code, paid_at) 
                VALUES (?, ?, ?, ?, ?, " . ($status === 'SUCCESS' ? 'NOW()' : 'NULL') . ")";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$invoiceId, $method, $amount, $status, $transactionCode]);
    }
}
?>