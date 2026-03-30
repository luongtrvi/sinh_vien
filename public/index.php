<?php
// public/index.php
// Nạp file autoload của Composer

session_start();

define('PROJECT_ROOT', dirname(__DIR__));

require_once PROJECT_ROOT . '/vendor/autoload.php';

use Luongtrieuvi\Bai01QuanlySv\Controllers\UserController;
use Luongtrieuvi\Bai01QuanlySv\Controllers\SinhvienController;
use Luongtrieuvi\Bai01QuanlySv\Controllers\PageController;

// Simple Router
$action = $_GET['action'] ?? 'index';

// Danh sách các action không yêu cầu đăng nhập
// $public_actions = ['login', 'register', 'do_login', 'do_register'];
// Nếu action không nằm trong danh sách public và người dùng chưa đăng nhập
// thì chuyển hướng họ về trang đăng nhập
// if (!in_array($action, $public_actions) && !isset($_SESSION['user_id'])) {
// header('Location: index.php?action=login');
// exit();
// }

// Danh sách các action được bảo vệ (yêu cầu đăng nhập)
$protected_actions = [
    'index',
    'edit',
    'update',
    'delete',
    'add',
    'dashboard',
    'detail',
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
    'logout'
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
    default:
        $controller = new SinhvienController();
        $controller->index();
        break;
}
