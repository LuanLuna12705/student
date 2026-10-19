# Web Sự Kiện

## Tổng quan

Dự án này là một ứng dụng Laravel để quản lý thông tin sinh viên, hoạt động ngoại khóa, sự kiện và câu lạc bộ trong môi trường trường học. Mục tiêu chính là giúp:

- Sinh viên xem và đăng ký sự kiện.
- Sinh viên tham gia và quản lý đơn đăng ký câu lạc bộ.
- Trưởng câu lạc bộ duyệt và từ chối đơn đăng ký.
- Quản trị viên quản lý tài khoản, vai trò và dữ liệu câu lạc bộ.

## Vai trò người dùng

- Admin: quản lý tài khoản, vai trò, câu lạc bộ và sự kiện.
- Club Leader: quản lý đơn đăng ký của câu lạc bộ mình phụ trách.
- Student: đăng ký sự kiện, tham gia câu lạc bộ và xem thông tin cá nhân.

## Tính năng chính

- Quản lý sự kiện: tạo, cập nhật, xóa, tìm kiếm, lọc theo trạng thái và đăng ký/hủy đăng ký.
- Quản lý câu lạc bộ: tạo câu lạc bộ, gán trưởng câu lạc bộ và duyệt đơn đăng ký.
- Quản lý tài khoản: phân quyền và thống kê người dùng theo vai trò.
- Bảo mật: authorization bằng Policy, kiểm tra quyền trước khi thao tác.

## Kiến trúc

- Route: [routes/web.php](routes/web.php)
- Controllers: [app/Http/Controllers](app/Http/Controllers)
- Models: [app/Models](app/Models)
- Policies: [app/Policies](app/Policies)
- Migrations: [database/migrations](database/migrations)
- Views: [resources/views](resources/views)

## Hướng dẫn cài đặt

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

## Chạy test

```bash
php artisan test
```

## Bảo mật và ràng buộc dữ liệu

- Mọi thao tác nhạy cảm được kiểm soát bằng Policy.
- Đăng ký sự kiện và đơn đăng ký câu lạc bộ đều kiểm tra quyền, trạng thái và điều kiện nghiệp vụ.
- Dữ liệu quan trọng được ràng buộc bằng unique index, foreign key và check constraint ở migration khi cần thiết.

## Ghi chú phát triển

- Hệ thống ưu tiên kiểm soát quyền bằng policy thay vì chỉ dựa trên route middleware.
- Các thay đổi theo từng tính năng nên đi kèm test để đảm bảo không phá vỡ logic nghiệp vụ.
- Mỗi migration nên được giữ tập trung, rõ ràng và tương thích với dữ liệu đang chạy.
