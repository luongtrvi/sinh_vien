<?php

use Luongtrieuvi\Bai01QuanlySv\Core\FlashMessage;

require_once __DIR__ . '/../views/layout/header.php';

?>

<body>
    <div class="container">
        <h1>Đổi mật khẩu</h1>
        <?php FlashMessage::display(); // Hiển thị thông báo ở đây
        ?>
        <form action="index.php?action=do_change_password"
            method="POST">
            <div class="form-group">
                <label for="old_password">Mật khẩu cũ:</label>
                <input type="password" id="old_password"
                    name="old_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">Mật khẩu mới:</label>
                <input type="password" id="new_password"
                    name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu
                    mới:</label>
                <input type="password" id="confirm_password"
                    name="confirm_password" required>
            </div>
            <button type="submit">Cập nhật mật khẩu</button>
        </form>
        <div class="nav-link">
            <a href="index.php">Quay về trang chủ</a>
        </div>
    </div>
</body>
<?php
// NẠP FOOTER
require_once __DIR__ . '/../views/layout/footer.php';
?>
