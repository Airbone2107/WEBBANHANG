<?php include 'app/views/shares/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 fw-bold"><i class="fas fa-shopping-cart me-2 text-primary"></i>Giỏ hàng</h1>
        <a href="/WEBBANHANG/Product" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Tiếp tục mua sắm
        </a>
    </div>
    
    <?php if (!empty($cart)): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Sản phẩm</th>
                                <th class="text-center">Giá</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-center">Thành tiền</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $totalAmount = 0; ?>
                            <?php foreach ($cart as $id => $item): ?>
                                <?php $itemTotal = $item['price'] * $item['quantity']; ?>
                                <?php $totalAmount += $itemTotal; ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($item['image'])): ?>
                                                <img src="/WEBBANHANG/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>" class="me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                    <i class="fas fa-image text-secondary"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-0 fw-medium"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <a href="/WEBBANHANG/Product/updateCart/<?php echo $id; ?>/-1" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-minus"></i>
                                            </a>
                                            <span class="mx-2 fw-medium"><?php echo $item['quantity']; ?></span>
                                            <a href="/WEBBANHANG/Product/updateCart/<?php echo $id; ?>/1" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold"><?php echo number_format($itemTotal, 0, ',', '.'); ?> VNĐ</td>
                                    <td class="text-center">
                                        <a href="/WEBBANHANG/Product/removeFromCart/<?php echo $id; ?>" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Tổng cộng:</td>
                                <td class="text-center fw-bold text-primary"><?php echo number_format($totalAmount, 0, ',', '.'); ?> VNĐ</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-end">
            <a href="/WEBBANHANG/Product/clearCart" class="btn btn-outline-secondary me-2">
                <i class="fas fa-trash-alt me-1"></i> Xóa giỏ hàng
            </a>
            <a href="/WEBBANHANG/Product/checkout" class="btn btn-primary">
                <i class="fas fa-credit-card me-1"></i> Thanh toán
            </a>
        </div>
    <?php else: ?>
        <div class="alert alert-info d-flex align-items-center">
            <i class="fas fa-info-circle me-3 fs-4"></i>
            <div>
                <h5 class="mb-1">Giỏ hàng của bạn đang trống</h5>
                <p class="mb-0">Hãy thêm sản phẩm vào giỏ hàng để tiến hành mua sắm.</p>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="/WEBBANHANG/Product" class="btn btn-primary">
                <i class="fas fa-shopping-bag me-1"></i> Khám phá sản phẩm ngay
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include 'app/views/shares/footer.php'; ?>
