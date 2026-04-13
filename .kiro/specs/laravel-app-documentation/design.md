# Design Document

## Overview

Laravel Application là một hệ thống web đầy đủ tính năng được xây dựng trên Laravel framework. Hệ thống kết hợp nhiều package nổi tiếng để cung cấp:

- **Authentication**: Laravel Fortify + Jetstream (đăng ký, đăng nhập, 2FA, quản lý team)
- **Admin Panel**: Orchid Platform (CRUD screens, permissions, menu)
- **API**: REST API với Sanctum token authentication
- **Frontend**: Inertia.js + Vue.js (SPA-style rendering)
- **Queue/Debug**: Laravel Horizon + Telescope

Ứng dụng được thiết kế theo mô hình **modular scaffolding** — mỗi domain (Post, Product, Transaction, v.v.) có cấu trúc đồng nhất gồm Model, Migration, Orchid Screen, Helper, và API Controller. Lệnh `management:create` tự động sinh toàn bộ cấu trúc này từ stub files.

---

## Architecture

```mermaid
graph TB
    subgraph Client
        Browser["Browser (Inertia/Vue)"]
        MobileApp["Mobile / API Client"]
    end

    subgraph Laravel Application
        subgraph Web Layer
            InertiaMiddleware["HandleInertiaRequests"]
            WebRoutes["routes/web.php"]
            FortifyRoutes["routes/fortify.php"]
            JetstreamRoutes["routes/jetstream.php"]
        end

        subgraph API Layer
            APIRoutes["routes/api.php"]
            SanctumAuth["auth:sanctum middleware"]
            AuthController["Api/AuthController"]
            BaseController["BaseController (index/show/recommendations)"]
            NotificationController["UserNotificationController"]
        end

        subgraph Admin Panel
            OrchidRoutes["routes/platform.php"]
            PlatformProvider["PlatformProvider (menu/permissions)"]
            OrchidScreens["Orchid Screens (List/Edit)"]
            OrchidHelpers["Orchid Helpers (routes/menu/permissions)"]
        end

        subgraph DomainModels["Domain Models"]
            User
            Post
            Product
            Transaction
            Team
            Comment
            Viewer
            OrderItem
            UserAdditionalInformation
            SendNotification
            Attachment
        end

        subgraph Services
            Fortify["Laravel Fortify (auth actions)"]
            Jetstream["Laravel Jetstream (teams)"]
            Horizon["Laravel Horizon (queues)"]
            Telescope["Laravel Telescope (debug)"]
        end
    end

    subgraph Infrastructure
        MySQL["MySQL (with FULLTEXT index)"]
        Storage["File Storage"]
    end

    Browser --> WebRoutes
    Browser --> OrchidRoutes
    MobileApp --> APIRoutes
    APIRoutes --> SanctumAuth
    SanctumAuth --> AuthController
    SanctumAuth --> BaseController
    SanctumAuth --> NotificationController
    WebRoutes --> InertiaMiddleware
    OrchidRoutes --> OrchidScreens
    OrchidScreens --> OrchidHelpers
    OrchidHelpers --> PlatformProvider
    BaseController --> DomainModels
    OrchidScreens --> DomainModels
    DomainModels --> MySQL
    Fortify --> User
    Jetstream --> Team
```

### Luồng xử lý chính

**API Request Flow:**
1. Request đến `routes/api.php` (chỉ active khi `fortify.user.enable = true`)
2. `auth:sanctum` middleware xác thực Bearer token
3. Controller kế thừa `BaseController` xử lý `index`/`show`/`recommendations`
4. Model query với `status = 1`, `orderBy id desc`
5. Response JSON

**Admin Panel Flow:**
1. Request đến `routes/platform.php` (Orchid prefix `/admin`)
2. Orchid middleware kiểm tra permission
3. Screen render layout + data
4. Helper class cung cấp routes, menu entries, permissions

**Scaffolding Flow (`management:create {name}`):**
1. Tạo Model từ stub
2. Tạo Migration
3. Tạo Orchid Helper, ListLayout, ListScreen, EditScreen
4. Tạo API Controller (optional)
5. Inject route vào `routes/platform.php` và `routes/api.php`
6. Inject menu/permission vào `PlatformProvider`

---

## Components and Interfaces

### BaseController (`app/Http/Controllers/BaseController.php`)

Controller gốc cho cả web và API. Tự động resolve Model từ tên class.

```
index()         → paginate() với status=1, orderBy id desc
show($id)       → tìm theo id, nếu có ?recommendations → gọi recommendations()
store()         → abort(403)
update()        → abort(403)
destroy()       → abort(403)
recommendations($id) → full-text search trên categories+tags, lấy tối đa 3 kết quả
```

Khi request `expectsJson()` → trả JSON. Khi không → render Inertia view (`{PluralName}/List` hoặc `{PluralName}/Show`).

### AuthController (`app/Http/Controllers/Api/AuthController.php`)

```
POST /api/register  → tạo user qua Fortify CreatesNewUsers, trả user + Sanctum token
POST /api/login     → xác thực email/password, kiểm tra 2FA nếu có, trả token
POST /api/logout    → xóa currentAccessToken
```

### UserNotificationController (`app/Http/Controllers/UserNotificationController.php`)

```
GET    /api/notifications           → paginate DashboardMessage notifications
GET    /api/notifications/unread    → paginate unread DashboardMessage notifications
POST   /api/notifications/markAllAsRead   → mark all as read
POST   /api/notifications/maskNotification → mark one as read, trả action URL
DELETE /api/notifications/removeAll → xóa tất cả DashboardMessage notifications
```

### Orchid Base Screens

- `BaseScreen` → screen gốc
- `BaseListScreen` → list screen với pagination
- `BaseEditScreen` → edit/create screen

Mỗi domain (Post, Product, Transaction, Team, SendNotification, UserAdditionalInformation) có `{Name}ListScreen` và `{Name}EditScreen` kế thừa từ base.

### Orchid Helpers (`app/Orchid/Helpers/`)

Mỗi Helper class cung cấp 3 method:
- `AddMenus($menu)` → thêm menu item vào admin navigation
- `AddPermissions($permissions)` → đăng ký permission
- `AddRoute()` → đăng ký Orchid screen routes

### Middleware

| Middleware | Mục đích |
|---|---|
| `HandleInertiaRequests` | Chia sẻ shared props cho Inertia |
| `LocalizationMiddleware` | Set locale từ `Accept-Language` header |
| `NormalizeLocale` | Chuẩn hóa locale value |

### Artisan Commands

| Command | Signature | Mục đích |
|---|---|---|
| `user:view` | `user:view {name}` | Tạo Vue list/show pages cho một resource |
| `management:create` | `management:create {name}` | Scaffold đầy đủ module mới (model, migration, screens, helper, API) |
| `notification:send` | `notification:send {title} {--user_ids=} {--message=} {--action=} {--type=}` | Gửi DashboardMessage notification |
| `generate:erd` | `generate:erd` | Sinh ERD diagram + Excel export |

---

## Data Models

### Entity Relationship Diagram

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at
        string password
        string remember_token
        bigint current_team_id FK
        string profile_photo_path
        tinyint status
        text two_factor_secret
        text two_factor_recovery_codes
        timestamp two_factor_confirmed_at
        json permissions
        timestamp created_at
        timestamp updated_at
    }

    teams {
        bigint id PK
        bigint user_id FK
        string name
        boolean personal_team
        timestamp created_at
        timestamp updated_at
    }

    team_user {
        bigint id PK
        bigint team_id FK
        bigint user_id FK
        string role
        timestamp created_at
        timestamp updated_at
    }

    team_invitations {
        bigint id PK
        bigint team_id FK
        string email
        string role
        timestamp created_at
        timestamp updated_at
    }

    posts {
        bigint id PK
        bigint author_id FK
        string slug UK
        string title
        string image
        text html
        text description
        string categories
        string tags
        tinyint status
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    comments {
        bigint id PK
        bigint author_id FK
        bigint post_id FK
        text content
        tinyint status
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    viewers {
        bigint id PK
        bigint author_id FK
        bigint post_id FK
        tinyint status
        timestamp created_at
        timestamp updated_at
    }

    products {
        bigint id PK
        string slug
        string name
        string image
        decimal price
        decimal quantity
        text description
        string categories
        string tags
        string currency
        tinyint status
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    transactions {
        bigint id PK
        bigint user_id FK
        string code UK
        text data
        string image
        timestamp issue_date
        timestamp payment_date
        decimal amount
        decimal tax
        string currency
        tinyint status
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    order_items {
        bigint id PK
        bigint transaction_id FK
        bigint product_id FK
        decimal price
        decimal quantity
        string currency
        tinyint status
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    notifications {
        uuid id PK
        string type
        string notifiable_type
        bigint notifiable_id
        json data
        timestamp read_at
        timestamp created_at
        timestamp updated_at
    }

    user_additional_informations {
        bigint id PK
        string slug
        string name
        string memo
        timestamp created_at
        timestamp updated_at
    }

    user_additional_information_user {
        bigint id PK
        bigint user_id FK
        bigint user_additional_information_id FK
        string value
        timestamp created_at
        timestamp updated_at
    }

    orchid_roles {
        bigint id PK
        string slug UK
        string name
        json permissions
        timestamp created_at
        timestamp updated_at
    }

    orchid_role_users {
        bigint role_id FK
        bigint user_id FK
    }

    attachments {
        bigint id PK
        bigint user_id FK
        string name
        string original_name
        string mime
        string extension
        bigint size
        string sort
        string path
        string description
        string alt
        string hash
        string disk
        string group
        timestamp created_at
        timestamp updated_at
    }

    attachmentable {
        bigint id PK
        bigint attachment_id FK
        bigint attachmentable_id
        string attachmentable_type
    }

    users ||--o{ teams : "owns"
    users }o--o{ teams : "member of (team_user)"
    teams ||--o{ team_invitations : "has"
    users ||--o{ posts : "authors"
    posts ||--o{ comments : "has"
    posts ||--o{ viewers : "has"
    users ||--o{ comments : "authors"
    users ||--o{ viewers : "views"
    users ||--o{ transactions : "has"
    transactions ||--o{ order_items : "contains"
    products ||--o{ order_items : "in"
    users }o--o{ user_additional_informations : "has (user_additional_information_user)"
    users ||--o{ notifications : "receives"
    users }o--o{ orchid_roles : "has (orchid_role_users)"
```

### Status Values

Các model sử dụng `tinyint status` với convention:
- `0` = private (ẩn)
- `1` = public (hiển thị)
- `2` = internal

API chỉ trả về records có `status = 1`.

### Model Traits

| Trait | Áp dụng cho |
|---|---|
| `SoftDeletes` | Post, Comment, Product, Transaction, OrderItem |
| `HasFullTextSearch` | Post (search trên description, categories, tags) |
| `AsSource` (Orchid) | Tất cả models qua Base |
| `HasValidationData` | Tất cả models qua Base |
| `HasApiTokens` (Sanctum) | User |
| `TwoFactorAuthenticatable` | User |
| `HasTeams` (Jetstream) | User |
| `HasProfilePhoto` | User |

---

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: API chỉ trả về records có status = 1

*For any* collection of records (Post, Product, Transaction) với mixed status values, khi gọi `index` endpoint, tất cả records trong response SHALL có `status = 1`.

**Validates: Requirements 7.5, 9.5, 10.6**

### Property 2: API show trả về đúng record theo id

*For any* record có `status = 1`, khi gọi `show($id)`, response SHALL chứa record với đúng `id` đó.

**Validates: Requirements 7.6, 9.6, 10.7**

### Property 3: Recommendations không chứa post gốc và có tối đa 3 kết quả

*For any* Post/Product có `status = 1`, khi gọi `show($id)?recommendations=1`, response SHALL chứa tối đa 3 items và SHALL NOT chứa item có `id = $id`.

**Validates: Requirements 7.8, 9.8**

### Property 4: Đăng ký trả về user và token

*For any* valid registration input (name, email, password), khi gọi `POST /api/register`, response SHALL chứa `user` object và `token` string.

**Validates: Requirements 1.5**

### Property 5: Đăng nhập với credentials sai trả về 401

*For any* email/password combination không khớp với user trong hệ thống, khi gọi `POST /api/login`, response SHALL có HTTP status 401.

**Validates: Requirements 2.2**

### Property 6: Protected endpoints yêu cầu token hợp lệ

*For any* protected endpoint (`/api/posts`, `/api/products`, `/api/transactions`, `/api/notifications`), khi gọi không có Bearer token, response SHALL có HTTP status 401.

**Validates: Requirements 13.3**

### Property 7: store/update/destroy luôn trả về 403

*For any* resource (Post, Product, Transaction), khi gọi `store`, `update`, hoặc `destroy` action, response SHALL có HTTP status 403.

**Validates: Requirements 13.4**

---

## Error Handling

### HTTP Status Codes

| Tình huống | Status Code |
|---|---|
| Record không tồn tại hoặc status ≠ 1 | 404 |
| Gọi store/update/destroy trên resource | 403 |
| Request không có valid Sanctum token | 401 |
| Credentials sai | 401 |
| 2FA code sai | 401 |
| Validation error (email trùng, password yếu) | 422 |

### 2FA Flow

Khi user có `two_factor_secret`:
1. Login với email/password → trả `{ requires_2fa: true }` với HTTP 200
2. Client gửi lại với `code` field
3. Server verify qua `TwoFactorAuthenticationProvider` (Google2FA)
4. Nếu code hợp lệ → trả token; nếu không → 401

### Soft Delete

Post, Comment, Product, Transaction, OrderItem sử dụng `SoftDeletes`. Records bị xóa qua Admin Panel sẽ có `deleted_at` được set, không bị xóa vật lý khỏi database.

### Feature Flags

Toàn bộ API routes và user-facing web routes được bọc trong:
```php
if (config('fortify.user.enable', false)) { ... }
```
Khi flag này là `false`, hệ thống chỉ phục vụ Admin Panel.

---

## Testing Strategy

### Unit Tests

Tập trung vào các pure functions và business logic:

- `BaseController::recommendations()` — verify logic lọc và merge recommendations
- `SendNotification::GetColorFromString()` — verify mapping string → Color enum
- `Base::displayStatus()` — verify HTML output cho từng status value
- `AuthController::login()` — verify 2FA flow với mock `TwoFactorAuthenticationProvider`

### Property-Based Tests

Sử dụng [PHPUnit](https://phpunit.de/) kết hợp với [eris/eris](https://github.com/giorgiosironi/eris) hoặc [Pest](https://pestphp.com/) với plugin property testing. Mỗi property test chạy tối thiểu 100 iterations.

**Tag format:** `Feature: laravel-app-documentation, Property {N}: {property_text}`

| Property | Test |
|---|---|
| Property 1 | Generate N posts với random status, gọi `GET /api/posts`, assert tất cả items có `status = 1` |
| Property 2 | Generate post với `status = 1`, gọi `GET /api/posts/{id}`, assert response id khớp |
| Property 3 | Generate posts với overlapping categories/tags, gọi recommendations, assert count ≤ 3 và không có id gốc |
| Property 4 | Generate valid user data, gọi `POST /api/register`, assert response có `user` và `token` |
| Property 5 | Generate random email/password không tồn tại, gọi `POST /api/login`, assert HTTP 401 |
| Property 6 | Gọi protected endpoints không có token, assert HTTP 401 |
| Property 7 | Gọi store/update/destroy trên bất kỳ resource, assert HTTP 403 |

### Integration Tests

- Orchid Admin Panel screens render đúng với dữ liệu thực
- Sanctum token authentication end-to-end
- Notification delivery qua `DashboardMessage`
- Soft delete không ảnh hưởng đến API responses
- Full-text search trên MySQL FULLTEXT index

### Example Tests

- Đăng ký với email đã tồn tại → 422
- Login với 2FA enabled, không có code → `requires_2fa: true`
- `notification:send` command gửi đến đúng users
- `management:create` sinh đúng files từ stubs
