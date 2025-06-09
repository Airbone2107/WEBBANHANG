# Thư mục `app/models`

Thư mục `models` chứa các lớp PHP đóng vai trò là **Model** trong mô hình kiến trúc Model-View-Controller (MVC).

## Vai trò của Models:

*   **Đại diện cho dữ liệu**: Model thường đại diện cho một thực thể hoặc một bảng dữ liệu trong cơ sở dữ liệu (ví dụ: `ProductModel` cho bảng sản phẩm, `CategoryModel` cho bảng danh mục).
*   **Tương tác với cơ sở dữ liệu**: Model chứa logic để thực hiện các thao tác với cơ sở dữ liệu như:
    *   Truy vấn dữ liệu (SELECT)
    *   Thêm mới dữ liệu (INSERT)
    *   Cập nhật dữ liệu (UPDATE)
    *   Xóa dữ liệu (DELETE)
*   **Cung cấp dữ liệu cho Controllers**: Model cung cấp các phương thức để Controller có thể lấy hoặc thao tác dữ liệu một cách dễ dàng mà không cần biết chi tiết về cách dữ liệu được lưu trữ hay truy vấn.
*   **Validate dữ liệu (tùy chọn)**: Trong một số trường hợp, Model cũng có thể chứa logic để kiểm tra tính hợp lệ của dữ liệu trước khi lưu vào cơ sở dữ liệu.

## Ví dụ:

*   `ProductModel.php`: Chứa các phương thức như `getProducts()`, `getProductById($id)`, `addProduct(...)`, `updateProduct(...)`, `deleteProduct($id)`.
*   `CategoryModel.php`: Chứa các phương thức tương tự để quản lý dữ liệu danh mục.

Sử dụng Model giúp tách biệt logic truy cập dữ liệu khỏi Controller, làm cho mã nguồn dễ đọc, dễ bảo trì và dễ tái sử dụng hơn.