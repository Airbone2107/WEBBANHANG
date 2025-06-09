# Thư mục `app`

Thư mục `app` là nơi chứa toàn bộ mã nguồn cốt lõi của ứng dụng Web Bán Hàng. Nó được tổ chức theo kiến trúc Model-View-Controller (MVC) để phân tách rõ ràng các thành phần của ứng dụng.

## Cấu trúc con

*   **`config/`**: Chứa các file cấu hình của ứng dụng.
*   **`controllers/`**: Chứa các lớp Controller, chịu trách nhiệm xử lý logic yêu cầu và điều phối.
*   **`models/`**: Chứa các lớp Model, chịu trách nhiệm tương tác với cơ sở dữ liệu.
*   **`views/`**: Chứa các file View, chịu trách nhiệm hiển thị giao diện người dùng.

Việc phân chia này giúp mã nguồn dễ quản lý, bảo trì và mở rộng hơn.
```

```markdown
<!-- app/config/Readme.md -->
# Thư mục `app/config`

Thư mục `config` chứa các file cấu hình cần thiết cho hoạt động của ứng dụng.

## Các file cấu hình tiêu biểu:

*   **`database.php`**: File này định nghĩa lớp `Database` với các thông tin cấu hình để kết nối đến cơ sở dữ liệu (ví dụ: host, tên cơ sở dữ liệu, username, password). Nó cũng cung cấp phương thức để thiết lập và trả về một đối tượng kết nối PDO.

Việc tách riêng các thông tin cấu hình giúp dễ dàng thay đổi cài đặt mà không cần sửa đổi mã nguồn logic của ứng dụng, đặc biệt hữu ích khi triển khai ứng dụng trên các môi trường khác nhau (development, staging, production).