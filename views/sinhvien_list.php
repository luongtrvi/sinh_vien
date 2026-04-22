<?php

use Luongtrieuvi\Bai01QuanlySv\Core\FlashMessage; // THÊM DÒNG NÀY VÀO ĐẦU
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,
initial-scale=1.0">
    <title>Quản lý sinh viên</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        form {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form input {
            display: block;
            margin-bottom: 10px;
            width:

                95%;
            padding: 8px;
        }

        form button {
            padding: 10px 15px;
            background-color:
                #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;

            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* views/sinhvien_list.php -> bên trong thẻ <style> */
        .flash-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #fff;
            font-weight: bold;
            opacity: 1;
            transition: opacity 0.5s ease-out;
            /* Hiệu ứng mờ dần */
        }

        .flash-success {
            background-color: #28a745;
        }

        .flash-error {
            background-color: #dc3545;
        }

        th a {
            text-decoration: none;
            color: #333;
            display: block;
            position: relative;
        }

        th a .sort-arrow {
            display: none;
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
        }

        th a.sort-asc .sort-arrow::after {
            content: " ▲";
        }

        th a.sort-desc .sort-arrow::after {
            content: " ▼";
        }

        th a.active .sort-arrow {
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="container">
        <div style="text-align: right; margin-bottom: 15px;">
            Chào mừng, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!
            <a href="index.php?action=change_password"
                style="margin-left: 15px;">Đổi mật khẩu</a>
            <a href="index.php?action=logout"
                style="margin-left: 15px;">Đăng xuất</a>
        </div>
        <?php FlashMessage::display(); // THÊM DÒNG NÀY ĐỂ HIỂN THỊ THÔNG BÁO
        ?>
    </div>
    <div class="container">
        <h1>
            <?php
            // Nếu có biến $keyword (tức là đang tìm kiếm), thì
            if (isset($keyword) && !empty($keyword)) {
                echo "Kết quả tìm kiếm cho: '" .
                    htmlspecialchars($keyword) . "'";
            } else {
                // Nếu không thì hiển thị tiêu đề mặc định
                echo "Quản Lý Sinh Viên";
            }
            ?>
        </h1>
        <form action="index.php" method="GET" style="margin-bottom: 20px;">
            <input type="text" name="keyword" placeholder="Tìm theo tên, email, sđt..."
                value="<?php echo htmlspecialchars($keyword ?? ''); ?>">
            <button type="submit">Tìm kiếm</button>
            <a href="index.php" style="padding: 8px 12px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 3px;">Reset</a>
            <a href="index.php?action=dashboard" style="padding: 8px 12px; background-color: #17a2b8; color: white; text-decoration: none; border-radius: 3px;">Xem Thống kê</a>
        </form>
        <h2>Danh sách sinh viên</h2>
        <table>
            <thead>
                <tr>
                    <th>
                        <?php
                        // Xác định thứ tự cho cột này
                        // Nếu đang sort cột này, thì dùng $nextOrder, nếu không thì dùng 'asc'
                        $currentColOrder = ($sortby === 'id') ?
                            $nextOrder : 'asc';
                        $activeClass = ($sortby === 'id') ? 'active sort-' . $order : ''; ?>
                        <a href="?keyword=<?php echo
                                            urlencode($keyword ?? ''); ?>&page=<?php echo $currentPage;
                                                                                ?>&sortby=id&order=<?php echo $currentColOrder; ?>"
                            class="<?php echo $activeClass; ?>">
                            ID <span class="sort-arrow"></span>
                        </a>
                    </th>
                    <th>Ảnh đại diện</th>
                    <th>
                        <?php

                        $currentColOrder = ($sortby === 'name') ?
                            $nextOrder : 'asc';
                        $activeClass = ($sortby === 'name') ? 'active sort-' . $order : '';
                        ?>
                        <a href="?keyword=<?php echo
                                            urlencode($keyword ?? ''); ?>&page=<?php echo $currentPage;
                                                                                ?>&sortby=name&order=<?php echo $currentColOrder; ?>"
                            class="<?php echo $activeClass; ?>">
                            Họ và Tên <span class="sort-arrow"></span>
                        </a>
                    </th>
                    <th>
                        <?php
                        $currentColOrder = ($sortby === 'email') ?
                            $nextOrder : 'asc';
                        $activeClass = ($sortby === 'email') ?
                            'active sort-' . $order : '';
                        ?>

                        <a href="?keyword=<?php echo
                                            urlencode($keyword ?? ''); ?>&page=<?php echo $currentPage;
                                                                                ?>&sortby=email&order=<?php echo $currentColOrder; ?>"
                            class="<?php echo $activeClass; ?>">
                            Email <span class="sort-arrow"></span>
                        </a>
                    </th>
                    <th>
                        <?php

                        $currentColOrder = ($sortby === 'phone') ?
                            $nextOrder : 'asc';
                        $activeClass = ($sortby === 'phone') ?
                            'active sort-' . $order : '';
                        ?>

                        <a href="?keyword=<?php echo
                                            urlencode($keyword ?? ''); ?>&page=<?php echo $currentPage;
                                                                                ?>&sortby=phone&order=<?php echo $currentColOrder; ?>"
                            class="<?php echo $activeClass; ?>">
                            Số điện thoại <span
                                class="sort-arrow"></span>
                        </a>
                    </th>
                    <th>Khóa học</th>
                    <th>Tên lớp</th>
                    <th>Nghành học</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo $student['id']; ?></td>
                        <td>
                            <?php if (!empty($student['avatar'])): ?>
                                <img src="uploads/avatars/<?php echo $student['avatar']; ?>"
                                    alt="Avatar" width="50" height="50" style="border-radius: 50%;">

                            <?php else: ?>
                                <img src="uploads/avatars/default-avatar.png" alt="Avatar"

                                    width="50" height="50" style="border-radius: 50%;">
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="index.php?action=detail&id=<?php echo $student['id']; ?>">
                                <?php echo htmlspecialchars($student['name']); ?>
                            </a>
                        </td>
                        <td><?php echo
                            htmlspecialchars($student['email']); ?></td>
                        <td><?php echo
                            htmlspecialchars($student['phone']); ?></td>
                        <td><?php echo htmlspecialchars($student['course'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($student['class_name'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($student['major'] ?? '—'); ?></td>
                        <td>
                            <a href="index.php?action=edit&id=<?php echo $student['id'];
                                                                ?>">Sửa</a>
                            ====
                            <a href="index.php?action=delete&id=<?php echo
                                                                $student['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa sinh viên này ra khỏi danh sách lớp K17 không?');">
                                Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="5">Chưa có sinh viên nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?keyword=<?php echo
                                    urlencode($keyword ?? ''); ?>&page=<?php echo $i; ?>"
                    class="<?php echo ($i == $currentPage) ?
                                'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
        <form action="index.php?action=add" method="POST"
            enctype="multipart/form-data">

            <h3>Thêm sinh viên mới</h3>
            <input type="text" name="name" placeholder="Họ và Tên"

                required>

            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Số điện thoại"

                required>

            <input type="text" name="course" placeholder="Khóa học (vd: K17)">
            <input type="text" name="class_name" placeholder="Tên lớp (vd: CNTT01)">
            <input type="text" name="major" placeholder="Ngành học (vd: Công nghệ thông tin)">
            <label for="avatar">Ảnh đại diện:</label>
            <input type="file" id="avatar" name="avatar">
            <button type="submit">Thêm mới</button>
        </form>
    </div>
    <script>
        // Lấy tất cả các phần tử flash message
        const flashMessages = document.querySelectorAll('.flash-message');
        // Nếu có thông báo, đặt một bộ đếm thời gian để ẩn nó sau 5 giây
        if (flashMessages.length > 0) {
            setTimeout(() => {
                flashMessages.forEach(function(message) {
                    // Làm cho thông báo mờ dần trước khi xóa
                    message.style.opacity = '0';
                    // Xóa hẳn phần tử khỏi DOM sau khi hiệu ứng mờ kết thúc
                    setTimeout(() => {
                        message.style.display = 'none';
                    }, 500); // 0.5 giây, khớp với transition của CSS
                });
            }, 5000); // 5000 milliseconds = 5 giây
        }
    </script>
</body>

</html>
