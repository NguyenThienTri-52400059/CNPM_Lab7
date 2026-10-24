// proof of concept
<?php
// dao/UserDAO.php

class UserDAO {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Tìm thông tin người dùng bằng email hoặc tên đăng nhập để xác thực
     * Dựa trên cấu trúc bảng users (id, full_name, email, password_hash, role, status)[cite: 7].
     */
    public function getUserByEmail($email) {
        $sql = "SELECT id, full_name, email, password_hash, role, status 
                FROM users 
                WHERE email = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        
        return $stmt->fetch();
    }
}
?>