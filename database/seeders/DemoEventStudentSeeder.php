<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoEventStudentSeeder extends Seeder
{
    private const STUDENTS = [
        'Nguyễn Minh Anh', 'Trần Gia Bảo', 'Lê Hoàng Chi', 'Phạm Quốc Dũng', 'Võ Thanh Hà',
        'Đặng Nhật Huy', 'Bùi Ngọc Khánh', 'Đỗ Phương Linh', 'Hồ Tuấn Minh', 'Ngô Quỳnh Như',
        'Dương Đức Phúc', 'Lý Bảo Quyên', 'Mai Thành Sơn', 'Cao Thảo Trang', 'Huỳnh Minh Tú',
        'Trương Hải Yến', 'Phan Anh Khoa', 'Vũ Hà My', 'Đinh Gia Phúc', 'Tạ Ngọc Lan',
    ];

    private const EVENTS = [
        'Ngày hội công nghệ sinh viên', 'Workshop kỹ năng phỏng vấn', 'Ngày hội việc làm 2026',
        'Cuộc thi khởi nghiệp trẻ', 'Tọa đàm chuyển đổi số', 'Hội thảo trí tuệ nhân tạo',
        'Ngày hội câu lạc bộ', 'Chương trình hiến máu nhân đạo', 'Chiến dịch mùa hè xanh',
        'Giải chạy kết nối sinh viên', 'Đêm nhạc chào tân sinh viên', 'Workshop thiết kế sáng tạo',
        'Cuộc thi lập trình mở rộng', 'Diễn đàn sinh viên nghiên cứu khoa học', 'Ngày hội sách và văn hóa đọc',
        'Chương trình bảo vệ môi trường', 'Hội thảo an toàn thông tin', 'Giao lưu doanh nghiệp công nghệ',
        'Ngày hội tư vấn sức khỏe', 'Lễ hội ẩm thực sinh viên',
    ];

    public function run(): void
    {
        foreach (self::STUDENTS as $index => $name) {
            User::query()->firstOrCreate(
                ['email' => sprintf('sinhvien%02d@campus.test', $index + 1)],
                [
                    'name' => $name,
                    'password' => 'student123',
                    'role' => UserRole::Student->value,
                    'email_verified_at' => now(),
                ],
            );
        }

        $locations = ['Hội trường A', 'Hội trường B', 'Trung tâm sinh viên', 'Sân trường'];

        foreach (self::EVENTS as $index => $title) {
            $eventDate = now()->addDays(7 + $index)->setTime(8 + ($index % 8), 0);

            Event::query()->firstOrCreate(
                ['title' => $title],
                [
                    'description' => 'Tham gia '.$title.' và kết nối với cộng đồng sinh viên.',
                    'location' => $locations[$index % count($locations)],
                    'event_date' => $eventDate,
                    'registration_deadline' => $eventDate->copy()->subDay(),
                    'max_participants' => 80 + ($index * 5),
                    'status' => 'upcoming',
                ],
            );
        }
    }
}
