// proof of concept
<?php
// dao/MenuDAO.php

class MenuDAO {
    private $pdo;

    // Nhận vào đối tượng kết nối PDO từ config/db.php
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách các món ăn đang sẵn sàng phục vụ (AVAILABLE)
     * Áp dụng theo cấu trúc bảng menu_items trong tài liệu thiết kế[cite: 5, 9].
     */
    public function getAvailableMenuItems() {
        $sql = "SELECT id, category_id, name, description, price, availability_status 
                FROM menu_items 
                WHERE availability_status = 'AVAILABLE'";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(); // Trả về mảng chứa danh sách món ăn
    }

    /**
     * Lấy thông tin chi tiết một món ăn theo ID
     */
    public function getMenuItemById($id) {
        $sql = "SELECT id, category_id, name, description, price, availability_status 
                FROM menu_items 
                WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch();
    }
}
?>