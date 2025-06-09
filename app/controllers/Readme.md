# Thư mục `app/controllers`

Thư mục `controllers` chứa các lớp PHP đóng vai trò là **Controller** trong mô hình kiến trúc Model-View-Controller (MVC).

## Vai trò của Controllers:

*   **Tiếp nhận yêu cầu**: Controller là điểm đầu tiên xử lý yêu cầu từ người dùng sau khi được định tuyến bởi `index.php`.
*   **Xử lý logic nghiệp vụ**: Dựa trên yêu cầu, controller sẽ thực thi các logic nghiệp vụ cần thiết.
*   **Tương tác với Models**: Controller gọi các phương thức từ Model để truy vấn hoặc cập nhật dữ liệu trong cơ sở dữ liệu.
*   **Chuẩn bị dữ liệu cho Views**: Sau khi nhận dữ liệu từ Model, Controller có thể xử lý hoặc định dạng lại dữ liệu đó.
*   **Chọn và truyền dữ liệu cho Views**: Cuối cùng, Controller chọn View thích hợp để hiển thị kết quả và truyền dữ liệu cần thiết cho View đó.

## Ví dụ:

*   `ProductController.php`: Xử lý các yêu cầu liên quan đến sản phẩm như hiển thị danh sách sản phẩm, xem chi tiết sản phẩm, thêm, sửa, xóa sản phẩm.
*   `CategoryController.php`: Xử lý các yêu cầu liên quan đến danh mục sản phẩm.

Mỗi phương thức công khai trong một lớp Controller thường tương ứng với một "action" hoặc một trang cụ thể của ứng dụng.