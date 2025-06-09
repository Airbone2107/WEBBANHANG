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