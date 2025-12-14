# Business CRM - Laravel 12 Application

Modern, scalable Business/CRM application built with Laravel 12, featuring role-based access control, modular DDD architecture, and comprehensive user management.

## 🌟 Features

- ✅ **Role-Based Access Control** - SysAdmin, Admin, Moderator roles with fine-grained permissions
- ✅ **Modern Dashboard** - Statistics, recent activities, and user management
- ✅ **User Management** - Full CRUD with activation/deactivation
- ✅ **Activity Logging** - Track all user actions with detailed change history
- ✅ **Visibility Management** - Control data visibility per role
- ✅ **Modular DDD Architecture** - Repository & Service patterns
- ✅ **Policy-Based Authorization** - Secure and maintainable access control
- ✅ **TailwindCSS UI** - Responsive, modern design
- ✅ **Soft Deletes** - Safe data management
- ✅ **Alpine.js** - Lightweight JavaScript framework

## 🚀 Quick Start

### Prerequisites

- PHP 8.2+
- Composer
- Warden (Docker)

### Installation

1. **Install dependencies:**
```bash
cd src/
composer install
npm install
```

2. **Install Laravel Breeze:**
```bash
cd /path/business-app/src
```

3. **Run migrations and seed database:**
```bash
warden env exec php-fpm php artisan migrate
warden env exec php-fpm php artisan db:seed
```

4. **Build frontend assets:**
```bash
npm run build
```

5. **Access the application:**
```
https://business.test
```

### Default Credentials

| Role | Email | Password |
|------|-------|----------|
| System Administrator | sysadmin@business.test | password |
| Administrator | admin@business.test | password |
| Moderator | moderator@business.test | password |


## 🏗️ Architecture

### Modular DDD Structure
```
app/
├── Core/                    # Core functionality (Repositories, Services, Traits)
├── Modules/                 # Domain modules (UserManagement, Dashboard)
├── Http/Middleware/         # Custom middleware
├── Models/                  # Eloquent models
└── Policies/                # Authorization policies
```

### Database Schema

- **roles** - System roles with hierarchy
- **permissions** - Granular permissions by module
- **role_permission** - Many-to-many pivot
- **users** - Extended with role, status, creator tracking
- **activity_logs** - Complete audit trail
- **visibility_settings** - Role-based visibility control

## 🔐 Security Features

- CSRF Protection
- XSS Prevention
- SQL Injection Protection (Eloquent ORM)
- Password Hashing (Bcrypt)
- Role-Based Access Control
- Policy Authorization
- Activity Logging
- Soft Deletes

## 🎯 Key Components

### Role System

**SysAdmin** (Level 100)
- Full system access
- Can create all user types
- Manages visibility settings
- Assigns permissions

**Admin** (Level 50)
- Limited user creation
- Sees assigned data
- Restricted CRUD operations

**Moderator** (Level 10)
- Read-only access
- Views public data only


## 🛠️ Development

### Running Tests
```bash
warden env exec php-fpm php artisan test
```

### Clear Cache
```bash
warden env exec php-fpm php artisan cache:clear
```

## 📦 Technology Stack

- **Backend:** Laravel 12, PHP 8.2
- **Frontend:** Blade Templates, TailwindCSS, Alpine.js
- **Database:** SQLite (configurable for MySQL/PostgreSQL)
- **Development:** Warden (Docker)
- **Design Pattern:** DDD, Repository, Service Layer

## 🔧 Configuration

All middleware registered in `bootstrap/app.php`:
- `role` - Role verification
- `permission` - Permission checking
- `visibility` - Visibility enforcement

Policies registered in `AppServiceProvider`:
- UserPolicy for user authorization
- Super admin bypass gate

## 📝 License

MIT License

## 👨‍💻 Author

Business CRM System
Version 1.0.0

---
**Laravel Version:** 12.x
**PHP Version:** 8.2+
