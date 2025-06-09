<?php
/**
 * Lớp ProductController
 * 
 * Lớp điều khiển này quản lý các hoạt động liên quan đến sản phẩm:
 * - Hiển thị danh sách và thông tin chi tiết sản phẩm
 * - Thêm sản phẩm mới
 * - Chỉnh sửa sản phẩm
 * - Xóa sản phẩm
 * - Quản lý giỏ hàng
 * 
 * @author  Web Bán Hàng Team
 * @version 1.0
 */
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
require_once('app/helpers/SessionHelper.php');

class ProductController {
    /**
     * Đối tượng ProductModel để tương tác với dữ liệu sản phẩm
     * @var ProductModel
     */
    private $productModel;
    
    /**
     * Kết nối cơ sở dữ liệu
     * @var PDO
     */
    private $db;

    /**
     * Khởi tạo đối tượng ProductController
     * Thiết lập kết nối cơ sở dữ liệu và khởi tạo model
     */
    public function __construct() {
        // Khởi tạo kết nối cơ sở dữ liệu
        $this->db = (new Database())->getConnection();
        // Khởi tạo đối tượng ProductModel
        $this->productModel = new ProductModel($this->db);
    }

    /**
     * Hiển thị danh sách tất cả các sản phẩm
     * Phương thức này lấy danh sách sản phẩm và hiển thị trang list
     */
    public function index() {
        // Lấy danh sách sản phẩm từ model
        $products = $this->productModel->getProducts();
        // Load view hiển thị danh sách
        include 'app/views/product/list.php';
    }

    /**
     * Hiển thị thông tin chi tiết của một sản phẩm
     * 
     * @param int $id ID của sản phẩm cần xem chi tiết
     */
    public function show($id) {
        // Lấy thông tin sản phẩm theo ID
        $product = $this->productModel->getProductById($id);
        
        // Kiểm tra nếu sản phẩm tồn tại
        if ($product) {
            // Load view hiển thị chi tiết sản phẩm
            include 'app/views/product/show.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }

    /**
     * Hiển thị form thêm sản phẩm mới (Chỉ Admin)
     * Phương thức này lấy danh sách danh mục để hiển thị trong form
     */
    public function add() {
        if (!SessionHelper::isAdmin()) {
            include 'app/views/errors/unauthorized.php';
            exit();
        }
        // Lấy danh sách danh mục để hiển thị trong dropdown
        $categories = (new CategoryModel($this->db))->getCategories();
        // Load view form thêm mới sản phẩm
        include_once 'app/views/product/add.php';
    }

    /**
     * Xử lý lưu sản phẩm mới từ form thêm (Chỉ Admin)
     * Kiểm tra tính hợp lệ của dữ liệu và lưu vào cơ sở dữ liệu
     */
    public function save() {
        if (!SessionHelper::isAdmin()) {
            include 'app/views/errors/unauthorized.php';
            exit();
        }
        // Kiểm tra nếu là phương thức POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lấy dữ liệu từ form
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;

            // Kiểm tra và xử lý upload hình ảnh
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $image = $this->uploadImage($_FILES['image']);
            } else {
                $image = "";
            }

            // Thêm sản phẩm vào cơ sở dữ liệu
            $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image);
            
            // Kiểm tra kết quả và xử lý tương ứng
            if (is_array($result)) {
                // Nếu có lỗi, hiển thị lại form với thông báo lỗi
                $errors = $result;
                $categories = (new CategoryModel($this->db))->getCategories();
                include 'app/views/product/add.php';
            } else {
                // Nếu thành công, chuyển hướng về trang danh sách sản phẩm
                header('Location: /WEBBANHANG/Product');
            }
        }
    }

    /**
     * Hiển thị form chỉnh sửa sản phẩm (Chỉ Admin)
     * 
     * @param int $id ID của sản phẩm cần chỉnh sửa
     */
    public function edit($id) {
        if (!SessionHelper::isAdmin()) {
            include 'app/views/errors/unauthorized.php';
            exit();
        }
        // Lấy thông tin sản phẩm theo ID
        $product = $this->productModel->getProductById($id);
        // Lấy danh sách danh mục để hiển thị trong dropdown
        $categories = (new CategoryModel($this->db))->getCategories();
        
        // Kiểm tra nếu sản phẩm tồn tại
        if ($product) {
            // Load view form chỉnh sửa sản phẩm
            include 'app/views/product/edit.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }

    /**
     * Xử lý cập nhật sản phẩm từ form chỉnh sửa (Chỉ Admin)
     * Kiểm tra tính hợp lệ của dữ liệu và cập nhật vào cơ sở dữ liệu
     */
    public function update() {
        if (!SessionHelper::isAdmin()) {
            include 'app/views/errors/unauthorized.php';
            exit();
        }
        // Kiểm tra nếu là phương thức POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ form
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category_id = $_POST['category_id'];
            
            // Kiểm tra và xử lý upload hình ảnh
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $image = $this->uploadImage($_FILES['image']);
            } else {
                $image = $_POST['existing_image'];
            }
            
            // Cập nhật sản phẩm vào cơ sở dữ liệu
            $edit = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);
            
            // Kiểm tra kết quả và xử lý tương ứng
            if ($edit) {
                // Nếu thành công, chuyển hướng về trang danh sách sản phẩm
                header('Location: /WEBBANHANG/Product');
            } else {
                echo "Đã xảy ra lỗi khi lưu sản phẩm.";
            }
        }
    }

    /**
     * Xóa sản phẩm khỏi hệ thống (Chỉ Admin)
     * 
     * @param int $id ID của sản phẩm cần xóa
     */
    public function delete($id) {
        if (!SessionHelper::isAdmin()) {
            include 'app/views/errors/unauthorized.php';
            exit();
        }
        // Xóa sản phẩm và kiểm tra kết quả
        if ($this->productModel->deleteProduct($id)) {
            // Nếu thành công, chuyển hướng về trang danh sách sản phẩm
            header('Location: /WEBBANHANG/Product');
        } else {
            echo "Đã xảy ra lỗi khi xóa sản phẩm.";
        }
    }

    /**
     * Xử lý upload hình ảnh sản phẩm
     * 
     * @param array $file Mảng thông tin file từ $_FILES
     * @return string Đường dẫn đến file hình ảnh đã upload
     * @throws Exception Nếu có lỗi xảy ra trong quá trình upload
     */
    private function uploadImage($file) {
        // Thư mục lưu trữ hình ảnh
        $target_dir = "uploads/";
        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Đường dẫn đầy đủ đến file
        $target_file = $target_dir . basename($file["name"]);
        // Lấy phần mở rộng của file
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        // Kiểm tra xem file có phải là hình ảnh thật không
        $check = getimagesize($file["tmp_name"]);

        if ($check === false) {
            throw new Exception("File không phải là hình ảnh.");
        }

        // Kiểm tra kích thước file (giới hạn 10MB)
        if ($file["size"] > 10 * 1024 * 1024) {
            throw new Exception("Hình ảnh có kích thước quá lớn.");
        }

        // Kiểm tra định dạng file
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            throw new Exception("Chỉ cho phép các định dạng JPG, JPEG, PNG và GIF.");
        }

        // Di chuyển file từ thư mục tạm lên server
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Có lỗi xảy ra khi tải lên hình ảnh.");
        }

        return $target_file;
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     * 
     * @param int $id ID của sản phẩm cần thêm vào giỏ hàng
     */
    public function addToCart($id) {
        // Lấy thông tin sản phẩm theo ID
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            echo "Không tìm thấy sản phẩm.";
            return;
        }

        // Khởi tạo giỏ hàng nếu chưa tồn tại
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Thêm sản phẩm vào giỏ hàng hoặc tăng số lượng nếu đã tồn tại
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image
            ];
        }

        // Chuyển hướng đến trang giỏ hàng
        header('Location: /WEBBANHANG/Product/cart');
    }

    /**
     * Hiển thị lại danh sách sản phẩm
     * (Phương thức bổ sung cho chức năng liệt kê)
     */
    public function list() {
        // Lấy danh sách sản phẩm từ model
        $products = $this->productModel->getProducts();
        // Load view hiển thị danh sách
        require_once 'app/views/product/list.php';
    }

    /**
     * Hiển thị giỏ hàng của người dùng
     */
    public function cart()
    {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        include 'app/views/product/cart.php';
    }

    /**
     * Hiển thị trang thanh toán
     */
    public function checkout()
    {
        include 'app/views/product/checkout.php';
    }

    /**
     * Xử lý quá trình thanh toán
     */
    public function processCheckout()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            // Kiểm tra giỏ hàng
            if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
                echo "Giỏ hàng trống.";
                return;
            }
            // Bắt đầu giao dịch
            $this->db->beginTransaction();
            try {
                // Lưu thông tin đơn hàng vào bảng orders
                $query = "INSERT INTO orders (name, phone, address) VALUES (:name, :phone, :address)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':address', $address);
                $stmt->execute();
                $order_id = $this->db->lastInsertId();
                
                // Lưu chi tiết đơn hàng vào bảng order_details
                $cart = $_SESSION['cart'];
                foreach ($cart as $product_id => $item) {
                    $query = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':order_id', $order_id);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->bindParam(':quantity', $item['quantity']);
                    $stmt->bindParam(':price', $item['price']);
                    $stmt->execute();
                }
                
                // Xóa giỏ hàng sau khi đặt hàng thành công
                unset($_SESSION['cart']);
                
                // Commit giao dịch
                $this->db->commit();
                
                // Chuyển hướng đến trang xác nhận đơn hàng
                header('Location: /WEBBANHANG/Product/orderConfirmation');
            } catch (Exception $e) {
                // Rollback giao dịch nếu có lỗi
                $this->db->rollBack();
                echo "Đã xảy ra lỗi khi xử lý đơn hàng: " . $e->getMessage();
            }
        }
    }

    /**
     * Hiển thị trang xác nhận đơn hàng
     */
    public function orderConfirmation()
    {
        include 'app/views/product/orderConfirmation.php';
    }
    
    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     * 
     * @param int $id ID của sản phẩm cần cập nhật
     * @param int $quantity Số lượng cần thay đổi (có thể là số âm để giảm)
     */
    public function updateCart($id, $quantity = 1)
    {
        // Kiểm tra xem giỏ hàng đã tồn tại chưa
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Kiểm tra xem sản phẩm có trong giỏ hàng không
        if (isset($_SESSION['cart'][$id])) {
            // Cập nhật số lượng
            $_SESSION['cart'][$id]['quantity'] += (int)$quantity;
            
            // Nếu số lượng <= 0, xóa sản phẩm khỏi giỏ hàng
            if ($_SESSION['cart'][$id]['quantity'] <= 0) {
                unset($_SESSION['cart'][$id]);
            }
        }
        
        // Chuyển hướng về trang giỏ hàng
        header('Location: /WEBBANHANG/Product/cart');
    }
    
    /**
     * Xóa một sản phẩm khỏi giỏ hàng
     * 
     * @param int $id ID của sản phẩm cần xóa
     */
    public function removeFromCart($id)
    {
        // Kiểm tra xem sản phẩm có trong giỏ hàng không
        if (isset($_SESSION['cart'][$id])) {
            // Xóa sản phẩm khỏi giỏ hàng
            unset($_SESSION['cart'][$id]);
        }
        
        // Chuyển hướng về trang giỏ hàng
        header('Location: /WEBBANHANG/Product/cart');
    }
    
    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clearCart()
    {
        // Xóa giỏ hàng
        unset($_SESSION['cart']);
        
        // Chuyển hướng về trang giỏ hàng
        header('Location: /WEBBANHANG/Product/cart');
    }
}
?>
