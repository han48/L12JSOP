# Requirements Document

## Introduction

Tài liệu này mô tả các yêu cầu của Laravel Application hiện tại — một hệ thống web đầy đủ tính năng được xây dựng trên Laravel với Jetstream/Fortify (authentication), Orchid (admin panel), Sanctum (API token), và Inertia.js (frontend). Ứng dụng bao gồm quản lý người dùng, bài viết, sản phẩm, giao dịch, nhóm, thông báo, và REST API.

---

## Glossary

- **System**: Toàn bộ Laravel application
- **API**: REST API phục vụ qua prefix `/api`, xác thực bằng Sanctum token
- **Admin_Panel**: Giao diện quản trị được xây dựng trên Orchid Platform
- **Auth_System**: Hệ thống xác thực dựa trên Laravel Fortify + Jetstream
- **User**: Người dùng đã đăng ký trong hệ thống
- **Admin**: Người dùng có quyền truy cập Admin_Panel
- **Role**: Nhóm quyền hạn được gán cho User trong Admin_Panel (Orchid Role)
- **Team**: Nhóm người dùng được quản lý qua Jetstream
- **Post**: Bài viết có tiêu đề, nội dung HTML, ảnh, categories, tags, tác giả
- **Product**: Sản phẩm có tên, giá, số lượng, mô tả, categories, tags, tiền tệ
- **Transaction**: Giao dịch tài chính liên kết với User, gồm code, amount, tax, currency, issue_date, payment_date
- **OrderItem**: Dòng chi tiết trong Transaction, liên kết với Product
- **Comment**: Bình luận của User trên Post
- **Viewer**: Bản ghi lượt xem Post của User
- **Notification**: Thông báo hệ thống gửi đến User, lưu trong bảng `notifications`
- **UserAdditionalInformation**: Thông tin bổ sung dạng key-value gán cho User
- **Attachment**: File đính kèm được quản lý qua Orchid
- **Token**: Sanctum Personal Access Token dùng để xác thực API
- **2FA**: Xác thực hai yếu tố (Two-Factor Authentication) qua Google Authenticator
- **Telescope**: Laravel Telescope — công cụ debug
- **Horizon**: Laravel Horizon — công cụ quản lý queue

---

## Requirements

### Requirement 1: Đăng ký người dùng

**User Story:** As a visitor, I want to register an account, so that I can access the system.

#### Acceptance Criteria

1. WHEN a visitor submits a registration form with name, email, and password, THE Auth_System SHALL create a new User account.
2. WHEN a new User is created, THE Auth_System SHALL automatically create a personal Team for that User.
3. IF the submitted email already exists in the system, THEN THE Auth_System SHALL return a validation error.
4. IF the submitted password does not meet the password rules, THEN THE Auth_System SHALL return a validation error.
5. WHEN a User registers via the API endpoint `POST /api/register`, THE API SHALL return the User object and a Sanctum Token.

---

### Requirement 2: Đăng nhập và đăng xuất

**User Story:** As a registered User, I want to log in and log out, so that I can securely access my account.

#### Acceptance Criteria

1. WHEN a User submits valid email and password to `POST /api/login`, THE Auth_System SHALL return the User object and a Sanctum Token.
2. IF the submitted email or password is incorrect, THEN THE Auth_System SHALL return HTTP 401 with an error message.
3. WHILE a User has 2FA enabled, WHEN the User submits email and password without a 2FA code, THE Auth_System SHALL return a response indicating 2FA is required.
4. WHEN a User submits a valid 2FA code along with credentials, THE Auth_System SHALL verify the code and return a Token.
5. IF the submitted 2FA code is invalid, THEN THE Auth_System SHALL return HTTP 401.
6. WHEN a User calls `POST /api/logout` with a valid Token, THE Auth_System SHALL delete the current access Token.

---

### Requirement 3: Quản lý hồ sơ người dùng

**User Story:** As a logged-in User, I want to update my profile information, so that my account details stay current.

#### Acceptance Criteria

1. WHEN a User submits updated name and email, THE Auth_System SHALL validate and save the new profile information.
2. WHEN a User uploads a profile photo (jpg, jpeg, png, max 1024KB), THE Auth_System SHALL update the User's profile photo.
3. IF the new email is already used by another User, THEN THE Auth_System SHALL return a validation error.
4. WHEN a User changes their password, THE Auth_System SHALL validate the new password against password rules and save the hashed password.
5. WHEN a User enables 2FA, THE Auth_System SHALL generate and store an encrypted two_factor_secret for the User.

---

### Requirement 4: Quản lý người dùng trong Admin Panel

**User Story:** As an Admin, I want to manage Users from the Admin Panel, so that I can control user access and roles.

#### Acceptance Criteria

1. THE Admin_Panel SHALL display a paginated list of Users with filters by name, email, and date range.
2. WHEN an Admin creates or edits a User, THE Admin_Panel SHALL allow setting name, email, password, Roles, and individual permissions.
3. WHEN an Admin saves a User, THE Admin_Panel SHALL validate that the email is unique.
4. WHEN an Admin deletes a User, THE Admin_Panel SHALL permanently remove the User and all associated data.
5. WHEN an Admin uses the impersonate feature on a User, THE Admin_Panel SHALL log in as that User.
6. THE Admin_Panel SHALL require the `platform.systems.users` permission to access User management screens.

---

### Requirement 5: Quản lý Role trong Admin Panel

**User Story:** As an Admin, I want to manage Roles, so that I can define permission sets for Users.

#### Acceptance Criteria

1. THE Admin_Panel SHALL display a list of Roles.
2. WHEN an Admin creates or edits a Role, THE Admin_Panel SHALL allow defining the Role name and associated permissions.
3. WHEN an Admin deletes a Role, THE Admin_Panel SHALL remove the Role from the system.
4. THE Admin_Panel SHALL require the `platform.systems.roles` permission to access Role management screens.

---

### Requirement 6: Quản lý Team

**User Story:** As a User, I want to create and manage Teams, so that I can collaborate with other Users.

#### Acceptance Criteria

1. WHEN a User creates a Team, THE System SHALL save the Team with a name and associate it with the owner User.
2. WHEN a Team owner invites a User by email, THE System SHALL validate that the email belongs to a registered User and that the User is not already a member.
3. IF the invited email does not exist in the system, THEN THE System SHALL return an error message.
4. IF the invited User is already a member of the Team, THEN THE System SHALL return an error message.
5. WHEN a Team owner adds a Team member, THE System SHALL attach the User to the Team with an optional role.
6. WHEN a Team owner removes a Team member, THE System SHALL detach the User from the Team.
7. WHEN a Team is updated, THE System SHALL dispatch a TeamUpdated event.
8. WHEN a Team is deleted, THE System SHALL dispatch a TeamDeleted event.
9. THE Admin_Panel SHALL display a list of Teams and allow create, edit, and delete operations.
10. THE Admin_Panel SHALL require the `platform.systems.teams` permission to access Team management screens.

---

### Requirement 7: Quản lý Post

**User Story:** As an Admin, I want to manage Posts, so that I can publish and organize content.

#### Acceptance Criteria

1. THE System SHALL store Posts with fields: author_id, slug (unique), title, image, html content, description, categories (array), tags (array), and status.
2. THE Admin_Panel SHALL display a paginated list of Posts and allow create, edit, and delete operations.
3. WHEN a Post is deleted via Admin_Panel, THE Admin_Panel SHALL soft-delete the Post.
4. THE Admin_Panel SHALL require the `platform.systems.posts` permission to access Post management screens.
5. WHEN a client calls `GET /api/posts`, THE API SHALL return a paginated list of Posts with status = 1, ordered by id descending.
6. WHEN a client calls `GET /api/posts/{id}`, THE API SHALL return the Post with the given id if status = 1.
7. IF the requested Post does not exist or has status ≠ 1, THEN THE API SHALL return HTTP 404.
8. WHEN a client calls `GET /api/posts/{id}?recommendations=1`, THE API SHALL return up to 3 recommended Posts based on matching categories and tags using full-text search.
9. THE System SHALL support full-text search on Post description, categories, and tags fields.

---

### Requirement 8: Quản lý Comment và Viewer của Post

**User Story:** As a developer, I want the system to track comments and views on Posts, so that engagement data is available.

#### Acceptance Criteria

1. THE System SHALL store Comments with fields: author_id, post_id, content, and status.
2. THE System SHALL store Viewers with fields: author_id, post_id, and status.
3. WHEN a Comment is deleted, THE System SHALL soft-delete the Comment record.

---

### Requirement 9: Quản lý Product

**User Story:** As an Admin, I want to manage Products, so that I can maintain the product catalog.

#### Acceptance Criteria

1. THE System SHALL store Products with fields: slug, name, image, price (decimal), quantity (decimal, -1 = unlimited), description, categories (array), tags (array), currency, and status.
2. THE Admin_Panel SHALL display a paginated list of Products and allow create, edit, and delete operations.
3. WHEN a Product is deleted via Admin_Panel, THE Admin_Panel SHALL soft-delete the Product.
4. THE Admin_Panel SHALL require the `platform.systems.products` permission to access Product management screens.
5. WHEN a client calls `GET /api/products`, THE API SHALL return a paginated list of Products with status = 1, ordered by id descending.
6. WHEN a client calls `GET /api/products/{id}`, THE API SHALL return the Product with the given id if status = 1.
7. IF the requested Product does not exist or has status ≠ 1, THEN THE API SHALL return HTTP 404.
8. WHEN a client calls `GET /api/products/{id}?recommendations=1`, THE API SHALL return up to 3 recommended Products based on matching categories and tags.
9. THE System SHALL support full-text search on Product description, categories, and tags fields.

---

### Requirement 10: Quản lý Transaction

**User Story:** As an Admin, I want to manage Transactions, so that I can track financial records.

#### Acceptance Criteria

1. THE System SHALL store Transactions with fields: user_id, code (unique), data (JSON), image, issue_date, payment_date, amount (decimal), tax (decimal), currency, and status.
2. THE System SHALL store OrderItems linked to a Transaction and a Product, with fields: price, quantity, currency, and status.
3. THE Admin_Panel SHALL display a paginated list of Transactions and allow create, edit, and delete operations.
4. WHEN a Transaction is deleted via Admin_Panel, THE Admin_Panel SHALL soft-delete the Transaction.
5. THE Admin_Panel SHALL require the `platform.systems.transactions` permission to access Transaction management screens.
6. WHEN a client calls `GET /api/transactions`, THE API SHALL return a paginated list of Transactions with status = 1, ordered by id descending.
7. WHEN a client calls `GET /api/transactions/{id}`, THE API SHALL return the Transaction with the given id if status = 1.
8. IF the requested Transaction does not exist or has status ≠ 1, THEN THE API SHALL return HTTP 404.

---

### Requirement 11: Quản lý UserAdditionalInformation

**User Story:** As an Admin, I want to manage UserAdditionalInformation, so that I can define and assign custom attributes to Users.

#### Acceptance Criteria

1. THE System SHALL store UserAdditionalInformation records with fields: slug, name, and memo.
2. THE System SHALL store UserAdditionalInformation-User relations with fields: user_id, user_additional_information_id, and value.
3. THE Admin_Panel SHALL display a paginated list of UserAdditionalInformation records and allow create, edit, and delete operations.
4. THE Admin_Panel SHALL require the `platform.systems.user_additional_informations` permission to access UserAdditionalInformation management screens.

---

### Requirement 12: Hệ thống Notification

**User Story:** As a User, I want to receive and manage notifications, so that I stay informed about system events.

#### Acceptance Criteria

1. THE System SHALL store Notifications in the `notifications` table with fields: type, notifiable_type, notifiable_id, data (JSON), and read_at.
2. WHEN a client calls `GET /api/notifications`, THE API SHALL return a paginated list of Notifications of type `DashboardMessage` for the authenticated User.
3. WHEN a client calls `GET /api/notifications/unread`, THE API SHALL return a paginated list of unread Notifications of type `DashboardMessage` for the authenticated User.
4. WHEN a client calls `POST /api/notifications/markAllAsRead`, THE API SHALL mark all unread DashboardMessage Notifications of the authenticated User as read.
5. WHEN a client calls `POST /api/notifications/maskNotification` with a notification id, THE API SHALL mark the specified Notification as read and return the action URL.
6. WHEN a client calls `DELETE /api/notifications/removeAll`, THE API SHALL delete all DashboardMessage Notifications of the authenticated User.
7. THE Admin_Panel SHALL display a list of sent Notifications with data fields: title, action, message, type, time, and recipient email.
8. THE Admin_Panel SHALL allow Admins to create and send Notifications via the SendNotification management screen.

---

### Requirement 13: REST API — Xác thực và bảo mật

**User Story:** As a developer, I want the API to be secured, so that only authenticated users can access protected resources.

#### Acceptance Criteria

1. THE API SHALL use Laravel Sanctum for token-based authentication on all protected endpoints.
2. WHILE a User is authenticated, THE API SHALL allow access to `/api/posts`, `/api/products`, `/api/transactions`, and `/api/notifications` endpoints.
3. IF a request to a protected endpoint does not include a valid Token, THEN THE API SHALL return HTTP 401.
4. THE API SHALL only expose the `index` and `show` actions for Post, Product, and Transaction resources (store, update, destroy return HTTP 403).
5. WHERE the `fortify.user.enable` configuration is false, THE System SHALL disable all API authentication and user-facing routes.

---

### Requirement 14: Admin Panel — Cấu trúc và điều hướng

**User Story:** As an Admin, I want a structured admin panel, so that I can navigate and manage all resources efficiently.

#### Acceptance Criteria

1. THE Admin_Panel SHALL provide a navigation menu with sections: Access Controls (Users, Roles) and Management (SendNotification, Teams, UserAdditionalInformation, Products, Transactions, Posts).
2. THE Admin_Panel SHALL display a DEBUG section with links to Telescope and Horizon for users with appropriate permissions.
3. THE Admin_Panel SHALL require the `platform.systems.telescope` permission to access Telescope.
4. THE Admin_Panel SHALL require the `platform.systems.horizon` permission to access Horizon.
5. WHERE the `DEV_MODE` environment variable is true, THE Admin_Panel SHALL display additional development and example screens.

---

### Requirement 15: Localization

**User Story:** As a developer, I want the system to support multiple locales, so that the application can serve users in different languages.

#### Acceptance Criteria

1. THE System SHALL apply locale settings via the `LocalizationMiddleware` on incoming requests.
2. THE System SHALL normalize locale values via the `NormalizeLocale` middleware.

---

### Requirement 16: Artisan Commands

**User Story:** As a developer, I want CLI commands, so that I can manage the system from the command line.

#### Acceptance Criteria

1. THE System SHALL provide an Artisan command `user:create` to create a new User from the command line.
2. THE System SHALL provide an Artisan command `management:create` to scaffold new management modules.
3. THE System SHALL provide an Artisan command `send:notification` to send Notifications from the command line.
4. THE System SHALL provide an Artisan command `generate:erd` to generate an Entity Relationship Diagram export.

---

### Requirement 17: File Attachments

**User Story:** As an Admin, I want to manage file attachments, so that media files can be associated with content.

#### Acceptance Criteria

1. THE System SHALL store Attachments with metadata via the Orchid Attachment model.
2. THE System SHALL support polymorphic attachment relations via the `attachmentable` pivot table.
