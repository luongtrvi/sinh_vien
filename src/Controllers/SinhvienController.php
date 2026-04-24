<?php
// src/Controllers/SinhvienController.php
namespace Luongtrieuvi\Bai01QuanlySv\Controllers;

use Luongtrieuvi\Bai01QuanlySv\Models\SinhvienModel;
use Luongtrieuvi\Bai01QuanlySv\Core\FlashMessage;
use Luongtrieuvi\Bai01QuanlySv\Core\Logger; //<-- THÊM DÒNG NÀY

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

        // --- XỬ LÝ SẮP XẾP (PHẦN MỚI) ---
        // 1. Danh sách các cột được phép sắp xếp (để bảo mật)
        $allowedSortCols = ['id', 'name', 'email', 'phone'];
        // 2. Lấy cột sắp xếp từ URL, mặc định là 'id'
        $sortby = $_GET['sortby'] ?? 'id';
        if (!in_array($sortby, $allowedSortCols)) {
            $sortby = 'id'; // Nếu cột không hợp lệ, quay về mặc định
        }
        // 3. Lấy thứ tự sắp xếp, mặc định là 'desc' (mới nhất lên đầu)
        $order = $_GET['order'] ?? 'desc';
        $order = strtolower($order) === 'asc' ? 'asc' : 'desc'; // Chỉ cho phép 'asc' hoặc 'desc'

        // 4. Tính toán thứ tự đảo ngược (để dùng trong View)
        $nextOrder = ($order === 'asc' ? 'desc' : 'asc');
        // --- GỌI MODEL (cập nhật) ---
        // Truyền thêm $sortby và $order vào Model
        $result = $this->sinhvienModel->getStudents(
            $keyword,

            $recordsPerPage,
            $offset,
            $sortby,
            $order
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
            $course = $_POST['course'] ?? null;
            $class_name = $_POST['class_name'] ?? null;
            $major = $_POST['major'] ?? null;
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
                    $avatar,
                    $course,
                    $class_name,
                    $major
                );
                Logger::log('create_student', "Student Name: " . $name); // <-- GHI LOG
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
            $course = $_POST['course'] ?? null;
            $class_name = $_POST['class_name'] ?? null;
            $major = $_POST['major'] ?? null;
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
                    $avatar,
                    $course,
                    $class_name,
                    $major
                );
                Logger::log('update_student', "Student ID: " . $id); // <-- GHI LOG
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
                Logger::log('delete_student', "Student ID: " . $id); // <-- GHI LOG
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

    /**
     * Hiển thị trang dashboard thống kê
     */
    public function dashboard()
    {
        // Gọi model để lấy dữ liệu thống kê
        $stats = $this->sinhvienModel->getStatistics();
        // Nạp file view và truyền biến $stats ra
        require_once __DIR__ . '/../../views/dashboard.php';
    }

    /**
     * HÀM MỚI: Xử lý xuất danh sách sinh viên ra file CSV
     */
    public function exportCsv()
    {
        // 1. Lấy từ khóa tìm kiếm (nếu có)
        $keyword = $_GET['keyword'] ?? null;
        // 2. Lấy toàn bộ dữ liệu từ Model
        $students = $this->sinhvienModel->getStudentsForExport($keyword);
        // 3. Đặt tên file
        $filename = "danh-sach-sinh-vien-" . date('Y-m-d') . ".csv";
        // 4. Thiết lập HTTP Headers để trình duyệt hiểu là file tải về
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename .

            '"');

        // 5. Mở luồng ghi "php://output"
        // Luồng này cho phép ghi dữ liệu trực tiếp vào body của response
        $output = fopen('php://output', 'w');
        // 6. (QUAN TRỌNG) Thêm UTF-8 BOM
        // Bước này rất cần thiết để Microsoft Excel đọc file CSV có tiếng Việt

        fputs($output, "\xEF\xBB\xBF");
        // 7. Ghi dòng tiêu đề (Header) của file CSV
        fputcsv($output, [
            'ID',
            'Họ và Tên',
            'Email',
            'Số điện thoại',
            'Khóa học',
            'Lớp',
            'Ngành học'
        ]);
        // 8. Lặp qua dữ liệu và ghi từng dòng

        foreach ($students as $student) {
            fputcsv($output, [
                $student['id'],
                $student['name'],
                $student['email'],
                $student['phone'],
                $student['course'] ?? '', // Dùng ?? '' để tránh lỗi nếu giá trị là NULL

                $student['class_name'] ?? '',
                $student['major'] ?? ''
            ]);
        }
        // 9. Đóng luồng
        fclose($output);
        // 10. Dừng chương trình
        // Rất quan trọng, để ngăn không cho bất kỳ mã HTML/View nào
        // bị chèn vào sau nội dung file CSV.
        exit();
    }

    /**
     * HÀM MỚI: Hiển thị trang chi tiết sinh viên
     */
    public function detail()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            FlashMessage::set('student_action', 'ID sinh viên không hợp lệ.', 'error');
            header('Location: index.php');
            exit();
        }
        // Tái sử dụng hàm getStudentById đã có
        $student = $this->sinhvienModel->getStudentById($id);
        if (!$student) {
            FlashMessage::set('student_action', 'Không tìm thấy sinh viên.', 'error');
            header('Location: index.php');
            exit();
        }
        // Nạp file view chi tiết và truyền dữ liệu sinh viên
        require_once PROJECT_ROOT . '/src/views/detail.php';
    }
}
