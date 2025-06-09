<?php 
include 'app/views/shares/header.php'; 

// Lấy lỗi và dữ liệu cũ từ session nếu có (sau khi redirect)
$errors = $_SESSION['form_errors'] ?? [];
$old_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_data']);
?>

<div class="main-content">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="card-title text-center fw-bold mb-4"><i class="fas fa-user-plus me-2 text-primary"></i>Đăng ký tài khoản</h2>
                        
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($errors['general']); ?></div>
                        <?php endif; ?>

                        <form action="/WEBBANHANG/Account/save" method="POST" novalidate>
                            <div class="mb-3">
                                <label for="fullname" class="form-label fw-medium">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php echo isset($errors['fullname']) ? 'is-invalid' : ''; ?>" 
                                       id="fullname" name="fullname" 
                                       value="<?php echo htmlspecialchars($old_data['fullname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                <?php if (isset($errors['fullname'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['fullname']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label fw-medium">Tên đăng nhập <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                                       id="username" name="username" 
                                       value="<?php echo htmlspecialchars($old_data['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                <div class="form-text">Ít nhất 3 ký tự.</div>
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['username']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-medium">Mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                       id="password" name="password" required>
                                <div class="form-text">Ít nhất 6 ký tự.</div>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['password']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label fw-medium">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" class="form-control <?php echo isset($errors['confirmPassword']) ? 'is-invalid' : ''; ?>" 
                                       id="confirmPassword" name="confirmPassword" required>
                                <?php if (isset($errors['confirmPassword'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['confirmPassword']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-4">
                                <label for="role" class="form-label fw-medium">Loại tài khoản <span class="text-danger">*</span></label>
                                <select class="form-select <?php echo isset($errors['role']) ? 'is-invalid' : ''; ?>" id="role" name="role" required>
                                    <option value="user" <?php echo (isset($old_data['role']) && $old_data['role'] == 'user' || empty($old_data['role'])) ? 'selected' : ''; ?>>User</option>
                                    <option value="admin" <?php echo (isset($old_data['role']) && $old_data['role'] == 'admin') ? 'selected' : ''; ?>>Admin (Demo)</option>
                                </select>
                                <?php if (isset($errors['role'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['role']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check-circle me-1"></i> Đăng ký
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-0">Đã có tài khoản? <a href="/WEBBANHANG/Account/login" class="fw-bold text-decoration-none">Đăng nhập ngay</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?> 