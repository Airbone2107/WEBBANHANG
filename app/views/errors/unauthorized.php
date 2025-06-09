<?php 
// Đảm bảo SessionHelper.php đã được nạp, thường là qua header.php hoặc index.php
// Nếu header.php chưa tự include SessionHelper, bạn có thể thêm ở đây
// require_once __DIR__ . '/../../helpers/SessionHelper.php'; 
include_once __DIR__ . '/../shares/header.php'; 
?>

<div class="main-content">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm text-center border-danger">
                    <div class="card-body p-4 p-md-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h2 class="card-title fw-bold mb-3">Không Có Quyền Truy Cập</h2>
                        <p class="card-text text-muted mb-4">
                            Bạn không được phép truy cập vào trang hoặc thực hiện hành động này.
                        </p>
                        <a href="/WEBBANHANG/Product" class="btn btn-primary">
                            <i class="fas fa-home me-1"></i> Quay Về Trang Chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../shares/footer.php'; ?> 