<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class BillingServiceTest extends TestCase
{
    private BillingService $billingService;

    protected function setUp(): void
    {
        // Hàm tính tiền không truy cập cơ sở dữ liệu,
        // vì vậy có thể dùng đối tượng giả đơn giản.
        $fakeInvoiceDAO = new stdClass();

        $this->billingService = new BillingService($fakeInvoiceDAO);
    }

    public function testInv01CalculatesAmountAfterDiscount(): void
    {
        $result = $this->billingService->calculatePayableAmount(
            220000,
            20000
        );

        $this->assertSame(200000, $result);
    }

    public function testInv02CalculatesAmountWithoutDiscount(): void
    {
        $result = $this->billingService->calculatePayableAmount(
            220000,
            0
        );

        $this->assertSame(220000, $result);
    }

    public function testInv03AcceptsZeroTotalAndZeroDiscount(): void
    {
        $result = $this->billingService->calculatePayableAmount(0, 0);

        $this->assertSame(0, $result);
    }

    public function testInv04RejectsNegativeTotal(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Dữ liệu hóa đơn không hợp lệ'
        );

        $this->billingService->calculatePayableAmount(-100, 0);
    }

    public function testInv05RejectsNegativeDiscount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Dữ liệu hóa đơn không hợp lệ'
        );

        $this->billingService->calculatePayableAmount(1000, -100);
    }

    public function testInv06RejectsDiscountGreaterThanTotal(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Dữ liệu hóa đơn không hợp lệ'
        );

        $this->billingService->calculatePayableAmount(1000, 1200);
    }

    public function testInv07RejectsNan(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Dữ liệu hóa đơn không hợp lệ'
        );

        $this->billingService->calculatePayableAmount(NAN, 0);
    }

    public function testInv08RejectsInfinity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Dữ liệu hóa đơn không hợp lệ'
        );

        $this->billingService->calculatePayableAmount(INF, 0);
    }

    public function testInv09RejectsNumericString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Dữ liệu hóa đơn không hợp lệ'
        );

        $this->billingService->calculatePayableAmount('220000', 0);
    }

        public function testInv10CreatesUnpaidInvoice(): void
    {
        $invoiceDAO = $this->createMock(InvoiceDAO::class);

        $invoiceDAO
            ->expects($this->once())
            ->method('createInvoice')
            ->with(
                12,
                220000,
                'UNPAID'
            )
            ->willReturn(501);

        $billingService = new BillingService($invoiceDAO);

        $result = $billingService->createInvoice(12, 220000);

        $this->assertSame(501, $result);
    }
}