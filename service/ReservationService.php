// proof of concept
<?php
// service/ReservationService.php

class ReservationService {
    private $reservationDAO;

    public function __construct($reservationDAO) {
        $this->reservationDAO = $reservationDAO;
    }

    /**
     * Kiểm tra và tạo lịch đặt bàn mới
     */
    public function createReservation($data) {
        $tableId = $data['table_id'];
        $newStart = strtotime($data['reserved_at']);
        $newEnd = strtotime($data['end_at']);

        // Kiểm tra thời gian hợp lệ
        if ($newStart >= $newEnd) {
            throw new Exception("Thời gian kết thúc phải sau thời gian bắt đầu.");
        }

        // Lấy danh sách các lịch đặt hiện tại của bàn này để kiểm tra trùng lấn (BR01)
        $existingReservations = $this->reservationDAO->getReservationsByTable($tableId);

        foreach ($existingReservations as $res) {
            $existStart = strtotime($res['reserved_at']);
            $existEnd = strtotime($res['end_at']);

            // Kiểm tra điều kiện chồng lấn thời gian
            // (StartA < EndB) và (EndA > StartB)
            if ($newStart < $existEnd && $newEnd > $existStart) {
                throw new Exception("Bàn đã có lịch đặt trong khoảng thời gian này, không thể nhận lịch trùng lấn (BR01).");
            }
        }

        // Nếu không trùng lấn, tiến hành lưu đặt bàn
        $data['status'] = 'CONFIRMED';
        return $this->reservationDAO->createReservation($data);
    }
}
?>