<?php
/**
 * Lớp AccountModel
 * 
 * Lớp này chịu trách nhiệm quản lý và thao tác với dữ liệu tài khoản người dùng
 * trong cơ sở dữ liệu của ứng dụng Web Bán Hàng.
 * 
 * @author  Web Bán Hàng Team (Dựa trên Plan.md)
 * @version 1.0
 */
class AccountModel
{
    /**
     * Kết nối PDO đến cơ sở dữ liệu
     * @var PDO
     */
    private $conn;
    
    /**
     * Tên bảng tài khoản trong cơ sở dữ liệu
     * @var string
     */
    private $table_name = "account";

    /**
     * Khởi tạo đối tượng AccountModel
     * 
     * @param PDO $db Đối tượng kết nối PDO đến cơ sở dữ liệu
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Lấy thông tin tài khoản bằng tên đăng nhập
     * 
     * @param string $username Tên đăng nhập
     * @return object|false Đối tượng tài khoản hoặc false nếu không tìm thấy
     */
    public function getAccountByUsername($username)
    {
        $query = "SELECT id, username, password, fullname, role, created_at FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Lưu tài khoản mới vào cơ sở dữ liệu
     * 
     * @param string $username Tên đăng nhập
     * @param string $fullname Họ và tên
     * @param string $hashedPassword Mật khẩu đã được mã hóa
     * @param string $role Vai trò (mặc định 'user')
     * @return bool True nếu lưu thành công, False nếu thất bại
     */
    public function save($username, $fullname, $hashedPassword, $role = "user")
    {
        // Kiểm tra xem username đã tồn tại chưa để tránh lỗi UNIQUE constraint từ DB
        if ($this->getAccountByUsername($username)) {
            return false; // Username đã tồn tại
        }

        $query = "INSERT INTO " . $this->table_name . " (username, fullname, password, role) VALUES (:username, :fullname, :password, :role)";
        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu
        $username = htmlspecialchars(strip_tags($username));
        $fullname = htmlspecialchars(strip_tags($fullname));
        // $hashedPassword đã được hash, không cần strip_tags
        $role = htmlspecialchars(strip_tags($role));

        // Gán các tham số
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);

        // Thực thi truy vấn
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
} 