# 📋 Panduan Activity Log System

Sistem Activity Log ini menyediakan audit trail otomatis untuk melacak semua aktivitas CRUD dan aksi penting dalam aplikasi.

---

## 🏗️ Arsitektur

```
┌─────────────────────────────────────────────────────────────────┐
│                         APPLICATION                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────────┐   │
│  │    Model     │    │  Controller  │    │     Service      │   │
│  │ (with Trait) │    │              │    │                  │   │
│  └──────┬───────┘    └──────┬───────┘    └────────┬─────────┘   │
│         │                   │                     │              │
│         │ created/updated   │ login/logout        │ manual log   │
│         │ /deleted events   │                     │              │
│         ▼                   ▼                     ▼              │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │                    LogsActivity Trait                     │   │
│  │                  / ActivityLogService                     │   │
│  └──────────────────────────┬───────────────────────────────┘   │
│                             │                                    │
│                             ▼                                    │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │                  activity_logs table                      │   │
│  │  • user_id      • action       • description              │   │
│  │  • subject_type • subject_id   • old_values              │   │
│  │  • new_values   • properties   • ip_address              │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📦 Komponen

| File | Deskripsi |
|------|-----------|
| `app/Models/ActivityLog.php` | Model untuk menyimpan log |
| `app/Traits/LogsActivity.php` | Trait untuk auto-logging CRUD |
| `app/Services/ActivityLogService.php` | Service untuk operasi log manual |
| `app/Http/Controllers/ActivityLogController.php` | Controller untuk view |
| `resources/views/pages/activity-log/index.blade.php` | UI untuk melihat log |

---

## 🚀 Cara Penggunaan

### 1. Auto-Logging CRUD (Menggunakan Trait)

Tambahkan `LogsActivity` trait ke Model yang ingin di-track:

```php
<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'price', 'description'];
    
    // OPSIONAL: Tentukan field yang di-log (default: semua)
    protected static array $logAttributes = ['name', 'price'];
    
    // OPSIONAL: Field yang TIDAK di-log (default: password, remember_token, timestamps)
    protected static array $logExcept = ['internal_notes'];
}
```

**Hasil:** Setiap kali `Product` dibuat, diupdate, atau dihapus, sistem akan otomatis mencatat:
- Siapa yang melakukan
- Kapan dilakukan
- Data sebelum dan sesudah perubahan
- IP Address dan User Agent

### 2. Logging Manual (Non-CRUD)

Untuk aksi yang tidak ter-cover oleh trait CRUD, gunakan `ActivityLogService`:

```php
<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;

class ReportController extends Controller
{
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    public function export()
    {
        // ... proses export ...

        // Log aktivitas export
        $this->activityLogService->log(
            action: 'exported',
            description: 'User mengeksport laporan penjualan',
            subject: null, // atau Model tertentu
            properties: [
                'format' => 'xlsx',
                'date_range' => '2024-01-01 - 2024-12-31'
            ]
        );

        return $file;
    }
}
```

### 3. Logging Custom dari Model Instance

Jika Model sudah menggunakan trait, bisa juga log custom:

```php
$product = Product::find(1);

$product->logCustomActivity(
    action: 'viewed',
    description: 'User melihat detail produk',
    properties: ['referrer' => request()->headers->get('referer')]
);
```

---

## 🔍 Mengakses Log

### Via Web UI

Akses halaman: `/activity-log`

Fitur:
- Filter berdasarkan aksi, user, tanggal
- Lihat detail perubahan data (before/after)
- Pagination

### Via API

```javascript
// Get paginated logs
fetch('/activity-log/data?per_page=15&action=created')
    .then(res => res.json())
    .then(data => console.log(data));

// Get statistics
fetch('/activity-log/statistics?days=30')
    .then(res => res.json())
    .then(data => console.log(data));
```

### Via Code (Query)

```php
use App\Models\ActivityLog;
use App\Services\ActivityLogService;

// Query langsung
$recentLogs = ActivityLog::with('user')
    ->action('created')
    ->forModel(Product::class)
    ->betweenDates('2024-01-01', '2024-12-31')
    ->latest()
    ->take(10)
    ->get();

// Via Service
$service = app(ActivityLogService::class);

// Get logs untuk user tertentu
$userLogs = $service->getByUser($userId, limit: 10);

// Get logs untuk model tertentu
$productLogs = $service->getBySubject($product, limit: 10);

// Get statistik
$stats = $service->getStatistics(days: 30);
```

---

## 🔧 Konfigurasi

### Mengubah Field Default yang Di-ignore

Edit `app/Traits/LogsActivity.php`:

```php
protected static function getLogExceptAttributes(): array
{
    $defaults = ['password', 'remember_token', 'updated_at', 'created_at'];
    // Tambahkan field global yang tidak perlu di-log
    
    if (property_exists(static::class, 'logExcept')) {
        return array_merge($defaults, static::$logExcept);
    }
    
    return $defaults;
}
```

### Custom Description

Override method di Model:

```php
class Product extends Model
{
    use LogsActivity;
    
    protected static function getLogDescription(string $action, Model $model): string
    {
        return match ($action) {
            'created' => "Produk baru '{$model->name}' ditambahkan dengan harga Rp " . number_format($model->price),
            'updated' => "Produk '{$model->name}' diperbarui",
            'deleted' => "Produk '{$model->name}' dihapus dari sistem",
            default => parent::getLogDescription($action, $model),
        };
    }
}
```

---

## 🧹 Maintenance

### Cleanup Old Logs

Untuk membersihkan log lama (misalnya > 90 hari):

```php
// Via Service
$service = app(ActivityLogService::class);
$deletedCount = $service->cleanup(daysToKeep: 90);

// Atau buat scheduled command
// app/Console/Kernel.php
$schedule->call(function () {
    app(ActivityLogService::class)->cleanup(90);
})->daily();
```

---

## 📊 Tipe Aksi (Action Types)

| Action | Deskripsi | Trigger |
|--------|-----------|---------|
| `created` | Data baru dibuat | Model::created event |
| `updated` | Data diperbarui | Model::updated event |
| `deleted` | Data dihapus | Model::deleted event |
| `login` | User login | AuthController::login() |
| `logout` | User logout | AuthController::logout() |
| `viewed` | Data dilihat | Manual log |
| `exported` | Data diekspor | Manual log |
| `imported` | Data diimpor | Manual log |
| (custom) | Aksi custom | Manual log |

---

## ✅ Best Practices

1. **Jangan log data sensitif** - Gunakan `$logExcept` untuk field seperti password, token, dll
2. **Batasi log attributes** - Gunakan `$logAttributes` jika hanya perlu track field tertentu
3. **Cleanup reguler** - Set scheduled task untuk hapus log lama agar database tidak membengkak
4. **Index columns** - Pastikan kolom yang sering di-filter sudah di-index (sudah ada di migration)
