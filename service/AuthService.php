// proof of concept
<?php
// service/AuthService.php

class AuthService {
    private $userDAO;

    public function __construct($userDAO) {
        $this->userDAO = $userDAO;
    }

    /**
     * Xử lý nghiệp vụ đăng nhập người dùng (UI-01)
     */
    public function login($email, $password) {
        // Lấy thông tin tài khoản từ DAO dựa trên email[cite: 7]
        $user = $this->userDAO->getUserByEmail($email);

        if (!$user) {
            throw new Exception("Tài khoản hoặc mật khẩu không chính xác.");
        }

        // Kiểm tra trạng thái tài khoản (Ví dụ: phải là ACTIVE mới được đăng nhập)[cite: 7, 18]
        if ($user['status'] !== 'ACTIVE') {
            throw new Exception("Tài khoản của bạn đang bị khóa hoặc không hoạt động (BR09).");
        }

        // Kiểm tra mật khẩu sử dụng password_verify khớp với password_hash trong CSDL[cite: 7]
        if (!password_verify($password, $user['password_hash'])) {
            throw new Exception("Tài khoản hoặc mật khẩu không chính xác.");
        }

        // Đăng nhập thành công, loại bỏ password_hash trước khi trả về thông tin phiên làm việc
        unset($user['password_hash']);
        return $user;
    }
}
?>