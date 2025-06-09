<?php include 'app/views/shares/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 fw-bold"><i class="fas fa-plus-circle me-2 text-primary"></i>Thêm sản phẩm mới (API)</h1>
        <a href="/WEBBANHANG/Product/list" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <div id="form-message" class="mb-3"></div>
            
            <form id="add-product-form">
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
                        <div class="form-text">Lưu ý: Upload hình ảnh qua API không được hỗ trợ trong phiên bản này. Hình ảnh sẽ không được lưu.</div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary" id="submit-button">
                        <i class="fas fa-save me-1"></i> Lưu sản phẩm
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
        const categorySelect = document.getElementById('category_id');
        const addProductForm = document.getElementById('add-product-form');
        const formMessageContainer = document.getElementById('form-message');
        const submitButton = document.getElementById('submit-button');

        // Load categories for dropdown
        fetch('/WEBBANHANG/api/category')
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

        // Handle form submission
        addProductForm.addEventListener('submit', function(event) {
            event.preventDefault();
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang lưu...';
            formMessageContainer.innerHTML = '';

            const formData = new FormData(this);
            const jsonData = {};
            jsonData['name'] = formData.get('name');
            jsonData['description'] = formData.get('description');
            jsonData['price'] = formData.get('price');
            jsonData['category_id'] = formData.get('category_id');

            fetch('/WEBBANHANG/api/product/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(jsonData)
            })
            .then(response => {
                if (!response.headers.get("content-type")?.includes("application/json") && !response.ok) {
                     return response.text().then(text => { throw new Error("Server error: " + text + " (Status: " + response.status + ")") });
                }
                return response.json().then(data => ({ status: response.status, body: data }));
            })
            .then(({ status, body }) => {
                if (status === 201 && body.message === 'Product created successfully!') {
                    displayMessage('Thêm sản phẩm thành công!', 'success');
                    addProductForm.reset();
                } else if (status === 400 && body.errors) {
                    let errorMessages = "Lỗi nhập liệu:<ul class='mb-0'>";
                    for (const key in body.errors) {
                        errorMessages += `<li>${escapeHTML(body.errors[key])}</li>`;
                    }
                    errorMessages += "</ul>";
                    displayMessage(errorMessages, 'danger');
                } else {
                    displayMessage('Thêm sản phẩm thất bại: ' + (body.message || 'Lỗi không xác định từ server.'), 'danger');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                displayMessage('Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại. Chi tiết: ' + error.message, 'danger');
            })
            .finally(() => {
                 submitButton.disabled = false;
                 submitButton.innerHTML = '<i class="fas fa-save me-1"></i> Lưu sản phẩm';
            });
        });

        function displayMessage(message, type = 'info') {
            formMessageContainer.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                                                ${message}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                              </div>`;
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
    });
</script>