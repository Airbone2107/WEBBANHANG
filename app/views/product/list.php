<?php include 'app/views/shares/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 fw-bold"><i class="fas fa-box me-2 text-primary"></i>Danh sách sản phẩm</h1>
        <a href="/WEBBANHANG/Product/add" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Thêm sản phẩm mới
        </a>
    </div>
    
    <?php if (empty($products)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Chưa có sản phẩm nào. Hãy thêm sản phẩm mới!
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            <?php foreach ($products as $product): ?>
            <div class="col">
                <div class="card h-100 product-card shadow-sm">
                    <div style="height: 200px; overflow: hidden;">
                        <?php if ($product->image): ?>
                            <div style="height: 100%; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                <img src="/WEBBANHANG/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" 
                                     style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                     alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        <?php else: ?>
                            <div class="bg-light text-center d-flex align-items-center justify-content-center" style="height: 100%;">
                                <i class="fas fa-image fa-4x text-secondary"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold">
                            <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                        </h5>
                        
                        <p class="card-text text-truncate">
                            <?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary rounded-pill">
                                <i class="fas fa-tag me-1"></i>
                                <?php echo htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <span class="fw-bold text-danger">
                                <?php echo number_format($product->price, 0, ',', '.'); ?> VNĐ
                            </span>
                        </div>
                        
                        <div class="mt-auto pt-3 border-top">
                            <div class="d-flex flex-wrap gap-1">
                                <a href="/WEBBANHANG/Product/show/<?php echo $product->id; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i> Chi tiết
                                </a>
                                <a href="/WEBBANHANG/Product/addToCart/<?php echo $product->id; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ
                                </a>
                                <a href="/WEBBANHANG/Product/edit/<?php echo $product->id; ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit me-1"></i> Sửa
                                </a>
                                <a href="/WEBBANHANG/Product/delete/<?php echo $product->id; ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                    <i class="fas fa-trash me-1"></i> Xóa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'app/views/shares/footer.php'; ?>