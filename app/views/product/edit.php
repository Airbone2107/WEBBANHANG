<?php include 'app/views/shares/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-primary"></i>Chỉnh sửa sản phẩm (API)</h1>
        <a href="/WEBBANHANG/Product/list" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <div id="form-message" class="mb-3"></div>
            <form id="edit-product-form">
                <input type="hidden" id="id" name="id">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-medium">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="category_id" class="form-label fw-medium">Danh mục <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">-- Đang tải danh mục --</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label fw-medium">Mô tả sản phẩm</label>
                    <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="price" class="form-label fw-medium">Giá (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="price" name="price" min="0" required>
                    </div>
                    <div class="col-md-6">
                        <label for="image" class="form-label fw-medium">Hình ảnh</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="form-text">Lưu ý: Upload hình ảnh mới qua API không được hỗ trợ. Hình ảnh hiện tại sẽ được giữ nguyên.</div>
                    </div>
                </div>
                 <div class="mb-3" id="current-image-container" style="display:none;">
                    <label class="form-label fw-medium">Hình ảnh hiện tại</label>
                    <div><img id="current-image" src="" alt="Hình ảnh hiện tại" class="img-thumbnail" style="max-width: 150px; max-height: 150px;"></div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary" id="submit-button">
                        <i class="fas fa-save me-1"></i> Lưu thay đổi
                    </button>
                    <a href="/WEBBANHANG/Product/list" class="btn btn-secondary ms-2">
                        <i class="fas fa-times me-1"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const editProductForm = document.getElementById('edit-product-form');
        const categorySelect = document.getElementById('category_id');
        const formMessageContainer = document.getElementById('form-message');
        const submitButton = document.getElementById('submit-button');
        const currentImageContainer = document.getElementById('current-image-container');
        const currentImageElement = document.getElementById('current-image');

        // Extract product ID from URL (e.g., /WEBBANHANG/Product/edit/123)
        const pathSegments = window.location.pathname.split('/');
        const productId = pathSegments[pathSegments.length - 1]; 

        if (!productId || isNaN(productId)) {
            displayMessage('ID sản phẩm không hợp lệ.', 'danger');
            submitButton.disabled = true;
            return;
        }
        document.getElementById('id').value = productId;

        // Load categories
        const fetchCategories = fetch('/WEBBANHANG/api/category')
            .then(response => response.json())
            .then(categories => {
                categorySelect.innerHTML = '<option value="">-- Chọn danh mục --</option>';
                if (categories && categories.length > 0) {
                    categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = escapeHTML(category.name);
                        categorySelect.appendChild(option);
                    });
                } else {
                     categorySelect.innerHTML = '<option value="">-- Không có danh mục --</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching categories:', error);
                categorySelect.innerHTML = '<option value="">-- Lỗi tải danh mục --</option>';
                displayMessage('Lỗi tải danh mục sản phẩm.', 'danger');
            });

        // Load product data then set category
        const fetchProductData = fetch(`/WEBBANHANG/api/product/show/${productId}`) // Đảm bảo URL đúng
            .then(response => {
                if (!response.ok) {
                    if (response.status === 404) throw new Error('Product not found (404)');
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(product => {
                document.getElementById('name').value = escapeHTML(product.name);
                document.getElementById('description').value = escapeHTML(product.description);
                document.getElementById('price').value = product.price;
                if (product.image) {
                    currentImageElement.src = `/WEBBANHANG/${escapeHTML(product.image)}`;
                    currentImageContainer.style.display = 'block';
                }
                // Trả về product để promise tiếp theo có thể dùng
                return product; 
            })
            .catch(error => {
                console.error('Error fetching product data:', error);
                displayMessage('Lỗi tải dữ liệu sản phẩm: ' + error.message, 'danger');
                submitButton.disabled = true;
                // Ném lỗi để Promise.all biết
                throw error; 
            });

        // Sau khi cả hai fetch hoàn tất (categories và product data)
        Promise.all([fetchCategories, fetchProductData])
            .then((results) => {
                const product = results[1]; // product data từ fetchProductData
                if (product && product.category_id) {
                     // Kiểm tra xem option có tồn tại không trước khi set
                    if (categorySelect.querySelector('option[value="' + product.category_id + '"]')) {
                        categorySelect.value = product.category_id;
                    } else {
                        console.warn('Category ID ' + product.category_id + ' not found in select options.');
                    }
                }
            })
            .catch(error => {
                 // Lỗi đã được xử lý ở các catch riêng lẻ, chỉ log thêm nếu cần
                 console.error("Error in Promise.all for product edit page:", error);
            });


        // Handle form submission
        editProductForm.addEventListener('submit', function(event) {
            event.preventDefault();
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang cập nhật...';
            formMessageContainer.innerHTML = '';

            const formData = new FormData(this);
            const jsonData = {};
            jsonData['name'] = formData.get('name');
            jsonData['description'] = formData.get('description');
            jsonData['price'] = formData.get('price');
            jsonData['category_id'] = formData.get('category_id');
            // ID đã có trong jsonData.id thông qua input hidden

            // API ProductApiController.update không xử lý upload file image mới.
            // Nó sẽ giữ nguyên ảnh cũ nếu không có logic đặc biệt để xóa/thay đổi.
            // Trong ProductApiController, chúng ta đã code để nó giữ ảnh cũ.

            fetch(`/WEBBANHANG/api/product/update/${productId}`, { // Đảm bảo URL đúng
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                    // 'Authorization': 'Bearer YOUR_TOKEN_IF_NEEDED' 
                },
                body: JSON.stringify(jsonData)
            })
            .then(response => {
                 if (!response.headers.get("content-type")?.includes("application/json") && !response.ok) {
                     return response.text().then(text => { throw new Error("Server error: " + text + " (Status: " + response.status + ")") });
                }
                return response.json().then(data => ({ status: response.status, body: data }));
            })
            .then(({status, body}) => {
                if (status === 200 && body.message === 'Product updated successfully!') {
                    displayMessage('Cập nhật sản phẩm thành công!', 'success');
                     // Tùy chọn: Chuyển hướng sau khi thành công
                    // setTimeout(() => { window.location.href = '/WEBBANHANG/Product/list'; }, 2000);
                } else if (status === 400 && body.errors) {
                    let errorMessages = "Lỗi nhập liệu:<ul class='mb-0'>";
                     for (const key in body.errors) {
                        errorMessages += `<li>${escapeHTML(body.errors[key])}</li>`;
                    }
                    errorMessages += "</ul>";
                    displayMessage(errorMessages, 'danger');
                } else {
                     displayMessage('Cập nhật sản phẩm thất bại: ' + (body.message || 'Lỗi không xác định từ server.'), 'danger');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                displayMessage('Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại. Chi tiết: ' + error.message, 'danger');
            })
            .finally(() => {
                 submitButton.disabled = false;
                 submitButton.innerHTML = '<i class="fas fa-save me-1"></i> Lưu thay đổi';
            });
        });
        
        function displayMessage(message, type = 'info') {
            formMessageContainer.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                                                ${message}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                              </div>`;
        }
        // Helper function để tránh XSS
        function escapeHTML(str) {
             if (str === null || str === undefined) return '';
            return str.toString()
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    });
</script>