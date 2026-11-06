<?php

declare(strict_types=1);

class BillingService
{
    private $invoiceDAO;

    public function __construct($invoiceDAO)
    {
        $this->invoiceDAO = $invoiceDAO;
    }

    /**
     * Tính số tiền khách phải thanh toán sau khi giảm giá.
     *
     * @param mixed $totalAmount Tổng tiền món ăn.
     * @param mixed $discountAmount Số tiền được giảm.
     *
     * @return int|float Số tiền phải thanh toán.
     */
    public function calculatePayableAmount($totalAmount, $discountAmount = 0)
    {
        $totalIsNumber = is_int($totalAmount) || is_float($totalAmount);
        $discountIsNumber = is_int($discountAmount) || is_float($discountAmount);

        if (
            !$totalIsNumber ||
            !$discountIsNumber ||
            !is_finite((float) $totalAmount) ||
            !is_finite((float) $discountAmount) ||
            $totalAmount < 0 ||
            $discountAmount < 0 ||
            $discountAmount > $totalAmount
        ) {
            throw new InvalidArgumentException(
                'Dữ liệu hóa đơn không hợp lệ'
            );
        }

        return $totalAmount - $discountAmount;
    }

    /**
     * Lập hóa đơn cho đơn hàng.
     */
    public function createInvoice($orderId, $totalAmount)
    {
        // Gọi hàm tính tiền để kiểm tra tổng hóa đơn.
        $validatedAmount = $this->calculatePayableAmount($totalAmount);

        return $this->invoiceDAO->createInvoice(
            $orderId,
            $validatedAmount,
            'UNPAID'
        );
    }

    /**
     * Ghi nhận giao dịch thanh toán.
     */
    public function processPayment(
        $invoiceId,
        $method,
        $amount,
        $isSuccess,
        $transactionCode = null
    ) {
        $paymentStatus = $isSuccess ? 'SUCCESS' : 'FAILED';

        $result = $this->invoiceDAO->recordPayment(
            $invoiceId,
            $method,
            $amount,
            $paymentStatus,
            $transactionCode
        );

        if (!$result) {
            throw new RuntimeException(
                'Không thể ghi nhận giao dịch thanh toán vào hệ thống.'
            );
        }

        if (!$isSuccess) {
            throw new RuntimeException(
                'Giao dịch thanh toán thất bại. Hóa đơn vẫn chưa thanh toán.'
            );
        }

        return [
            'status' => 'SUCCESS',
            'message' => 'Thanh toán thành công, biên nhận đã được tạo.',
        ];
    }
}