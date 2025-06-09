# Plan for User Authentication System

This document outlines the steps to build a basic user authentication system based on the provided PHP and SQL code snippets.

## 1. Database Setup

**Objective:** Create the `account` table (or `users` as specified in SQL, but the model/queries expect `account`) to store user information.

**File:** (N/A - SQL Command)

**SQL Script:**

```sql
CREATE TABLE account (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```
**Note:** The provided `CREATE TABLE` statement uses `users` as the table name, but the `AccountModel` uses `$table_name = "account";` and the `getAccountByUsername` query uses `FROM account`. For consistency with the PHP code, it's recommended to name the table `account` in your database. Additionally, the provided `AccountController::save` sends `fullname` but the `CREATE TABLE` and `AccountModel::save` SQL do not include a `fullname` column. You might need to add `fullname VARCHAR(255)` to your table definition and update `AccountModel::save` to include it.

## 2. Models

**Objective:** Create the `AccountModel` to handle database interactions for user data.

**File:** `app/models/AccountModel.php`

**Code:**

```php
<?php

class AccountModel
{
    private $conn;
    private $table_name = "account"; // Note: The database CREATE TABLE was 'users', but code uses 'account'. Make sure your DB table is 'account'.

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAccountByUsername($username)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function save($username, $name, $password, $role = "user")
    {
        // Note: The original INSERT INTO query only includes 'username', 'password', 'role'.
        // The 'fullname' (passed as $name here) is not saved with this query.
        // To save fullname, you need to add 'fullname' column to the table
        // and include it in the INSERT query and bindParam.
        $query = "INSERT INTO " . $this->table_name . " (\"username\", \"password\", \"role\") VALUES (:username, :password, :role)";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $name = htmlspecialchars(strip_tags($name)); // 'name' is passed, but not used in INSERT statement below for fullname
        $username = htmlspecialchars(strip_tags($username));

        // Bind parameters
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
```

## 3. Controllers

**Objective:** Create the `AccountController` to handle user registration, login, and logout logic, interacting with the `AccountModel` and views.

**File:** `app/controllers/AccountController.php`

**Code:**

```php
<?php
require_once 'app/config/database.php';
require_once 'app/models/AccountModel.php';
require_once 'app/helpers/SessionHelper.php'; // Add this line if SessionHelper is not implicitly loaded

class AccountController
{
    private $AccountModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->AccountModel = new AccountModel($this->db);
    }

    public function register()
    {
        // This will display the registration form.
        include_once 'app/views/account/register.php';
    }

    public function login()
    {
        // This will display the login form.
        include_once 'app/views/account/login.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $fullName = $_POST['fullName'] ?? ''; // Note: This fullname is passed to AccountModel->save but not stored by the model's current SQL.
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmPassword'] ?? '';

            $errors = [];

            // Validation
            if (empty($username)) {
                $errors['username'] = "Vui long nhap userName!";
            }
            if (empty($fullName)) {
                $errors['fullName'] = "Vui long nhap fullName!";
            }
            if (empty($password)) {
                $errors['password'] = "Vui long nhap password!";
            }
            if ($password != $confirmPassword) {
                $errors['confirmPass'] = "Mat khau va xac nhan chua dung";
            }

            // Check if username already exists
            $account = $this->AccountModel->getAccountByUsername($username);

            if ($account) {
                $errors['account'] = "Tai khoan nay da co nguoi dang ky!";
            }

            if (count($errors) > 0) {
                // If there are errors, reload the registration form with error messages.
                include_once 'app/views/account/register.php';
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                // Save user to database
                $result = $this->AccountModel->save($username, $fullName, $hashedPassword); // fullname is passed here

                if ($result) {
                    // Redirect to login page on success
                    header('Location: /webbanhang/account/login');
                    exit();
                } else {
                    // Handle save error (e.g., echo message, redirect to error page)
                    echo "There was an error saving the account.";
                }
            }
        }
    }

    public function logout()
    {
        unset($_SESSION['username']);
        unset($_SESSION['role']);
        header('Location: /webbanhang/product'); // Redirect to product list after logout
        exit();
    }

    public function checkLogin()
    {
        // Check if data is submitted via POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Retrieve account by username
            $account = $this->AccountModel->getAccountByUsername($username);

            if ($account) {
                $pwd_hashed = $account->password; // Get hashed password from DB
                // Verify password
                if (password_verify($password, $pwd_hashed)) {
                    session_start(); // Start session if not already started
                    // Set session variables
                    $_SESSION['user_id'] = $account->id;
                    $_SESSION['user_role'] = $account->role; // Assumes a 'role' column exists in DB
                    $_SESSION['username'] = $account->username;
                    header('Location: /webbanhang/product'); // Redirect to product list
                    exit();
                } else {
                    echo "Password incorrect.";
                }
            } else {
                echo "Bao loi khong thay tai khoan"; // Account not found
            }
        }
    }
}
```

## 4. Views

**Objective:** Create the necessary view files for user interaction (registration, login) and common layout elements (header, footer).

### 4.1. Registration View

**File:** `app/views/account/register.php`

**Code:**

```php
<?php include 'app/views/shares/header.php'; ?>
<?php
// Display errors if any
if (isset($errors)) {
    echo "<ul>";
    foreach ($errors as $err) {
        echo "<li class='text-danger'>" . $err . "</li>";
    }
    echo "</ul>";
}
?>
<div class="card-body p-5 text-center">
    <form class="user" action="/webbanhang/account/save" method="post">
        <div class="form-group row">
            <div class="col-sm-6 mb-3 mb-sm-0">
                <input type="text" class="form-control form-control-user" id="username" name="username" placeholder="username">
            </div>
            <div class="col-sm-6">
                <input type="text" class="form-control form-control-user" id="fullname" name="fullName" placeholder="fullname">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-6 mb-3 mb-sm-0">
                <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="password">
            </div>
            <div class="col-sm-6">
                <input type="password" class="form-control form-control-user" id="confirmPassword" name="confirmPassword" placeholder="confirmPassword">
            </div>
        </div>
        <div class="form-group text-center">
            <button type="submit" class="btn btn-primary btn-icon-split p-3">
                Register
            </button>
        </div>
    </form>
</div>
<?php include 'app/views/shares/footer.php'; ?>
```

### 4.2. Login View

**File:** `app/views/account/login.php`

**Code:**

```php
<?php include 'app/views/shares/header.php'; ?>
<section class="vh-100 gradient-custom">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card bg-dark text-white" style="border-radius: 1rem;">
                    <div class="card-body p-5 text-center">
                        <form action="/webbanhang/account/checklogin" method="post">
                            <div class="mb-md-5 mt-md-4 pb-5">
                                <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
                                <p class="text-white-50 mb-5">Please enter your login and password!</p>

                                <div class="form-outline form-white mb-4">
                                    <input type="text" name="username" id="typeEmailX" class="form-control form-control-lg" />
                                    <label class="form-label" for="typeEmailX">Username</label>
                                </div>

                                <div class="form-outline form-white mb-4">
                                    <input type="password" name="password" id="typePasswordX" class="form-control form-control-lg" />
                                    <label class="form-label" for="typePasswordX">Password</label>
                                </div>

                                <p class="small mb-5 pb-lg-2"><a class="text-white-50" href="#!">Forgot password?</a></p>

                                <button class="btn btn-outline-light btn-lg px-5" type="submit">Login</button>

                                <div class="d-flex justify-content-center text-center mt-4 pt-1">
                                    <a href="#!" class="text-white"><i class="fab fa-facebook-f fa-lg"></i></a>
                                    <a href="#!" class="text-white"><i class="fab fa-twitter fa-lg mx-4 px-2"></i></a>
                                    <a href="#!" class="text-white"><i class="fab fa-google fa-lg"></i></a>
                                </div>
                            </div>

                            <div>
                                <p class="mb-0">Don't have an account? <a href="/webbanhang/account/register" class="text-white-50 fw-bold">Sign Up</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'app/views/shares/footer.php'; ?>
```

### 4.3. Header File

**Objective:** Update the `header.php` to conditionally display login/logout links based on session status using `SessionHelper`.

**File:** `app/views/shares/header.php`

**Code:**

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-image {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Quản lý sản phẩm</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/webbanhang/Product/Danh sách sản phẩm">Danh sách sản phẩm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/webbanhang/Product/add">Thêm sản phẩm</a>
                </li>
                <li class="nav-item">
                    <?php
                    // This assumes SessionHelper is already included or autoloaded
                    if (SessionHelper::isLoggedIn()) {
                        echo "<a class='nav-link' href='#'>" . $_SESSION['username'] . "</a>";
                    } else {
                        echo "<a class='nav-link' href='/webbanhang/account/login'>Login</a>";
                    }
                    ?>
                </li>
                <li class="nav-item">
                    <?php
                    if (SessionHelper::isLoggedIn()) {
                        echo "<a class='nav-link' href='/webbanhang/account/logout'>Logout</a>";
                    }
                    ?>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-4">
```

### 4.4. Footer File

**Objective:** Create the `footer.php` file (no code provided, but implied by `include`).

**File:** `app/views/shares/footer.php`

**Code:**

```php
    <!-- Add any JavaScript links or closing body/html tags here -->
    </div> <!-- Close .container mt-4 from header.php -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
```

## 5. SessionHelper File

**Objective:** Create a `SessionHelper` class to manage session-related checks.

**File:** `app/helpers/SessionHelper.php`

**Code:**

```php
<?php

class SessionHelper
{
    public static function isLoggedIn()
    {
        return isset($_SESSION['username']);
    }

    public static function isAdmin()
    {
        return isset($_SESSION['username']) && $_SESSION['user_role'] === 'admin';
    }
}
```

## 6. Routing (index.php)

**Objective:** Update the main `index.php` file to handle URL routing, dispatching requests to the appropriate controllers and actions.

**File:** `index.php` (at the root of your project)

**Code:**

```php
<?php
session_start(); // Start session at the beginning

// Required files
require_once 'app/models/ProductModel.php'; // ProductModel not provided, but included in original.
require_once 'app/helpers/SessionHelper.php';

// Get URL parts
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Determine controller
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'DefaultController'; // DefaultController not provided.
// Determine action
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// debug line (uncomment to see controller and action)
// die ("controller=$controllerName - action=$action");

// Check if controller file exists
if (!file_exists('app/controllers/' . $controllerName . '.php')) {
    // Handle controller not found
    die('Controller not found!');
}

require_once 'app/controllers/' . $controllerName . '.php';

// Instantiate the controller
$controller = new $controllerName();

// Check if action method exists
if (!method_exists($controller, $action)) {
    // Handle action not found
    die('Action not found!');
}

// Call the action with remaining URL segments as arguments
call_user_func_array([$controller, $action], array_slice($url, 2));