# CNPM_Lab7

## Yêu cầu:
STT | Sản phẩm | Minh chứng
---|---|---
1| Github repository | Đường dẫn repository
2| Sơ đồ Git Flow | Hình ảnh hoặc tài liệu mô tả nhánh
3| Sekeleton code | Cấu trúc thư mục và mã ngồn ban đầu
4| Lịch sử git | Các commit của từng thành viên
5| Pull request | Đường dẫn pull request đã tạo
6| Code review | Nhận xét, phản hồi và kết quả xử lý
7| README | Hướng dẫn cài đặt và chạy dự án
8| Báo cáo Lab07 | Tóm tắt công việc, khó khăn và cách giải quyết

### 1. Link:

https://github.com/NguyenThienTri-52400059/CNPM_Lab7.git

### 2. Git Flow

1. Mục đích & Cấu trúc các Nhánh (Branches)
main (Production Branch): Nhánh chính chứa mã nguồn ổn định nhất, đã hoàn thành và sẵn sàng demo/nghiệm thu. Chỉ hợp nhất (merge) từ nhánh development khi đã kiểm thử xong.

development (Integration Branch): Nhánh tích hợp chính. Nơi tập hợp tất cả các tính năng/tầng kiến trúc sau khi hoàn thành độc lập để tiến hành kiểm thử tích hợp.

Các nhánh tầng tính năng (Feature / Layer Branches): Tách ra từ development để triển khai từng phần công việc cụ thể:
- database: Tạo script CSDL (schema.sql) và file kết nối config/db.php.   
- DAO: Viết các lớp Data Access Object (UserDAO, MenuDAO, TableDAO...).   
- service: Cài đặt tầng xử lý quy tắc nghiệp vụ (AuthService, OrderService...).   
- controller: Xây dựng tầng điều phối xử lý request (AuthController, OrderController...).   
- view: Phát triển giao diện HTML/PHP/JS cho các màn hình (UI-01 đến UI-06). 


2. Quy trình làm việc thực tế (Workflow Rules)
- Khởi tạo công việc: Từ development, checkout ra nhánh tương ứng (ví dụ: git checkout -b DAO).
- Lập trình & Commit: Thực hiện code và commit thường xuyên với message rõ ràng (ví dụ: git commit -m "feat: Add MenuDAO class").
- Tích hợp (Merge): Sau khi hoàn thành và kiểm thử độc lập nhánh tầng đó, thực hiện Pull Request (PR) hoặc merge ngược về nhánh development.
- Phát hành (Release): Khi tất cả các nhánh tầng (database, DAO, service, controller, view) đã tích hợp hoàn chỉnh và chạy ổn định trên development, tiến hành merge development vào main.


### 3. Skeketon code

RestaurantManagement-app/
├── assets
|   ├── css/
|   ├── image/
|   └── js/
├── config/
|   └── db.php
├── controller/
|   ├── AuthController.php
|   ├── OrderController.php
|   └── PaymentController.php
├── dao/
|   ├── InvoiceDAO.php
|   ├── MenuDAO.php
|   ├── OrderDAO.php
|   ├── ReservationDAO.php
|   ├── TableDAO.php
|   └── UserDAO.php
├── service/
|   ├── AuthService.php
|   ├── BillingService.php
|   ├── OrderService.php
|   └── ReservationService.php
└── view/
    ├── admin.php
    ├── cashier.php
    ├── customer_order.php
    ├── kitchen.php
    └── login.php
index.php

### 4. Lịch sử git
None, git repository vừa được tạo gần đây

### 5. Đường dẫn pull request
https://github.com/NguyenThienTri-52400059/CNPM_Lab7/pull/1

### 6. Code review
Code mới tạo làm placeholder, chưa hoàn chỉnh.