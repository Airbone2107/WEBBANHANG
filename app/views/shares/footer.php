</main> <!-- Đóng thẻ main từ header -->

<footer class="bg-light py-4 mt-auto border-top">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-5 col-md-12">
                <h5 class="fw-bold mb-3"><i class="fas fa-shopping-cart text-primary me-2"></i>Web Bán Hàng</h5>
                <p class="text-muted">
                    Hệ thống bán hàng trực tuyến với đầy đủ các tính năng hiện đại, 
                    giao diện dễ sử dụng và hỗ trợ nhiều thiết bị khác nhau.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-secondary fs-5"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-secondary fs-5"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-secondary fs-5"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-secondary fs-5"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold mb-3">Liên kết nhanh</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="/WEBBANHANG/Product/" class="text-decoration-none text-secondary"><i class="fas fa-angle-right me-1"></i>Sản phẩm</a></li>
                    <li class="mb-2"><a href="/WEBBANHANG/Category/" class="text-decoration-none text-secondary"><i class="fas fa-angle-right me-1"></i>Danh mục</a></li>
                    <li class="mb-2"><a href="/WEBBANHANG/Product/Cart/" class="text-decoration-none text-secondary"><i class="fas fa-angle-right me-1"></i>Giỏ hàng</a></li>
                </ul>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <h6 class="fw-bold mb-3">Liên hệ với chúng tôi</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i>123 Đường ABC, Quận XYZ, TP. HCM</li>
                    <li class="mb-2"><i class="fas fa-phone-alt me-2 text-primary"></i>(+84) 123 456 789</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2 text-primary"></i>contact@webbanhang.com</li>
                </ul>
            </div>
        </div>
        
        <hr class="my-4">
        
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="small text-muted mb-0">&copy; <?php echo date('Y'); ?> Web Bán Hàng. Tất cả các quyền được bảo lưu.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="small text-muted mb-0">Thiết kế bởi <a href="#" class="text-decoration-none">Airbone</a></p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom scripts -->
<script>
    // Dark mode toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;
        const darkModeSaved = localStorage.getItem('darkMode') === 'true';
        
        // Apply dark mode if saved in localStorage
        if (darkModeSaved) {
            body.classList.add('dark-mode');
            darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        }
        
        // Toggle dark mode
        darkModeToggle.addEventListener('click', function() {
            body.classList.toggle('dark-mode');
            
            // If dark mode is active
            const isDarkMode = body.classList.contains('dark-mode');
            
            // Save to localStorage
            localStorage.setItem('darkMode', isDarkMode);
            
            // Update button icon
            darkModeToggle.innerHTML = isDarkMode ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
        });
    });
</script>
</body>
</html>
