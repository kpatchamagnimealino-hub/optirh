# OptiRH

[![CI](https://github.com/Dreykovic/optirh/actions/workflows/ci.yml/badge.svg)](https://github.com/Dreykovic/optirh/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20.svg?logo=laravel&logoColor=white)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4.svg?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1.svg?logo=mysql&logoColor=white)](https://www.mysql.com/)

**OptiRH** is a full-featured Human Resources management platform built with Laravel 10. Designed and deployed for a government agency, it provides two core modules for HR operations and administrative appeals processing, with role-based access control, automated workflows, real-time notifications, and PDF document generation.

---

## Key Features

### OptiHR — HR Management

| Feature | Description |
|---------|-------------|
| **Personnel** | Full employee lifecycle management with hierarchical organization (departments, jobs, duties) and N+1 manager chains |
| **Absences** | Multi-level approval workflow (N+1 → GRH → DG) with leave balance tracking, deductible logic, and automatic PDF generation |
| **Documents** | Automated HR document requests (certificates, attestations) with approval flow and downloadable PDFs |
| **Publications** | Internal communication board with file attachments, PDF preview, and real-time updates via Pusher WebSockets |
| **Dashboards** | Role-specific dashboards with statistics, charts, and quick-action widgets for each user role |
| **Activity Logs** | Comprehensive audit trail tracking every user action across the platform with scheduled cleanup |
| **Email Notifications** | Resilient email system with retry mechanism, failover chain (SMTP → Sendmail → Log), and async queue processing |

### Recours — Administrative Appeals

| Feature | Description |
|---------|-------------|
| **Appeal Filing** | Structured submission forms with applicant, authority, and case details |
| **Workflow Tracking** | Multi-stage status pipeline with automated day counting and configurable deadline reminders |
| **DAC Commission** | Collegiate decision management with generated PDF decision documents |
| **Analytics** | Dashboard with date-range filtering, status breakdowns, and trend charts |

### Access Control (RBAC)

Six predefined roles with granular permissions managed via [Spatie Permission](https://spatie.be/docs/laravel-permission):

| Role | Scope |
|------|-------|
| **Admin** | Full platform access, user management, system configuration |
| **DG** (Director General) | Approval authority, all module access, activity logs |
| **DSAF** (Finance Director) | Financial oversight, absence/document approvals |
| **GRH** (HR Manager) | Personnel management, absence/document processing, credentials |
| **DRAJ** | Appeals management, all module access |
| **Employee** | Self-service: own absences, documents, publications view |

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | Laravel 10, PHP 8.1+ |
| **Database** | MySQL 8.0+ |
| **Frontend** | Blade Templates, Bootstrap 5, JavaScript ES6+, DataTables |
| **Real-time** | Pusher WebSockets |
| **PDF Generation** | DomPDF, FPDI |
| **Authentication** | Laravel Sanctum |
| **Authorization** | Spatie Permission (RBAC) |
| **Email** | Queue-based with retry and failover |
| **Build Tools** | Vite, NPM |
| **CI/CD** | GitHub Actions |
| **Containerization** | Docker, Docker Compose, Nginx |

---

## Getting Started

### Prerequisites

- PHP 8.1+ (with `bcmath`, `pdo_mysql`, `gd` extensions)
- MySQL 8.0+
- Composer 2.x
- Node.js 18+ & NPM

### Installation

```bash
# Clone the repository
git clone https://github.com/Dreykovic/optirh.git
cd optirh

# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Set up database
php artisan migrate --seed

# Build frontend assets
npm run build

# Start the development server
php artisan serve
```

The application will be available at **http://localhost:8000**.

### Docker

```bash
docker-compose up -d
docker exec -it laravel-app php artisan migrate --seed
```

| Service | URL |
|---------|-----|
| Application | `http://localhost:8088` |
| phpMyAdmin | `http://localhost:8081` |

### Default Accounts

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `Admin@2024` |
| Director General | `director_general` | `Dg@2024` |
| Finance Director | `finance_director` | `Dsaf@2024` |
| HR Manager | `hr_manager` | `Grh@2024` |
| Employee | `employee1` | `Employee@2024` |

---

## Architecture

```
app/
├── Console/Commands/        # Scheduled tasks (cleanup, reminders, balance updates)
├── Http/
│   ├── Controllers/
│   │   ├── OptiHr/          # HR module (absences, documents, publications, employees...)
│   │   └── Recours/         # Appeals module (appeals, DAC, statistics)
│   └── Middleware/           # Auth, RBAC, JSON response handling
├── Models/
│   ├── OptiHr/              # Employee, Absence, Document, Publication, Holiday...
│   └── Recours/             # Appeal, Applicant, Authority, Dac, Decision...
├── Services/                # Business logic (AbsencePdfService, MailService, FileService)
├── Jobs/                    # Async processing (SendEmailJob, CleanupActivityLogsJob)
├── Mail/                    # Mailable classes for all notification types
├── Observers/               # Model lifecycle hooks (EmployeeObserver)
└── Traits/                  # Shared behavior (LogsActivity, SendsEmails)

resources/views/modules/
├── opti-hr/                 # HR views, email templates, PDF templates
└── recours/                 # Appeals views and partials

database/
├── migrations/              # 20+ migration files
└── seeders/                 # Full dev dataset (departments, roles, users, absence types)
```

### Scheduled Tasks

| Schedule | Command | Description |
|----------|---------|-------------|
| Yearly (Dec 31) | `duties:update-absence-balance` | Reset annual leave balances (+30 days) |
| Hourly (8am–6pm) | `appeals:update-day-count` | Increment appeal processing counters |
| Daily (12:00) | `appeals:send-daily-*-reminder` | Email reminders for overdue appeals |
| Weekly (Sun 01:00) | `cleanup:activity-logs` | Purge activity logs older than 90 days |

---

## Development

```bash
# Run the test suite
php artisan test

# Code style check (PSR-12 via Laravel Pint)
./vendor/bin/pint --test

# Auto-fix code style
./vendor/bin/pint

# Clear all caches
php artisan optimize:clear

# Vite dev server with hot module replacement
npm run dev
```

### Git Workflow

This project follows **GitFlow**:

- `main` — Production-ready releases
- `develop` — Integration branch
- `feature/*` — New features
- `fix/*` / `hot-fixes` — Bug fixes
- Pull requests required for merging into `main`

---

## Documentation

| Document | Description |
|----------|-------------|
| [User Guide](docs/USER_GUIDE.md) | End-user platform documentation |
| [Installation Guide](docs/INSTALLATION.md) | Detailed deployment and server setup |
| [Contributing Guide](docs/CONTRIBUTING.md) | Developer onboarding and code standards |
| [API Documentation](docs/API_DOCUMENTATION.md) | Internal AJAX endpoints reference |
| [Entity Documentation](docs/ENTITIES_DOCUMENTATION.md) | Database schema and model relationships |
| [Security Policy](docs/SECURITY.md) | Vulnerability reporting procedures |
| [Changelog](CHANGELOG.md) | Version history and release notes |

---

## Authors

- **[@Dreykovic](https://github.com/Dreykovic)** — Lead developer. Architecture, authentication, absence & document modules, dashboards, email system, CI/CD.
- **[@Fayssol](https://github.com/Fayssol)** — Appeals module, payroll integration, employee management, UI components.

## License

This project is licensed under the [MIT License](LICENSE).
