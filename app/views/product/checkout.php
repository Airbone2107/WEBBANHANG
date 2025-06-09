<?php include 'app/views/shares/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 fw-bold"><i class="fas fa-credit-card me-2 text-primary"></i>Thanh toán</h1>
        <a href="/WEBBANHANG/Product/cart" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại giỏ hàng
        </a>
    </div>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i> Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm vào giỏ hàng trước khi thanh toán.
        </div>
        <div class="text-center mt-4">
            <a href="/WEBBANHANG/Product" class="btn btn-primary">
                <i class="fas fa-shopping-bag me-1"></i> Tiếp tục mua sắm
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Thông tin đặt hàng</h5>
                    </div>
                    <div class="card-body">
                        <form action="/WEBBANHANG/Product/processCheckout" method="post">
                            <div class="mb-3">
                                <label for="name" class="form-label">Họ tên</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Địa chỉ giao hàng</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle me-1"></i> Hoàn tất đặt hàng
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'app/views/shares/footer.php'; ?>
