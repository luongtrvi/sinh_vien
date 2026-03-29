<?php
// src/Controllers/SinhvienController.php
namespace Luongtrieuvi\Bai01QuanlySv\Controllers;

use Luongtrieuvi\Bai01QuanlySv\Models\SinhvienModel;
use Luongtrieuvi\Bai01QuanlySv\Core\FlashMessage;

class SinhvienController
{
    private $sinhvienModel;
    public function __construct()
    {
        $this->sinhvienModel = new SinhvienModel();
    }
    // HÀM HELPER ĐỂ XỬ LÝ UPLOAD
    private function handleUpload($file)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Lỗi upload file.'];
        }
        $targetDir = PROJECT_ROOT . "/public/uploads/avatars/";
        $fileName = uniqid() . '-' . basename($file["name"]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo(
            $targetFile,

            PATHINFO_EXTENSION
        ));

        // Kiểm tra định dạng file
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedTypes)) {
            return ['error' => 'Chỉ cho phép upload file ảnh (JPG, JPEG, PNG, GIF).'];
        }
        // Di chuyển file
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return ['filename' => $fileName];
        } else {
            return ['error' => 'Đã có lỗi xảy ra khi upload file.'];
        }
    }

    // Hiển thị danh sách sinh viên
    // Cập nhật hàm index để xử lý tìm kiếm
    public function index()
    {
        // --- CÀI ĐẶT CÁC BIẾN PHÂN TRANG ---
        $recordsPerPage = 5; // Số sinh viên mỗi trang
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        $offset = ($currentPage - 1) * $recordsPerPage;
        // --- XỬ LÝ TÌM KIẾM ---
        $keyword = $_GET['keyword'] ?? null;
        // --- GỌI MODEL ---
        $result = $this->sinhvienModel->getStudents(
            $keyword,
            $recordsPerPage,
            $offset
        );
        $students = $result['data'];
        $totalRecords = $result['total'];
        // --- TÍNH TOÁN SỐ TRANG ---
        $totalPages = ceil($totalRecords / $recordsPerPage);
        // --- NẠP VIEW VÀ TRUYỀN DỮ LIỆU ---
        require_once __DIR__ . '/../../views/sinhvien_list.php';
    }
    // Xử lý thêm sinh viên
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $avatar = null;
            // Xử lý upload file
            if (
                isset($_FILES['avatar']) &&
                $_FILES['avatar']['error'] == 0
            ) {
                $uploadResult =
                    $this->handleUpload($_FILES['avatar']);

                if (isset($uploadResult['filename'])) {
                    $avatar = $uploadResult['filename'];
                } else {
                    FlashMessage::set(
                        'student_action',
                        $uploadResult['error'],
                        'error'
                    );
                    header('Location: index.php');
                    exit();
                }
            }
            if (!empty($name) && !empty($email) && !empty($phone)) {
                $this->sinhvienModel->addStudent(
                    $name,
                    $email,
                    $phone,
                    $avatar
                );
                // Đặt thông báo thành công
                FlashMessage::set('student_action', 'Thêm sinh viên thành công!', 'success');
            } else {
                // Đặt thông báo lỗi
                FlashMessage::set('student_action', 'Thêm sinh viên thất bại!', 'error');
            }
        }
        // Sau khi thêm, chuyển hướng về trang danh sách
        header('Location: index.php');
        exit();
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            // Nếu không có id, chuyển hướng về trang chủ
            header('Location: index.php');
            exit();
        }
        // Gọi model để lấy thông tin sinh viên
        $student = $this->sinhvienModel->getStudentById($id);
        // Nạp file view để hiển thị form
        require_once __DIR__ . '/../../views/sinhvien_edit.php';
    }

    // PHƯƠNG THỨC MỚI: Xử lý cập nhật dữ liệu (bài 03)
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $oldAvatar = $_POST['old_avatar'] ?? null;
            $avatar = $oldAvatar;
            // Xử lý upload file mới nếu có
            if (
                isset($_FILES['avatar']) &&
                $_FILES['avatar']['error'] == 0
            ) {
                $uploadResult =
                    $this->handleUpload($_FILES['avatar']);
                if (isset($uploadResult['filename'])) {
                    $avatar = $uploadResult['filename'];
                    // Xóa file ảnh cũ nếu upload thành công ảnh mới
                    if ($oldAvatar && file_exists(PROJECT_ROOT .
                        "/public/uploads/avatars/" . $oldAvatar)) {
                        unlink(PROJECT_ROOT .
                            "/public/uploads/avatars/" . $oldAvatar);
                    }
                } else {
                    FlashMessage::set(
                        'student_action',
                        $uploadResult['error'],
                        'error'
                    );
                    header('Location: index.php');
                    exit();
                }
            }
            if (
                $id && !empty($name) && !empty($email) &&
                !empty($phone)
            ) {
                $this->sinhvienModel->updateStudent(
                    $id,
                    $name,
                    $email,
                    $phone,
                    $avatar
                );
                FlashMessage::set('student_action', 'Cập nhật thông tin thành công!', 'success');
            } else {
                FlashMessage::set('student_action', 'Cập nhật thất bại!', 'error');
            }
        }
        // Sau khi cập nhật, chuyển hướng về trang danh sách
        header('Location: index.php');
        exit();
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            // Trước khi xóa record, lấy thông tin sinh viên để xóa file
            $student = $this->sinhvienModel->getStudentById($id);
            if ($student && !empty($student['avatar'])) {
                $avatarPath = PROJECT_ROOT .
                    "/public/uploads/avatars/" . $student['avatar'];
                if (file_exists($avatarPath)) {
                    unlink($avatarPath); // Xóa file vật lý
                }
            }
            if ($this->sinhvienModel->deleteStudent($id)) {
                FlashMessage::set('student_action', 'Xóa sinh viên thành công!', 'success');
            } else {
                FlashMessage::set(
                    'student_action',
                    'Xóa thất bại!',
                    'error'
                );
            }
        }
        // Gọi model để thực hiện xóa
        // Sau khi xóa, chuyển hướng người dùng về lại trang danh sách
        header('Location: index.php');
        exit();
    }
}
