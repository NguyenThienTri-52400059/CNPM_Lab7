# Báo cáo Lab 09 Unit Testing

## 1. Thông tin chung

- Đề tài: Hệ thống quản lý nhà hàng
- Ngôn ngữ: PHP 8.2
- Công cụ quản lý thư viện: Composer
- Framework kiểm thử: PHPUnit 11
- Công cụ Code Coverage: Xdebug
- Nhánh thực hiện: `duc/lab09-unit-testing`

## 2. Mục tiêu

Bài thực hành áp dụng Unit Testing cho các nghiệp vụ quan trọng của hệ thống quản lý nhà hàng:

1. Tính số tiền hóa đơn sau khi giảm giá.
2. Kiểm tra tính hợp lệ của yêu cầu đặt bàn.
3. Kiểm tra số tiền và kết quả thanh toán.
4. Đo Code Coverage và bảo đảm Lines Coverage đạt tối thiểu 70%.
5. Chứng minh Unit Test có thể phát hiện lỗi trong logic chương trình.

## 3. Phạm vi kiểm thử

Các lớp được kiểm thử:

- `BillingService`: tính tiền hóa đơn, lập hóa đơn và xử lý thanh toán.
- `ReservationService`: kiểm tra dữ liệu, sức chứa và thời gian đặt bàn.
- `InvoiceDAO` và `ReservationDAO` được thay thế bằng Mock Object trong Unit Test.
- Unit Test không kết nối MySQL và không thay đổi dữ liệu thật.

## 4. Cấu trúc kiểm thử

```text
tests/
├── BillingServiceTest.php
├── PaymentTest.php
└── ReservationServiceTest.php

## Minh chứng kiểm thử

### Unit Test phát hiện lỗi tính tiền

![Unit Test phát hiện lỗi tính tiền](evidence/lab09/01-test-failure.png)

Test Case INV01 phát hiện kết quả thực tế 240.000 đồng không khớp kết quả mong đợi 200.000 đồng.

### Toàn bộ Unit Test đạt

![Toàn bộ Unit Test đạt](evidence/lab09/02-all-tests-pass.png)

Kết quả cuối cùng có 37 test và 83 assertions, không có lỗi hoặc test thất bại.

### Báo cáo Code Coverage

![Báo cáo Code Coverage](evidence/lab09/03-code-coverage.png)

Code Coverage đạt 100% Classes, Methods và Lines trong phạm vi kiểm thử.

## 8. Kết quả tổng hợp

- Tổng số test: 37
- Tổng số assertions: 83
- Errors: 0
- Failures: 0
- Trạng thái: PASS