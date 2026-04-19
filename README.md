# JobFlow OMS

> **Disclaimer:** This system was developed solely for academic purposes as a school project. It is not intended for commercial use or production deployment.

![Laravel 13](https://img.shields.io/badge/Laravel-13.x-FF2D20)
![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-777BB4)
![Build](https://img.shields.io/badge/Build-No%20CI%20Configured-lightgrey)
![Last Commit](https://img.shields.io/github/last-commit/oneetnwt/JobFlow)

## 2. Table of Contents

1. [Project Banner](#jobflow-oms)
2. [Table of Contents](#2-table-of-contents)
3. [Overview](#3-overview)
4. [Features](#4-features)
5. [Tech Stack](#5-tech-stack)
6. [Architecture Overview](#6-architecture-overview)
7. [Prerequisites](#7-prerequisites)
8. [Installation](#8-installation)
9. [Environment Variables](#9-environment-variables)
10. [Subdomain Setup (Local Development)](#10-subdomain-setup-local-development)
11. [Database Seeding](#11-database-seeding)
12. [Pricing Plans](#12-pricing-plans)
13. [Folder Structure](#13-folder-structure)
14. [Key Routes](#14-key-routes)
15. [Academic Context](#15-academic-context)

## 3. Overview

JobFlow OMS is a Laravel-based operations platform for managing job orders, workforce assignments, and payroll workflows across multiple organizations. The system centralizes operational records that are commonly fragmented across spreadsheets and disconnected tools. It provides a single operational model for creating jobs, assigning workers, tracking progress, and generating payroll artifacts.

The application implements domain-based multitenancy using `stancl/tenancy`. A central application context handles public registration, pricing, and super-administrator governance, while each tenant workspace is resolved by domain and bootstrapped into an isolated tenant database. Tenant initialization and protection are enforced through tenancy middleware and a tenant-status gate.

Core capabilities include job order lifecycle management, task decomposition and completion tracking, role-gated worker management, payroll period generation and release, and centralized platform activity logging. Tenant onboarding is coupled with subscription plan selection, including auto-approval behavior for specific plans.

The target users are operational and administrative teams that manage field or service workflows: operations managers, logistics coordinators, dispatch teams, and tenant administrators responsible for workforce and payroll execution.

## 4. Features

### Multitenancy and Subdomain Routing

- Domain-resolved tenant context using `InitializeTenancyByDomain`.
- Central-domain protection using `PreventAccessFromCentralDomains`.
- Per-tenant database provisioning via tenancy event pipeline (`CreateDatabase`, `MigrateDatabase`).
- Tenant database naming convention: `jobflow_<tenant_id>`.

### Authentication and Role-Based Access Control

- Central guard: `auth:central` for super admin access.
- Tenant guard: session-based `auth` for tenant users.
- Role gates in `AppServiceProvider`: `manage-payroll`, `manage-workers`, `manage-jobs`.
- Worker roles in tenant DB: `admin`, `manager`, `worker`.

### Tenant Registration and Approval Workflow

- Public registration flow with validation and plan selection.
- Automatic tenant domain creation based on selected subdomain.
- Tenant admin bootstrap account creation inside tenant database.
- Status workflow: `pending`, `active`, `suspended`.
- Optional auto-approval based on selected plan (`auto_approve`).

### Pricing Plans and Subscription Management

- Centralized pricing plan CRUD in super admin portal.
- Plan metadata: monthly/annual pricing, worker/job limits, feature flags, order, status.
- Soft-archive strategy for plans used by active tenants.
- Public pricing page backed by active plan records.

### Super Admin Dashboard

- Platform KPIs: total, pending, active, and suspended tenant counts.
- Tenant management actions: approve, suspend, branding update, impersonate.
- Activity log review with pagination.

### Tenant Admin Dashboard

- Tenant operational metrics (active jobs, workers, completed jobs, pending payroll sum).
- Recent job summary with creator and assignee context.

### Job Order Management

- Full CRUD for job orders.
- Status model: `draft`, `open`, `assigned`, `in_progress`, `completed`, `cancelled`.
- Priority model: `low`, `medium`, `high`, `urgent`.
- Assignment-driven and completion-driven state transitions.

### Worker Assignment and Scheduling

- Worker and manager account creation with profile records.
- Extended worker profile attributes: employee ID, department, employment type, hourly rate, skills.
- Role-gated worker module access (`manage-workers`).

### Real-Time Job Tracking

- Task-level tracking under each job order.
- Task status toggle and completion timestamping.
- Computed progress percentage on job orders.
- Automatic job completion when all tasks are completed.

### Payroll Processing

- Payroll period lifecycle: create, generate, release.
- Payroll slip generation for workers using stored profile rate.
- Payroll status handling: period (`draft`, `processed`, `released`), slip (`pending`, `paid`).
- Role-gated payroll module access (`manage-payroll`).

### Activity Logging and Audit Trail

- Central activity log model with event, description, metadata, and source IP.
- Logged events for tenant registration, approval, suspension, branding updates, and impersonation.
- Super admin log viewer with pagination.

### Platform Settings

- Tenant branding fields (`brand_color`, `logo_url`).
- Tenant status enforcement via `TenantActiveMiddleware`.
- Central domain configuration through `config/tenancy.php`.

## 5. Tech Stack

| Layer                  | Technology                                               | Version                              |
| ---------------------- | -------------------------------------------------------- | ------------------------------------ |
| Backend                | Laravel Framework                                        | `^13.0`                              |
| Language               | PHP                                                      | `^8.3`                               |
| Multitenancy           | stancl/tenancy                                           | `^3.10`                              |
| Database               | MySQL (default), SQLite, MariaDB, PostgreSQL, SQL Server | Configured in `config/database.php`  |
| Frontend Rendering     | Blade Templates                                          | Laravel 13 component/view system     |
| JavaScript Bundler     | Vite                                                     | `^8.0.0`                             |
| CSS Framework          | Tailwind CSS                                             | `^4.0.0`                             |
| HTTP Client (Frontend) | Axios                                                    | `>=1.11.0 <=1.14.0`                  |
| Authentication         | Laravel Session Guards (`web`, `central`)                | Laravel 13 Auth                      |
| Queue                  | Laravel Queue (database driver default)                  | Laravel 13 Queue                     |
| Cache                  | Laravel Cache (database store default)                   | Laravel 13 Cache                     |
| Storage                | Local filesystem (default), optional S3                  | Laravel Filesystem                   |
| Testing                | Pest + PHPUnit                                           | `^4.4` / XML schema in `phpunit.xml` |

## 6. Architecture Overview

### Multitenant Subdomain Model

The platform separates central and tenant concerns by domain:

- `admin.jobflow.com` (production pattern): super admin portal.
- `{slug}.jobflow.com` (production pattern): tenant workspace domains.

In local configuration, central domains are currently:

- `localhost`
- `admin.localhost`
- `127.0.0.1`

Tenant resolution occurs at request time through tenancy middleware. Once a tenant is resolved, tenancy bootstrappers switch database/cache/filesystem/queue context so requests operate on tenant-scoped resources. Data isolation is implemented at the database level, with one tenant database per workspace.

### Role Structure

- Super Admin:
    - Authenticates using the `central` guard.
    - Manages tenants, pricing plans, approvals/suspensions, branding updates, impersonation, and platform logs.
- Tenant Admin:
    - Exists inside tenant database.
    - Can manage payroll and workers (through gate policies), plus full job operations.
- Worker/Staff (`worker`, `manager`):
    - `manager` can access worker management and job management gates.
    - `worker` participates in operational workflows without elevated administration permissions.

### Database Architecture

Central database stores platform metadata and governance data (`tenants`, `domains`, `plans`, `activity_logs`, central `users`). Each tenant has an isolated database containing operational tables (`users`, `job_orders`, `tasks`, `worker_profiles`, `payroll_periods`, `payrolls`).

Key relationships:

- Central:
    - `tenants.plan_id -> plans.id`
    - `domains.tenant_id -> tenants.id`
    - `activity_logs.user_id -> users.id`
    - `activity_logs.tenant_id -> tenants.id`
- Tenant:
    - `job_orders.created_by -> users.id`
    - `job_orders.assigned_to -> users.id`
    - `worker_profiles.user_id -> users.id`
    - `tasks.job_order_id -> job_orders.id`
    - `payrolls.payroll_period_id -> payroll_periods.id`
    - `payrolls.user_id -> users.id`

## 7. Prerequisites

- PHP: `8.3+` (project constraint is `^8.3`; local runtime observed: `8.5.1`).
- Composer: `2.x` (local observed: `2.9.5`).
- Node.js: modern LTS/current (local observed: `v22.21.0`).
- npm: modern version (local observed: `11.6.2`).
- Database engine: MySQL recommended for parity with `.env.example` defaults (`DB_CONNECTION=mysql`).
- Required PHP extensions (from `composer check-platform-reqs --no-dev`):
    - `ext-dom`
    - `ext-fileinfo`
    - `ext-filter`
    - `ext-hash`
    - `ext-iconv`
    - `ext-json`
    - `ext-libxml`
    - `ext-openssl`
    - `ext-pcre`
    - `ext-session`
    - `ext-tokenizer`
    - `ext-ctype` (polyfill-capable)
    - `ext-mbstring` (polyfill-capable)

## 8. Installation

```bash
# 1. Clone the repository
git clone https://github.com/oneetnwt/JobFlow.git
cd JobFlow
```

```bash
# 2. Install PHP dependencies
composer install
```

```bash
# 3. Install Node dependencies
npm install
```

```bash
# 4. Copy environment file
cp .env.example .env
```

```bash
# 5. Generate application key
php artisan key:generate
```

```bash
# 6. Configure your .env file
# - Set APP_URL
# - Set DB_* credentials for central database
# - Set mail settings if not using log mailer
```

```bash
# 7. Run central database migrations
php artisan migrate
```

```bash
# 8. Run database seeders
php artisan db:seed
```

```bash
# 9. Build frontend assets
npm run dev
# or for production:
npm run build
```

```bash
# 10. Start the development server
php artisan serve
```

## 9. Environment Variables

| Variable                      | Description                   | Example Value       | Required |
| ----------------------------- | ----------------------------- | ------------------- | -------- |
| `APP_NAME`                    | Application display name      | `JobFlow OMS`       | Yes      |
| `APP_ENV`                     | Runtime environment           | `local`             | Yes      |
| `APP_KEY`                     | Application encryption key    | `base64:...`        | Yes      |
| `APP_DEBUG`                   | Debug mode toggle             | `true`              | Yes      |
| `APP_URL`                     | Base application URL          | `http://localhost`  | Yes      |
| `APP_LOCALE`                  | Default locale                | `en`                | Yes      |
| `APP_FALLBACK_LOCALE`         | Fallback locale               | `en`                | Yes      |
| `APP_FAKER_LOCALE`            | Faker locale                  | `en_US`             | Yes      |
| `APP_MAINTENANCE_DRIVER`      | Maintenance mode driver       | `file`              | Yes      |
| `APP_MAINTENANCE_STORE`       | Maintenance cache store       | `database`          | No       |
| `PHP_CLI_SERVER_WORKERS`      | PHP built-in server workers   | `4`                 | No       |
| `BCRYPT_ROUNDS`               | Password hashing cost         | `12`                | Yes      |
| `LOG_CHANNEL`                 | Default log channel           | `stack`             | Yes      |
| `LOG_STACK`                   | Channels used by stack driver | `single`            | Yes      |
| `LOG_DEPRECATIONS_CHANNEL`    | Deprecation log channel       | `null`              | Yes      |
| `LOG_LEVEL`                   | Logging threshold             | `debug`             | Yes      |
| `DB_CONNECTION`               | Database connection driver    | `mysql`             | Yes      |
| `DB_HOST`                     | Database host                 | `127.0.0.1`         | Yes      |
| `DB_PORT`                     | Database port                 | `3306`              | Yes      |
| `DB_DATABASE`                 | Central database name         | `jobflow`           | Yes      |
| `DB_USERNAME`                 | Database username             | `root`              | Yes      |
| `DB_PASSWORD`                 | Database password             | ``                  | Yes      |
| `SESSION_DRIVER`              | Session backend driver        | `database`          | Yes      |
| `SESSION_LIFETIME`            | Session lifetime (minutes)    | `120`               | Yes      |
| `SESSION_ENCRYPT`             | Encrypt session payloads      | `false`             | Yes      |
| `SESSION_PATH`                | Session cookie path           | `/`                 | Yes      |
| `SESSION_DOMAIN`              | Session cookie domain         | `null`              | Yes      |
| `BROADCAST_CONNECTION`        | Broadcast driver              | `log`               | Yes      |
| `FILESYSTEM_DISK`             | Default filesystem disk       | `local`             | Yes      |
| `QUEUE_CONNECTION`            | Queue connection driver       | `database`          | Yes      |
| `CACHE_STORE`                 | Default cache store           | `database`          | Yes      |
| `CACHE_PREFIX`                | Cache key prefix              | `jobflow-cache-`    | No       |
| `MEMCACHED_HOST`              | Memcached host                | `127.0.0.1`         | No       |
| `REDIS_CLIENT`                | Redis client                  | `phpredis`          | No       |
| `REDIS_HOST`                  | Redis host                    | `127.0.0.1`         | No       |
| `REDIS_PASSWORD`              | Redis password                | `null`              | No       |
| `REDIS_PORT`                  | Redis port                    | `6379`              | No       |
| `MAIL_MAILER`                 | Mail transport                | `log`               | Yes      |
| `MAIL_SCHEME`                 | SMTP scheme                   | `null`              | No       |
| `MAIL_HOST`                   | Mail host                     | `127.0.0.1`         | Yes      |
| `MAIL_PORT`                   | Mail port                     | `2525`              | Yes      |
| `MAIL_USERNAME`               | Mail username                 | `null`              | No       |
| `MAIL_PASSWORD`               | Mail password                 | `null`              | No       |
| `MAIL_FROM_ADDRESS`           | Sender address                | `hello@example.com` | Yes      |
| `MAIL_FROM_NAME`              | Sender name                   | `${APP_NAME}`       | Yes      |
| `AWS_ACCESS_KEY_ID`           | AWS access key                | ``                  | No       |
| `AWS_SECRET_ACCESS_KEY`       | AWS secret key                | ``                  | No       |
| `AWS_DEFAULT_REGION`          | AWS region                    | `us-east-1`         | No       |
| `AWS_BUCKET`                  | S3 bucket name                | ``                  | No       |
| `AWS_USE_PATH_STYLE_ENDPOINT` | S3 path-style endpoint flag   | `false`             | No       |
| `VITE_APP_NAME`               | Frontend-exposed app name     | `${APP_NAME}`       | Yes      |

## 10. Subdomain Setup (Local Development)

### Using /etc/hosts (or Windows hosts file)

```bash
# Add entries to your hosts file
127.0.0.1   jobflow.test
127.0.0.1   admin.jobflow.test
127.0.0.1   demo.jobflow.test
127.0.0.1   acme.jobflow.test
```

For this codebase, update `config/tenancy.php` `central_domains` to match local central hostnames (for example `jobflow.test` and `admin.jobflow.test`), then clear cached config.

```bash
php artisan config:clear
```

### Using Laravel Valet (macOS)

```bash
valet link jobflow
valet secure jobflow
# Access at: https://jobflow.test
```

### Using Laragon (Windows)

1. Place the project under Laragon web root (or create a custom host).
2. Create virtual hosts for central and admin domains, for example:

```apache
<VirtualHost *:80>
		ServerName jobflow.test
		DocumentRoot "C:/laragon/www/jobflow-oms/public"
</VirtualHost>

<VirtualHost *:80>
		ServerName admin.jobflow.test
		DocumentRoot "C:/laragon/www/jobflow-oms/public"
</VirtualHost>

<VirtualHost *:80>
		ServerName demo.jobflow.test
		DocumentRoot "C:/laragon/www/jobflow-oms/public"
</VirtualHost>
```

3. Add matching host entries in `C:\Windows\System32\drivers\etc\hosts`.
4. Ensure `config/tenancy.php` includes your central domains only (not tenant domains).

### APP_URL Configuration

Use a central-domain URL and a parent-cookie strategy for subdomain auth behavior:

```bash
APP_URL=http://jobflow.test
SESSION_DOMAIN=.jobflow.test
```

Example `config/tenancy.php` central domains for local subdomain routing:

```php
'central_domains' => [
		'jobflow.test',
		'admin.jobflow.test',
],
```

## 11. Database Seeding

```bash
# Run all seeders
php artisan db:seed
```

```bash
# Run a specific seeder
php artisan db:seed --class=PlanSeeder
php artisan db:seed --class=SuperAdminSeeder
```

**Seeded Data:**

| Seeder             | What It Creates                                                                                                        |
| ------------------ | ---------------------------------------------------------------------------------------------------------------------- |
| `PlanSeeder`       | 4 pricing plans (`Free`, `Starter`, `Professional`, `Enterprise`) with limits, pricing, feature arrays, and plan flags |
| `SuperAdminSeeder` | Default super admin user in central `users` table                                                                      |
| `DatabaseSeeder`   | Calls `PlanSeeder` + `SuperAdminSeeder`, and creates `test@example.com` sample user                                    |

**Default Super Admin Credentials:**

Email: `admin@jobflow.com`  
Password: `password`

Note: Change default credentials immediately in production.

## 12. Pricing Plans

| Plan         | Monthly Price | Annual Price | Workers   | Job Orders | Payroll | Auto-Approve |
| ------------ | ------------: | -----------: | --------- | ---------- | ------- | ------------ |
| Free         |            ₱0 |           ₱0 | 3         | 20/month   | No      | Yes          |
| Starter      |          ₱999 |       ₱9,990 | 10        | 150/month  | No      | No           |
| Professional |        ₱2,499 |      ₱24,990 | 50        | Unlimited  | Yes     | No           |
| Enterprise   |        Custom |       Custom | Unlimited | Unlimited  | Yes     | No           |

## 13. Folder Structure

```text
jobflow-oms/
├── app/                            # Core application code
│   ├── Http/                       # Controllers, middleware, and request validation
│   │   ├── Controllers/            # Central and tenant HTTP controllers
│   │   │   ├── Central/            # Public/central and admin domain controllers
│   │   │   │   └── Admin/          # Super admin feature controllers
│   │   │   └── Tenant/             # Tenant workspace controllers
│   │   ├── Middleware/             # Tenant-active and super-admin access middleware
│   │   └── Requests/               # Form request validation for central/tenant workflows
│   ├── Models/                     # Eloquent models for central and tenant entities
│   ├── Notifications/              # Notification classes (tenant approval mail)
│   ├── Providers/                  # Service providers (gates, tenancy bootstrapping)
│   └── Services/                   # Domain services for onboarding, activity logs, payroll
├── bootstrap/                      # Application bootstrap and provider registration
├── config/                         # Laravel and tenancy configuration files
├── database/                       # Migrations, factories, and seeders
│   ├── factories/                  # Model factories for tests/dev data
│   ├── migrations/                 # Central database migrations
│   │   └── tenant/                 # Tenant database migrations
│   └── seeders/                    # Seeders for plans, super admin, and base data
├── public/                         # Web root and built frontend assets
├── resources/                      # Frontend source files
│   ├── css/                        # Tailwind CSS entrypoint and design tokens
│   ├── js/                         # JavaScript entrypoints and Axios bootstrap
│   └── views/                      # Blade templates and layout components
├── routes/                         # Route definitions for central, tenant, and console
├── storage/                        # Logs, framework cache, and generated files
├── tests/                          # Pest feature/unit test suites
├── composer.json                   # PHP dependencies and Composer scripts
├── package.json                    # Node dependencies and Vite scripts
├── phpunit.xml                     # Test runner configuration
└── vite.config.js                  # Vite + Laravel plugin configuration
```

## 14. Key Routes

### Public Routes (central domains)

| Method | URI         | Route Name               | Description                |
| ------ | ----------- | ------------------------ | -------------------------- |
| GET    | `/`         | `home`                   | Public landing page        |
| GET    | `/pricing`  | `pricing`                | Public pricing page        |
| GET    | `/register` | `tenant.register.create` | Tenant registration form   |
| POST   | `/register` | `tenant.register.store`  | Submit tenant registration |

### Super Admin Routes (`admin.<central-domain>`)

| Method    | URI                             | Route Name                  | Description                      |
| --------- | ------------------------------- | --------------------------- | -------------------------------- |
| GET       | `/login`                        | `login`, `admin.login`      | Super admin login form           |
| POST      | `/login`                        | `admin.login.store`         | Super admin login submit         |
| POST      | `/logout`                       | `admin.logout`              | Super admin logout               |
| GET       | `/`                             | `admin.dashboard`           | Super admin dashboard            |
| GET       | `/tenants`                      | `admin.tenants.index`       | Tenant list                      |
| GET       | `/tenants/{tenant}`             | `admin.tenants.show`        | Tenant details and metrics       |
| PUT       | `/tenants/{tenant}`             | `admin.tenants.update`      | Update tenant branding metadata  |
| POST      | `/tenants/{tenant}/approve`     | `admin.tenants.approve`     | Approve pending tenant           |
| POST      | `/tenants/{tenant}/suspend`     | `admin.tenants.suspend`     | Suspend tenant workspace         |
| GET       | `/tenants/{tenant}/impersonate` | `admin.tenants.impersonate` | Impersonate tenant admin session |
| GET       | `/logs`                         | `admin.logs.index`          | Platform activity logs           |
| GET       | `/plans`                        | `admin.plans.index`         | Pricing plans index              |
| POST      | `/plans`                        | `admin.plans.store`         | Create pricing plan              |
| GET       | `/plans/create`                 | `admin.plans.create`        | Create plan form                 |
| GET       | `/plans/{plan}/edit`            | `admin.plans.edit`          | Edit plan form                   |
| PUT/PATCH | `/plans/{plan}`                 | `admin.plans.update`        | Update pricing plan              |
| DELETE    | `/plans/{plan}`                 | `admin.plans.destroy`       | Archive/delete pricing plan      |

### Tenant Routes (`{slug}.<central-root-domain>`)

| Method    | URI                          | Route Name                | Description                        |
| --------- | ---------------------------- | ------------------------- | ---------------------------------- |
| GET       | `/login`                     | `tenant.login`            | Tenant user login form             |
| POST      | `/login`                     | `tenant.login.store`      | Tenant login submit                |
| POST      | `/logout`                    | `tenant.logout`           | Tenant logout                      |
| GET       | `/`                          | —                         | Redirect to tenant dashboard       |
| GET       | `/dashboard`                 | `tenant.dashboard`        | Tenant dashboard                   |
| GET       | `/jobs`                      | `tenant.jobs.index`       | Job orders list                    |
| POST      | `/jobs`                      | `tenant.jobs.store`       | Create job order                   |
| GET       | `/jobs/create`               | `tenant.jobs.create`      | Job order create form              |
| GET       | `/jobs/{job}`                | `tenant.jobs.show`        | Job order details                  |
| GET       | `/jobs/{job}/edit`           | `tenant.jobs.edit`        | Job order edit form                |
| PUT/PATCH | `/jobs/{job}`                | `tenant.jobs.update`      | Update job order                   |
| DELETE    | `/jobs/{job}`                | `tenant.jobs.destroy`     | Delete job order                   |
| POST      | `/jobs/{job}/tasks`          | `tenant.tasks.store`      | Create task under job              |
| POST      | `/tasks/{task}/toggle`       | `tenant.tasks.toggle`     | Toggle task completion             |
| DELETE    | `/tasks/{task}`              | `tenant.tasks.destroy`    | Delete task                        |
| GET       | `/workers`                   | `tenant.workers.index`    | Workers list (gated)               |
| POST      | `/workers`                   | `tenant.workers.store`    | Create worker (gated)              |
| GET       | `/workers/create`            | `tenant.workers.create`   | Worker create form (gated)         |
| GET       | `/workers/{worker}`          | `tenant.workers.show`     | Worker details (gated)             |
| GET       | `/workers/{worker}/edit`     | `tenant.workers.edit`     | Worker edit form (gated)           |
| PUT/PATCH | `/workers/{worker}`          | `tenant.workers.update`   | Update worker (gated)              |
| DELETE    | `/workers/{worker}`          | `tenant.workers.destroy`  | Delete worker (gated)              |
| GET       | `/payroll`                   | `tenant.payroll.index`    | Payroll period list (gated)        |
| GET       | `/payroll/create`            | `tenant.payroll.create`   | Create payroll period form (gated) |
| POST      | `/payroll`                   | `tenant.payroll.store`    | Store payroll period (gated)       |
| GET       | `/payroll/{period}`          | `tenant.payroll.show`     | Payroll period detail (gated)      |
| POST      | `/payroll/{period}/generate` | `tenant.payroll.generate` | Generate payroll slips (gated)     |
| POST      | `/payroll/{period}/release`  | `tenant.payroll.release`  | Release payroll period (gated)     |

## 15. Academic Context

This project was developed strictly for academic purposes as part of a university course or capstone requirement. The system demonstrates principles of software engineering, multi-tenant architecture, role-based access control, and database design.

- **Developer:** Student / Developer (Repository Owner)
- **Status:** Non-Commercial / Educational Use Only
- **Notice:** This software is provided "as is" without warranty or ongoing maintenance. Pull requests and external open-source contributions are not actively monitored or accepted as this is a closed academic submission.
