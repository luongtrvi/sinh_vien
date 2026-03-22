<?php
// src/Models/SinhvienModel.php
namespace Luongtrieuvi\Bai01QuanlySv\Models;

use Luongtrieuvi\Bai01QuanlySv\Database;
use PDO;

class SinhvienModel
{
    private $conn;
    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }
    // Lấy tất cả sinh viên
    public function getAllStudents($keyword = null)
    {
        // Bắt đầu câu lệnh SQL
        $sql = "SELECT * FROM students";
        // Nếu có từ khóa tìm kiếm, thêm điều kiện WHERE
        if ($keyword) {
            // Sử dụng LIKE để tìm kiếm gần đúng
            $sql .= " WHERE name LIKE :keyword";
        }
        $sql .= " ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        // Nếu có từ khóa, gán giá trị cho tham số :keyword
        if ($keyword) {
            // Thêm dấu % vào hai bên từ khóa để tìm kiếm bất kỳ chuỗi nào chứa từ khóa đó
            $searchKeyword = "%{$keyword}%";
            $stmt->bindParam(':keyword', $searchKeyword);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Thêm sinh viên mới
    public function addStudent($name, $email, $phone)
    {
        $stmt = $this->conn->prepare("INSERT INTO students (name, email, phone) VALUES (:name, :email, :phone)");
        // Làm sạch dữ liệu
        $name = htmlspecialchars(strip_tags($name));
        $email = htmlspecialchars(strip_tags($email));
        $phone = htmlspecialchars(strip_tags($phone));
        // Gán dữ liệu vào câu lệnh
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Lấy thông tin sinh viên theo ID
    public function getStudentById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin sinh viên
    public function updateStudent($id, $name, $email, $phone)
    {
        $stmt = $this->conn->prepare(
            "UPDATE students SET name = :name, email = :email, phone = :phone WHERE id = :id"
        );
        // Làm sạch dữ liệu
        $name = htmlspecialchars(strip_tags($name));
        $email = htmlspecialchars(strip_tags($email));
        $phone = htmlspecialchars(strip_tags($phone));
        // Gán dữ liệu vào câu lệnh
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Xóa sinh viên
    public function deleteStudent($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM students WHERE id = :id");
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getStudents($keyword = null, $limit = 5, $offset = 0)
    {
        // --- BƯỚC 1: ĐẾM ---
        $sqlCount = "SELECT COUNT(*) FROM students";
        $params = [];

        if ($keyword) {
            $sqlCount .= " WHERE name LIKE :keyword OR email LIKE :keyword OR phone LIKE :keyword";
            $params[':keyword'] = "%{$keyword}%";
        }

        $stmtCount = $this->conn->prepare($sqlCount);
        $stmtCount->execute($params);
        $totalRecords = $stmtCount->fetchColumn();

        // --- BƯỚC 2: LẤY DATA ---
        $sqlData = "SELECT * FROM students";

        if ($keyword) {
            $sqlData .= " WHERE name LIKE :keyword OR email LIKE :keyword OR phone LIKE :keyword";
        }

        // ✅ QUAN TRỌNG: ORDER BY trước LIMIT
        $sqlData .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";

        $stmtData = $this->conn->prepare($sqlData);

        // ✅ bindValue thay vì bindParam (tránh lỗi tham chiếu)
        if ($keyword) {
            $stmtData->bindValue(':keyword', "%{$keyword}%", PDO::PARAM_STR);
        }

        $stmtData->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmtData->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmtData->execute();
        $students = $stmtData->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $students,
            'total' => $totalRecords
        ];
    }
}
