# Tài liệu Dự án Web Bán Hàng

## Tổng quan

Dự án này là một ứng dụng web bán hàng đơn giản được xây dựng bằng PHP thuần, theo mô hình MVC (Model-View-Controller) cơ bản.

## Cấu trúc thư mục

Dưới đây là mô tả chi tiết về cấu trúc thư mục của dự án:

```
.
├── app/                      # Thư mục chính chứa mã nguồn của ứng dụng
│   ├── config/               # Chứa các file cấu hình (ví dụ: kết nối cơ sở dữ liệu)
│   ├── controllers/          # Chứa các controller xử lý yêu cầu từ người dùng
│   ├── models/               # Chứa các model tương tác với cơ sở dữ liệu
│   └── views/                # Chứa các file giao diện người dùng (HTML, PHP)
│       ├── category/         # Views liên quan đến quản lý danh mục
│       ├── product/          # Views liên quan đến quản lý sản phẩm
│       └── shares/           # Views dùng chung (header, footer, ...)
├── uploads/                  # Thư mục chứa các file được người dùng tải lên (ví dụ: hình ảnh sản phẩm)
├── .htaccess                 # File cấu hình của Apache để điều hướng URL
├── index.php                 # Điểm vào chính của ứng dụng (Front Controller)
└── Document.md               # File này, mô tả tổng quan dự án
```

### Giải thích vai trò của từng thư mục và file chính:

*   **`app/`**: Thư mục gốc chứa toàn bộ logic và các thành phần cốt lõi của ứng dụng.
    *   **`app/config/`**: Lưu trữ các file cấu hình cho ứng dụng. Ví dụ, file `database.php` chứa thông tin kết nối đến cơ sở dữ liệu.
    *   **`app/controllers/`**: Các file PHP trong thư mục này đóng vai trò là Controller trong mô hình MVC. Chúng nhận yêu cầu từ người dùng (thông qua `index.php`), xử lý logic nghiệp vụ, tương tác với Model để lấy dữ liệu, và sau đó chọn View phù hợp để hiển thị kết quả cho người dùng.
    *   **`app/models/`**: Chứa các file PHP định nghĩa Model. Model chịu trách nhiệm tương tác với cơ sở dữ liệu (truy vấn, thêm, sửa, xóa dữ liệu) và cung cấp dữ liệu cho Controller.
    *   **`app/views/`**: Bao gồm các file template (thường là HTML trộn lẫn với PHP) để hiển thị giao diện người dùng.
        *   **`app/views/category/`**: Chứa các view cụ thể cho việc hiển thị và quản lý danh mục sản phẩm (ví dụ: danh sách danh mục, form thêm/sửa danh mục).
        *   **`app/views/product/`**: Chứa các view cụ thể cho việc hiển thị và quản lý sản phẩm (ví dụ: danh sách sản phẩm, chi tiết sản phẩm, form thêm/sửa sản phẩm).
        *   **`app/views/shares/`**: Chứa các phần giao diện được sử dụng lại ở nhiều trang, như header, footer, sidebar.
*   **`uploads/`**: Thư mục này được sử dụng để lưu trữ các tệp tin mà người dùng tải lên, ví dụ như hình ảnh sản phẩm.
*   **`.htaccess`**: Đây là file cấu hình cho web server Apache. Trong dự án này, nó được sử dụng để điều hướng tất cả các yêu cầu đến `index.php` (URL Rewriting), giúp tạo ra các URL thân thiện hơn.
*   **`index.php`**: Đây là điểm vào (entry point) duy nhất của ứng dụng. Mọi yêu cầu từ trình duyệt đều được chuyển hướng đến file này. `index.php` sẽ phân tích URL để xác định Controller và Action tương ứng cần được gọi để xử lý yêu cầu.