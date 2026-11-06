<?php

declare(strict_types=1);

class ReservationService
{
    private $reservationDAO;
    private $tableDAO;

    public function __construct($reservationDAO, $tableDAO)
    {
        $this->reservationDAO = $reservationDAO;
        $this->tableDAO = $tableDAO;
    }

    /**
     * Kiểm tra và tạo lịch đặt bàn mới.
     */
    public function createReservation(array $data)
    {
        $requiredFields = [
            'customer_id',
            'table_id',
            'reserved_at',
            'end_at',
            'guest_count',
        ];

        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $data)) {
                throw new InvalidArgumentException(
                    'Dữ liệu đặt bàn không hợp lệ'
                );
            }
        }

        $customerId = $this->toPositiveInteger(
            $data['customer_id'],
            'Mã khách hàng không hợp lệ'
        );

        $tableId = $this->toPositiveInteger(
            $data['table_id'],
            'Mã bàn không hợp lệ'
        );

        $guestCount = $this->toPositiveInteger(
            $data['guest_count'],
            'Số khách phải là số nguyên dương'
        );

        $newStart = $this->toTimestamp($data['reserved_at']);
        $newEnd = $this->toTimestamp($data['end_at']);

        if ($newStart >= $newEnd) {
            throw new InvalidArgumentException(
                'Thời gian kết thúc phải sau thời gian bắt đầu'
            );
        }

        $table = $this->tableDAO->getTableById($tableId);

        if (!$table) {
            throw new DomainException('Bàn không tồn tại');
        }

        if (($table['status'] ?? null) === 'OUT_OF_SERVICE') {
            throw new DomainException('Bàn đang ngừng sử dụng');
        }

        $capacity = $this->toPositiveInteger(
            $table['capacity'] ?? null,
            'Sức chứa của bàn không hợp lệ'
        );

        if ($guestCount > $capacity) {
            throw new DomainException(
                'Số khách vượt quá sức chứa của bàn'
            );
        }

        $existingReservations =
            $this->reservationDAO->getReservationsByTable($tableId);

        foreach ($existingReservations as $reservation) {
            $status = $reservation['status'] ?? 'CONFIRMED';

            // Lịch đã hủy không tiếp tục giữ chỗ.
            if (!in_array($status, ['PENDING', 'CONFIRMED'], true)) {
                continue;
            }

            $existingStart =
                $this->toTimestamp($reservation['reserved_at']);

            $existingEnd =
                $this->toTimestamp($reservation['end_at']);

            $isOverlapping =
                $newStart < $existingEnd &&
                $newEnd > $existingStart;

            if ($isOverlapping) {
                throw new DomainException(
                    'Bàn đã có lịch đặt trong khoảng thời gian này'
                );
            }
        }

        $data['customer_id'] = $customerId;
        $data['table_id'] = $tableId;
        $data['guest_count'] = $guestCount;
        $data['status'] = 'CONFIRMED';

        return $this->reservationDAO->createReservation($data);
    }

    private function toPositiveInteger($value, string $errorMessage): int
    {
        if (is_int($value)) {
            $number = $value;
        } elseif (
            is_string($value) &&
            $value !== '' &&
            ctype_digit($value)
        ) {
            $number = (int) $value;
        } else {
            throw new InvalidArgumentException($errorMessage);
        }

        if ($number <= 0) {
            throw new InvalidArgumentException($errorMessage);
        }

        return $number;
    }

    private function toTimestamp($value): int
    {
        if (!is_string($value) || trim($value) === '') {
            throw new InvalidArgumentException(
                'Thời gian đặt bàn không hợp lệ'
            );
        }

        $timestamp = strtotime($value);

        if ($timestamp === false) {
            throw new InvalidArgumentException(
                'Thời gian đặt bàn không hợp lệ'
            );
        }

        return $timestamp;
    }
}