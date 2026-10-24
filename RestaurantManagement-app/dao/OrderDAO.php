// proof of concept
<?php
// dao/OrderDAO.php

class OrderDAO {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Tạo một đơn hàng mới (orders) và trả về ID của đơn vừa tạo
     */
    public function createOrder($tableId, $customerId, $createdByUserId, $status) {
        $sql = "INSERT INTO orders (table_id, customer_id, created_by_user_id, status, created_at) 
                VALUES (?, ?, ?, ?, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$tableId, $customerId, $createdByUserId, $status]);
        
        return $this->pdo->lastInsertId(); // Lấy ID đơn hàng vừa phát sinh
    }

    /**
     * Thêm chi tiết món ăn vào đơn hàng (order_items)
     */
    public function addOrderItem($orderId, $menuItemId, $quantity, $unitPrice, $status) {
        $sql = "INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price, status) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$orderId, $menuItemId, $quantity, $unitPrice, $status]);
    }
}
?>