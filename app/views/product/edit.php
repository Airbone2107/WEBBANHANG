<?php include 'app/views/shares/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-primary"></i>Chỉnh sửa sản phẩm</h1>
        <a href="/WEBBANHANG/Product" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="/WEBBANHANG/Product/update" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $product->id; ?>">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-medium">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="category_id" class="form-label fw-medium">Danh mục <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category->id; ?>" <?php echo ($category->id == $product->category_id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label fw-medium">Mô tả sản phẩm</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="price" class="form-label fw-medium">Giá (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="price" name="price" value="<?php echo $product->price; ?>" min="0" required>
                    </div>
                    <div class="col-md-6">
                        <label for="image" class="form-label fw-medium">Hình ảnh</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="form-text">Để trống nếu không muốn thay đổi hình ảnh hiện tại.</div>
                    </div>
                </div>
                
                <?php if (!empty($product->image)): ?>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Hình ảnh hiện tại</label>
                        <div class="d-flex align-items-center">
                            <img src="/WEBBANHANG/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" 
                                 alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>" 
                                 class="img-thumbnail me-3" style="width: 100px; height: 100px; object-fit: cover;">
                            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu thay đổi
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