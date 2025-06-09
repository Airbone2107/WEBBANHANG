Tuyệt vời! Để tích hợp hệ thống xác thực người dùng từ `Plan.md` vào dự án hiện tại của bạn, tôi sẽ tạo một file `TODO.md` chi tiết. File này sẽ bao gồm tất cả các bước cần thiết, mã nguồn đầy đủ cho các file mới hoặc file được chỉnh sửa, và đảm bảo tính tương thích với cấu trúc và giao diện hiện tại của dự án.

Dưới đây là nội dung cho file `TODO.md`:

```markdown
<!-- TODO.md -->
# TODO: Tích hợp Hệ thống Xác thực Người dùng vào Dự án

Tài liệu này hướng dẫn từng bước để tích hợp các tính năng xác thực người dùng (đăng ký, đăng nhập, đăng xuất) từ `Plan.md` vào dự án Web Bán Hàng hiện tại. Các thay đổi sẽ được thực hiện để phù hợp với giao diện và cấu trúc hiện có của dự án.

**LƯU Ý QUAN TRỌNG:**
*   **Backup Dự Án:** Trước khi bắt đầu, hãy tạo một bản sao lưu (backup) toàn bộ thư mục dự án của bạn để đảm bảo an toàn dữ liệu.
*   **Đường dẫn URL:** Dự án hiện tại sử dụng base URL là `/WEBBANHANG/`. Tất cả các đường dẫn trong code mới sẽ tuân theo quy ước này.
*   **Bootstrap 5:** Các view mới sẽ được thiết kế để phù hợp với Bootstrap 5 và Font Awesome đã được sử dụng trong dự án.

## Mục lục
1.  [Bước 1: Thiết lập Cơ sở dữ liệu](#bước-1-thiết-lập-cơ-sở-dữ-liệu)
2.  [Bước 2: Tạo Thư mục Helpers](#bước-2-tạo-thư-mục-helpers)
3.  [Bước 3: Tạo SessionHelper](#bước-3-tạo-sessionhelper)
4.  [Bước 4: Tạo AccountModel](#bước-4-tạo-accountmodel)
5.  [Bước 5: Tạo AccountController](#bước-5-tạo-accountcontroller)
6.  [Bước 6: Tạo Views cho Account](#bước-6-tạo-views-cho-account)
    *   [6.1. Tạo thư mục `app/views/account`](#61-tạo-thư-mục-appviewsaccount)
    *   [6.2. Tạo View Đăng ký (`register.php`)](#62-tạo-view-đăng-ký-registerphp)
    *   [6.3. Tạo View Đăng nhập (`login.php`)](#63-tạo-view-đăng-nhập-loginphp)
7.  [Bước 7: Cập nhật Header Chung](#bước-7-cập-nhật-header-chung)
8.  [Bước 8: Cập nhật File Điều hướng Chính (`index.php`)](#bước-8-cập-nhật-file-điều-hướng-chính-indexphp)
9.  [Bước 9: Kiểm tra và Hoàn thiện](#bước-9-kiểm-tra-và-hoàn-thiện)

---

## Bước 1: Thiết lập Cơ sở dữ liệu

**Mục tiêu:** Tạo bảng `account` trong cơ sở dữ liệu `my_store` để lưu trữ thông tin người dùng.

**Thực hiện:**
Kết nối vào công cụ quản lý cơ sở dữ liệu của bạn (ví dụ: phpMyAdmin) và thực thi câu lệnh SQL sau để tạo bảng `account`. Bảng này sẽ bao gồm các cột `id`, `username`, `password`, `fullname`, `role`, và `created_at`.

```sql
CREATE TABLE `my_store`.`account` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `fullname` VARCHAR(255) NOT NULL,
    `role` VARCHAR(50) NOT NULL DEFAULT 'user',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Bảng này sẽ lưu trữ:
*   `username`: Tên đăng nhập (duy nhất).
*   `password`: Mật khẩu đã được mã hóa.
*   `fullname`: Họ và tên đầy đủ của người dùng.
*   `role`: Vai trò của người dùng (ví dụ: 'user', 'admin'). Mặc định là 'user'.
*   `created_at`: Thời gian tài khoản được tạo.

---

## Bước 2: Tạo Thư mục Helpers

**Mục tiêu:** Tạo thư mục `app/helpers` nếu nó chưa tồn tại. Thư mục này sẽ chứa các lớp tiện ích.

**Thực hiện:**
Kiểm tra trong thư mục `app/` của dự án. Nếu chưa có thư mục con tên là `helpers`, hãy tạo nó.
Cấu trúc thư mục sẽ là:
```
app/
├── config/
├── controllers/
├── helpers/     <-- Thư mục cần tạo (nếu chưa có)
├── models/
└── views/
```

---

## Bước 3: Tạo SessionHelper

**Mục tiêu:** Tạo file `SessionHelper.php` để quản lý các hàm liên quan đến session, giúp kiểm tra trạng thái đăng nhập và vai trò người dùng.

**Thực hiện:**
Tạo file mới tại đường dẫn `app/helpers/SessionHelper.php` với nội dung sau:

```markdown
<!-- app/helpers/SessionHelper.php -->
<?php

class SessionHelper
{
    /**
     * Kiểm tra xem người dùng đã đăng nhập hay chưa.
     * @return bool True nếu đã đăng nhập, False nếu chưa.
     */
    public static function isLoggedIn()
    {
        // session_status() == PHP_SESSION_NONE thì gọi session_start()
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    /**
     * Kiểm tra xem người dùng có phải là admin hay không.
     * @return bool True nếu là admin, False nếu không phải.
     */
    public static function isAdmin()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    /**
     * Lấy thông tin người dùng từ session.
     * @param string $key Khóa thông tin cần lấy (ví dụ: 'user_id', 'username', 'fullname', 'user_role').
     * @return mixed Giá trị của thông tin hoặc null nếu không tồn tại.
     */
    public static function getUser($key = null)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if ($key === null) {
            // Trả về tất cả thông tin người dùng nếu có
            $userInfo = [];
            if (isset($_SESSION['user_id'])) $userInfo['user_id'] = $_SESSION['user_id'];
            if (isset($_SESSION['username'])) $userInfo['username'] = $_SESSION['username'];
            if (isset($_SESSION['fullname'])) $userInfo['fullname'] = $_SESSION['fullname'];
            if (isset($_SESSION['user_role'])) $userInfo['user_role'] = $_SESSION['user_role'];
            return !empty($userInfo) ? (object)$userInfo : null;
        }
        return $_SESSION[$key] ?? null;
    }

    /**
     * Thiết lập session cho người dùng sau khi đăng nhập thành công.
     * @param object $user Đối tượng người dùng từ database (phải có id, username, fullname, role).
     */
    public static function setUserSession($user)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['fullname'] = $user->fullname;
        $_SESSION['user_role'] = $user->role;
    }

    /**
     * Xóa session người dùng (Đăng xuất).
     */
    public static function destroyUserSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['fullname']);
        unset($_SESSION['user_role']);
        // Để an toàn hơn, có thể hủy toàn bộ session nếu không còn gì cần giữ lại
        // session_destroy(); 
    }
}
```

---

## Bước 4: Tạo AccountModel

**Mục tiêu:** Tạo file `AccountModel.php` để xử lý các tương tác với bảng `account` trong cơ sở dữ liệu.

**Thực hiện:**
Tạo file mới tại đường dẫn `app/models/AccountModel.php` với nội dung sau:

```markdown
<!-- app/models/AccountModel.php -->
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
```

---

## Bước 5: Tạo AccountController

**Mục tiêu:** Tạo file `AccountController.php` để xử lý logic liên quan đến đăng ký, đăng nhập và đăng xuất người dùng.

**Thực hiện:**
Tạo file mới tại đường dẫn `app/controllers/AccountController.php` với nội dung sau:

```markdown
<!-- app/controllers/AccountController.php -->
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

                // Lưu tài khoản vào cơ sở dữ liệu
                // Mặc định vai trò là 'user'
                if ($this->accountModel->save($username, $fullname, $hashedPassword, 'user')) {
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
```

---

## Bước 6: Tạo Views cho Account

### 6.1. Tạo thư mục `app/views/account`
**Mục tiêu:** Tạo thư mục chứa các view liên quan đến tài khoản người dùng.

**Thực hiện:**
Trong thư mục `app/views/`, tạo một thư mục con mới tên là `account`.
Cấu trúc thư mục sẽ là:
```
app/
└── views/
    ├── account/     <-- Thư mục cần tạo
    ├── category/
    ├── product/
    └── shares/
```

### 6.2. Tạo View Đăng ký (`register.php`)

**Mục tiêu:** Tạo file `register.php` để hiển thị form đăng ký tài khoản.

**Thực hiện:**
Tạo file mới tại đường dẫn `app/views/account/register.php` với nội dung sau. File này sẽ sử dụng header, footer chung và được thiết kế theo Bootstrap 5.

```markdown
<!-- app/views/account/register.php -->
<?php 
include 'app/views/shares/header.php'; 

// Lấy lỗi và dữ liệu cũ từ session nếu có (sau khi redirect)
$errors = $_SESSION['form_errors'] ?? [];
$old_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_data']);
?>

<div class="main-content">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="card-title text-center fw-bold mb-4"><i class="fas fa-user-plus me-2 text-primary"></i>Đăng ký tài khoản</h2>
                        
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($errors['general']); ?></div>
                        <?php endif; ?>

                        <form action="/WEBBANHANG/Account/save" method="POST" novalidate>
                            <div class="mb-3">
                                <label for="fullname" class="form-label fw-medium">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php echo isset($errors['fullname']) ? 'is-invalid' : ''; ?>" 
                                       id="fullname" name="fullname" 
                                       value="<?php echo htmlspecialchars($old_data['fullname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                <?php if (isset($errors['fullname'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['fullname']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label fw-medium">Tên đăng nhập <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                                       id="username" name="username" 
                                       value="<?php echo htmlspecialchars($old_data['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                <div class="form-text">Ít nhất 3 ký tự.</div>
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['username']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-medium">Mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                       id="password" name="password" required>
                                <div class="form-text">Ít nhất 6 ký tự.</div>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['password']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-4">
                                <label for="confirmPassword" class="form-label fw-medium">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" class="form-control <?php echo isset($errors['confirmPassword']) ? 'is-invalid' : ''; ?>" 
                                       id="confirmPassword" name="confirmPassword" required>
                                <?php if (isset($errors['confirmPassword'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['confirmPassword']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check-circle me-1"></i> Đăng ký
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-0">Đã có tài khoản? <a href="/WEBBANHANG/Account/login" class="fw-bold text-decoration-none">Đăng nhập ngay</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
```

### 6.3. Tạo View Đăng nhập (`login.php`)

**Mục tiêu:** Tạo file `login.php` để hiển thị form đăng nhập.

**Thực hiện:**
Tạo file mới tại đường dẫn `app/views/account/login.php` với nội dung sau:

```markdown
<!-- app/views/account/login.php -->
<?php 
include 'app/views/shares/header.php'; 

// Lấy lỗi và dữ liệu cũ từ session nếu có
$errors = $_SESSION['form_errors'] ?? [];
$old_data = $_SESSION['form_data'] ?? [];
$success_message = $_SESSION['success_message'] ?? null;

unset($_SESSION['form_errors'], $_SESSION['form_data'], $_SESSION['success_message']);
?>

<div class="main-content">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="card-title text-center fw-bold mb-4"><i class="fas fa-sign-in-alt me-2 text-primary"></i>Đăng nhập</h2>
                        
                        <?php if ($success_message): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($errors['general']); ?></div>
                        <?php endif; ?>

                        <form action="/WEBBANHANG/Account/checkLogin" method="POST" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label fw-medium">Tên đăng nhập <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                                       id="username" name="username" 
                                       value="<?php echo htmlspecialchars($old_data['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['username']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-medium">Mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                       id="password" name="password" required>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['password']); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Ghi nhớ đăng nhập</label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-1"></i> Đăng nhập
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-2"><a href="#" class="text-decoration-none">Quên mật khẩu?</a></p>
                            <p class="mb-0">Chưa có tài khoản? <a href="/WEBBANHANG/Account/register" class="fw-bold text-decoration-none">Đăng ký ngay</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
```

---

## Bước 7: Cập nhật Header Chung

**Mục tiêu:** Cập nhật file `app/views/shares/header.php` để hiển thị các liên kết "Đăng nhập", "Đăng ký" hoặc "Tên người dùng", "Đăng xuất" tùy theo trạng thái đăng nhập.

**Thực hiện:**
Mở file `app/views/shares/header.php` và **thay thế toàn bộ nội dung** bằng code sau. Đảm bảo `SessionHelper.php` được include trước khi sử dụng. Trong trường hợp này, `index.php` sẽ load nó.

```markdown
<!-- app/views/shares/header.php -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Bán Hàng</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom styles -->
    <style>
        /* Inline styles for immediate use */
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        main {
            flex: 1;
        }
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 15px;
        }
        @media (min-width: 768px) {
            .main-content {
                padding: 20px 30px;
            }
        }
        @media (min-width: 992px) {
            .main-content {
                padding: 25px 50px;
            }
        }
        .product-card {
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.12);
        }
        /* Dark mode styling */
        body.dark-mode {
            background-color: #212529;
            color: #f8f9fa;
        }
        body.dark-mode .bg-light {
            background-color: #343a40 !important;
        }
        body.dark-mode .text-dark {
            color: #f8f9fa !important;
        }
        body.dark-mode .card {
            background-color: #343a40;
            color: #f8f9fa;
        }
        body.dark-mode .navbar-light .navbar-nav .nav-link {
            color: rgba(248, 249, 250, 0.8);
        }
        body.dark-mode .navbar-light .navbar-brand {
            color: #f8f9fa;
        }
        body.dark-mode .list-group-item {
            background-color: #343a40;
            color: #f8f9fa;
            border-color: #495057;
        }
        body.dark-mode .table {
            color: #f8f9fa;
        }
        body.dark-mode .card-header {
            border-bottom: 1px solid #495057;
        }
        body.dark-mode .modal-content {
            background-color: #343a40;
            color: #f8f9fa;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="/WEBBANHANG/Product">
                    <i class="fas fa-shopping-cart me-2 text-primary"></i>
                    <span class="fw-bold">Web Bán Hàng</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#navbarNav" aria-controls="navbarNav" 
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="productDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-box me-1"></i> Sản phẩm
                            </a>
                            <ul class="dropdown-menu shadow" aria-labelledby="productDropdown">
                                <li><a class="dropdown-item" href="/WEBBANHANG/Product/"><i class="fas fa-list me-1"></i> Danh sách sản phẩm</a></li>
                                <li><a class="dropdown-item" href="/WEBBANHANG/Product/add"><i class="fas fa-plus me-1"></i> Thêm sản phẩm</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-tags me-1"></i> Danh mục
                            </a>
                            <ul class="dropdown-menu shadow" aria-labelledby="categoryDropdown">
                                <li><a class="dropdown-item" href="/WEBBANHANG/Category/"><i class="fas fa-list me-1"></i> Danh sách danh mục</a></li>
                                <li><a class="dropdown-item" href="/WEBBANHANG/Category/add"><i class="fas fa-plus me-1"></i> Thêm danh mục</a></li>
                            </ul>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center">
                        <a href="/WEBBANHANG/Product/cart" class="btn btn-outline-primary me-3 position-relative">
                            <i class="fas fa-shopping-cart"></i>
                            <?php 
                            // Đếm số lượng sản phẩm trong giỏ hàng
                            $cartItemCount = 0;
                            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                                foreach ($_SESSION['cart'] as $item) {
                                    $cartItemCount += $item['quantity'];
                                }
                            }
                            if ($cartItemCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $cartItemCount; ?>
                                <span class="visually-hidden">unread messages</span>
                            </span>
                            <?php endif; ?>
                        </a>

                        <?php if (SessionHelper::isLoggedIn()): ?>
                            <?php $currentUser = SessionHelper::getUser(); ?>
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($currentUser->fullname ?? $currentUser->username); ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-1"></i> Hồ sơ</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-1"></i> Cài đặt</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/WEBBANHANG/Account/logout"><i class="fas fa-sign-out-alt me-1"></i> Đăng xuất</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="/WEBBANHANG/Account/login" class="btn btn-outline-success me-2">
                                <i class="fas fa-sign-in-alt me-1"></i> Đăng nhập
                            </a>
                            <a href="/WEBBANHANG/Account/register" class="btn btn-success">
                                <i class="fas fa-user-plus me-1"></i> Đăng ký
                            </a>
                        <?php endif; ?>
                        
                        <button id="darkModeToggle" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-moon"></i>
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="py-4">
<?php
// File app/views/shares/footer.php sẽ đóng thẻ main và body, html
// Không cần đóng ở đây.
?>
```

---

## Bước 8: Cập nhật File Điều hướng Chính (`index.php`)

**Mục tiêu:** Cập nhật file `index.php` ở thư mục gốc của dự án để nó có thể gọi `SessionHelper` và xử lý các route cho `AccountController`. Đồng thời, thay đổi controller mặc định thành `ProductController` nếu không có controller nào được chỉ định.

**Thực hiện:**
Mở file `index.php` (ở thư mục gốc) và **thay thế toàn bộ nội dung** bằng code sau:

```markdown
<!-- index.php -->
<?php
// Luôn bắt đầu session ở đầu file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Các file cần thiết
require_once 'app/helpers/SessionHelper.php'; // Load SessionHelper sớm
// Các model và file khác sẽ được controller tự require nếu cần

// Phân tích URL
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', $url);

// Xác định Controller
// Mặc định là ProductController nếu không có controller nào trong URL
$controllerName = !empty($urlParts[0]) ? ucfirst(strtolower($urlParts[0])) . 'Controller' : 'ProductController';

// Xác định Action
// Mặc định là 'index' nếu không có action nào trong URL
$action = isset($urlParts[1]) && !empty($urlParts[1]) ? strtolower($urlParts[1]) : 'index';

// Tham số
$params = array_slice($urlParts, 2);

// Kiểm tra file controller có tồn tại không
$controllerFile = 'app/controllers/' . $controllerName . '.php';
if (!file_exists($controllerFile)) {
    // Nếu controller không tồn tại, có thể hiển thị trang 404 hoặc chuyển hướng
    // Hiện tại, để đơn giản, báo lỗi và dừng.
    // Trong thực tế, bạn nên có một trang lỗi thân thiện hơn.
    // Ví dụ: header("HTTP/1.0 404 Not Found"); include 'app/views/errors/404.php'; exit();
    die('Lỗi: Controller "' . htmlspecialchars($controllerName) . '" không tìm thấy.');
}

require_once $controllerFile;

// Kiểm tra lớp controller có tồn tại không
if (!class_exists($controllerName)) {
    die('Lỗi: Lớp Controller "' . htmlspecialchars($controllerName) . '" không được định nghĩa trong file.');
}

$controller = new $controllerName();

// Kiểm tra action có tồn tại trong controller không
if (!method_exists($controller, $action)) {
    // Nếu action không tồn tại, có thể hiển thị trang 404 hoặc gọi action mặc định (ví dụ: index)
    // Hiện tại, báo lỗi và dừng.
    die('Lỗi: Action "' . htmlspecialchars($action) . '" không tìm thấy trong Controller "' . htmlspecialchars($controllerName) . '".');
}

// Gọi action với các tham số
call_user_func_array([$controller, $action], $params);
```

---

## Bước 9: Kiểm tra và Hoàn thiện

**Mục tiêu:** Kiểm tra tất cả các chức năng mới và đảm bảo chúng hoạt động chính xác.

**Thực hiện:**
1.  **Xóa cache trình duyệt (nếu cần):** Để đảm bảo bạn thấy các thay đổi mới nhất.
2.  **Kiểm tra chức năng Đăng ký:**
    *   Truy cập `/WEBBANHANG/Account/register`.
    *   Thử đăng ký với thông tin hợp lệ.
    *   Thử đăng ký với thông tin không hợp lệ (để trống, mật khẩu không khớp, username đã tồn tại) và kiểm tra thông báo lỗi.
    *   Sau khi đăng ký thành công, bạn có được chuyển hướng đến trang đăng nhập với thông báo thành công không?
3.  **Kiểm tra chức năng Đăng nhập:**
    *   Truy cập `/WEBBANHANG/Account/login`.
    *   Thử đăng nhập với tài khoản vừa đăng ký.
    *   Thử đăng nhập với thông tin sai (sai username, sai password) và kiểm tra thông báo lỗi.
    *   Sau khi đăng nhập thành công, bạn có được chuyển hướng đến trang sản phẩm không? Header có hiển thị tên người dùng và nút "Đăng xuất" không?
4.  **Kiểm tra chức năng Đăng xuất:**
    *   Nhấn vào nút "Đăng xuất".
    *   Bạn có được chuyển hướng về trang sản phẩm không? Header có trở lại trạng thái "Đăng nhập", "Đăng ký" không?
5.  **Kiểm tra Session:**
    *   Đăng nhập, sau đó đóng trình duyệt và mở lại. Bạn có còn đăng nhập không (nếu chưa làm chức năng "ghi nhớ tôi")?
    *   Thử truy cập các trang yêu cầu đăng nhập (nếu có) khi chưa đăng nhập.
    *   Thử truy cập trang đăng nhập/đăng ký khi đã đăng nhập (bạn sẽ được chuyển hướng đi).
6.  **Kiểm tra tính tương thích:**
    *   Các chức năng quản lý sản phẩm, danh mục hiện có còn hoạt động bình thường không?
    *   Giao diện có bị vỡ hay hiển thị sai ở đâu không?
7.  **Xem xét vai trò người dùng (Admin):**
    *   Hiện tại, `AccountModel::save` mặc định vai trò là 'user'. Nếu bạn cần chức năng admin, bạn sẽ cần:
        *   Một cách để tạo tài khoản admin (ví dụ: trực tiếp trong database, hoặc một form đăng ký đặc biệt).
        *   Cập nhật `SessionHelper::isAdmin()` và các controller/view để kiểm tra quyền admin cho các chức năng quản trị.

---

Chúc bạn tích hợp thành công!
```