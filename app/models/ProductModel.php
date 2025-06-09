<?php
/**
 * Lớp ProductModel
 * 
 * Lớp này chịu trách nhiệm quản lý và thao tác với dữ liệu sản phẩm
 * trong cơ sở dữ liệu của ứng dụng Web Bán Hàng.
 * 
 * @author  Web Bán Hàng Team
 * @version 1.0
 */
class ProductModel
{
    /**
     * Kết nối PDO đến cơ sở dữ liệu
     * @var PDO
     */
    private $conn;
    
    /**
     * Tên bảng sản phẩm trong cơ sở dữ liệu
     * @var string
     */
    private $table_name = "product";

    /**
     * Khởi tạo đối tượng ProductModel
     * 
     * @param PDO $db Đối tượng kết nối PDO đến cơ sở dữ liệu
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Lấy danh sách tất cả các sản phẩm kèm theo tên danh mục
     * 
     * @return array Mảng các đối tượng sản phẩm
     */
    public function getProducts()
    {
        // Truy vấn SQL để lấy tất cả sản phẩm và kết hợp với bảng danh mục
        $query = "SELECT p.id, p.name, p.description, p.price, p.image, c.name as category_name
            FROM " . $this->table_name . " p
            LEFT JOIN category c ON p.category_id = c.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    /**
     * Lấy thông tin sản phẩm theo ID
     * 
     * @param int $id ID của sản phẩm cần lấy thông tin
     * @return object|bool Đối tượng sản phẩm hoặc false nếu không tìm thấy
     */
    public function getProductById($id)
    {
        // Truy vấn SQL để lấy thông tin sản phẩm theo ID và kết hợp với bảng danh mục
        $query = "SELECT p.*, c.name as category_name
            FROM " . $this->table_name . " p
            LEFT JOIN category c ON p.category_id = c.id
            WHERE p.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    /**
     * Thêm sản phẩm mới
     * 
     * @param string $name Tên sản phẩm
     * @param string $description Mô tả sản phẩm
     * @param float $price Giá sản phẩm
     * @param int $category_id ID của danh mục sản phẩm
     * @param string $image Đường dẫn đến hình ảnh sản phẩm
     * @return array|bool Mảng lỗi nếu kiểm tra thất bại, true nếu thêm thành công, false nếu thêm thất bại
     */
    public function addProduct($name, $description, $price, $category_id, $image)
    {
        // Mảng lưu trữ thông báo lỗi
        $errors = [];
        
        // Kiểm tra hợp lệ đầu vào
        if (empty($name)) {
            $errors['name'] = 'Tên sản phẩm không được để trống';
        }
        if (empty($description)) {
            $errors['description'] = 'Mô tả không được để trống';
        }
        // Kiểm tra price. Nếu $price là null từ controller (do ?? ''), is_numeric('') là false.
        if (!is_numeric($price) || $price < 0) {
            $errors['price'] = 'Giá sản phẩm không hợp lệ';
        }
        
        // Trả về mảng lỗi nếu có lỗi
        if (count($errors) > 0) {
            return $errors;
        }

        // Truy vấn SQL để thêm sản phẩm mới
        $query = "INSERT INTO " . $this->table_name . " (name, description, price, category_id, image) 
            VALUES (:name, :description, :price, :category_id, :image)";
        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu để tránh SQL injection và xử lý lỗi deprecated
        // Sử dụng toán tử null coalescing (?? '') để đảm bảo strip_tags nhận chuỗi
        $cleaned_name = htmlspecialchars(strip_tags($name ?? ''));
        $cleaned_description = htmlspecialchars(strip_tags($description ?? ''));
        $cleaned_price = htmlspecialchars(strip_tags($price ?? '')); // Dòng 113 đã được sửa
        
        // Xử lý category_id: nếu cột trong DB cho phép NULL và muốn giữ NULL, cần xử lý bindParam khác.
        // Hiện tại, nếu category_id là null, nó sẽ trở thành chuỗi rỗng.
        $cleaned_category_id = htmlspecialchars(strip_tags($category_id ?? ''));
        
        // Xử lý image: tương tự category_id
        $cleaned_image = htmlspecialchars(strip_tags($image ?? ''));

        // Gán các tham số cho truy vấn
        $stmt->bindParam(':name', $cleaned_name);
        $stmt->bindParam(':description', $cleaned_description);
        $stmt->bindParam(':price', $cleaned_price);
        
        // Nếu category_id có thể là NULL trong DB và bạn muốn truyền NULL thay vì 0 (khi '' được convert)
        if ($cleaned_category_id === '' && ($category_id === null || $category_id === '')) {
             // Giả sử cột category_id trong DB cho phép NULL và bạn muốn lưu NULL nếu không có category_id
             // Nếu cột không cho phép NULL, việc truyền '' (sau đó MySQL có thể convert thành 0) là hành vi hiện tại.
             // Nếu bạn muốn bind NULL một cách tường minh khi $category_id ban đầu là null:
            if ($category_id === null) {
                $stmt->bindParam(':category_id', $category_id, PDO::PARAM_NULL);
            } else { // $category_id là '', có thể do người dùng nhập hoặc logic khác
                $stmt->bindParam(':category_id', $cleaned_category_id); // Sẽ bind ''
            }
        } else {
            $stmt->bindParam(':category_id', $cleaned_category_id);
        }

        // Tương tự cho image
        if ($cleaned_image === '' && ($image === null || $image === '')) {
            if ($image === null) {
                $stmt->bindParam(':image', $image, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':image', $cleaned_image);
            }
        } else {
            $stmt->bindParam(':image', $cleaned_image);
        }


        // Thực thi truy vấn và trả về kết quả
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Cập nhật thông tin sản phẩm
     * 
     * @param int $id ID của sản phẩm cần cập nhật
     * @param string $name Tên sản phẩm mới
     * @param string $description Mô tả sản phẩm mới
     * @param float $price Giá sản phẩm mới
     * @param int $category_id ID danh mục mới
     * @param string $image Đường dẫn hình ảnh mới
     * @return bool Trả về true nếu thao tác thành công, ngược lại false
     */
    public function updateProduct($id,$name,$description,$price,$category_id,$image) {
        // Truy vấn SQL để cập nhật thông tin sản phẩm
        $query = "UPDATE " . $this->table_name . "
            SET name=:name, description=:description, price=:price,
            category_id=:category_id, image=:image WHERE id=:id";  
        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu để tránh SQL injection và xử lý lỗi deprecated
        $cleaned_name = htmlspecialchars(strip_tags($name ?? ''));
        $cleaned_description = htmlspecialchars(strip_tags($description ?? ''));
        $cleaned_price = htmlspecialchars(strip_tags($price ?? ''));
        $cleaned_category_id = htmlspecialchars(strip_tags($category_id ?? ''));
        $cleaned_image = htmlspecialchars(strip_tags($image ?? ''));

        // Gán các tham số cho truy vấn
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $cleaned_name);
        $stmt->bindParam(':description', $cleaned_description);
        $stmt->bindParam(':price', $cleaned_price);

        // Xử lý bindParam cho category_id và image tương tự như addProduct nếu cần thiết
        if ($cleaned_category_id === '' && ($category_id === null || $category_id === '')) {
            if ($category_id === null) {
                $stmt->bindParam(':category_id', $category_id, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':category_id', $cleaned_category_id);
            }
        } else {
            $stmt->bindParam(':category_id', $cleaned_category_id);
        }

        if ($cleaned_image === '' && ($image === null || $image === '')) {
            if ($image === null) {
                $stmt->bindParam(':image', $image, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':image', $cleaned_image);
            }
        } else {
            $stmt->bindParam(':image', $cleaned_image);
        }

        // Thực thi truy vấn và trả về kết quả
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Xóa sản phẩm
     * 
     * @param int $id ID của sản phẩm cần xóa
     * @return bool Trả về true nếu thao tác thành công, ngược lại false
     */
    public function deleteProduct($id)
    {
        // Truy vấn SQL để xóa sản phẩm
        $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        // Thực thi truy vấn và trả về kết quả
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}