# TÀI LIỆU CẤU TRÚC DATABASE - HỆ THỐNG QUẢN LÝ SINH VIÊN VÀ CÂU LẠC BỘ

## 📋 MỤC LỤC
1. [Bảng Users](#1-bảng-users)
2. [Bảng Clubs](#2-bảng-clubs)
3. [Bảng Club Members](#3-bảng-club-members)
4. [Bảng Club Posts](#4-bảng-club-posts)
5. [Bảng Club Post Comments](#5-bảng-club-post-comments)
6. [Bảng Club Post Reactions](#6-bảng-club-post-reactions)
7. [Bảng Club Events](#7-bảng-club-events)
8. [Bảng Event Participants](#8-bảng-event-participants)
9. [Bảng Notifications](#9-bảng-notifications)

---

## 1. BẢNG `users`

### 📝 Chức năng:
Lưu trữ thông tin tài khoản của sinh viên trong hệ thống.

### 🔑 Các trường:
- `id` (Primary Key) - ID duy nhất của user
- `name` - Tên đầy đủ của sinh viên
- `email` (Unique) - Email đăng nhập (duy nhất)
- `email_verified_at` - Thời gian xác thực email
- `password` - Mật khẩu đã mã hóa
- `student_code` (Unique, Nullable) - Mã sinh viên (duy nhất)
- `avatar` (Nullable) - Đường dẫn ảnh đại diện
- `status` (Enum: 'active', 'banned') - Trạng thái tài khoản (mặc định: 'active')
- `remember_token` - Token để nhớ đăng nhập
- `created_at` - Thời gian tạo
- `updated_at` - Thời gian cập nhật

### 🔗 Mối quan hệ:
- **hasMany** → `clubs` (qua `created_by`) - Các câu lạc bộ do user tạo
- **hasMany** → `club_members` - Các thành viên câu lạc bộ của user
- **belongsToMany** → `clubs` (qua `club_members`) - Các câu lạc bộ user tham gia
- **hasMany** → `club_posts` - Các bài đăng user đã tạo
- **hasMany** → `club_post_comments` - Các bình luận user đã tạo
- **hasMany** → `club_post_reactions` - Các reaction user đã tạo
- **hasMany** → `club_events` (qua `created_by`) - Các sự kiện user đã tạo
- **hasMany** → `event_participants` - Các tham gia sự kiện của user
- **belongsToMany** → `club_events` (qua `event_participants`) - Các sự kiện user tham gia
- **hasMany** → `notifications` - Các thông báo của user

---

## 2. BẢNG `clubs`

### 📝 Chức năng:
Lưu trữ thông tin các câu lạc bộ trong hệ thống.

### 🔑 Các trường:
- `id` (Primary Key) - ID duy nhất của câu lạc bộ
- `name` - Tên câu lạc bộ
- `description` (Nullable) - Mô tả về câu lạc bộ
- `avatar` (Nullable) - Đường dẫn ảnh đại diện câu lạc bộ
- `cover_image` (Nullable) - Đường dẫn ảnh bìa câu lạc bộ
- `founded_date` (Nullable) - Ngày thành lập
- `status` (Enum: 'active', 'inactive') - Trạng thái câu lạc bộ (mặc định: 'active')
- `created_by` (Foreign Key → `users.id`) - ID người tạo câu lạc bộ
- `created_at` - Thời gian tạo
- `updated_at` - Thời gian cập nhật

### 🔗 Mối quan hệ:
- **belongsTo** → `users` (qua `created_by`) - Người tạo câu lạc bộ
- **hasMany** → `club_members` - Danh sách thành viên
- **belongsToMany** → `users` (qua `club_members`) - Các user là thành viên
- **hasMany** → `club_posts` - Các bài đăng trong câu lạc bộ
- **hasMany** → `club_events` - Các sự kiện của câu lạc bộ

### ⚠️ Ràng buộc:
- Khi user bị xóa → Câu lạc bộ cũng bị xóa (CASCADE)

---

## 3. BẢNG `club_members`

### 📝 Chức năng:
Quản lý mối quan hệ giữa user và câu lạc bộ (bảng trung gian).

### 🔑 Các trường:
- `id` (Primary Key) - ID duy nhất
- `club_id` (Foreign Key → `clubs.id`) - ID câu lạc bộ
- `user_id` (Foreign Key → `users.id`) - ID thành viên
- `role` (Enum: 'member', 'admin', 'owner') - Vai trò trong câu lạc bộ (mặc định: 'member')
- `status` (Enum: 'pending', 'approved', 'rejected', 'left') - Trạng thái tham gia (mặc định: 'pending')
- `joined_at` (Nullable) - Thời gian tham gia
- `created_at` - Thời gian tạo
- `updated_at` - Thời gian cập nhật

### 🔗 Mối quan hệ:
- **belongsTo** → `clubs` - Câu lạc bộ
- **belongsTo** → `users` - Thành viên

### ⚠️ Ràng buộc:
- **UNIQUE** (`club_id`, `user_id`) - Một user chỉ có thể tham gia một câu lạc bộ một lần
- Khi club bị xóa → Thành viên cũng bị xóa (CASCADE)
- Khi user bị xóa → Thành viên cũng bị xóa (CASCADE)

---

## 4. BẢNG `club_posts`

### 📝 Chức năng:
Lưu trữ các bài đăng trong câu lạc bộ.

### 🔑 Các trường:
- `id` (Primary Key) - ID duy nhất của bài đăng
- `club_id` (Foreign Key → `clubs.id`) - ID câu lạc bộ
- `user_id` (Foreign Key → `users.id`) - ID người đăng
- `content` - Nội dung bài đăng
- `is_anonymous` (Boolean, Default: 0) - Có ẩn danh hay không (0 = không, 1 = có)
- `status` (Enum: 'pending', 'approved', 'hidden') - Trạng thái bài đăng (mặc định: 'pending')
- `created_at` - Thời gian tạo
- `updated_at` - Thời gian cập nhật

### 🔗 Mối quan hệ:
- **belongsTo** → `clubs` - Câu lạc bộ chứa bài đăng
- **belongsTo** → `users` - Người tạo bài đăng
- **hasMany** → `club_post_comments` - Các bình luận của bài đăng
- **hasMany** → `club_post_reactions` - Các reaction của bài đăng

### ⚠️ Ràng buộc:
- Khi club bị xóa → Bài đăng cũng bị xóa (CASCADE)
- Khi user bị xóa → Bài đăng cũng bị xóa (CASCADE)

---

## 5. BẢNG `club_post_comments`

### 📝 Chức năng:
Lưu trữ các bình luận trên bài đăng.

### 🔑 Các trường:
- `id` (Primary Key) - ID duy nhất của bình luận
- `post_id` (Foreign Key → `club_posts.id`) - ID bài đăng
- `user_id` (Foreign Key → `users.id`) - ID người bình luận
- `content` - Nội dung bình luận
- `created_at` - Thời gian tạo
- `updated_at` - Thời gian cập nhật

### 🔗 Mối quan hệ:
- **belongsTo** → `club_posts` - Bài đăng được bình luận
- **belongsTo** → `users` - Người bình luận

### ⚠️ Ràng buộc:
- Khi post bị xóa → Bình luận cũng bị xóa (CASCADE)
- Khi user bị xóa → Bình luận cũng bị xóa (CASCADE)

---

## 6. BẢNG `club_post_reactions`

### 📝 Chức năng:
Lưu trữ các reaction (like, heart, haha) trên bài đăng.

### 🔑 Các trường:
- `id` (Primary Key) - ID duy nhất
- `post_id` (Foreign Key → `club_posts.id`) - ID bài đăng
- `user_id` (Foreign Key → `users.id`) - ID người reaction
- `type` (Enum: 'like', 'heart', 'haha') - Loại reaction (mặc định: 'like')
- `created_at` - Thời gian tạo
- `updated_at` - Thời gian cập nhật

### 🔗 Mối quan hệ:
- **belongsTo** → `club_posts` - Bài đăng được reaction
- **belongsTo** → `users` - Người reaction

### ⚠️ Ràng buộc:
- **UNIQUE** (`post_id`, `user_id`) - Một user chỉ có thể reaction một bài đăng một lần
- Khi post bị xóa → Reaction cũng bị xóa (CASCADE)
- Khi user bị xóa → Reaction cũng bị xóa (CASCADE)

---

## 7. BẢNG `club_events`

### 📝 Chức năng:
Lưu trữ thông tin các sự kiện của câu lạc bộ.

### 🔑 Các trường:
- `id` (Primary Key) - ID duy nhất của sự kiện
- `club_id` (Foreign Key → `clubs.id`) - ID câu lạc bộ tổ chức
- `title` - Tiêu đề sự kiện
- `description` (Nullable) - Mô tả sự kiện
- `start_time` - Thời gian bắt đầu
- `end_time` (Nullable) - Thời gian kết thúc
- `location` (Nullable) - Địa điểm tổ chức
- `created_by` (Foreign Key → `users.id`) - ID người tạo sự kiện
- `created_at` - Thời gian tạo
- `updated_at` - Thời gian cập nhật

### 🔗 Mối quan hệ:
- **belongsTo** → `clubs` - Câu lạc bộ tổ chức
- **belongsTo** → `users` (qua `created_by`) - Người tạo sự kiện
- **hasMany** → `event_participants` - Danh sách người tham gia
- **belongsToMany** → `users` (qua `event_participants`) - Các user tham gia sự kiện

### ⚠️ Ràng buộc:
- Khi club bị xóa → Sự kiện cũng bị xóa (CASCADE)
- Khi user bị xóa → Sự kiện cũng bị xóa (CASCADE)

---

## 8. BẢNG `event_participants`

### 📝 Chức năng:
Quản lý mối quan hệ giữa user và sự kiện (bảng trung gian).

### 🔑 Các trường:
- `id` (Primary Key) - ID duy nhất
- `event_id` (Foreign Key → `club_events.id`) - ID sự kiện
- `user_id` (Foreign Key → `users.id`) - ID người tham gia
- `status` (Enum: 'going', 'maybe', 'not_going') - Trạng thái tham gia (mặc định: 'going')
- `created_at` - Thời gian tạo
- `updated_at` - Thời gian cập nhật

### 🔗 Mối quan hệ:
- **belongsTo** → `club_events` - Sự kiện
- **belongsTo** → `users` - Người tham gia

### ⚠️ Ràng buộc:
- **UNIQUE** (`event_id`, `user_id`) - Một user chỉ có thể tham gia một sự kiện một lần
- Khi event bị xóa → Người tham gia cũng bị xóa (CASCADE)
- Khi user bị xóa → Người tham gia cũng bị xóa (CASCADE)

---

## 9. BẢNG `notifications`

### 📝 Chức năng:
Lưu trữ các thông báo cho user.

### 🔑 Các trường:
- `id` (Primary Key) - ID duy nhất của thông báo
- `user_id` (Foreign Key → `users.id`) - ID user nhận thông báo
- `content` - Nội dung thông báo
- `type` (Nullable) - Loại thông báo (ví dụ: 'post_approved', 'event_reminder', etc.)
- `is_read` (Boolean, Default: 0) - Đã đọc hay chưa (0 = chưa, 1 = đã đọc)
- `created_at` - Thời gian tạo
- `updated_at` - Thời gian cập nhật

### 🔗 Mối quan hệ:
- **belongsTo** → `users` - User nhận thông báo

### ⚠️ Ràng buộc:
- Khi user bị xóa → Thông báo cũng bị xóa (CASCADE)

---

## 📊 SƠ ĐỒ MỐI QUAN HỆ TỔNG QUAN

```
users (1) ──< (N) clubs (created_by)
users (1) ──< (N) club_members (N) >── (1) clubs
users (1) ──< (N) club_posts (N) >── (1) clubs
users (1) ──< (N) club_post_comments (N) >── (1) club_posts
users (1) ──< (N) club_post_reactions (N) >── (1) club_posts
users (1) ──< (N) club_events (created_by)
clubs (1) ──< (N) club_events
users (1) ──< (N) event_participants (N) >── (1) club_events
users (1) ──< (N) notifications
```

### Giải thích ký hiệu:
- `(1)` = Một
- `(N)` = Nhiều
- `──<` = hasMany / belongsTo
- `>──` = belongsTo / hasMany

---

## 🔄 LUỒNG DỮ LIỆU CHÍNH

1. **User tạo Club** → `clubs.created_by` = `users.id`
2. **User tham gia Club** → Tạo record trong `club_members`
3. **User đăng bài trong Club** → Tạo record trong `club_posts`
4. **User bình luận bài đăng** → Tạo record trong `club_post_comments`
5. **User reaction bài đăng** → Tạo/update record trong `club_post_reactions`
6. **User tạo sự kiện** → Tạo record trong `club_events`
7. **User tham gia sự kiện** → Tạo record trong `event_participants`
8. **Hệ thống gửi thông báo** → Tạo record trong `notifications`

---

## ⚙️ CÁC ENUM VALUES

### `users.status`:
- `active` - Tài khoản hoạt động bình thường
- `banned` - Tài khoản bị cấm

### `clubs.status`:
- `active` - Câu lạc bộ đang hoạt động
- `inactive` - Câu lạc bộ không hoạt động

### `club_members.role`:
- `member` - Thành viên thường
- `admin` - Quản trị viên
- `owner` - Chủ sở hữu

### `club_members.status`:
- `pending` - Đang chờ duyệt
- `approved` - Đã được duyệt
- `rejected` - Bị từ chối
- `left` - Đã rời khỏi

### `club_posts.status`:
- `pending` - Đang chờ duyệt
- `approved` - Đã được duyệt
- `hidden` - Đã bị ẩn

### `club_post_reactions.type`:
- `like` - Thích
- `heart` - Yêu thích
- `haha` - Cười

### `event_participants.status`:
- `going` - Sẽ tham gia
- `maybe` - Có thể tham gia
- `not_going` - Không tham gia

---

## 📌 LƯU Ý QUAN TRỌNG

1. **CASCADE DELETE**: Khi xóa parent record, tất cả child records sẽ tự động bị xóa
2. **UNIQUE Constraints**: 
   - Một user chỉ có thể tham gia một club một lần
   - Một user chỉ có thể reaction một post một lần
   - Một user chỉ có thể tham gia một event một lần
3. **Soft Delete**: Hiện tại chưa có soft delete, nếu cần có thể thêm `deleted_at` column
4. **Indexing**: Các foreign keys đã được tự động index bởi Laravel

