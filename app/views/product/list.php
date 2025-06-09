<?php include 'app/views/shares/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 fw-bold"><i class="fas fa-box me-2 text-primary"></i>Danh sách sản phẩm (API Loaded)</h1>
        <?php if (SessionHelper::isAdmin()): ?>
        <a href="/WEBBANHANG/Product/add" id="addProductLink" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Thêm sản phẩm mới
        </a>
        <?php endif; ?>
    </div>
    
    <div id="product-list-container" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
        <div class="col-12 text-center">
            <p>Đang tải danh sách sản phẩm...</p>
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetchProducts();
    });

    function fetchProducts() {
        const productListContainer = document.getElementById('product-list-container');
        fetch('/WEBBANHANG/api/product') 
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(products => {
                productListContainer.innerHTML = '';
                if (products && products.length > 0) {
                    products.forEach(product => {
                        const productCard = `
                            <div class="col">
                                <div class="card h-100 product-card shadow-sm">
                                    <div style="height: 200px; overflow: hidden;">
                                        ${product.image ? 
                                            `<div style="height: 100%; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                                <img src="/WEBBANHANG/${escapeHTML(product.image)}" 
                                                     style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                                     alt="${escapeHTML(product.name)}">
                                            </div>` : 
                                            `<div class="bg-light text-center d-flex align-items-center justify-content-center" style="height: 100%;">
                                                <i class="fas fa-image fa-4x text-secondary"></i>
                                            </div>`
                                        }
                                    </div>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title fw-bold">
                                            ${escapeHTML(product.name)}
                                        </h5>
                                        
                                        <p class="card-text text-truncate">
                                            ${escapeHTML(product.description)}
                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary rounded-pill">
                                                <i class="fas fa-tag me-1"></i>
                                                ${escapeHTML(product.category_name || 'N/A')}
                                            </span>
                                            <span class="fw-bold text-danger">
                                                ${Number(product.price).toLocaleString('vi-VN')} VNĐ
                                            </span>
                                        </div>
                                        
                                        <div class="mt-auto pt-3 border-top">
                                            <div class="d-flex flex-wrap gap-1">
                                                <a href="/WEBBANHANG/Product/show/${product.id}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i> Chi tiết
                                                </a>
                                                <a href="/WEBBANHANG/Product/addToCart/${product.id}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ
                                                </a>
                                                <?php if (SessionHelper::isAdmin()): ?>
                                                <a href="/WEBBANHANG/Product/edit/${product.id}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-edit me-1"></i> Sửa
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${product.id}, '${escapeJS(product.name)}')">
                                                    <i class="fas fa-trash me-1"></i> Xóa
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        productListContainer.insertAdjacentHTML('beforeend', productCard);
                    });
                } else {
                    productListContainer.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Chưa có sản phẩm nào.
                                <?php if (SessionHelper::isAdmin()): ?>
                                    Hãy thêm sản phẩm mới!
                                <?php endif; ?>
                            </div>
                        </div>`;
                }
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                productListContainer.innerHTML = '<div class="col-12"><div class="alert alert-danger">Lỗi khi tải danh sách sản phẩm. Vui lòng thử lại.</div></div>';
            });
    }

    function deleteProduct(id, productName) {
        if (confirm('Bạn có chắc chắn muốn xóa sản phẩm "' + productName + '"?')) {
            fetch(`/WEBBANHANG/api/product/destroy/${id}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.message === 'Product deleted successfully!') {
                    alert('Xóa sản phẩm thành công!');
                    fetchProducts();
                } else {
                    alert('Xóa sản phẩm thất bại: ' + (data.message || 'Lỗi không xác định'));
                }
            })
            .catch(error => {
                console.error('Error deleting product:', error);
                alert('Lỗi khi xóa sản phẩm. Vui lòng thử lại.');
            });
        }
    }

    function escapeHTML(str) {
        if (str === null || str === undefined) return '';
        return str.toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function escapeJS(str) {
        if (str === null || str === undefined) return '';
        return str.toString().replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
    }
</script>