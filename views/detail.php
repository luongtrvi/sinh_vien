<?php

use Luongtrieuvi\Bai01QuanlySv\Core\FlashMessage;

require_once __DIR__ . '/../views/layout/header.php';

?>

<body>
    <div class="profile-card">
        <div class="profile-header">
            <img src="uploads/avatars/<?php echo $student['avatar'] ??

                                            'default-avatar.png'; ?>" alt="Avatar">

            <h1><?php echo htmlspecialchars($student['name']); ?></h1>
            <p><?php echo htmlspecialchars($student['major'] ?? 'Chưa cập nhật ngành học'); ?></p>

        </div>
        <div class="profile-body">
            <div class="info-group">
                <label>Email:</label>
                <span><?php echo htmlspecialchars($student['email']);

                        ?></span>

            </div>
            <div class="info-group">
                <label>Số điện thoại:</label>
                <span><?php echo htmlspecialchars($student['phone']);

                        ?></span>

            </div>

            <div class="info-group">
                <label>Khóa học:</label>
                <span><?php echo htmlspecialchars($student['course'] ??

                            'Chưa cập nhật'); ?></span>

            </div>
            <div class="info-group">
                <label>Lớp:</label>
                <span><?php echo htmlspecialchars($student['class_name']

                            ?? 'Chưa cập nhật'); ?></span>

            </div>
        </div>
        <a href="index.php" class="back-link">Quay về danh sách</a>
    </div>
</body>
<?php
// NẠP FOOTER
require_once __DIR__ . '/../views/layout/footer.php';
?>
