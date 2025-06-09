# TODO: Tích hợp API RESTful vào Project Web Bán Hàng

Tài liệu này hướng dẫn từng bước để tích hợp các tính năng API RESTful (dựa trên `Plan.md`) vào project Web Bán Hàng hiện tại. Các API này sẽ cho phép quản lý Sản phẩm và Danh mục thông qua các HTTP request tiêu chuẩn, đồng thời các trang giao diện web hiện tại sẽ được cập nhật để tương tác với các API này.

## Mục lục

1.  [Giới thiệu](#1-giới-thiệu)
2.  [Chuẩn bị](#2-chuẩn-bị)
3.  [Tạo API Controllers](#3-tạo-api-controllers)
    *   [3.1. Tạo `ProductApiController.php`](#31-tạo-productapicontrollerphp)
    *   [3.2. Tạo `CategoryApiController.php`](#32-tạo-categoryapicontrollerphp)
4.  [Cập nhật Router (`index.php`)](#4-cập-nhật-router-indexphp)
5.  [Cập nhật Views để sử dụng API](#5-cập-nhật-views-để-sử-dụng-api)
    *   [5.1. Cập nhật `app/views/product/list.php`](#51-cập-nhật-appviewsproductlistphp)
    *   [5.2. Cập nhật `app/views/product/add.php`](#52-cập-nhật-appviewsproductaddphp)
    *   [5.3. Cập nhật `app/views/product/edit.php`](#53-cập-nhật-appviewsproducteditphp)
6.  [Lưu ý về Model](#6-lưu-ý-về-model)
7.  [Kiểm tra và Hoàn tất](#7-kiểm-tra-và-hoàn-tất)

## 1. Giới thiệu

Việc tích hợp API RESTful nhằm mục đích:
- Cung cấp một giao diện lập trình ứng dụng (API) cho việc quản lý sản phẩm và danh mục.
- Cho phép các ứng dụng khác (ví dụ: mobile app, frontend JavaScript framework) có thể tương tác với dữ liệu của hệ thống.
- Cập nhật các trang quản lý sản phẩm hiện tại để sử dụng các API này, giúp tách biệt logic frontend và backend rõ ràng hơn.

## 2. Chuẩn bị

Trước khi bắt đầu, hãy đảm bảo:
- Cấu trúc thư mục của project khớp với những gì được mô tả trong `Document.md`.
- Các file cốt lõi như `app/config/database.php`, `app/models/ProductModel.php`, `app/models/CategoryModel.php`, và `app/helpers/SessionHelper.php` đã tồn tại và hoạt động đúng với project hiện tại.
- Composer đã được cài đặt và `vendor/autoload.php` đã được tạo (nếu có sử dụng thư viện bên ngoài, ví dụ: `firebase/php-jwt` như trong `composer.json`).

Các file hiện tại của project sẽ được ưu tiên sử dụng.

## 3. Tạo API Controllers

Chúng ta sẽ tạo hai API controller mới: một cho Product và một cho Category.

### 3.1. Tạo `ProductApiController.php`

Controller này sẽ xử lý các yêu cầu API liên quan đến sản phẩm (CRUD operations).

Tạo file mới tại đường dẫn `app/controllers/ProductApiController.php` với nội dung sau:

```php
<!-- app/controllers/ProductApiController.php -->
<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
// CategoryModel không thực sự cần thiết trong ProductApiController theo Plan.md, 
// vì category_id chỉ là một trường dữ liệu.
// Tuy nhiên, nếu có logic phức tạp liên quan đến category khi xử lý product, bạn có thể include nó.
// require_once 'app/models/CategoryModel.php'; 

class ProductApiController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        header('Access-Control-Allow-Origin: *'); // Cho phép CORS từ mọi nguồn (cân nhắc kỹ cho production)
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        // Xử lý preflight request cho CORS
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(204); // No Content
            exit;
        }

        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    // Lấy danh sách sản phẩm
    // GET /WEBBANHANG/api/product
    public function index()
    {
        header('Content-Type: application/json');
        $products = $this->productModel->getProducts();
        echo json_encode($products);
    }

    // Lấy thông tin sản phẩm theo ID
    // GET /WEBBANHANG/api/product/show/{id}
    public function show($id)
    {
        header('Content-Type: application/json');
        $product = $this->productModel->getProductById($id);
        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Product not found']);
        }
    }

    // Thêm sản phẩm mới
    // POST /WEBBANHANG/api/product/store
    public function store()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $category_id = $data['category_id'] ?? null;
        // API này không xử lý upload image theo Plan.md
        $image = null; 

        // ProductModel hiện tại có thể yêu cầu image. 
        // Chúng ta sẽ truyền null hoặc một giá trị mặc định nếu API không cung cấp image.
        // Hoặc ProductModel cần được điều chỉnh để chấp nhận image là optional.
        // Giả sử ProductModel::addProduct chấp nhận image là optional
        $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image);

        if (is_array($result) && !empty($result['errors'])) { // Giả sử model trả về mảng lỗi
            http_response_code(400); // Bad Request
            echo json_encode(['errors' => $result['errors']]);
        } elseif ($result === true) { // Giả sử model trả về true khi thành công
            http_response_code(201); // Created
            echo json_encode(['message' => 'Product created successfully!']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['message' => 'Product creation failed!']);
        }
    }

    // Cập nhật sản phẩm theo ID
    // PUT /WEBBANHANG/api/product/update/{id}
    public function update($id)
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $category_id = $data['category_id'] ?? null;
        // API này không xử lý upload image theo Plan.md
        // Để giữ nguyên ảnh cũ, chúng ta cần lấy thông tin sản phẩm hiện tại
        $existingProduct = $this->productModel->getProductById($id);
        if (!$existingProduct) {
            http_response_code(404);
            echo json_encode(['message' => 'Product not found for update.']);
            return;
        }
        $image = $existingProduct->image; // Giữ nguyên ảnh cũ

        $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);

        if ($result) {
            echo json_encode(['message' => 'Product updated successfully!']);
        } else {
            http_response_code(400); // Bad Request hoặc 500 Internal Server Error tùy logic
            echo json_encode(['message' => 'Product update failed!']);
        }
    }

    // Xóa sản phẩm theo ID
    // DELETE /WEBBANHANG/api/product/destroy/{id}
    public function destroy($id)
    {
        header('Content-Type: application/json');
        $result = $this->productModel->deleteProduct($id);

        if ($result) {
            echo json_encode(['message' => 'Product deleted successfully!']);
        } else {
            http_response_code(400); // Bad Request hoặc 500 Internal Server Error
            echo json_encode(['message' => 'Product deletion failed!']);
        }
    }
}
?>
```

### 3.2. Tạo `CategoryApiController.php`

Controller này sẽ xử lý các yêu cầu API liên quan đến danh mục.

Tạo file mới tại đường dẫn `app/controllers/CategoryApiController.php` với nội dung sau:

```php
<!-- app/controllers/CategoryApiController.php -->
<?php
require_once 'app/config/database.php';
require_once 'app/models/CategoryModel.php';

class CategoryApiController
{
    private $categoryModel;
    private $db;

    public function __construct()
    {
        header('Access-Control-Allow-Origin: *'); // Cho phép CORS từ mọi nguồn (cân nhắc kỹ cho production)
        header('Access-Control-Allow-Methods: GET, OPTIONS'); // Chỉ cho phép GET cho API này theo Plan.md
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // Xử lý preflight request cho CORS
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(204); // No Content
            exit;
        }

        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    // Lấy danh sách danh mục
    // GET /WEBBANHANG/api/category
    public function index()
    {
        header('Content-Type: application/json');
        $categories = $this->categoryModel->getCategories(); 
        echo json_encode($categories);
    }

    // Các phương thức store, update, destroy cho Category có thể được thêm vào đây nếu cần,
    // tương tự như ProductApiController, nhưng Plan.md chỉ đề cập đến index (GET).
}
?>
```

## 4. Cập nhật Router (`index.php`)

Chúng ta cần cập nhật file `index.php` để nó có thể nhận diện và điều hướng các yêu cầu đến API controllers. Các URL cho API sẽ có dạng `/WEBBANHANG/api/{controller_name}/{action}/{params}`.

Thay thế toàn bộ nội dung file `index.php` bằng mã sau:

```php
<!-- index.php -->
<?php
// Luôn bắt đầu session ở đầu file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Các file cần thiết
require_once 'app/helpers/SessionHelper.php'; // Load SessionHelper sớm

// Phân tích URL
$fullUrlPath = $_GET['url'] ?? '';
$fullUrlPath = rtrim($fullUrlPath, '/');
$fullUrlPath = filter_var($fullUrlPath, FILTER_SANITIZE_URL);
$urlParts = explode('/', $fullUrlPath);

// Mặc định cho MVC
$controllerNameMvc = 'ProductController';
$actionMvc = 'index';
$paramsMvc = [];

// Base path của ứng dụng (nếu có) - Ví dụ: /WEBBANHANG
// Điều này giúp xác định đúng các phần của URL
// Tuy nhiên, với .htaccess hiện tại, urlParts[0] sẽ là controller hoặc 'api'
// Chúng ta không cần base path ở đây nữa nếu .htaccess đã rewrite đúng.

// Kiểm tra xem có phải là yêu cầu API không
// URL API có dạng: api/controllerNameApi/actionApi/id
if (isset($urlParts[0]) && strtolower($urlParts[0]) === 'api') {
    // Đây là một yêu cầu API
    $apiControllerSegment = $urlParts[1] ?? ''; // Ví dụ: 'product' hoặc 'category'
    $apiControllerName = ucfirst(strtolower($apiControllerSegment)) . 'ApiController'; // Ví dụ: 'ProductApiController'
    
    // Xác định action và id cho API
    $method = $_SERVER['REQUEST_METHOD'];
    $apiAction = '';
    $apiParams = [];

    // URL: api/resource/{id} -> action là show (GET), update (PUT), destroy (DELETE)
    // URL: api/resource      -> action là index (GET), store (POST)
    // Plan.md đề xuất các tên action tường minh trong controller (index, show, store, update, destroy)
    // Router sẽ cần ánh xạ method + URL segment tới các action đó.
    
    // Ví dụ: /api/product/show/123 -> $urlParts[2] = 'show', $urlParts[3] = '123'
    // Ví dụ: /api/product/store -> $urlParts[2] = 'store'
    // Ví dụ: /api/product (GET) -> index
    // Ví dụ: /api/product/{id} (GET) -> show(id)

    $apiActionNameFromUrl = $urlParts[2] ?? null; // Tên action tường minh từ URL nếu có
    $idFromUrl = $urlParts[3] ?? null; // ID nếu action là show, update, destroy VÀ action được chỉ định tường minh

    if ($apiActionNameFromUrl) {
        $apiAction = strtolower($apiActionNameFromUrl);
        if ($idFromUrl) {
            $apiParams[] = $idFromUrl;
        }
    } else {
        // Nếu không có action tường minh, xác định dựa trên method và sự tồn tại của ID (segment thứ 2 sau tên controller)
        // /api/product/123 (GET) -> show(123)
        // /api/product (GET) -> index
        // /api/product (POST) -> store
        // /api/product/123 (PUT) -> update(123)
        // /api/product/123 (DELETE) -> destroy(123)
        $potentialId = $urlParts[2] ?? null;

        switch ($method) {
            case 'GET':
                if ($potentialId) {
                    $apiAction = 'show';
                    $apiParams[] = $potentialId;
                } else {
                    $apiAction = 'index';
                }
                break;
            case 'POST':
                $apiAction = 'store';
                // Dữ liệu POST thường được lấy từ php://input trong controller
                break;
            case 'PUT':
                if ($potentialId) {
                    $apiAction = 'update';
                    $apiParams[] = $potentialId;
                } else {
                    // PUT thường yêu cầu ID
                    http_response_code(400); // Bad Request
                    echo json_encode(['message' => 'Resource ID missing for PUT request. Use format: api/resource/id']);
                    exit;
                }
                break;
            case 'DELETE':
                if ($potentialId) {
                    $apiAction = 'destroy';
                    $apiParams[] = $potentialId;
                } else {
                    // DELETE thường yêu cầu ID
                    http_response_code(400); // Bad Request
                    echo json_encode(['message' => 'Resource ID missing for DELETE request. Use format: api/resource/id']);
                    exit;
                }
                break;
            case 'OPTIONS': // Xử lý CORS preflight
                 // Header CORS đã được thiết lập trong constructor của APIController
                 // Nếu chưa, bạn có thể thêm ở đây:
                 // header('Access-Control-Allow-Origin: *');
                 // header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
                 // header('Access-Control-Allow-Headers: Content-Type, Authorization');
                http_response_code(204); // No Content
                exit;
            default:
                http_response_code(405); // Method Not Allowed
                echo json_encode(['message' => 'Method Not Allowed']);
                exit;
        }
    }


    $controllerFile = 'app/controllers/' . $apiControllerName . '.php';

    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        if (class_exists($apiControllerName)) {
            $controller = new $apiControllerName();
            if (method_exists($controller, $apiAction)) {
                call_user_func_array([$controller, $apiAction], $apiParams);
            } else {
                http_response_code(404);
                echo json_encode(['message' => "Action '$apiAction' not found in controller '$apiControllerName'."]);
            }
        } else {
            http_response_code(404);
            echo json_encode(['message' => "Controller class '$apiControllerName' not found."]);
        }
    } else {
        http_response_code(404);
        echo json_encode(['message' => "Controller file '$controllerFile' not found."]);
    }
} else {
    // Xử lý yêu cầu MVC thông thường
    $controllerNameMvc = !empty($urlParts[0]) ? ucfirst(strtolower($urlParts[0])) . 'Controller' : 'ProductController';
    $actionMvc = isset($urlParts[1]) && !empty($urlParts[1]) ? strtolower($urlParts[1]) : 'index';
    $paramsMvc = array_slice($urlParts, 2);

    $controllerFileMvc = 'app/controllers/' . $controllerNameMvc . '.php';

    if (!file_exists($controllerFileMvc)) {
        // Hiển thị trang lỗi 404 thân thiện
        // include 'app/views/errors/404.php'; // Bạn cần tạo file này
        header("HTTP/1.0 404 Not Found");
        die('Lỗi: Controller "' . htmlspecialchars($controllerNameMvc) . '" không tìm thấy. File: ' . $controllerFileMvc);
    }

    require_once $controllerFileMvc;

    if (!class_exists($controllerNameMvc)) {
        header("HTTP/1.0 404 Not Found");
        die('Lỗi: Lớp Controller "' . htmlspecialchars($controllerNameMvc) . '" không được định nghĩa trong file.');
    }

    $controller = new $controllerNameMvc();

    if (!method_exists($controller, $actionMvc)) {
         header("HTTP/1.0 404 Not Found");
        die('Lỗi: Action "' . htmlspecialchars($actionMvc) . '" không tìm thấy trong Controller "' . htmlspecialchars($controllerNameMvc) . '".');
    }

    call_user_func_array([$controller, $actionMvc], $paramsMvc);
}
?>
```
**Lưu ý về Router:**
Router trên giả định các endpoint API như sau:
- `GET /WEBBANHANG/api/product` -> `ProductApiController@index`
- `GET /WEBBANHANG/api/product/show/{id}` -> `ProductApiController@show($id)` (Plan.md dùng `show($id)`)
- `POST /WEBBANHANG/api/product/store` -> `ProductApiController@store()` (Plan.md dùng `store()`)
- `PUT /WEBBANHANG/api/product/update/{id}` -> `ProductApiController@update($id)` (Plan.md dùng `update($id)`)
- `DELETE /WEBBANHANG/api/product/destroy/{id}` -> `ProductApiController@destroy($id)` (Plan.md dùng `destroy($id)`)
- `GET /WEBBANHANG/api/category` -> `CategoryApiController@index`

Router trên cố gắng linh hoạt với việc tên action có thể nằm trong URL (ví dụ: `api/product/show/123`) hoặc được suy ra từ HTTP method (ví dụ: `GET api/product/123` cũng sẽ gọi `show(123)`). Trong `Plan.md`, các ví dụ JavaScript gọi API theo cách tường minh (ví dụ `fetch('/webbanhang/api/product/${id}')` cho GET chi tiết, sẽ được router này hiểu là gọi action `show`).

## 5. Cập nhật Views để sử dụng API

Bây giờ, chúng ta sẽ cập nhật các file view hiện tại của trang quản lý sản phẩm để chúng gọi đến các API mới tạo bằng JavaScript, thay vì load dữ liệu trực tiếp bằng PHP.

### 5.1. Cập nhật `app/views/product/list.php`

File này sẽ hiển thị danh sách sản phẩm lấy từ API.

Thay thế toàn bộ nội dung file `app/views/product/list.php` bằng:

```php
<!-- app/views/product/list.php -->
<?php include 'app/views/shares/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 fw-bold"><i class="fas fa-box me-2 text-primary"></i>Danh sách sản phẩm (API Loaded)</h1>
        <?php if (SessionHelper::isAdmin()): ?>
        <a href="/WEBBANHANG/Product/add" id="addProductLink" class="btn btn-primary"> <!-- Sẽ cập nhật link này ở bước sau nếu trang add cũng dùng API -->
            <i class="fas fa-plus-circle me-1"></i> Thêm sản phẩm mới
        </a>
        <?php endif; ?>
    </div>
    
    <div id="product-list-container" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
        <!-- Danh sách sản phẩm sẽ được tải từ API và hiển thị tại đây bằng JavaScript -->
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
        // Sử dụng URL tuyệt đối hoặc tương đối đúng với cấu hình của bạn
        // Giả định .htaccess đã xử lý WEBBANHANG
        fetch('/WEBBANHANG/api/product') 
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(products => {
                productListContainer.innerHTML = ''; // Xóa thông báo loading
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
                                                <a href="/WEBBANHANG/Product/edit/${product.id}" class="btn btn-sm btn-outline-secondary"> <!-- Sẽ cập nhật link này ở bước sau nếu trang edit cũng dùng API -->
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
            fetch(`/WEBBANHANG/api/product/destroy/${id}`, { // Đảm bảo URL đúng
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.message === 'Product deleted successfully!') {
                    alert('Xóa sản phẩm thành công!');
                    fetchProducts(); // Tải lại danh sách sản phẩm
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

    // Helper functions để tránh XSS
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
```

### 5.2. Cập nhật `app/views/product/add.php`

File này sẽ cho phép thêm sản phẩm mới thông qua API. Form sẽ được submit bằng JavaScript.

Thay thế toàn bộ nội dung file `app/views/product/add.php` bằng:

```php
<!-- app/views/product/add.php -->
<?php include 'app/views/shares/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 fw-bold"><i class="fas fa-plus-circle me-2 text-primary"></i>Thêm sản phẩm mới (API)</h1>
        <a href="/WEBBANHANG/Product/list" class="btn btn-outline-primary"> <!-- Giả sử list đã được cập nhật hoặc sẽ là trang list truyền thống -->
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <div id="form-message" class="mb-3"></div> <!-- Để hiển thị thông báo lỗi/thành công -->
            
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
                            <!-- Các danh mục sẽ được tải từ API và hiển thị tại đây -->
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
                categorySelect.innerHTML = '<option value="">-- Chọn danh mục --</option>'; // Xóa "Đang tải"
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
            // Chỉ lấy các trường mà API hỗ trợ (name, description, price, category_id)
            jsonData['name'] = formData.get('name');
            jsonData['description'] = formData.get('description');
            jsonData['price'] = formData.get('price');
            jsonData['category_id'] = formData.get('category_id');

            // API ProductApiController.store không xử lý file image
            // Nếu muốn xử lý image, cần dùng ProductController (MVC) hiện tại
            // hoặc nâng cấp API.

            fetch('/WEBBANHANG/api/product/store', { // Đảm bảo URL đúng
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                    // 'Authorization': 'Bearer YOUR_TOKEN_IF_NEEDED' 
                },
                body: JSON.stringify(jsonData)
            })
            .then(response => {
                // Kiểm tra nếu response không phải JSON mà là lỗi server dạng text
                if (!response.headers.get("content-type")?.includes("application/json") && !response.ok) {
                     return response.text().then(text => { throw new Error("Server error: " + text + " (Status: " + response.status + ")") });
                }
                return response.json().then(data => ({ status: response.status, body: data }));
            })
            .then(({ status, body }) => {
                if (status === 201 && body.message === 'Product created successfully!') {
                    displayMessage('Thêm sản phẩm thành công!', 'success');
                    addProductForm.reset(); // Xóa form
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
```

### 5.3. Cập nhật `app/views/product/edit.php`

File này sẽ cho phép chỉnh sửa sản phẩm hiện có thông qua API.

Thay thế toàn bộ nội dung file `app/views/product/edit.php` bằng:

```php
<!-- app/views/product/edit.php -->
<?php include 'app/views/shares/header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-primary"></i>Chỉnh sửa sản phẩm (API)</h1>
        <a href="/WEBBANHANG/Product/list" class="btn btn-outline-primary"> <!-- Giả sử list đã được cập nhật -->
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <div id="form-message" class="mb-3"></div> <!-- Để hiển thị thông báo lỗi/thành công -->
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
```