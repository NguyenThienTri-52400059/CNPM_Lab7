// proof of concept
<?php
// dao/TableDAO.php

class TableDAO {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lấy toàn bộ danh sách bàn của nhà hàng
     * Dựa trên các cột: id, table_code, capacity, status[cite: 7, 8].
     */
    public function getAllTables() {
        $sql = "SELECT id, table_code, capacity, status FROM restaurant_tables";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Cập nhật trạng thái của bàn (Ví dụ: AVAILABLE -> OCCUPIED)
     */
    public function updateTableStatus($tableId, $status) {
        $sql = "UPDATE restaurant_tables SET status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $tableId]);
    }
}
?>