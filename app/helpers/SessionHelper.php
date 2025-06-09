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