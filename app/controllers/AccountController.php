<?php
/**
 * Lớp AccountController
 * 
 * Lớp điều khiển này quản lý các hoạt động liên quan đến tài khoản người dùng:
 * - Đăng ký
 * - Đăng nhập
 * - Đăng xuất
 * 
 * @author  Web Bán Hàng Team (Dựa trên Plan.md)
 * @version 1.0
 */
require_once 'app/config/database.php';
require_once 'app/models/AccountModel.php';
require_once 'app/helpers/SessionHelper.php';

class AccountController
{
    /**
     * Đối tượng AccountModel để tương tác với dữ liệu tài khoản
     * @var AccountModel
     */
    private $accountModel;
    
    /**
     * Kết nối cơ sở dữ liệu
     * @var PDO
     */
    private $db;

    /**
     * Khởi tạo đối tượng AccountController
     * Thiết lập kết nối cơ sở dữ liệu và khởi tạo model
     */
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
        // Đảm bảo session được khởi tạo
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Hiển thị form đăng ký tài khoản
     */
    public function register()
    {
        // Nếu đã đăng nhập, chuyển hướng về trang sản phẩm
        if (SessionHelper::isLoggedIn()) {
            header('Location: /WEBBANHANG/Product');
            exit();
        }
        include_once 'app/views/account/register.php';
    }

    /**
     * Hiển thị form đăng nhập
     */
    public function login()
    {
        // Nếu đã đăng nhập, chuyển hướng về trang sản phẩm
        if (SessionHelper::isLoggedIn()) {
            header('Location: /WEBBANHANG/Product');
            exit();
        }
        include_once 'app/views/account/login.php';
    }

    /**
     * Xử lý lưu tài khoản mới từ form đăng ký
     */
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username'] ?? '');
            $fullname = trim($_POST['fullname'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmPassword'] ?? '';
            $role = trim($_POST['role'] ?? 'user'); // Lấy vai trò từ form, mặc định là 'user'

            $errors = [];

            // Kiểm tra dữ liệu đầu vào
            if (empty($username)) {
                $errors['username'] = "Tên đăng nhập không được để trống.";
            } elseif (strlen($username) < 3) {
                 $errors['username'] = "Tên đăng nhập phải có ít nhất 3 ký tự.";
            }

            if (empty($fullname)) {
                $errors['fullname'] = "Họ và tên không được để trống.";
            }

            if (empty($password)) {
                $errors['password'] = "Mật khẩu không được để trống.";
            } elseif (strlen($password) < 6) {
                $errors['password'] = "Mật khẩu phải có ít nhất 6 ký tự.";
            }

            if ($password !== $confirmPassword) {
                $errors['confirmPassword'] = "Mật khẩu xác nhận không khớp.";
            }

            // Kiểm tra vai trò hợp lệ
            if (!in_array($role, ['user', 'admin'])) {
                $errors['role'] = "Vai trò tài khoản không hợp lệ.";
                $role = 'user'; // Đặt lại về mặc định nếu không hợp lệ
            }

            // Kiểm tra xem tên đăng nhập đã tồn tại chưa
            if (empty($errors['username']) && $this->accountModel->getAccountByUsername($username)) {
                $errors['username'] = "Tên đăng nhập này đã được sử dụng.";
            }

            if (count($errors) > 0) {
                // Truyền lỗi và dữ liệu đã nhập lại cho view
                $_SESSION['form_data'] = $_POST;
                $_SESSION['form_errors'] = $errors;
                header('Location: /WEBBANHANG/Account/register');
                exit();
            } else {
                // Mã hóa mật khẩu
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                // Lưu tài khoản vào cơ sở dữ liệu với vai trò đã chọn
                if ($this->accountModel->save($username, $fullname, $hashedPassword, $role)) {
                    $_SESSION['success_message'] = "Đăng ký tài khoản thành công! Vui lòng đăng nhập.";
                    header('Location: /WEBBANHANG/Account/login');
                    exit();
                } else {
                    $_SESSION['form_data'] = $_POST;
                    $_SESSION['form_errors'] = ['general' => 'Đã có lỗi xảy ra trong quá trình đăng ký. Vui lòng thử lại.'];
                    header('Location: /WEBBANHANG/Account/register');
                    exit();
                }
            }
        } else {
            // Nếu không phải POST request, chuyển hướng về trang đăng ký
            header('Location: /WEBBANHANG/Account/register');
            exit();
        }
    }

    /**
     * Xử lý đăng xuất người dùng
     */
    public function logout()
    {
        SessionHelper::destroyUserSession();
        header('Location: /WEBBANHANG/Product'); // Chuyển hướng về trang danh sách sản phẩm
        exit();
    }

    /**
     * Xử lý kiểm tra thông tin đăng nhập
     */
    public function checkLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $errors = [];

            if (empty($username)) {
                $errors['username'] = "Tên đăng nhập không được để trống.";
            }
            if (empty($password)) {
                $errors['password'] = "Mật khẩu không được để trống.";
            }

            if (count($errors) > 0) {
                $_SESSION['form_data'] = $_POST;
                $_SESSION['form_errors'] = $errors;
                header('Location: /WEBBANHANG/Account/login');
                exit();
            }

            $account = $this->accountModel->getAccountByUsername($username);

            if ($account) {
                // Xác thực mật khẩu
                if (password_verify($password, $account->password)) {
                    // Đăng nhập thành công, lưu thông tin vào session
                    SessionHelper::setUserSession($account);
                    header('Location: /WEBBANHANG/Product'); // Chuyển hướng về trang danh sách sản phẩm
                    exit();
                } else {
                    // Sai mật khẩu
                    $errors['password'] = "Tên đăng nhập hoặc mật khẩu không chính xác.";
                }
            } else {
                // Không tìm thấy tài khoản
                $errors['username'] = "Tên đăng nhập hoặc mật khẩu không chính xác.";
            }

            // Nếu có lỗi (sai tk/mk)
            $_SESSION['form_data'] = $_POST; // Giữ lại username đã nhập
            $_SESSION['form_errors'] = $errors;
            header('Location: /WEBBANHANG/Account/login');
            exit();

        } else {
             // Nếu không phải POST request, chuyển hướng về trang đăng nhập
            header('Location: /WEBBANHANG/Account/login');
            exit();
        }
    }
} 