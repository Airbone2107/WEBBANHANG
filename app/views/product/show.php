<?php include 'app/views/shares/header.php'; ?>

<div class="main-content">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/WEBBANHANG/Product" class="text-decoration-none">Sản phẩm</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm" style="height: 400px; overflow: hidden;">
                <?php if ($product->image): ?>
                    <div style="height: 100%; display: flex; align-items: center; justify-content: center;">
                        <img src="/WEBBANHANG/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" 
                             style="max-width: 100%; max-height: 100%; object-fit: contain;"
                             alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100%;">
                        <i class="fas fa-image fa-5x text-secondary"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-md-6">
            <h1 class="fw-bold mb-3"><?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></h1>
            
            <div class="mb-3">
                <span class="badge bg-primary rounded-pill">
                    <i class="fas fa-tag me-1"></i>
                    <?php echo htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </div>
            
            <h3 class="text-danger fw-bold mb-4">
                <?php echo number_format($product->price, 0, ',', '.'); ?> VNĐ
            </h3>
            
            <div class="mb-4">
                <h5 class="fw-bold">Mô tả sản phẩm</h5>
                <p class="text-muted">
                    <?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            </div>
            
            <div class="d-grid gap-2 d-md-flex">
                <a href="/WEBBANHANG/Product/addToCart/<?php echo $product->id; ?>" class="btn btn-primary">
                    <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ hàng
                </a>
                <a href="/WEBBANHANG/Product" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
    
    <?php if (SessionHelper::isAdmin()): ?>
    <div class="mt-5 pt-4 border-top">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold"><i class="fas fa-cogs me-2"></i>Quản lý sản phẩm</h4>
            <div>
                <a href="/WEBBANHANG/Product/edit/<?php echo $product->id; ?>" class="btn btn-outline-primary me-2">
                    <i class="fas fa-edit me-1"></i> Sửa sản phẩm
                </a>
                <a href="/WEBBANHANG/Product/delete/<?php echo $product->id; ?>" 
                   class="btn btn-outline-danger"
                   onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm \'<?php echo htmlspecialchars(addslashes($product->name), ENT_QUOTES, 'UTF-8'); ?>\'?');">
                    <i class="fas fa-trash me-1"></i> Xóa sản phẩm
                </a>
            </div>
        </div>
        
        <div class="card bg-light">
            <div class="card-body">
                <p class="mb-0 text-muted">
                    <i class="fas fa-info-circle me-1"></i> 
                    Là Admin, bạn có thể sửa hoặc xóa sản phẩm này. Lưu ý rằng việc xóa sản phẩm không thể hoàn tác.
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'app/views/shares/footer.php'; ?>