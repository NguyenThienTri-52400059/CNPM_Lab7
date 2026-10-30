// proof of concept
<?php
// service/OrderService.php

class OrderService {
    private $orderDAO;
    private $menuDAO;

    public function __construct($orderDAO, $menuDAO) {
        $this->orderDAO = $orderDAO;
        $this->menuDAO = $menuDAO;
    }

    /**
     * Xử lý nghiệp vụ thêm món vào đơn hàng
     */
    public function addItemsToOrder($orderId, $items) {
        foreach ($items as $item) {
            $menuItemId = $item['menu_item_id'];
            $quantity = $item['quantity'];

            // Kiểm tra quy tắc BR03: Số lượng phải > 0
            if ($quantity <= 0) {
                throw new Exception("Số lượng của mỗi món phải lớn hơn 0 (BR03).");
            }

            // Kiểm tra món ăn trong CSDL
            $menuItem = $this->menuDAO->getMenuItemById($menuItemId);
            if (!$menuItem) {
                throw new Exception("Món ăn không tồn tại trong thực đơn.");
            }

            // Kiểm tra quy tắc BR02: Chỉ món còn phục vụ (AVAILABLE) mới được thêm
            if ($menuItem['availability_status'] !== 'AVAILABLE') {
                throw new Exception("Món '{$menuItem['name']}' hiện không còn phục vụ (BR02).");
            }

            // Lấy giá tại thời điểm gọi (BR07) và tiến hành lưu chi tiết đơn
            $unitPrice = $menuItem['price'];
            $this->orderDAO->addOrderItem($orderId, $menuItemId, $quantity, $unitPrice, 'WAITING');
        }

        return true;
    }
}
?>