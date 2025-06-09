<?php include 'app/views/shares/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 fw-bold"><i class="fas fa-tags me-2 text-primary"></i>Danh sách danh mục</h1>
        <a href="/WEBBANHANG/Category/add" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Thêm danh mục mới
        </a>
    </div>
    
    <?php if (empty($categories)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Chưa có danh mục nào. Hãy thêm danh mục mới!
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Tên danh mục</th>
                                <th>Mô tả</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td class="ps-3"><?php echo $category->id; ?></td>
                                    <td class="fw-medium"><?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($category->description, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="/WEBBANHANG/Category/edit/<?php echo $category->id; ?>" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit me-1"></i> Sửa
                                            </a>
                                            <a href="/WEBBANHANG/Category/delete/<?php echo $category->id; ?>" 
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                                <i class="fas fa-trash me-1"></i> Xóa
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'app/views/shares/footer.php'; ?>
