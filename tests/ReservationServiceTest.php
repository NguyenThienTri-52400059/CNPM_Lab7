<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ReservationServiceTest extends TestCase
{
    private ReservationDAO $reservationDAO;
    private TableDAO $tableDAO;
    private ReservationService $reservationService;

    protected function setUp(): void
    {
        $this->reservationDAO =
            $this->createMock(ReservationDAO::class);

        $this->tableDAO =
            $this->createMock(TableDAO::class);

        $this->reservationService = new ReservationService(
            $this->reservationDAO,
            $this->tableDAO
        );
    }

    private function validReservation(): array
    {
        return [
            'customer_id' => 1,
            'table_id' => 10,
            'reserved_at' => '2026-11-10 18:00:00',
            'end_at' => '2026-11-10 19:00:00',
            'guest_count' => 4,
        ];
    }

    private function availableTable(): array
    {
        return [
            'id' => 10,
            'table_code' => 'B10',
            'capacity' => 4,
            'status' => 'AVAILABLE',
        ];
    }

    public function testRes01CreatesValidReservation(): void
    {
        $data = $this->validReservation();

        $this->tableDAO
            ->method('getTableById')
            ->with(10)
            ->willReturn($this->availableTable());

        $this->reservationDAO
            ->method('getReservationsByTable')
            ->with(10)
            ->willReturn([]);

        $this->reservationDAO
            ->expects($this->once())
            ->method('createReservation')
            ->with($this->callback(
                static function (array $savedData): bool {
                    return
                        $savedData['status'] === 'CONFIRMED' &&
                        $savedData['guest_count'] === 4;
                }
            ))
            ->willReturn(true);

        $this->assertTrue(
            $this->reservationService->createReservation($data)
        );
    }

    public function testRes02RejectsEqualStartAndEndTime(): void
    {
        $data = $this->validReservation();
        $data['end_at'] = $data['reserved_at'];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Thời gian kết thúc phải sau thời gian bắt đầu'
        );

        $this->reservationService->createReservation($data);
    }

    public function testRes03RejectsEndBeforeStart(): void
    {
        $data = $this->validReservation();
        $data['end_at'] = '2026-11-10 17:00:00';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Thời gian kết thúc phải sau thời gian bắt đầu'
        );

        $this->reservationService->createReservation($data);
    }

    public function testRes04RejectsZeroGuests(): void
    {
        $data = $this->validReservation();
        $data['guest_count'] = 0;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Số khách phải là số nguyên dương'
        );

        $this->reservationService->createReservation($data);
    }

    public function testRes05RejectsGuestCountAboveCapacity(): void
    {
        $data = $this->validReservation();
        $data['guest_count'] = 5;

        $this->tableDAO
            ->method('getTableById')
            ->willReturn($this->availableTable());

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Số khách vượt quá sức chứa của bàn'
        );

        $this->reservationService->createReservation($data);
    }

    public function testRes06RejectsMissingTable(): void
    {
        $this->tableDAO
            ->method('getTableById')
            ->willReturn(false);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Bàn không tồn tại');

        $this->reservationService->createReservation(
            $this->validReservation()
        );
    }

    public function testRes07RejectsOutOfServiceTable(): void
    {
        $table = $this->availableTable();
        $table['status'] = 'OUT_OF_SERVICE';

        $this->tableDAO
            ->method('getTableById')
            ->willReturn($table);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Bàn đang ngừng sử dụng');

        $this->reservationService->createReservation(
            $this->validReservation()
        );
    }

    public function testRes08RejectsOverlappingReservation(): void
    {
        $this->tableDAO
            ->method('getTableById')
            ->willReturn($this->availableTable());

        $this->reservationDAO
            ->method('getReservationsByTable')
            ->willReturn([
                [
                    'reserved_at' => '2026-11-10 18:30:00',
                    'end_at' => '2026-11-10 19:30:00',
                    'status' => 'CONFIRMED',
                ],
            ]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(
            'Bàn đã có lịch đặt trong khoảng thời gian này'
        );

        $this->reservationService->createReservation(
            $this->validReservation()
        );
    }

    public function testRes09AllowsAdjacentReservation(): void
    {
        $data = $this->validReservation();
        $data['reserved_at'] = '2026-11-10 19:00:00';
        $data['end_at'] = '2026-11-10 20:00:00';

        $this->tableDAO
            ->method('getTableById')
            ->willReturn($this->availableTable());

        $this->reservationDAO
            ->method('getReservationsByTable')
            ->willReturn([
                [
                    'reserved_at' => '2026-11-10 18:00:00',
                    'end_at' => '2026-11-10 19:00:00',
                    'status' => 'CONFIRMED',
                ],
            ]);

        $this->reservationDAO
            ->method('createReservation')
            ->willReturn(true);

        $this->assertTrue(
            $this->reservationService->createReservation($data)
        );
    }

    public function testRes10CanceledReservationDoesNotHoldTable(): void
    {
        $this->tableDAO
            ->method('getTableById')
            ->willReturn($this->availableTable());

        $this->reservationDAO
            ->method('getReservationsByTable')
            ->willReturn([
                [
                    'reserved_at' => '2026-11-10 18:00:00',
                    'end_at' => '2026-11-10 19:00:00',
                    'status' => 'CANCELED',
                ],
            ]);

        $this->reservationDAO
            ->method('createReservation')
            ->willReturn(true);

        $this->assertTrue(
            $this->reservationService->createReservation(
                $this->validReservation()
            )
        );
    }
}