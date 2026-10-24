// proof of concept
<?php
// views/cashier.php
session_start();
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['CASHIER', 'CUSTOMER'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa Đơn Thanh Toán (UI-05)</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="cashier-container">
        <h2>HÓA ĐƠN THANH TOÁN</h2>

        <div class="invoice-box">
            <p><strong>Mã đơn:</strong> ORD-001 &nbsp;|&nbsp; <strong>Bàn:</strong> B05</p>
            
            <table class="invoice-table">
                <tr>
                    <td>Cơm chiên × 2</td>
                    <td>100.000đ</td>
                </tr>
                <tr>
                    <td>Nước ép × 1</td>
                    <td>30.000đ</td>
                </tr>
            </table>

            <hr>
            <div class="total-amount-section">
                <h3>Tổng thanh toán: 130.000₫</h3>
            </div>

            <!-- Form gửi dữ liệu thanh toán đến PaymentController -->
            <form action="../controllers/PaymentController.php" method="POST">
                <input type="hidden" name="invoice_id" value="1">
                <input type="hidden" name="amount" value="130000">

                <div class="payment-methods">
                    <label><strong>Phương thức thanh toán:</strong></label><br>
                    <label>
                        <input type="radio" name="method" value="CASH" checked> Tiền mặt
                    </label><br>
                    <label>
                        <input type="radio" name="method" value="ONLINE"> Thanh toán trực tuyến (Cổng thanh toán)
                    </label>
                </div>

                <button type="submit" class="btn-primary" style="margin-top: 20px;">XÁC NHẬN THANH TOÁN</button>
            </form>
        </div>
    </div>
</body>
</html>