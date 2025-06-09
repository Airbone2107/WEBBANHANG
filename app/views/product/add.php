<?php include 'app/views/shares/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 fw-bold"><i class="fas fa-plus-circle me-2 text-primary"></i>Thêm sản phẩm mới</h1>
        <a href="/WEBBANHANG/Product" class="btn btn-outline-primary">
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
            
            <form action="/WEBBANHANG/Product/save" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-medium">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="category_id" class="form-label fw-medium">Danh mục <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category->id; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category->id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label fw-medium">Mô tả sản phẩm</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="price" class="form-label fw-medium">Giá (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="price" name="price" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price'], ENT_QUOTES, 'UTF-8') : ''; ?>" min="0" required>
                    </div>
                    <div class="col-md-6">
                        <label for="image" class="form-label fw-medium">Hình ảnh</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu sản phẩm
                    </button>
                    <a href="/WEBBANHANG/Product" class="btn btn-secondary ms-2">
                        <i class="fas fa-times me-1"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>