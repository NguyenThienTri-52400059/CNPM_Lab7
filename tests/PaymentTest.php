<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PaymentTest extends TestCase
{
    private InvoiceDAO $invoiceDAO;
    private BillingService $billingService;

    protected function setUp(): void
    {
        $this->invoiceDAO =
            $this->createMock(InvoiceDAO::class);

        $this->billingService =
            new BillingService($this->invoiceDAO);
    }

    public function testPay01AcceptsValidAmount(): void
    {
        $this->assertTrue(
            $this->billingService->validatePaymentAmount(500000)
        );
    }

    public function testPay02AcceptsMinimumAmount(): void
    {
        $this->assertTrue(
            $this->billingService->validatePaymentAmount(1)
        );
    }

    public function testPay03RejectsZero(): void
    {
        $this->assertFalse(
            $this->billingService->validatePaymentAmount(0)
        );
    }

    public function testPay04RejectsNegativeAmount(): void
    {
        $this->assertFalse(
            $this->billingService->validatePaymentAmount(-100)
        );
    }

    public function testPay05RejectsNumericString(): void
    {
        $this->assertFalse(
            $this->billingService->validatePaymentAmount('500000')
        );
    }

    public function testPay06RejectsNan(): void
    {
        $this->assertFalse(
            $this->billingService->validatePaymentAmount(NAN)
        );
    }

    public function testPay07RejectsInfinity(): void
    {
        $this->assertFalse(
            $this->billingService->validatePaymentAmount(INF)
        );
    }

    public function testPay08RejectsNull(): void
    {
        $this->assertFalse(
            $this->billingService->validatePaymentAmount(null)
        );
    }

    public function testPay09ProcessesSuccessfulPayment(): void
    {
        $this->invoiceDAO
            ->expects($this->once())
            ->method('recordPayment')
            ->with(
                7,
                'CASH',
                500000,
                'SUCCESS',
                null
            )
            ->willReturn(true);

        $result = $this->billingService->processPayment(
            7,
            'CASH',
            500000,
            true
        );

        $this->assertSame('SUCCESS', $result['status']);
        $this->assertSame(
            'Thanh toán thành công, biên nhận đã được tạo.',
            $result['message']
        );
    }

    public function testPay10DoesNotSaveInvalidAmount(): void
    {
        $this->invoiceDAO
            ->expects($this->never())
            ->method('recordPayment');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Số tiền thanh toán không hợp lệ'
        );

        $this->billingService->processPayment(
            7,
            'CASH',
            0,
            true
        );
    }

    public function testPay11ThrowsWhenDaoCannotSavePayment(): void
    {
        $this->invoiceDAO
            ->method('recordPayment')
            ->willReturn(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Không thể ghi nhận giao dịch thanh toán vào hệ thống.'
        );

        $this->billingService->processPayment(
            7,
            'CASH',
            500000,
            true
        );
    }

    public function testPay12RecordsFailedTransaction(): void
    {
        $this->invoiceDAO
            ->expects($this->once())
            ->method('recordPayment')
            ->with(
                7,
                'ONLINE',
                500000,
                'FAILED',
                null
            )
            ->willReturn(true);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Giao dịch thanh toán thất bại'
        );

        $this->billingService->processPayment(
            7,
            'ONLINE',
            500000,
            false
        );
    }
}