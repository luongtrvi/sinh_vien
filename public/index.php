<?php
// public/index.php
// Nạp file autoload của Composer
require_once __DIR__ . '/../vendor/autoload.php';

use Luongtrieuvi\Bai01QuanlySv\Controllers\SinhvienController;
// Simple Router
$action = $_GET['action'] ?? 'index';
$controller = new SinhvienController();
switch ($action) {
    case 'add':
        $controller->add();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'update':
        $controller->update();
        break;
    default:
        $controller->index();
        break;
}
