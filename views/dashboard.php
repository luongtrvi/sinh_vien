<?php

use Luongtrieuvi\Bai01QuanlySv\Core\FlashMessage;

require_once __DIR__ . '/../views/layout/header.php';

?>

<body>
    <div class="container">
        <h1>Dashboard Thống kê</h1>
        <div class="stats-container">
            <div class="stat-card">
                <div class="number"><?php echo
                                    htmlspecialchars($stats['total_students'] ?? 0); ?></div>
                <div class="title">Tổng số sinh viên</div>
            </div>
            <div class="stat-card green">
                <div class="number"><?php echo
                                    htmlspecialchars($stats['edu_emails'] ?? 0); ?></div>
                <div class="title">SV có email @tdu.edu.vn</div>
            </div>
            <div class="stat-card orange">
                <div class="number"><?php echo
                                    htmlspecialchars($stats['sdt_09'] ?? 0); ?></div>
                <div class="title">SV có SĐT đầu 09</div>
            </div>
        </div>
        <a href="index.php" class="back-link">Quay về danh sách</a>
    </div>
</body>
<?php
// NẠP FOOTER
require_once __DIR__ . '/../views/layout/footer.php';
?>
