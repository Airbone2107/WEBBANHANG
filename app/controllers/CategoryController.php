<?php
/**
 * Lớp CategoryController
 * 
 * Lớp điều khiển này quản lý các hoạt động liên quan đến danh mục sản phẩm:
 * - Hiển thị danh sách danh mục
 * - Thêm danh mục mới
 * - Chỉnh sửa danh mục
 * - Xóa danh mục
 * 
 * @author  Web Bán Hàng Team
 * @version 1.0
 */
require_once('app/config/database.php');
require_once('app/models/CategoryModel.php');

class CategoryController {
    /**
     * Đối tượng CategoryModel để tương tác với dữ liệu danh mục
     * @var CategoryModel
     */
    private $categoryModel;
    
    /**
     * Kết nối cơ sở dữ liệu
     * @var PDO
     */
    private $db;

    /**
     * Khởi tạo đối tượng CategoryController
     * Thiết lập kết nối cơ sở dữ liệu và khởi tạo model
     */
    public function __construct() {
        // Khởi tạo kết nối cơ sở dữ liệu
        $this->db = (new Database())->getConnection();
        // Khởi tạo đối tượng CategoryModel
        $this->categoryModel = new CategoryModel($this->db);
    }

    /**
     * Hiển thị danh sách tất cả các danh mục
     * Phương thức này lấy danh sách danh mục và hiển thị trang list
     */
    public function index() {
        // Lấy danh sách danh mục từ model
        $categories = $this->categoryModel->getCategories();
        // Load view hiển thị danh sách
        include 'app/views/category/list.php';
    }

    /**
     * Hiển thị form thêm danh mục mới
     */
    public function add() {
        // Load view form thêm mới danh mục
        include 'app/views/category/add.php';
    }

    /**
     * Xử lý lưu danh mục mới từ form thêm
     * Kiểm tra tính hợp lệ của dữ liệu và lưu vào cơ sở dữ liệu
     */
    public function save() {
        // Kiểm tra nếu là phương thức POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lấy dữ liệu từ form
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            // Kiểm tra tính hợp lệ của dữ liệu
            if (empty($name)) {
                $error = "Tên danh mục không được để trống.";
                include 'app/views/category/add.php';
                return;
            }

            // Lưu danh mục vào cơ sở dữ liệu
            $this->categoryModel->createCategory($name, $description);
            // Chuyển hướng về trang danh sách danh mục
            header('Location: /WEBBANHANG/Category');
            exit();
        }
    }

    /**
     * Hiển thị form chỉnh sửa danh mục
     * 
     * @param int $id ID của danh mục cần chỉnh sửa
     */
    public function edit($id) {
        // Lấy thông tin danh mục theo ID
        $category = $this->categoryModel->getCategoryById($id);
        
        // Kiểm tra nếu danh mục tồn tại
        if ($category) {
            // Load view form chỉnh sửa danh mục
            include 'app/views/category/edit.php';
        } else {
            echo "Không tìm thấy danh mục.";
        }
    }

    /**
     * Xử lý cập nhật danh mục từ form chỉnh sửa
     * Kiểm tra tính hợp lệ của dữ liệu và cập nhật vào cơ sở dữ liệu
     */
    public function update() {
        // Kiểm tra nếu là phương thức POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lấy dữ liệu từ form
            $id = $_POST['id'] ?? '';
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            // Kiểm tra tính hợp lệ của dữ liệu
            if (empty($name)) {
                $error = "Tên danh mục không được để trống.";
                $category = (object)[ 'id' => $id, 'name' => $name, 'description' => $description ];
                include 'app/views/category/edit.php';
                return;
            }

            // Cập nhật danh mục vào cơ sở dữ liệu
            $this->categoryModel->updateCategory($id, $name, $description);
            // Chuyển hướng về trang danh sách danh mục
            header('Location: /WEBBANHANG/Category');
            exit();
        }
    }

    /**
     * Xóa danh mục khỏi hệ thống
     * 
     * @param int $id ID của danh mục cần xóa
     */
    public function delete($id) {
        // Xóa danh mục
        $this->categoryModel->deleteCategory($id);
        // Chuyển hướng về trang danh sách danh mục
        header('Location: /WEBBANHANG/Category');
        exit();
    }
}
?>
