<?php 
include 'app/views/shares/header.php'; 

// Lấy lỗi và dữ liệu cũ từ session nếu có
$errors = $_SESSION['form_errors'] ?? [];
$old_data = $_SESSION['form_data'] ?? [];
$success_message = $_SESSION['success_message'] ?? null;

unset($_SESSION['form_errors'], $_SESSION['form_data'], $_SESSION['success_message']);
?>

<div class="main-content">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="card-title text-center fw-bold mb-4"><i class="fas fa-sign-in-alt me-2 text-primary"></i>Đăng nhập</h2>
                        
                        <?php if ($success_message): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($errors['general']); ?></div>
                        <?php endif; ?>

                        <form action="/WEBBANHANG/Account/checkLogin" method="POST" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label fw-medium">Tên đăng nhập <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                                       id="username" name="username" 
                                       value="<?php echo htmlspecialchars($old_data['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['username']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-medium">Mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                       id="password" name="password" required>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['password']); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Ghi nhớ đăng nhập</label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-1"></i> Đăng nhập
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-2"><a href="#" class="text-decoration-none">Quên mật khẩu?</a></p>
                            <p class="mb-0">Chưa có tài khoản? <a href="/WEBBANHANG/Account/register" class="fw-bold text-decoration-none">Đăng ký ngay</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?> 