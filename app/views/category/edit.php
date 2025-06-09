<?php include 'app/views/shares/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-primary"></i>Chỉnh sửa danh mục</h1>
        <a href="/WEBBANHANG/Category" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-danger mb-4">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Lỗi nhập liệu</h5>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="/WEBBANHANG/Category/update" method="POST">
                <input type="hidden" name="id" value="<?php echo $category->id; ?>">
                
                <div class="mb-3">
                    <label for="name" class="form-label fw-medium">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label fw-medium">Mô tả danh mục</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($category->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu thay đổi
                    </button>
                    <a href="/WEBBANHANG/Category" class="btn btn-secondary ms-2">
                        <i class="fas fa-times me-1"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
