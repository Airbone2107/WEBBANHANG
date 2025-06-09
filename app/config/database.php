<?php
/**
 * Lớp Database
 * 
 * Lớp này chịu trách nhiệm tạo và quản lý kết nối đến cơ sở dữ liệu MySQL
 * sử dụng PDO (PHP Data Objects) cho ứng dụng Web Bán Hàng.
 * 
 * @author  Web Bán Hàng Team
 * @version 1.0
 */
class Database {
    /**
     * Địa chỉ máy chủ cơ sở dữ liệu
     * @var string
     */
    private $host = "localhost";
    
    /**
     * Tên cơ sở dữ liệu
     * @var string
     */
    private $db_name = "my_store";
    
    /**
     * Tên người dùng cơ sở dữ liệu
     * @var string
     */
    private $username = "root";
    
    /**
     * Mật khẩu cơ sở dữ liệu
     * @var string
     */
    private $password = "";
    
    /**
     * Kết nối PDO đến cơ sở dữ liệu
     * @var PDO
     */
    public $conn;

    /**
     * Thiết lập kết nối đến cơ sở dữ liệu
     * 
     * Phương thức này tạo một kết nối PDO mới đến cơ sở dữ liệu MySQL
     * và thiết lập bảng mã UTF-8 cho kết nối.
     * 
     * @return PDO|null Trả về đối tượng kết nối PDO hoặc null nếu kết nối thất bại
     */
    public function getConnection() {
        $this->conn = null;

        try {
            // Tạo kết nối PDO với cơ sở dữ liệu MySQL
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Thiết lập bảng mã UTF-8 cho kết nối
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            // Hiển thị thông báo lỗi nếu kết nối thất bại
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}