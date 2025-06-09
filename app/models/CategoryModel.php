<?php 
/**
 * Lớp CategoryModel
 * 
 * Lớp này chịu trách nhiệm quản lý và thao tác với dữ liệu danh mục sản phẩm
 * trong cơ sở dữ liệu của ứng dụng Web Bán Hàng.
 * 
 * @author  Web Bán Hàng Team
 * @version 1.0
 */
class CategoryModel
{
    /**
     * Kết nối PDO đến cơ sở dữ liệu
     * @var PDO
     */
    private $conn;
    
    /**
     * Tên bảng danh mục trong cơ sở dữ liệu
     * @var string
     */
    private $table_name = "category";

    /**
     * Khởi tạo đối tượng CategoryModel
     * 
     * @param PDO $db Đối tượng kết nối PDO đến cơ sở dữ liệu
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Lấy danh sách tất cả các danh mục
     * 
     * @return array Mảng các đối tượng danh mục
     */
    public function getCategories()
    {
        // Truy vấn SQL để lấy tất cả danh mục
        $query = "SELECT id, name, description FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Lấy thông tin danh mục theo ID
     * 
     * @param int $id ID của danh mục cần lấy thông tin
     * @return object|bool Đối tượng danh mục hoặc false nếu không tìm thấy
     */
    public function getCategoryById($id)
    {
        // Truy vấn SQL để lấy danh mục theo ID
        $query = "SELECT id, name, description FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Tạo danh mục mới
     * 
     * @param string $name Tên danh mục
     * @param string $description Mô tả danh mục
     * @return bool Trả về true nếu thao tác thành công, ngược lại false
     */
    public function createCategory($name, $description)
    {
        // Truy vấn SQL để tạo danh mục mới
        $query = "INSERT INTO " . $this->table_name . " (name, description) VALUES (:name, :description)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        return $stmt->execute();
    }

    /**
     * Cập nhật thông tin danh mục
     * 
     * @param int $id ID của danh mục cần cập nhật
     * @param string $name Tên danh mục mới
     * @param string $description Mô tả danh mục mới
     * @return bool Trả về true nếu thao tác thành công, ngược lại false
     */
    public function updateCategory($id, $name, $description)
    {
        // Truy vấn SQL để cập nhật danh mục
        $query = "UPDATE " . $this->table_name . " SET name = :name, description = :description WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    /**
     * Xóa danh mục
     * 
     * @param int $id ID của danh mục cần xóa
     * @return bool Trả về true nếu thao tác thành công, ngược lại false
     */
    public function deleteCategory($id)
    {
        // Truy vấn SQL để xóa danh mục
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
