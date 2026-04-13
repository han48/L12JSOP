# Implementation Plan: Laravel Application Documentation

## Overview

Tài liệu hóa source code hiện tại của Laravel Application. Các tasks dưới đây phản ánh các thành phần đã được xây dựng trong source code — không phải implement mới mà là ghi nhận và xác nhận những gì đã tồn tại.

## Tasks

- [x] 1. Tài liệu hóa Authentication System
  - Xác nhận `app/Actions/Fortify/CreateNewUser.php` — tạo user + personal team
  - Xác nhận `app/Actions/Fortify/PasswordValidationRules.php` — password rules
  - Xác nhận `app/Actions/Fortify/UpdateUserProfileInformation.php` — cập nhật profile
  - Xác nhận `app/Actions/Fortify/UpdateUserPassword.php` — đổi mật khẩu
  - Xác nhận `app/Actions/Fortify/ResetUserPassword.php` — reset mật khẩu
  - Xác nhận `app/Actions/Fortify/EnableTwoFactorAuthentication.php` — bật 2FA
  - Xác nhận `app/Notifications/ResetPassword.php` và `VerifyEmail.php`
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 2.3, 2.4, 2.5, 3.1, 3.2, 3.3, 3.4, 3.5_

- [x] 2. Tài liệu hóa API Layer
  - [x] 2.1 Tài liệu hóa AuthController
    - Xác nhận `app/Http/Controllers/Api/AuthController.php` — register, login (2FA), logout
    - Xác nhận feature flag `fortify.user.enable` bọc API routes
    - _Requirements: 1.5, 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 13.5_

  - [x] 2.2 Viết property test cho AuthController
    - **Property 4: Đăng ký trả về user và token**
    - **Validates: Requirements 1.5**
    - **Property 5: Đăng nhập với credentials sai trả về 401**
    - **Validates: Requirements 2.2**

  - [x] 2.3 Tài liệu hóa BaseController
    - Xác nhận `app/Http/Controllers/BaseController.php` — index, show, recommendations
    - Xác nhận `app/Http/Controllers/Api/BaseController.php` — API variant
    - Xác nhận logic `status = 1`, `orderBy id desc`, `expectsJson()` branching
    - _Requirements: 7.5, 7.6, 7.7, 7.8, 9.5, 9.6, 9.7, 9.8, 10.6, 10.7, 10.8_

  - [x] 2.4 Viết property test cho BaseController
    - **Property 1: API chỉ trả về records có status = 1**
    - **Validates: Requirements 7.5, 9.5, 10.6**
    - **Property 2: API show trả về đúng record theo id**
    - **Validates: Requirements 7.6, 9.6, 10.7**
    - **Property 3: Recommendations không chứa post gốc và có tối đa 3 kết quả**
    - **Validates: Requirements 7.8, 9.8**
    - **Property 7: store/update/destroy luôn trả về 403**
    - **Validates: Requirements 13.4**

  - [x] 2.5 Tài liệu hóa UserNotificationController
    - Xác nhận `app/Http/Controllers/UserNotificationController.php`
    - Xác nhận 5 endpoints: list, unread, markAllAsRead, maskNotification, removeAll
    - _Requirements: 12.2, 12.3, 12.4, 12.5, 12.6_

  - [x] 2.6 Viết property test cho API security
    - **Property 6: Protected endpoints yêu cầu token hợp lệ**
    - **Validates: Requirements 13.3**

  - [x] 2.7 Tài liệu hóa API Controllers cho từng resource
    - Xác nhận `Api/PostController.php`, `Api/ProductController.php`, `Api/TransactionController.php`
    - Xác nhận `Api/UserAdditionalInformationController.php`
    - _Requirements: 7.5, 7.6, 9.5, 9.6, 10.6, 10.7, 13.1, 13.2_

- [x] 3. Checkpoint — Xác nhận API layer đã được tài liệu hóa đầy đủ
  - Đảm bảo tất cả endpoints trong `routes/api.php` đã được phản ánh trong tài liệu.

- [x] 4. Tài liệu hóa Domain Models
  - [x] 4.1 Tài liệu hóa User model
    - Xác nhận `app/Models/User.php` — traits: HasApiTokens, TwoFactorAuthenticatable, HasTeams, HasProfilePhoto
    - Xác nhận `app/Models/UserAdditionalInformation.php` và `UserAdditionalInformationUser.php`
    - _Requirements: 3.5, 4.1, 11.1, 11.2_

  - [x] 4.2 Tài liệu hóa Post, Comment, Viewer models
    - Xác nhận `app/Models/Post.php` — SoftDeletes, HasFullTextSearch, fields
    - Xác nhận `app/Models/Comment.php` — SoftDeletes, fields
    - Xác nhận `app/Models/Viewer.php` — fields
    - _Requirements: 7.1, 7.3, 7.9, 8.1, 8.2, 8.3_

  - [x] 4.3 Tài liệu hóa Product, Transaction, OrderItem models
    - Xác nhận `app/Models/Product.php` — SoftDeletes, fields (quantity -1 = unlimited)
    - Xác nhận `app/Models/Transaction.php` — SoftDeletes, fields
    - Xác nhận `app/Models/OrderItem.php` — SoftDeletes, fields
    - _Requirements: 9.1, 9.3, 10.1, 10.2, 10.4_

  - [x] 4.4 Tài liệu hóa Team, Membership, TeamInvitation models
    - Xác nhận `app/Models/Team.php` — TeamUpdated/TeamDeleted events
    - Xác nhận `app/Models/Membership.php` và `TeamInvitation.php`
    - _Requirements: 6.1, 6.7, 6.8_

  - [x] 4.5 Tài liệu hóa Core models
    - Xác nhận `app/Models/Core/Attachment.php` và `Attachmentable.php` — polymorphic
    - Xác nhận `app/Models/Core/Role.php` và `RoleUser.php` — Orchid roles
    - Xác nhận `app/Models/Base.php` — AsSource, HasValidationData traits
    - Xác nhận `app/Models/SendNotification.php`
    - _Requirements: 5.1, 17.1, 17.2_

  - [x] 4.6 Viết unit tests cho Base model helpers
    - Test `Base::displayStatus()` — verify HTML output cho từng status value
    - Test `SendNotification::GetColorFromString()` — verify mapping string → Color enum
    - _Requirements: 7.1, 9.1_

- [x] 5. Tài liệu hóa Orchid Admin Panel
  - [x] 5.1 Tài liệu hóa PlatformProvider và navigation
    - Xác nhận `app/Orchid/PlatformProvider.php` — menu sections, permissions
    - Xác nhận menu structure: Access Controls (Users, Roles), Management (các modules)
    - Xác nhận DEBUG section (Telescope, Horizon) và DEV_MODE screens
    - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5_

  - [x] 5.2 Tài liệu hóa Orchid Base Screens
    - Xác nhận `app/Orchid/Screens/BaseScreen.php`, `BaseListScreen.php`, `BaseEditScreen.php`
    - _Requirements: 14.1_

  - [x] 5.3 Tài liệu hóa User và Role management screens
    - Xác nhận `Screens/User/UserListScreen.php`, `UserEditScreen.php`, `UserProfileScreen.php`
    - Xác nhận `Screens/Role/RoleListScreen.php`, `RoleEditScreen.php`
    - Xác nhận permissions: `platform.systems.users`, `platform.systems.roles`
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 5.1, 5.2, 5.3, 5.4_

  - [x] 5.4 Tài liệu hóa Post, Product, Transaction management screens
    - Xác nhận `Screens/Post/PostListScreen.php`, `PostEditScreen.php`
    - Xác nhận `Screens/Product/ProductListScreen.php`, `ProductEditScreen.php`
    - Xác nhận `Screens/Transaction/TransactionListScreen.php`, `TransactionEditScreen.php`
    - Xác nhận soft-delete behavior và permissions
    - _Requirements: 7.2, 7.3, 7.4, 9.2, 9.3, 9.4, 10.3, 10.4, 10.5_

  - [x] 5.5 Tài liệu hóa Team, SendNotification, UserAdditionalInformation screens
    - Xác nhận `Screens/Team/`, `Screens/SendNotification/`, `Screens/UserAdditionalInformation/`
    - Xác nhận permissions tương ứng
    - _Requirements: 6.9, 6.10, 11.3, 11.4, 12.7, 12.8_

  - [x] 5.6 Tài liệu hóa Orchid Helpers
    - Xác nhận `app/Orchid/Helpers/` — Base, Post, Product, SendNotification, Team, Transaction, UserAdditionalInformation
    - Xác nhận 3 methods: `AddMenus()`, `AddPermissions()`, `AddRoute()`
    - _Requirements: 14.1_

  - [x] 5.7 Tài liệu hóa Orchid Layouts và Fields
    - Xác nhận `app/Orchid/Layouts/` — layout classes cho từng module
    - Xác nhận `app/Orchid/Fields/VideoPlayer.php` — custom field
    - Xác nhận `app/Orchid/Filters/RoleFilter.php`
    - Xác nhận `app/Orchid/Presenters/UserPresenter.php`
    - _Requirements: 4.1, 14.1_

- [x] 6. Checkpoint — Xác nhận Admin Panel đã được tài liệu hóa đầy đủ
  - Đảm bảo tất cả screens, helpers, và permissions đã được phản ánh trong tài liệu.

- [x] 7. Tài liệu hóa Middleware và HTTP Layer
  - Xác nhận `app/Http/Middleware/HandleInertiaRequests.php` — shared Inertia props
  - Xác nhận `app/Http/Middleware/LocalizationMiddleware.php` — set locale từ Accept-Language
  - Xác nhận `app/Http/Middleware/NormalizeLocale.php` — chuẩn hóa locale
  - Xác nhận `app/Http/Responses/AdminLoginResponse.php`
  - Xác nhận `app/Http/Controllers/AuthenticatedSessionController.php`
  - _Requirements: 15.1, 15.2_

- [x] 8. Tài liệu hóa Artisan Commands
  - Xác nhận `app/Console/Commands/UserCreate.php` — `user:create`
  - Xác nhận `app/Console/Commands/ManagementCreate.php` — `management:create {name}` scaffolding flow
  - Xác nhận `app/Console/Commands/SendNotification.php` — `send:notification` với options
  - Xác nhận `app/Console/Commands/GenerateERD.php` — `generate:erd`
  - Xác nhận `app/Exports/ERD.php`, `ERDSheet.php`, `ERDSheetTable.php`
  - _Requirements: 16.1, 16.2, 16.3, 16.4_

- [x] 9. Tài liệu hóa Team Actions (Jetstream)
  - Xác nhận `app/Actions/Jetstream/CreateTeam.php`
  - Xác nhận `app/Actions/Jetstream/AddTeamMember.php` — validate email, not already member
  - Xác nhận `app/Actions/Jetstream/InviteTeamMember.php`
  - Xác nhận `app/Actions/Jetstream/RemoveTeamMember.php`
  - Xác nhận `app/Actions/Jetstream/UpdateTeamName.php`
  - Xác nhận `app/Actions/Jetstream/DeleteTeam.php`
  - Xác nhận `app/Actions/Jetstream/DeleteUser.php`
  - Xác nhận `app/Policies/TeamPolicy.php`
  - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 6.7, 6.8_

- [x] 10. Final checkpoint — Xác nhận toàn bộ source code đã được tài liệu hóa
  - Đảm bảo tất cả requirements (1–17) đều có ít nhất một task tương ứng.
  - Đảm bảo tất cả components trong design document đều được phản ánh.

## Notes

- Tasks marked with `*` are optional và có thể bỏ qua nếu chỉ cần tài liệu hóa
- Đây là tài liệu hóa code đã tồn tại — không implement mới
- Mỗi task tham chiếu đến requirements cụ thể để đảm bảo traceability
- Property tests (nếu thực hiện) sử dụng PHPUnit + eris/eris hoặc Pest
