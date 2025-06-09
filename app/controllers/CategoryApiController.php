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