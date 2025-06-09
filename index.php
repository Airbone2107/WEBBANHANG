<?php
// Luôn bắt đầu session ở đầu file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Các file cần thiết
require_once 'app/helpers/SessionHelper.php'; // Load SessionHelper sớm
// Các model và file khác sẽ được controller tự require nếu cần

// Phân tích URL
$fullUrlPath = $_GET['url'] ?? '';
$fullUrlPath = rtrim($fullUrlPath, '/');
$fullUrlPath = filter_var($fullUrlPath, FILTER_SANITIZE_URL);
$urlParts = explode('/', $fullUrlPath);

// Mặc định cho MVC
$controllerNameMvc = 'ProductController';
$actionMvc = 'index';
$paramsMvc = [];

// Base path của ứng dụng (nếu có) - Ví dụ: /WEBBANHANG
// Điều này giúp xác định đúng các phần của URL
// Tuy nhiên, với .htaccess hiện tại, urlParts[0] sẽ là controller hoặc 'api'
// Chúng ta không cần base path ở đây nữa nếu .htaccess đã rewrite đúng.

// Kiểm tra xem có phải là yêu cầu API không
// URL API có dạng: api/controllerNameApi/actionApi/id
if (isset($urlParts[0]) && strtolower($urlParts[0]) === 'api') {
    // Đây là một yêu cầu API
    $apiControllerSegment = $urlParts[1] ?? ''; // Ví dụ: 'product' hoặc 'category'
    $apiControllerName = ucfirst(strtolower($apiControllerSegment)) . 'ApiController'; // Ví dụ: 'ProductApiController'
    
    // Xác định action và id cho API
    $method = $_SERVER['REQUEST_METHOD'];
    $apiAction = '';
    $apiParams = [];

    // URL: api/resource/{id} -> action là show (GET), update (PUT), destroy (DELETE)
    // URL: api/resource      -> action là index (GET), store (POST)
    // Plan.md đề xuất các tên action tường minh trong controller (index, show, store, update, destroy)
    // Router sẽ cần ánh xạ method + URL segment tới các action đó.
    
    // Ví dụ: /api/product/show/123 -> $urlParts[2] = 'show', $urlParts[3] = '123'
    // Ví dụ: /api/product/store -> $urlParts[2] = 'store'
    // Ví dụ: /api/product (GET) -> index
    // Ví dụ: /api/product/{id} (GET) -> show(id)

    $apiActionNameFromUrl = $urlParts[2] ?? null; // Tên action tường minh từ URL nếu có
    $idFromUrl = $urlParts[3] ?? null; // ID nếu action là show, update, destroy VÀ action được chỉ định tường minh

    if ($apiActionNameFromUrl) {
        $apiAction = strtolower($apiActionNameFromUrl);
        if ($idFromUrl) {
            $apiParams[] = $idFromUrl;
        }
    } else {
        // Nếu không có action tường minh, xác định dựa trên method và sự tồn tại của ID (segment thứ 2 sau tên controller)
        // /api/product/123 (GET) -> show(123)
        // /api/product (GET) -> index
        // /api/product (POST) -> store
        // /api/product/123 (PUT) -> update(123)
        // /api/product/123 (DELETE) -> destroy(123)
        $potentialId = $urlParts[2] ?? null;

        switch ($method) {
            case 'GET':
                if ($potentialId) {
                    $apiAction = 'show';
                    $apiParams[] = $potentialId;
                } else {
                    $apiAction = 'index';
                }
                break;
            case 'POST':
                $apiAction = 'store';
                // Dữ liệu POST thường được lấy từ php://input trong controller
                break;
            case 'PUT':
                if ($potentialId) {
                    $apiAction = 'update';
                    $apiParams[] = $potentialId;
                } else {
                    // PUT thường yêu cầu ID
                    http_response_code(400); // Bad Request
                    echo json_encode(['message' => 'Resource ID missing for PUT request. Use format: api/resource/id']);
                    exit;
                }
                break;
            case 'DELETE':
                if ($potentialId) {
                    $apiAction = 'destroy';
                    $apiParams[] = $potentialId;
                } else {
                    // DELETE thường yêu cầu ID
                    http_response_code(400); // Bad Request
                    echo json_encode(['message' => 'Resource ID missing for DELETE request. Use format: api/resource/id']);
                    exit;
                }
                break;
            case 'OPTIONS': // Xử lý CORS preflight
                 // Header CORS đã được thiết lập trong constructor của APIController
                 // Nếu chưa, bạn có thể thêm ở đây:
                 // header('Access-Control-Allow-Origin: *');
                 // header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
                 // header('Access-Control-Allow-Headers: Content-Type, Authorization');
                http_response_code(204); // No Content
                exit;
            default:
                http_response_code(405); // Method Not Allowed
                echo json_encode(['message' => 'Method Not Allowed']);
                exit;
        }
    }


    $controllerFile = 'app/controllers/' . $apiControllerName . '.php';

    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        if (class_exists($apiControllerName)) {
            $controller = new $apiControllerName();
            if (method_exists($controller, $apiAction)) {
                call_user_func_array([$controller, $apiAction], $apiParams);
            } else {
                http_response_code(404);
                echo json_encode(['message' => "Action '$apiAction' not found in controller '$apiControllerName'."]);
            }
        } else {
            http_response_code(404);
            echo json_encode(['message' => "Controller class '$apiControllerName' not found."]);
        }
    } else {
        http_response_code(404);
        echo json_encode(['message' => "Controller file '$controllerFile' not found."]);
    }
} else {
    // Xử lý yêu cầu MVC thông thường
    $controllerNameMvc = !empty($urlParts[0]) ? ucfirst(strtolower($urlParts[0])) . 'Controller' : 'ProductController';
    $actionMvc = isset($urlParts[1]) && !empty($urlParts[1]) ? strtolower($urlParts[1]) : 'index';
    $paramsMvc = array_slice($urlParts, 2);

    $controllerFileMvc = 'app/controllers/' . $controllerNameMvc . '.php';

    if (!file_exists($controllerFileMvc)) {
        // Hiển thị trang lỗi 404 thân thiện
        // include 'app/views/errors/404.php'; // Bạn cần tạo file này
        header("HTTP/1.0 404 Not Found");
        die('Lỗi: Controller "' . htmlspecialchars($controllerNameMvc) . '" không tìm thấy. File: ' . $controllerFileMvc);
    }

    require_once $controllerFileMvc;

    if (!class_exists($controllerNameMvc)) {
        header("HTTP/1.0 404 Not Found");
        die('Lỗi: Lớp Controller "' . htmlspecialchars($controllerNameMvc) . '" không được định nghĩa trong file.');
    }

    $controller = new $controllerNameMvc();

    if (!method_exists($controller, $actionMvc)) {
         header("HTTP/1.0 404 Not Found");
        die('Lỗi: Action "' . htmlspecialchars($actionMvc) . '" không tìm thấy trong Controller "' . htmlspecialchars($controllerNameMvc) . '".');
    }

    call_user_func_array([$controller, $actionMvc], $paramsMvc);
}