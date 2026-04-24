<?php

use Luongtrieuvi\Bai01QuanlySv\Core\FlashMessage; // THÊM DÒNG NÀY VÀO ĐẦU
require_once __DIR__ . '/../views/layout/header.php';
?>

<?php FlashMessage::display(); // THÊM DÒNG NÀY ĐỂ HIỂN THỊ THÔNG BÁO
?>


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
    <a href="index.php" style="padding: 8px 12px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 3px;">
        Reset
    </a>
    <a href="index.php?action=dashboard" style="padding: 8px 12px; background-color: #17a2b8; color: white; text-decoration: none; border-radius: 3px;">
        Xem Thống kê
    </a>
    <a href="index.php?action=export_csv&keyword=<?php echo urlencode($keyword ?? ''); ?>"
        style="padding: 8px 12px; background-color: #28a745; color: white; text-decoration: none; border-radius: 3px; margin-left:10px;">
        Xuất CSV
    </a>
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
<?php
// NẠP FOOTER
require_once __DIR__ . '/../views/layout/footer.php';
?>
