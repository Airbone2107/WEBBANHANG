<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Bán Hàng</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom styles -->
    <style>
        /* Inline styles for immediate use */
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        main {
            flex: 1;
        }
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 15px;
        }
        @media (min-width: 768px) {
            .main-content {
                padding: 20px 30px;
            }
        }
        @media (min-width: 992px) {
            .main-content {
                padding: 25px 50px;
            }
        }
        .product-card {
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.12);
        }
        /* Dark mode styling */
        body.dark-mode {
            background-color: #212529;
            color: #f8f9fa;
        }
        body.dark-mode .bg-light {
            background-color: #343a40 !important;
        }
        body.dark-mode .text-dark {
            color: #f8f9fa !important;
        }
        body.dark-mode .card {
            background-color: #343a40;
            color: #f8f9fa;
        }
        body.dark-mode .navbar-light .navbar-nav .nav-link {
            color: rgba(248, 249, 250, 0.8);
        }
        body.dark-mode .navbar-light .navbar-brand {
            color: #f8f9fa;
        }
        body.dark-mode .list-group-item {
            background-color: #343a40;
            color: #f8f9fa;
            border-color: #495057;
        }
        body.dark-mode .table {
            color: #f8f9fa;
        }
        body.dark-mode .card-header {
            border-bottom: 1px solid #495057;
        }
        body.dark-mode .modal-content {
            background-color: #343a40;
            color: #f8f9fa;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="/WEBBANHANG/Product">
                    <i class="fas fa-shopping-cart me-2 text-primary"></i>
                    <span class="fw-bold">Web Bán Hàng</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#navbarNav" aria-controls="navbarNav" 
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="productDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-box me-1"></i> Sản phẩm
                            </a>
                            <ul class="dropdown-menu shadow" aria-labelledby="productDropdown">
                                <li><a class="dropdown-item" href="/WEBBANHANG/Product/"><i class="fas fa-list me-1"></i> Danh sách sản phẩm</a></li>
                                <?php if (SessionHelper::isAdmin()): ?>
                                <li><a class="dropdown-item" href="/WEBBANHANG/Product/add"><i class="fas fa-plus me-1"></i> Thêm sản phẩm</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-tags me-1"></i> Danh mục
                            </a>
                            <ul class="dropdown-menu shadow" aria-labelledby="categoryDropdown">
                                <li><a class="dropdown-item" href="/WEBBANHANG/Category/"><i class="fas fa-list me-1"></i> Danh sách danh mục</a></li>
                                <?php if (SessionHelper::isAdmin()): ?>
                                <li><a class="dropdown-item" href="/WEBBANHANG/Category/add"><i class="fas fa-plus me-1"></i> Thêm danh mục</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center">
                        <a href="/WEBBANHANG/Product/cart" class="btn btn-outline-primary me-3 position-relative">
                            <i class="fas fa-shopping-cart"></i>
                            <?php 
                            // Đếm số lượng sản phẩm trong giỏ hàng
                            $cartItemCount = 0;
                            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                                foreach ($_SESSION['cart'] as $item) {
                                    $cartItemCount += $item['quantity'];
                                }
                            }
                            if ($cartItemCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $cartItemCount; ?>
                                <span class="visually-hidden">unread messages</span>
                            </span>
                            <?php endif; ?>
                        </a>

                        <?php if (SessionHelper::isLoggedIn()): ?>
                            <?php $currentUser = SessionHelper::getUser(); ?>
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($currentUser->fullname ?? $currentUser->username); ?>
                                    <?php if (SessionHelper::isAdmin()): ?>
                                        <span class="badge bg-info text-dark ms-1">Admin</span>
                                    <?php endif; ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-1"></i> Hồ sơ</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-1"></i> Cài đặt</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/WEBBANHANG/Account/logout"><i class="fas fa-sign-out-alt me-1"></i> Đăng xuất</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="/WEBBANHANG/Account/login" class="btn btn-outline-success me-2">
                                <i class="fas fa-sign-in-alt me-1"></i> Đăng nhập
                            </a>
                            <a href="/WEBBANHANG/Account/register" class="btn btn-success">
                                <i class="fas fa-user-plus me-1"></i> Đăng ký
                            </a>
                        <?php endif; ?>
                        
                        <button id="darkModeToggle" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-moon"></i>
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="py-4">
</body>
</html>