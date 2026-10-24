// proof of concept
<?php
// dao/ReservationDAO.php

class ReservationDAO {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lưu thông tin đặt bàn mới vào CSDL
     * Dựa trên bảng reservations[cite: 8].
     */
    public function createReservation($data) {
        $sql = "INSERT INTO reservations (customer_id, table_id, reserved_at, end_at, guest_count, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['customer_id'],
            $data['table_id'],
            $data['reserved_at'],
            $data['end_at'],
            $data['guest_count'],
            $data['status'] // Ví dụ: PENDING, CONFIRMED[cite: 8]
        ]);
    }

    /**
     * Lấy danh sách đặt bàn của một bàn để kiểm tra thời gian chồng lấn (BR01)[cite: 2]
     */
    public function getReservationsByTable($tableId) {
        $sql = "SELECT id, customer_id, table_id, reserved_at, end_at, guest_count, status 
                FROM reservations 
                WHERE table_id = ? AND status IN ('PENDING', 'CONFIRMED')";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$tableId]);
        
        return $stmt->fetchAll();
    }
}
?>