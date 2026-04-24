<?php

use Luongtrieuvi\Bai01QuanlySv\Core\FlashMessage;

require_once __DIR__ . '/../views/layout/header.php';

?>

<body>
    <div class="container">
        <h1>Chỉnh sửa thông tin sinh viên</h1>
        <div>
            <p>Ảnh đại diện hiện tại:</p>
            <img src="uploads/avatars/<?php echo $student['avatar'] ??
                                            'default-avatar.png'; ?>" alt="Current Avatar" width="100" height="100">
        </div>
        <form action="index.php?action=update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
            <input type="hidden" name="old_avatar" value="<?php echo $student['avatar'] ?? ''; ?>">
            <label for="name">Họ và Tên:</label>
            <input type="text" id="name" name="name"
                value="<?php echo htmlspecialchars($student['name']); ?>"
                required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email"
                value="<?php echo htmlspecialchars($student['email']); ?>"
                required>

            <label for="phone">Số điện thoại:</label>
            <input type="text" id="phone" name="phone"
                value="<?php echo htmlspecialchars($student['phone']); ?>"
                required>

            <label for="course">Khóa học:</label>
            <input type="text" id="course" name="course"
                value="<?php echo htmlspecialchars($student['course'] ?? ''); ?>"
                placeholder="vd: K17">

            <label for="class_name">Tên lớp:</label>
            <input type="text" id="class_name" name="class_name"
                value="<?php echo htmlspecialchars($student['class_name'] ?? ''); ?>"
                placeholder="vd: CNTT01">

            <label for="major">Ngành học:</label>
            <input type="text" id="major" name="major"
                value="<?php echo htmlspecialchars($student['major'] ?? ''); ?>"
                placeholder="vd: Công nghệ thông tin">

            <label for="avatar">Thay đổi ảnh đại diện:</label>
            <input type="file" id="avatar" name="avatar">

            <button type="submit">Lưu thay đổi</button>
        </form>
        <p><a href="index.php">Quay về danh sách</a></p>
    </div>
</body>
<?php
// NẠP FOOTER
require_once __DIR__ . '/../views/layout/footer.php';
?>
