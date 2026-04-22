<?php
// src/Controllers/UserController.php
namespace Luongtrieuvi\Bai01QuanlySv\Controllers;

use Luongtrieuvi\Bai01QuanlySv\Models\UserModel;
use Luongtrieuvi\Bai01QuanlySv\Core\Mailer;
use Luongtrieuvi\Bai01QuanlySv\Core\FlashMessage;

class UserController
{
    private $userModel;
    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    //hàm gởi mail
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $email = $_POST['email'] ?? ''; // Giả sử có thu thập email khi đăng ký

            // Nếu chưa, hãy thêm trường 'email' vào bảng users và form đăng ký

            // --- GIẢ ĐỊNH: Bảng 'users' đã có cột 'email' ---
            // Nếu chưa có, hãy chạy lệnh SQL này trong phpMyAdmin:
            // ALTER TABLE `users` ADD `email` VARCHAR(100) NOT NULL AFTER `username`;

            // Và thêm <input type="email" name="email"> vào views/register.php

            if (
                empty($name) || empty($username) || empty($password) ||

                empty($email)
            ) {

                $error = "Vui lòng điền đầy đủ thông tin.";
                require_once PROJECT_ROOT . '/src/views/register.php';
                return;
            }
            // Truyền email vào hàm createUser
            $result = $this->userModel->createUser(
                $name,
                $username,
                $password,
                $email
            );
            if ($result) {
                // Đăng ký thành công, TIẾN HÀNH GỬI EMAIL
                $subject = "Chào mừng bạn đến với Ứng dụng Quản lý Sinh viên!";
                $body = "<h1>Chào mừng, " . htmlspecialchars($name) . "!</h1>
                    <p>Cảm ơn bạn đã đăng ký tài khoản tại ứng dụng của chúng tôi.</p>
                    <p>Tên đăng nhập của bạn là: <strong>" .
                    htmlspecialchars($username) . "</strong></p>
                    <p>Trân trọng,<br>Ban quản trị</p>";
                // Gọi hàm gửi mail
                if (Mailer::send($email, $name, $subject, $body)) {
                    FlashMessage::set('login_form', 'Đăng ký thành công! Vui lòng kiểm tra email để xác nhận.', 'success');
                } else {
                    FlashMessage::set('login_form', 'Đăng ký thành công, nhưng không thể gửi email xác nhận.', 'error');
                }
                // Chuyển hướng đến trang đăng nhập
                header('Location: index.php?action=login');
                exit();
            } else {
                // Tên đăng nhập đã tồn tại
                $error = "Tên đăng nhập hoặc email đã tồn tại. Vui lòng chọn tên khác.";
                require_once PROJECT_ROOT . '/src/views/register.php';
            }
        }
    }

    // Hiển thị form đăng ký
    public function showRegisterForm()
    {
        require_once __DIR__ . '/../../views/dangky.php';
    }

    // HÀM MỚI: Hiển thị form đăng nhập
    public function showLoginForm()
    {
        require_once __DIR__ . '/../../views/dangnhap.php';
    }
    // HÀM MỚI: Xử lý logic đăng nhập
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            if (empty($username) || empty($password)) {
                $error = "Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.";
                require_once PROJECT_ROOT . '/src/views/dangnhap.php';
                return;
            }
            // Tìm người dùng trong CSDL
            $user = $this->userModel->findUserByUsername($username);
            // --- BƯỚC BẢO MẬT QUAN TRỌNG NHẤT ---
            // So sánh mật khẩu người dùng nhập với mật khẩu đã
            if ($user && password_verify(
                $password,
                $user['password']
            )) {
                // Mật khẩu chính xác, đăng nhập thành công
                // Lưu thông tin người dùng vào Session để ghi
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                // Chuyển hướng đến trang quản lý sinh viên
                header('Location: index.php');
                exit();
            } else {
                // Tên đăng nhập hoặc mật khẩu không đúng
                $error = "Tên đăng nhập hoặc mật khẩu không chính xác.";
                require_once __DIR__  . '/../../views/dangnhap.php';
            }
        }
    }

    // HÀM MỚI: Xử lý đăng xuất
    public function logout()
    {
        // Hủy tất cả các biến session.
        $_SESSION = [];
        // Nếu muốn hủy session hoàn toàn, hãy xóa cả cookie
        // Lưu ý: Điều này sẽ phá hủy session, và không chỉ dữ liệu session!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        // Cuối cùng, hủy session.
        session_destroy();
        // Chuyển hướng về trang đăng nhập
        header('Location: index.php?action=login');
        exit();
    }

    /**
     * HÀM MỚI: Hiển thị form đổi mật khẩu
     */
    public function showChangePasswordForm()
    {
        require_once __DIR__ . '/../../views/change_password.php';
    }
    /**
     * HÀM MỚI: Xử lý logic đổi mật khẩu
     */
    public function handleChangePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old_password = $_POST['old_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $userId = $_SESSION['user_id']; // Lấy ID từ user đang đăng nhập

            // 1. Kiểm tra các trường có trống không
            if (
                empty($old_password) || empty($new_password) ||

                empty($confirm_password)
            ) {

                FlashMessage::set('change_pass_form', 'Vui lòng điền đầy

đủ thông tin.', 'error');

                header('Location: index.php?action=change_password');
                exit();
            }
            // 2. Kiểm tra mật khẩu mới có khớp không
            if ($new_password !== $confirm_password) {
                FlashMessage::set('change_pass_form', 'Mật khẩu mới và xác

nhận mật khẩu không khớp.', 'error');

                header('Location: index.php?action=change_password');
                exit();
            }
            // 3. Kiểm tra mật khẩu cũ có đúng không
            $user = $this->userModel->findUserById($userId);
            if (!$user || !password_verify(
                $old_password,

                $user['password']
            )) {

                FlashMessage::set('change_pass_form', 'Mật khẩu cũ không

chính xác.', 'error');

                header('Location: index.php?action=change_password');

                exit();
            }
            // 4. Nếu mọi thứ OK, cập nhật mật khẩu mới
            if ($this->userModel->updatePassword($userId, $new_password)) {

                FlashMessage::set('student_action', 'Đổi mật khẩu thành

công! Vui lòng đăng nhập lại.', 'success');

                // Đăng xuất người dùng để họ đăng nhập lại với mật khẩu mới

                $this->logoutAndRedirect();
            } else {
                FlashMessage::set('change_pass_form', 'Đã có lỗi xảy ra.

Vui lòng thử lại.', 'error');

                header('Location: index.php?action=change_password');
                exit();
            }
        }
    }
    /**
     * HÀM HELPER MỚI: Dùng riêng cho việc đổi mật khẩu
     */
    private function logoutAndRedirect()
    {
        session_unset();
        session_destroy();
        header('Location: index.php?action=login');
        exit();
    }
}
