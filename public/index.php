<?php
// public/index.php
// Nạp file autoload của Composer

session_start();

define('PROJECT_ROOT', dirname(__DIR__));

require_once PROJECT_ROOT . '/vendor/autoload.php';

use Luongtrieuvi\Bai01QuanlySv\Controllers\UserController;
use Luongtrieuvi\Bai01QuanlySv\Controllers\SinhvienController;
use Luongtrieuvi\Bai01QuanlySv\Controllers\PageController;

$action = $_GET['action'] ?? 'index';
// Danh sách các action KHÔNG yêu cầu đăng nhập
$public_actions = [
    'login', // Hiển thị form đăng nhập
    'do_login', // Xử lý logic đăng nhập
    'register', // Hiển thị form đăng ký
    'do_register', // Xử lý logic đăng ký
    'verify', // Xử lý link xác nhận email
    'contact', // Hiển thị form liên hệ
    'submit_contact' // Xử lý logic gửi liên hệ
];

// --- TRẠM KIỂM SOÁT BẢO MẬT ---
// Nếu action KHÔNG nằm trong danh sách public VÀ người dùng CHƯA đăng nhập
if (
    !in_array($action, $public_actions) &&
    !isset($_SESSION['user_id'])
) {
    // Ghi lại lỗi (tùy chọn, dùng FlashMessage)
    // App\Core\FlashMessage::set('login_form', 'Vui lòng đăng nhập để tiếp tục.', 'error');
    // Chuyển hướng về trang đăng nhập
    header('Location: index.php?action=login');
    exit(); // Dừng thực thi ngay lập tức
}

// Danh sách các action được bảo vệ (yêu cầu đăng nhập)
$protected_actions = [
    'index',
    'edit',
    'update',
    'delete',
    'add',
    'dashboard',
    'detail',
    'change_password',
    'do_change_password',
];
if (
    in_array($action, $protected_actions) &&
    !isset($_SESSION['user_id'])
) {
    header('Location: index.php?action=login');
    exit();
}

$controller = new SinhvienController();

// Danh sách các action không yêu cầu đăng nhập
$public_actions = [
    'login',
    'register',
    'do_login',
    'do_register',
    'contact',
    'submit_contact',
];
// Nếu action không nằm trong danh sách public và người dùng chưa đăng nhập thì chuyển hướng họ về trang đăng nhập
if (
    !in_array($action, $public_actions) &&
    !isset($_SESSION['user_id'])
) {
    header('Location: index.php?action=login');
    exit();
}

// Khởi tạo controller dựa trên action
if (in_array($action, [
    'login',
    'register',
    'do_login',
    'do_register',
    'logout',
    'verify',
    'change_password',
    'do_change_password',
])) {
    $controller = new UserController();
} elseif (in_array($action, ['contact', 'submit_contact'])) {
    $controller = new PageController();
} else {
    $controller = new SinhvienController();
}

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'dashboard':
        $controller->dashboard();
        break;
    case 'add':
        $controller->add();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    // Các action của UserController
    case 'login':
        $controller->showLoginForm();
        break;
    case 'do_login':
        $controller->login();
        break;
    case 'register':
        $controller->showRegisterForm();
        break;
    case 'do_register':
        $controller->register();
        break;
    case 'logout':
        $controller->logout();
        break;
    case 'contact':
        $controller->showContactForm();
        break;
    case 'submit_contact':
        $controller->submitContact();
        break;
    case 'detail':
        $controller->detail();
        break;
    case 'change_password':
        $controller->showChangePasswordForm();
        break;
    case 'do_change_password':
        $controller->handleChangePassword();
        break;
    default:
        $controller = new SinhvienController();
        $controller->index();
        break;
}
