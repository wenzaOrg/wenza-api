# wenza-api

> **Wenza Backend API** — Laravel 11 REST API powering the Wenza online tech academy ecosystem.

This is the PHP backend for [Wenza](https://wenza.com). It serves a JSON API at `api.wenza.com/api/v1/*`, hosts a Filament admin panel at `api.wenza.com/admin`, and handles authentication, payments, certificate generation, and async background jobs.

For project vision, brand, and the frontend repo, see the [root README](../README.md).
For the consuming frontend, see [`wenza-web`](../wenza-web/README.md).

---

## Table of Contents

1. [Stack](#1-stack)
2. [Local Setup](#2-local-setup)
3. [Project Structure](#3-project-structure)
4. [API Response Contract](#4-api-response-contract)
5. [Database Schema](#5-database-schema)
6. [Authentication](#6-authentication)
7. [API Endpoints](#7-api-endpoints)
8. [Certificate Generation](#8-certificate-generation)
9. [Payments](#9-payments)
10. [Queues & Background Jobs](#10-queues--background-jobs)
11. [Filament Admin Panel](#11-filament-admin-panel)
12. [Testing](#12-testing)
13. [Deployment](#13-deployment)
14. [Environment Variables](#14-environment-variables)
15. [Conventions & Code Style](#15-conventions--code-style)

---

## 1. Stack

| Layer | Choice |
|---|---|
| Language | PHP 8.3+ |
| Framework | Laravel 11 |
| Database | MySQL 8 (PostgreSQL 15 supported as alternative) |
| ORM | Eloquent |
| Auth | Laravel Sanctum (API token mode) |
| Authorisation | Spatie Laravel Permission + Laravel Policies |
| Cache + Queue | Redis |
| Admin Panel | Filament 3 |
| API Docs | Scribe (`knuckleswtf/scribe`) |
| PDF | `barryvdh/laravel-dompdf` |
| QR Codes | `simplesoftwareio/simple-qrcode` |
| Payments | `yabacon/paystack-php` (primary), Flutterwave PHP SDK (fallback) |
| Mail | Laravel Mail with Resend transport |
| Storage | S3-compatible (Cloudflare R2) via `league/flysystem-aws-s3-v3` |
| Testing | Pest (built on PHPUnit) |
| Static Analysis | PHPStan level 8 |
| Code Style | Laravel Pint (PSR-12) |

---

## 2. Local Setup

### Prerequisites

- PHP 8.3+ with extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `intl`, `json`, `mbstring`, `openssl`, `pcre`, `pdo_mysql`, `tokenizer`, `xml`, `zip`
- Composer 2.x
- MySQL 8.0+
- Redis 7+
- Node.js 20+ (only for Filament asset compilation)

### Steps

```bash
# Clone
git clone https://github.com/<org>/wenza-api.git
cd wenza-api

# Install dependencies
composer install
npm install                     # for Filament assets

# Environment
cp .env.example .env
php artisan key:generate

# Edit .env: set DB_*, REDIS_*, MAIL_*, PAYSTACK_*, GOOGLE_* values

# Database
php artisan migrate
php artisan db:seed             # seeds: roles, 16 courses, 5 cohorts, 10 testimonials, admin user

# Storage symlink (for local file serving)
php artisan storage:link

# Build Filament assets
npm run build

# Run dev server (terminal 1)
php artisan serve               # http://127.0.0.1:8000

# Run queue worker (terminal 2)
php artisan queue:work --queue=default,notifications,certificates

# Optional: run scheduler (terminal 3, only if testing scheduled jobs)
php artisan schedule:work
```

### Default Admin Credentials (after seeding)

```
Email:    admin@wenza.com
Password: WenzaAdmin2026!
URL:      http://127.0.0.1:8000/admin
```

Change immediately on first login.

---

## 3. Project Structure

```
wenza-api/
├── app/
│   ├── Filament/                       # Admin panel resources
│   │   └── Resources/
│   │       ├── CourseResource.php
│   │       ├── UserResource.php
│   │       ├── ScholarshipApplicationResource.php
│   │       └── ...
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── Concerns/
│   │   │       │   └── ApiResponse.php           # ★ envelope helper trait
│   │   │       ├── Auth/
│   │   │       │   ├── LoginController.php
│   │   │       │   ├── RegisterController.php
│   │   │       │   └── PasswordController.php
│   │   │       ├── CourseController.php
│   │   │       ├── EnrollmentController.php
│   │   │       ├── CertificateController.php
│   │   │       ├── PaymentController.php
│   │   │       └── PaystackWebhookController.php
│   │   ├── Requests/                   # Form Request validation
│   │   ├── Resources/                  # API Resources (response shaping)
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Course.php
│   │   ├── Cohort.php
│   │   ├── Module.php
│   │   ├── Lesson.php
│   │   ├── Enrollment.php
│   │   ├── Progress.php
│   │   ├── Certificate.php
│   │   ├── ScholarshipApplication.php
│   │   ├── Payment.php
│   │   └── ...
│   ├── Policies/
│   ├── Services/                       # Business logic
│   │   ├── PaystackService.php
│   │   ├── CertificateService.php
│   │   └── EnrollmentService.php
│   ├── Jobs/                           # Queued jobs
│   │   ├── GenerateCertificate.php
│   │   ├── ProcessPaystackWebhook.php
│   │   └── SendEnrollmentConfirmation.php
│   └── Mail/                           # Mailables
├── bootstrap/
│   └── app.php                         # ★ exception handlers for envelope errors
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── RoleSeeder.php
│   │   ├── CourseSeeder.php
│   │   └── TestimonialSeeder.php
│   └── factories/
├── resources/
│   └── views/
│       ├── certificates/template.blade.php       # PDF template
│       └── emails/
├── routes/
│   ├── api.php                         # /api/v1/* routes
│   └── web.php                         # Filament + health check only
├── storage/
├── tests/
│   ├── Feature/
│   │   ├── Auth/
│   │   ├── CoursesTest.php
│   │   ├── EnrollmentTest.php
│   │   └── CertificateTest.php
│   └── Unit/
├── composer.json
├── phpstan.neon
├── pint.json
└── .env.example
```

---

## 4. API Response Contract

> **This is the most important section in this README.** The frontend `useRequest` hook depends on a strict response envelope. Deviations break the entire frontend.

### 4.1 The Envelope

Every successful response:

```json
{
  "status": "success",
  "message": "Course retrieved",
  "data": { /* resource */ }
}
```

Every paginated response:

```json
{
  "status": "success",
  "message": "Records retrieved",
  "data": {
    "records": [ /* items */ ],
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "from": 1,
    "to": 15,
    "total": 73,
    "first_page_url": "...",
    "last_page_url": "...",
    "next_page_url": "...",
    "prev_page_url": null,
    "path": "...",
    "links": [ /* Laravel-style page links */ ]
  }
}
```

Every error:

```json
{
  "status": "error",
  "message": "What went wrong",
  "errors": { /* optional field-level errors */ }
}
```

**Critical rules:**
- The list in a paginated response MUST be on `data.records`. Not `data.items`. Not `data.data`. Not `data.results`.
- 401 responses MUST contain "Unauthenticated" in the message.
- Pagination metadata uses snake_case (`current_page`, `last_page`, `per_page`, `total`, `next_page_url`, `prev_page_url`).

### 4.2 The `ApiResponse` Trait

Every controller MUST use this trait. Do not return raw `response()->json([...])` — always go through `success()`, `error()`, or `paginated()`.

```php
// app/Http/Controllers/Api/Concerns/ApiResponse.php
<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    protected function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function error(
        string $message,
        int $status = 400,
        ?array $errors = null
    ): JsonResponse {
        $payload = ['status' => 'error', 'message' => $message];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }

    protected function paginated(
        LengthAwarePaginator $paginator,
        string $message = 'Records retrieved',
        ?string $resourceClass = null
    ): JsonResponse {
        $records = $resourceClass
            ? $resourceClass::collection($paginator->items())
            : $paginator->items();

        return $this->success([
            'records' => $records,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
            'first_page_url' => $paginator->url(1),
            'last_page_url' => $paginator->url($paginator->lastPage()),
            'next_page_url' => $paginator->nextPageUrl(),
            'prev_page_url' => $paginator->previousPageUrl(),
            'path' => $paginator->path(),
            'links' => $paginator->linkCollection()->toArray(),
        ], $message);
    }
}
```

### 4.3 Exception Handlers

Override Laravel's default exception rendering in `bootstrap/app.php` to ensure errors match the envelope:

```php
->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (ValidationException $e, Request $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }
    });

    $exceptions->render(function (AuthenticationException $e, Request $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated.',
            ], 401);
        }
    });

    $exceptions->render(function (ModelNotFoundException $e, Request $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Resource not found',
            ], 404);
        }
    });

    $exceptions->render(function (AuthorizationException $e, Request $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?: 'This action is unauthorized.',
            ], 403);
        }
    });
})
```

### 4.4 Reference Controller

```php
// app/Http/Controllers/Api/CourseController.php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $courses = Course::query()
            ->where('is_published', true)
            ->when($request->category, fn ($q, $cat) => $q->where('category', $cat))
            ->orderBy('title')
            ->paginate($request->per_page ?? 15);

        return $this->paginated($courses, 'Courses retrieved', CourseResource::class);
    }

    public function show(string $slug): JsonResponse
    {
        $course = Course::with(['modules.lessons', 'cohorts'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return $this->success(
            new CourseResource($course),
            'Course retrieved'
        );
    }

    public function store(StoreCourseRequest $request): JsonResponse
    {
        $course = Course::create($request->validated());

        return $this->success(
            new CourseResource($course),
            'Course created successfully',
            201
        );
    }
}
```

### 4.5 Rate-Limited Responses

When the throttle fires, format the response to match the error envelope:

```php
// app/Providers/AppServiceProvider.php (in boot())
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)
        ->by($request->user()?->id ?: $request->ip())
        ->response(function () {
            return response()->json([
                'status' => 'error',
                'message' => 'Too many requests, please slow down',
            ], 429);
        });
});
```

---

## 5. Database Schema

MySQL 8. Snake_case table names, conventional Laravel pluralisation. Migrations live in `database/migrations/`. Models in `app/Models/`.

### 5.1 Tables

```php
// users
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('email')->unique();
    $table->string('password');
    $table->string('first_name');
    $table->string('last_name');
    $table->enum('role', ['student', 'mentor', 'admin'])->default('student');
    $table->string('avatar_url')->nullable();
    $table->timestamp('email_verified_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
});

// courses
Schema::create('courses', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique();
    $table->string('title');
    $table->string('category');
    $table->text('description');
    $table->unsignedInteger('duration_weeks');
    $table->enum('format', ['cohort', 'self_paced'])->default('cohort');
    $table->unsignedBigInteger('price_ngn');
    $table->unsignedBigInteger('price_usd')->nullable();
    $table->unsignedBigInteger('scholarship_price_ngn')->nullable();
    $table->string('thumbnail_url')->nullable();
    $table->boolean('is_published')->default(false);
    $table->timestamps();
});

// cohorts
Schema::create('cohorts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('course_id')->constrained()->cascadeOnDelete();
    $table->string('name');                          // "Phoenix Cohort"
    $table->date('start_date');
    $table->date('end_date');
    $table->unsignedInteger('capacity');
    $table->enum('status', ['upcoming', 'active', 'completed'])->default('upcoming');
    $table->timestamps();
});

// modules
Schema::create('modules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('course_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->unsignedInteger('order');
    $table->text('description')->nullable();
    $table->timestamps();
});

// lessons
Schema::create('lessons', function (Blueprint $table) {
    $table->id();
    $table->foreignId('module_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->unsignedInteger('order');
    $table->enum('content_type', ['video', 'text', 'quiz']);
    $table->string('content_url')->nullable();
    $table->longText('content_body')->nullable();
    $table->unsignedInteger('duration_minutes')->nullable();
    $table->timestamps();
});

// enrollments
Schema::create('enrollments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('cohort_id')->constrained()->cascadeOnDelete();
    $table->timestamp('enrolled_at');
    $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
    $table->boolean('scholarship_applied')->default(false);
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
    $table->unique(['user_id', 'cohort_id']);
});

// progress
Schema::create('progress', function (Blueprint $table) {
    $table->id();
    $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
    $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
    $table->timestamp('completed_at');
    $table->timestamps();
    $table->unique(['enrollment_id', 'lesson_id']);
});

// assignments
Schema::create('assignments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->text('description');
    $table->timestamp('due_at')->nullable();
    $table->timestamps();
});

// submissions
Schema::create('submissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->timestamp('submitted_at');
    $table->longText('content');
    $table->unsignedTinyInteger('grade')->nullable();
    $table->text('feedback')->nullable();
    $table->timestamps();
});

// certificates
Schema::create('certificates', function (Blueprint $table) {
    $table->string('id')->primary();                 // "WZ-2026-00042"
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('course_id')->constrained()->cascadeOnDelete();
    $table->timestamp('issued_at');
    $table->string('pdf_url')->nullable();
    $table->unsignedInteger('verification_count')->default(0);
    $table->timestamps();
});

// scholarship_applications
Schema::create('scholarship_applications', function (Blueprint $table) {
    $table->id();
    $table->string('reference')->unique();
    $table->string('full_name');
    $table->string('email');
    $table->string('phone');
    $table->foreignId('course_id')->constrained();
    $table->text('motivation');
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->string('discount_code')->nullable();
    $table->timestamp('submitted_at');
    $table->timestamps();
});

// payments
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->foreignId('enrollment_id')->constrained();
    $table->unsignedBigInteger('amount_ngn');
    $table->enum('gateway', ['paystack', 'flutterwave']);
    $table->string('reference')->unique();
    $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
    $table->timestamp('paid_at')->nullable();
    $table->json('gateway_response')->nullable();
    $table->timestamps();
});

// testimonials
Schema::create('testimonials', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    $table->enum('source', ['twitter', 'linkedin', 'manual']);
    $table->text('content');
    $table->string('author_name');
    $table->string('author_role')->nullable();
    $table->boolean('is_featured')->default(false);
    $table->timestamps();
});

// jobs (board listings, not queued jobs)
Schema::create('job_listings', function (Blueprint $table) {
    $table->id();
    $table->string('company_name');
    $table->string('title');
    $table->text('description');
    $table->string('location');
    $table->enum('type', ['full_time', 'part_time', 'contract', 'internship']);
    $table->string('apply_url');
    $table->timestamp('posted_at');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

> **Naming note:** the job board table is `job_listings` (not `jobs`) to avoid colliding with Laravel's queue `jobs` table.

### 5.2 Eloquent Relationships

```php
// User
public function enrollments() { return $this->hasMany(Enrollment::class); }
public function certificates() { return $this->hasMany(Certificate::class); }
public function submissions() { return $this->hasMany(Submission::class); }

// Course
public function modules() { return $this->hasMany(Module::class)->orderBy('order'); }
public function cohorts() { return $this->hasMany(Cohort::class); }

// Module
public function lessons() { return $this->hasMany(Lesson::class)->orderBy('order'); }
public function course() { return $this->belongsTo(Course::class); }

// Enrollment
public function user() { return $this->belongsTo(User::class); }
public function cohort() { return $this->belongsTo(Cohort::class); }
public function progress() { return $this->hasMany(Progress::class); }
```

---

## 6. Authentication

### 6.1 Strategy

Sanctum in **API token mode** (not SPA cookie mode). The frontend stores the token in Redux and attaches it as `Authorization: Bearer {token}` on every request.

### 6.2 Setup

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

Add the `HasApiTokens` trait to the `User` model:

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    // ...
}
```

### 6.3 Login Endpoint

```php
// app/Http/Controllers/Api/Auth/LoginController.php
public function __invoke(LoginRequest $request): JsonResponse
{
    if (! Auth::attempt($request->only('email', 'password'))) {
        return $this->error('Invalid credentials', 401);
    }

    $user = $request->user();
    $token = $user->createToken('wenza-frontend')->plainTextToken;

    return $this->success([
        'user' => new UserResource($user),
        'token' => $token,
    ], 'Login successful');
}
```

### 6.4 Logout Endpoint

```php
public function __invoke(Request $request): JsonResponse
{
    $request->user()->currentAccessToken()->delete();
    return $this->success(null, 'Logged out');
}
```

### 6.5 Roles

Three roles seeded by `RoleSeeder`: `student`, `mentor`, `admin`. Use Spatie middleware:

```php
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('admin/courses', [CourseController::class, 'store']);
});
```

### 6.6 Password Policy

Min 8 chars, 1 uppercase, 1 number. Enforced in `RegisterRequest`:

```php
public function rules(): array
{
    return [
        'first_name' => 'required|string|max:100',
        'last_name' => 'required|string|max:100',
        'email' => 'required|email|unique:users',
        'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
    ];
}
```

---

## 7. API Endpoints

All endpoints are prefixed with `/api/v1/`. Use `routes/api.php`:

```php
Route::prefix('v1')->group(function () {
    // Public auth
    Route::post('auth/register', RegisterController::class);
    Route::post('auth/login', LoginController::class);
    Route::post('auth/forgot-password', ForgotPasswordController::class);
    Route::post('auth/reset-password', ResetPasswordController::class);
    Route::get('auth/verify-email/{token}', VerifyEmailController::class);

    // Public catalog
    Route::get('courses', [CourseController::class, 'index']);
    Route::get('courses/{slug}', [CourseController::class, 'show']);
    Route::get('testimonials', [TestimonialController::class, 'index']);
    Route::get('jobs', [JobController::class, 'index']);

    // Public certificate verification
    Route::get('verify/{id}', [CertificateController::class, 'verify'])
        ->middleware('throttle:60,1');

    // Public scholarship application
    Route::post('scholarship-applications', [ScholarshipController::class, 'store']);
    Route::get('scholarship-applications/{reference}', [ScholarshipController::class, 'show']);

    // Paystack webhook (no auth, signature-verified)
    Route::post('webhooks/paystack', PaystackWebhookController::class);

    // Authenticated
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me', MeController::class);
        Route::post('auth/logout', LogoutController::class);

        Route::get('me/enrollments', [EnrollmentController::class, 'mine']);
        Route::post('enrollments', [EnrollmentController::class, 'store']);

        Route::post('lessons/{lesson}/complete', [ProgressController::class, 'complete']);

        Route::get('me/certificates', [CertificateController::class, 'mine']);
        Route::get('certificates/{id}/download', [CertificateController::class, 'download']);

        Route::post('payments/initialise', [PaymentController::class, 'initialise']);
    });
});
```

### Endpoint Catalog

| Method | URL | Auth | Purpose |
|---|---|---|---|
| POST | `/auth/register` | — | Register a new student |
| POST | `/auth/login` | — | Returns user + Sanctum token |
| POST | `/auth/logout` | ✓ | Revokes current token |
| GET | `/auth/me` | ✓ | Current authenticated user |
| GET | `/courses` | — | Paginated catalog (filterable by `category`) |
| GET | `/courses/{slug}` | — | Single course with modules + cohorts |
| POST | `/enrollments` | ✓ | Create a pending enrollment |
| GET | `/me/enrollments` | ✓ | Current user's enrollments |
| POST | `/lessons/{id}/complete` | ✓ | Mark lesson complete |
| GET | `/verify/{id}` | — | Public certificate verification |
| GET | `/me/certificates` | ✓ | List user's earned certificates |
| GET | `/certificates/{id}/download` | ✓ | Download certificate PDF |
| POST | `/payments/initialise` | ✓ | Initialise Paystack transaction |
| POST | `/webhooks/paystack` | signature | Paystack webhook handler |
| POST | `/scholarship-applications` | — | Submit scholarship application |
| GET | `/scholarship-applications/{ref}` | — | Status lookup |

Generate full OpenAPI docs:

```bash
php artisan scribe:generate
# serves at /docs
```

---

## 8. Certificate Generation

### 8.1 Certificate ID Format

`WZ-YYYY-NNNNN` — e.g., `WZ-2026-00042`

- `WZ` = Wenza
- `YYYY` = year of issuance
- `NNNNN` = zero-padded sequential

### 8.2 Generation Flow

When a student completes the final lesson of a course, `ProgressController::complete` checks if all lessons in the cohort are done. If yes, it dispatches `GenerateCertificate`:

```php
// app/Jobs/GenerateCertificate.php
<?php

namespace App\Jobs;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Certificate $certificate) {}

    public function handle(): void
    {
        $qrCode = base64_encode(
            QrCode::format('png')->size(120)->generate(
                config('app.cert_url') . "/verify/{$this->certificate->id}"
            )
        );

        $pdf = Pdf::loadView('certificates.template', [
            'certificate' => $this->certificate->load(['user', 'course']),
            'qrCode' => $qrCode,
        ]);

        $path = "certificates/{$this->certificate->id}.pdf";
        Storage::disk('r2')->put($path, $pdf->output());

        $this->certificate->update([
            'pdf_url' => Storage::disk('r2')->url($path),
        ]);
    }
}
```

### 8.3 Verification Endpoint

Cache responses for 5 minutes via Redis to consistently hit the < 200ms target:

```php
// app/Http/Controllers/Api/CertificateController.php
public function verify(string $id): JsonResponse
{
    $certificate = Cache::remember(
        "certificate:{$id}",
        now()->addMinutes(5),
        fn () => Certificate::with(['user', 'course'])->find($id)
    );

    if (! $certificate) {
        return $this->error('Certificate not found', 404);
    }

    Certificate::where('id', $id)->increment('verification_count');

    return $this->success([
        'valid' => true,
        'holder_name' => $certificate->user->first_name . ' ' . $certificate->user->last_name,
        'course_title' => $certificate->course->title,
        'issued_at' => $certificate->issued_at->toIso8601String(),
    ], 'Certificate is valid');
}
```

### 8.4 PDF Template

Lives at `resources/views/certificates/template.blade.php`. Should include the Wenza logo (in brand primary `#B05010`), holder name in Urbanist Bold, course title, cohort name, issuance date, signature image, and the QR code.

---

## 9. Payments

Primary: Paystack via `yabacon/paystack-php`. Fallback: Flutterwave.

### 9.1 Initialise Flow

```php
// app/Http/Controllers/Api/PaymentController.php
public function initialise(InitialisePaymentRequest $request, PaystackService $paystack): JsonResponse
{
    $enrollment = Enrollment::findOrFail($request->enrollment_id);

    $this->authorize('pay', $enrollment);

    $payment = Payment::create([
        'user_id' => $request->user()->id,
        'enrollment_id' => $enrollment->id,
        'amount_ngn' => $enrollment->cohort->course->scholarship_price_ngn ?? $enrollment->cohort->course->price_ngn,
        'gateway' => 'paystack',
        'reference' => 'WZP-' . Str::upper(Str::random(12)),
        'status' => 'pending',
    ]);

    $response = $paystack->initialise([
        'email' => $request->user()->email,
        'amount' => $payment->amount_ngn * 100,        // Paystack expects kobo
        'reference' => $payment->reference,
        'callback_url' => config('app.frontend_url') . '/payments/callback',
    ]);

    return $this->success([
        'authorization_url' => $response['data']['authorization_url'],
        'reference' => $payment->reference,
    ], 'Payment initialised');
}
```

### 9.2 Webhook Handler

```php
// app/Http/Controllers/Api/PaystackWebhookController.php
public function __invoke(Request $request): Response
{
    $signature = $request->header('x-paystack-signature');
    $expected = hash_hmac('sha512', $request->getContent(), config('services.paystack.secret'));

    if (! hash_equals($expected, $signature)) {
        abort(401);
    }

    ProcessPaystackWebhook::dispatch($request->all());

    return response('', 200);
}
```

Exclude this route from CSRF in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: [
        'api/v1/webhooks/*',
    ]);
})
```

---

## 10. Queues & Background Jobs

Redis-backed queues with three named queues:

| Queue | Used For |
|---|---|
| `default` | General async work |
| `notifications` | Emails (welcome, enrollment confirmation, password reset) |
| `certificates` | PDF generation |

Run worker locally:

```bash
php artisan queue:work --queue=certificates,notifications,default --sleep=3 --tries=3
```

Production (Forge): managed by Supervisor with auto-restart on deploy.

### 10.1 Scheduled Tasks

Defined in `routes/console.php`:

```php
Schedule::command('backup:run')->daily()->at('02:00');
Schedule::command('certificates:cleanup-failed')->hourly();
Schedule::command('cohorts:activate-due')->dailyAt('00:01');
```

Forge runs `php artisan schedule:run` every minute via cron.

---

## 11. Filament Admin Panel

Filament 3 lives at `/admin` and provides admin CRUD for everything. Setup:

```bash
composer require filament/filament:"^3.0"
php artisan filament:install --panels
php artisan make:filament-user
```

### 11.1 Resources

Build a Filament Resource for each admin-managed model:

- `CourseResource` — CRUD, manage modules/lessons inline
- `CohortResource` — schedule cohorts, view enrolments
- `UserResource` — manage students, mentors, admins
- `EnrollmentResource` — view + filter all enrolments
- `PaymentResource` — payment audit
- `ScholarshipApplicationResource` — review + approve/reject applications, generate discount codes
- `TestimonialResource` — feature/unfeature testimonials
- `JobListingResource` — manage job board

### 11.2 Sample Resource

```php
// app/Filament/Resources/CourseResource.php
class CourseResource extends Resource
{
    protected static ?string $model = Course::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')->required(),
            TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Select::make('category')->options([
                'engineering' => 'Engineering & Development',
                'data' => 'Data & Emerging Technologies',
                'design' => 'Design & Creativity',
                'business' => 'Management & Business',
                'security' => 'Security',
            ])->required(),
            Textarea::make('description')->rows(4),
            TextInput::make('duration_weeks')->numeric()->required(),
            TextInput::make('price_ngn')->numeric()->prefix('₦')->required(),
            TextInput::make('scholarship_price_ngn')->numeric()->prefix('₦'),
            Toggle::make('is_published'),
        ]);
    }

    // table(), getPages() ...
}
```

### 11.3 Theming

Apply Wenza's brand colours to the Filament panel by extending `AdminPanelProvider`:

```php
->colors([
    'primary' => Color::hex('#B05010'),
])
->brandName('Wenza Admin')
->brandLogo(asset('images/wenza-logo.svg'))
```

---

## 12. Testing

### 12.1 Stack

- **Pest** for feature + unit tests
- **PHPStan** level 8 for static analysis
- **Laravel Pint** (PSR-12) for code style
- Coverage target: ≥ 80% on Services, Controllers, Jobs

### 12.2 Running Tests

```bash
./vendor/bin/pest                       # all tests
./vendor/bin/pest --coverage            # with coverage
./vendor/bin/pest tests/Feature         # feature tests only
./vendor/bin/phpstan analyse            # static analysis
./vendor/bin/pint                       # auto-fix code style
./vendor/bin/pint --test                # check without fixing
```

### 12.3 Envelope Compliance Tests

Every endpoint MUST have a Pest test asserting the envelope shape. Reusable matcher:

```php
// tests/Pest.php
expect()->extend('toMatchSuccessEnvelope', function () {
    return $this
        ->toHaveKey('status', 'success')
        ->toHaveKey('message')
        ->toHaveKey('data');
});

expect()->extend('toMatchPaginatedEnvelope', function () {
    return $this
        ->toMatchSuccessEnvelope()
        ->and($this->value['data'])
        ->toHaveKeys([
            'records', 'current_page', 'last_page', 'per_page',
            'total', 'next_page_url', 'prev_page_url', 'links',
        ]);
});

expect()->extend('toMatchErrorEnvelope', function () {
    return $this
        ->toHaveKey('status', 'error')
        ->toHaveKey('message');
});
```

### 12.4 Sample Test

```php
// tests/Feature/CoursesTest.php
it('returns paginated courses in the correct envelope', function () {
    Course::factory()->count(20)->create(['is_published' => true]);

    $response = $this->getJson('/api/v1/courses');

    $response->assertOk();
    expect($response->json())->toMatchPaginatedEnvelope();
    expect($response->json('data.records'))->toHaveCount(15);  // default per_page
});

it('returns 401 with the exact unauthenticated message', function () {
    $response = $this->getJson('/api/v1/auth/me');

    $response->assertStatus(401);
    expect($response->json())->toMatchErrorEnvelope();
    expect($response->json('message'))->toContain('Unauthenticated');
});
```

---

## 13. Deployment

### 13.1 Production Stack

- **Laravel Forge** managing a **DigitalOcean** ($24/mo, 4GB RAM) or **Hetzner** (cheaper, ~€8/mo) VPS
- **Cloudflare** for DNS + DDoS + SSL termination
- **MySQL 8** on the same VPS
- **Redis** on the same VPS for cache + queue
- **R2** for file storage
- API hosted at `api.wenza.com`

### 13.2 Forge Deploy Script

```bash
cd /home/forge/api.wenza.com
git pull origin main
composer install --no-dev --optimize-autoloader

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

php artisan queue:restart

if [ -f artisan ]; then
    sudo -S service php8.3-fpm reload
fi
```

### 13.3 Supervisor for Queue Workers

Forge configures Supervisor automatically. Manual config example:

```ini
[program:wenza-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/forge/api.wenza.com/artisan queue:work --queue=certificates,notifications,default --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=forge
numprocs=2
redirect_stderr=true
stdout_logfile=/home/forge/api.wenza.com/storage/logs/worker.log
stopwaitsecs=3600
```

### 13.4 CI/CD

GitHub Actions workflow on every PR:

```yaml
# .github/workflows/ci.yml
name: CI
on: [pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    services:
      mysql: { image: mysql:8, env: { MYSQL_ROOT_PASSWORD: root } }
      redis: { image: redis:7 }
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with: { php-version: '8.3' }
      - run: composer install --prefer-dist --no-progress
      - run: cp .env.example .env && php artisan key:generate
      - run: ./vendor/bin/pint --test
      - run: ./vendor/bin/phpstan analyse
      - run: ./vendor/bin/pest
```

On merge to `main`, hit the Forge deploy webhook.

---

## 14. Environment Variables

```env
APP_NAME=Wenza
APP_ENV=production
APP_KEY=                       # php artisan key:generate
APP_DEBUG=false
APP_URL=https://api.wenza.com
APP_TIMEZONE=Africa/Lagos

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wenza
DB_USERNAME=
DB_PASSWORD=

# Cache + Queue
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Sanctum (API token mode — no stateful domains needed)
SANCTUM_STATEFUL_DOMAINS=

# CORS
FRONTEND_URL=https://wenza.com
APP_FRONTEND_URL=https://app.wenza.com
CERT_FRONTEND_URL=https://certificates.wenza.com
SCHOLARSHIP_FRONTEND_URL=https://scholarship.wenza.com

# Mail
MAIL_MAILER=resend
RESEND_API_KEY=
MAIL_FROM_ADDRESS=hello@wenza.com
MAIL_FROM_NAME=Wenza

# Payments
PAYSTACK_SECRET_KEY=
PAYSTACK_PUBLIC_KEY=
FLUTTERWAVE_SECRET_KEY=
FLUTTERWAVE_PUBLIC_KEY=

# Google OAuth
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=https://api.wenza.com/api/v1/auth/google/callback

# File Storage (Cloudflare R2)
FILESYSTEM_DISK=r2
R2_ACCESS_KEY_ID=
R2_SECRET_ACCESS_KEY=
R2_BUCKET=wenza
R2_ENDPOINT=
R2_URL=

# Sentry
SENTRY_LARAVEL_DSN=
```

CORS config in `config/cors.php` should allow all four frontend origins:

```php
'allowed_origins' => [
    env('FRONTEND_URL'),
    env('APP_FRONTEND_URL'),
    env('CERT_FRONTEND_URL'),
    env('SCHOLARSHIP_FRONTEND_URL'),
],
```

---

## 15. Conventions & Code Style

- **PSR-12** enforced via Pint
- **PHPStan level 8** — no `mixed` returns without explicit annotation
- **snake_case** for database columns, JSON keys, and URLs
- **PascalCase** for classes
- **camelCase** for variables and methods
- **Form Requests** for ALL validation — never validate inline in controllers
- **API Resources** for ALL response shaping — never return raw Eloquent models
- **Services** for business logic — controllers stay thin
- **Policies** for authorisation — never check roles inside controllers
- **Jobs** for anything that takes > 200ms — never block the request
- **UK English** in user-facing copy and comments

### 15.1 Pull Request Checklist

Before opening a PR:

- [ ] `./vendor/bin/pint` passes
- [ ] `./vendor/bin/phpstan analyse` passes
- [ ] `./vendor/bin/pest` passes
- [ ] New endpoints have feature tests asserting the envelope
- [ ] New endpoints documented via Scribe annotations
- [ ] Migrations are reversible (`down()` method works)
- [ ] No raw `response()->json()` — used `ApiResponse` trait
- [ ] No `dd()`, `dump()`, or `var_dump()` left in code

---

## Appendix: Common Commands

```bash
# Make a migration
php artisan make:migration create_foo_table

# Make a model + migration + factory + seeder
php artisan make:model Foo -mfs

# Make a controller in the API namespace
php artisan make:controller Api/FooController

# Make a Form Request
php artisan make:request StoreFooRequest

# Make an API Resource
php artisan make:resource FooResource

# Make a Filament Resource
php artisan make:filament-resource Foo --generate

# Tinker (REPL)
php artisan tinker

# Clear all caches
php artisan optimize:clear

# Re-cache for production
php artisan optimize
```

---

For the project vision and brand, see [`../README.md`](../README.md).
For the frontend that consumes this API, see [`../wenza-web/README.md`](../wenza-web/README.md).