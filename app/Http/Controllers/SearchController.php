<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Club;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Handle global search across events, announcements, clubs, navigation, and users.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = trim((string) $request->input('q', $request->input('search', '')));
        $currentUser = auth()->user();

        $events = collect();
        $announcements = collect();
        $clubs = collect();
        $students = collect();
        $navigation = collect();

        $allNavigationItems = [
            [
                'title' => 'Trang chủ',
                'description' => 'Trang tổng quan hoạt động và sự kiện sinh viên',
                'url' => route('dashboard'),
                'icon' => '🏠',
                'category' => 'Điều hướng',
                'keywords' => 'trang chu dashboard home bang dieu khien',
            ],
            [
                'title' => 'Tin tức & Thông báo',
                'description' => 'Xem tin tức, hoạt động và thông báo mới nhất',
                'url' => route('announcements.index'),
                'icon' => '📰',
                'category' => 'Tin tức',
                'keywords' => 'tin tuc thong bao news announcement',
            ],
            [
                'title' => 'Góc hoạt động',
                'description' => 'Khám phá các phong trào và trải nghiệm sinh viên',
                'url' => route('activities.index'),
                'icon' => '🎓',
                'category' => 'Hoạt động',
                'keywords' => 'goc hoat dong phong trao trai nghiem activities',
            ],
            [
                'title' => 'Tra cứu hoạt động & Điểm ngoại khóa',
                'description' => 'Xem chi tiết các hoạt động đã tham gia và điểm rèn luyện',
                'url' => route('activities.scores'),
                'icon' => '📊',
                'category' => 'Hoạt động',
                'keywords' => 'tra cuu diem ngoai khoa ren luyen activities scores',
            ],
            [
                'title' => 'Đăng ký sự kiện',
                'description' => 'Xem danh sách và đăng ký tham gia các sự kiện sắp diễn ra',
                'url' => route('events.index'),
                'icon' => '📅',
                'category' => 'Sự kiện',
                'keywords' => 'dang ky su kien events register tham gia',
            ],
            [
                'title' => 'Sự kiện đang đăng ký',
                'description' => 'Danh sách các sự kiện bạn đã gửi đơn hoặc đã được duyệt',
                'url' => route('events.joined'),
                'icon' => '🎟️',
                'category' => 'Sự kiện',
                'keywords' => 'su kien dang dang ky joined events',
            ],
            [
                'title' => 'Lịch sự kiện',
                'description' => 'Xem lịch tổng quan các sự kiện trong tháng theo dạng lịch',
                'url' => route('events.calendar'),
                'icon' => '🗓️',
                'category' => 'Sự kiện',
                'keywords' => 'lich su kien calendar thang date',
            ],
            [
                'title' => 'Câu lạc bộ của tôi',
                'description' => 'Xem danh sách câu lạc bộ bạn tham gia hoặc phụ trách',
                'url' => route('clubs.index'),
                'icon' => '🏆',
                'category' => 'Câu lạc bộ',
                'keywords' => 'cau lac bo cua toi my clubs clb',
            ],
            [
                'title' => 'Đăng ký câu lạc bộ',
                'description' => 'Gửi đơn đăng ký tham gia các câu lạc bộ sinh viên',
                'url' => route('clubs.register'),
                'icon' => '✍️',
                'category' => 'Câu lạc bộ',
                'keywords' => 'dang ky cau lac bo apply clb register',
            ],
            [
                'title' => 'Hồ sơ cá nhân',
                'description' => 'Xem và cập nhật thông tin tài khoản, đổi mật khẩu',
                'url' => route('profile.edit'),
                'icon' => '👤',
                'category' => 'Tài khoản',
                'keywords' => 'ho so ca nhan profile tai khoan mat khau edit',
            ],
        ];

        if ($currentUser && $currentUser->isAdmin()) {
            $allNavigationItems[] = [
                'title' => 'Quản lý sinh viên',
                'description' => 'Xem danh sách sinh viên, trưởng CLB và cập nhật quyền hạn',
                'url' => route('admin.students.index'),
                'icon' => '👥',
                'category' => 'Quản trị',
                'keywords' => 'quan ly sinh vien danh sach admin students',
            ];
            $allNavigationItems[] = [
                'title' => 'Danh sách quản trị viên',
                'description' => 'Quản lý tài khoản ban quản trị và phân quyền',
                'url' => route('admin.administrators.index'),
                'icon' => '🛡️',
                'category' => 'Quản trị',
                'keywords' => 'danh sach quan tri vien administrators admin',
            ];
            $allNavigationItems[] = [
                'title' => 'Quản lý câu lạc bộ',
                'description' => 'Tạo mới, chỉ định trưởng CLB và xem danh sách CLB',
                'url' => route('admin.clubs.index'),
                'icon' => '🏛️',
                'category' => 'Quản trị',
                'keywords' => 'quan ly cau lac bo admin clubs leader',
            ];
        }

        if ($query !== '') {
            $queryLower = mb_strtolower($query, 'UTF-8');

            // 1. Navigation items
            $navigation = collect($allNavigationItems)->filter(function (array $item) use ($queryLower): bool {
                $titleMatch = str_contains(mb_strtolower($item['title'], 'UTF-8'), $queryLower);
                $descMatch = str_contains(mb_strtolower($item['description'], 'UTF-8'), $queryLower);
                $keywordMatch = str_contains(mb_strtolower($item['keywords'], 'UTF-8'), $queryLower);

                return $titleMatch || $descMatch || $keywordMatch;
            })->values();

            // 2. Events
            $events = Event::query()
                ->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('location', 'like', "%{$query}%")
                ->orderBy('event_date', 'desc')
                ->limit(10)
                ->get()
                ->map(fn (Event $event) => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'location' => $event->location,
                    'time' => $event->event_date?->format('H:i - d/m/Y'),
                    'event_date' => $event->event_date?->format('d/m/Y'),
                    'url' => route('events.index', ['search' => $event->title]),
                    'image_url' => $event->image_url ?: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80',
                ]);

            // 3. Announcements
            $announcements = Announcement::query()
                ->where('title', 'like', "%{$query}%")
                ->orWhere('body', 'like', "%{$query}%")
                ->orWhere('category', 'like', "%{$query}%")
                ->latest('published_at')
                ->limit(10)
                ->get()
                ->map(fn (Announcement $a) => [
                    'id' => $a->id,
                    'title' => $a->title,
                    'body' => $a->body,
                    'category' => $a->category,
                    'published_at' => $a->published_at?->format('d/m/Y'),
                    'url' => route('announcements.index', ['q' => $a->title]),
                ]);

            // 4. Clubs
            $clubs = Club::query()
                ->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->with('leader')
                ->limit(10)
                ->get()
                ->map(fn (Club $c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'description' => $c->description,
                    'leader_name' => $c->leader?->name,
                    'url' => route('clubs.index'),
                ]);

            // 5. Students & Users (Only if admin)
            if ($currentUser && ($currentUser->isAdmin() || $currentUser->can('manageStudents', User::class))) {
                $students = User::query()
                    ->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('student_code', 'like', "%{$query}%")
                    ->orWhere('class_name', 'like', "%{$query}%")
                    ->limit(10)
                    ->get()
                    ->map(fn (User $u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                        'email' => $u->email,
                        'role' => $u->role->label(),
                        'student_code' => $u->student_code,
                        'avatar' => $u->avatar_url,
                        'url' => route('admin.students.show', $u),
                    ]);
            }
        }

        $totalCount = $navigation->count() + $events->count() + $announcements->count() + $clubs->count() + $students->count();

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'query' => $query,
                'total' => $totalCount,
                'events' => $events,
                'announcements' => $announcements,
                'clubs' => $clubs,
                'students' => $students,
                'navigation' => $navigation,
            ]);
        }

        return view('search.index', [
            'query' => $query,
            'totalCount' => $totalCount,
            'events' => $events,
            'announcements' => $announcements,
            'clubs' => $clubs,
            'students' => $students,
            'navigation' => $navigation,
        ]);
    }
}
