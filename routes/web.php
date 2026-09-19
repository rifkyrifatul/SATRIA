<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LetterController as AdminLetterController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\LetterController as StaffLetterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Post-Login Redirect — Arahkan user ke dashboard sesuai role
|--------------------------------------------------------------------------
| Breeze mengarahkan ke '/dashboard' setelah login. Di sini kita
| intercept dan redirect ke dashboard yang sesuai peran user.
*/
Route::get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = auth()->user();

    return match ($user->role) {
        'super_admin' => redirect()->route('super_admin.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        'staff' => redirect()->route('staff.dashboard'),
        default => redirect()->route('login'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Global Authenticated Routes (Notifications dll)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark_all_read');
});

/*
|--------------------------------------------------------------------------
| Staff Routes — Hanya bisa diakses oleh 'staff'
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {

        // Dashboard Staff
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])
            ->name('dashboard');

        // ── Tracking Posisi Surat ─────────────────────────────────────────
        Route::get('/posisi-surat', [StaffLetterController::class, 'tracking'])->name('tracking.index');
        Route::get('/posisi-surat/{letter}', [StaffLetterController::class, 'trackingShow'])->name('tracking.show');

        // ── Manajemen Surat Staff ──────────────────────────────────────────
        // Resource routes: index, create, store, show, edit, update, destroy
        Route::resource('letters', StaffLetterController::class);

        // Route download file — terpisah karena bukan bagian dari resource standar
        // GET /staff/letters/{letter}/download → staff.letters.download
        Route::get('/letters/{letter}/download', [StaffLetterController::class, 'download'])
            ->name('letters.download');

        // GET /staff/letters/{letter}/preview → staff.letters.preview
        Route::get('/letters/{letter}/preview', [StaffLetterController::class, 'preview'])
            ->name('letters.preview');

        // GET /staff/letters/{letter}/print → staff.letters.print
        Route::get('/letters/{letter}/print', [StaffLetterController::class, 'print'])
            ->name('letters.print');

        // Template Surat (Hanya View & Download)
        Route::get('/letter-templates', [\App\Http\Controllers\Staff\LetterTemplateController::class, 'index'])->name('letter_templates.index');
        Route::get('/letter-templates/{letterTemplate}/download', [\App\Http\Controllers\Staff\LetterTemplateController::class, 'download'])->name('letter_templates.download');

        // Arsip Surat Staff
        Route::get('/archives', [\App\Http\Controllers\Staff\ArchiveController::class, 'index'])->name('archives.index');
        Route::delete('/archives/{letter}', [\App\Http\Controllers\Staff\ArchiveController::class, 'destroy'])->name('archives.destroy');

        // Tong Sampah Staff
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::get('letters', [\App\Http\Controllers\Staff\TrashController::class, 'letters'])->name('letters');
            Route::delete('letters/empty', [\App\Http\Controllers\Staff\TrashController::class, 'emptyTrash'])->name('letters.empty_trash');
            Route::put('letters/{id}/restore', [\App\Http\Controllers\Staff\TrashController::class, 'restoreLetter'])->name('letters.restore');
            Route::delete('letters/{id}', [\App\Http\Controllers\Staff\TrashController::class, 'forceDeleteLetter'])->name('letters.force_delete');
        });

        // Buku Agenda / Surat Masuk & Keluar (Read-Only)
        Route::get('/mail-registries', [\App\Http\Controllers\Staff\MailRegistryController::class, 'index'])->name('mail_registries.index');
        Route::get('/mail-registries/{mailRegistry}/download', [\App\Http\Controllers\Staff\MailRegistryController::class, 'download'])->name('mail_registries.download');

        // Renbut (Rencana Kebutuhan)
        Route::resource('renbuts', \App\Http\Controllers\Staff\RenbutController::class)->except(['edit', 'update', 'destroy']);
        Route::get('/renbuts/{renbut}/download-attachment', [\App\Http\Controllers\Staff\RenbutController::class, 'downloadAttachment'])->name('renbuts.download_attachment');
        Route::get('/renbuts/{renbut}/download-response', [\App\Http\Controllers\Staff\RenbutController::class, 'downloadResponse'])->name('renbuts.download_response');
    });


/*
|--------------------------------------------------------------------------
| Super Admin Routes — Akses master data
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super_admin.')
    ->group(function () {
        // Dashboard Global
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/posisi-surat', [AdminLetterController::class, 'tracking'])->name('tracking.index');
        Route::get('/posisi-surat/{letter}', [AdminLetterController::class, 'trackingShow'])->name('tracking.show');

        // Manajemen Surat (Read Only untuk super_admin, karena tidak review)
        Route::resource('letters', AdminLetterController::class)->only(['index', 'show']);
        Route::get('/letters/{letter}/download', [AdminLetterController::class, 'download'])->name('letters.download');
        Route::get('/letters/{letter}/download-final', [AdminLetterController::class, 'downloadFinal'])->name('letters.download_final');
        Route::get('/letters/{letter}/preview', [AdminLetterController::class, 'preview'])->name('letters.preview');
        Route::post('/letters/{letter}/manual-progress', [AdminLetterController::class, 'manualProgress'])->name('letters.manual_progress');

        // Manajemen User
        Route::resource('users', AdminUserController::class)->except(['show']);
        Route::post('users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset_password');

        // Manajemen Kategori
        Route::resource('categories', AdminCategoryController::class)->except(['show']);

        // Manajemen Konfigurasi
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

        // Log Aktivitas
        Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity_logs.index');

        // Template Surat
        Route::resource('letter-templates', \App\Http\Controllers\Admin\LetterTemplateController::class)
            ->parameters(['letter-templates' => 'letterTemplate'])
            ->names('letter_templates')
            ->except(['show']);
        Route::get('/letter-templates/{letterTemplate}/download', [\App\Http\Controllers\Admin\LetterTemplateController::class, 'download'])->name('letter_templates.download');


        // Laporan & Statistik
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
            Route::get('/export/csv', [\App\Http\Controllers\Admin\ReportController::class, 'exportExcel'])->name('export.excel');
            Route::get('/export/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('export.pdf');
        });

        // Tong Sampah
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SuperAdminTrashController::class, 'index'])->name('index');
            Route::put('/letters/{id}/restore', [\App\Http\Controllers\Admin\SuperAdminTrashController::class, 'restoreLetter'])->name('letters.restore');
            Route::delete('/letters/{id}', [\App\Http\Controllers\Admin\SuperAdminTrashController::class, 'forceDeleteLetter'])->name('letters.force_delete');
            Route::put('/templates/{id}/restore', [\App\Http\Controllers\Admin\SuperAdminTrashController::class, 'restoreTemplate'])->name('templates.restore');
            Route::delete('/templates/{id}', [\App\Http\Controllers\Admin\SuperAdminTrashController::class, 'forceDeleteTemplate'])->name('templates.force_delete');
            Route::put('/users/{id}/restore', [\App\Http\Controllers\Admin\SuperAdminTrashController::class, 'restoreUser'])->name('users.restore');
            Route::delete('/users/{id}', [\App\Http\Controllers\Admin\SuperAdminTrashController::class, 'forceDeleteUser'])->name('users.force_delete');
            Route::put('/categories/{id}/restore', [\App\Http\Controllers\Admin\SuperAdminTrashController::class, 'restoreCategory'])->name('categories.restore');
            Route::delete('/categories/{id}', [\App\Http\Controllers\Admin\SuperAdminTrashController::class, 'forceDeleteCategory'])->name('categories.force_delete');
        });

        // Arsip Surat
        Route::get('/archives', [\App\Http\Controllers\Admin\ArchiveController::class, 'index'])->name('archives.index');
        Route::get('/archives/recap/pdf', [\App\Http\Controllers\Admin\ArchiveController::class, 'exportMonthlyPdf'])->name('archives.recap_pdf');
        Route::get('/archives/recap/excel', [\App\Http\Controllers\Admin\ArchiveController::class, 'exportMonthlyExcel'])->name('archives.recap_excel');
        Route::delete('/archives/{letter}', [\App\Http\Controllers\Admin\ArchiveController::class, 'destroy'])->name('archives.destroy');

        // Buku Agenda / Surat Masuk & Keluar
        Route::get('/mail-registries/recap/pdf', [\App\Http\Controllers\Admin\MailRegistryController::class, 'exportMonthlyPdf'])->name('mail_registries.recap_pdf');
        Route::get('/mail-registries/recap/excel', [\App\Http\Controllers\Admin\MailRegistryController::class, 'exportMonthlyExcel'])->name('mail_registries.recap_excel');
        Route::resource('mail-registries', \App\Http\Controllers\Admin\MailRegistryController::class)
            ->names('mail_registries')
            ->except(['show']);
        Route::get('/mail-registries/{mailRegistry}/download', [\App\Http\Controllers\Admin\MailRegistryController::class, 'download'])->name('mail_registries.download');
        Route::get('/mail-registries/{mailRegistry}/disposition', [\App\Http\Controllers\Admin\MailRegistryController::class, 'dispositionForm'])->name('mail_registries.disposition');
        Route::post('/mail-registries/{mailRegistry}/disposition', [\App\Http\Controllers\Admin\MailRegistryController::class, 'storeDisposition'])->name('mail_registries.disposition.store');

        // Berkas SPJ (Super Admin Access)
        Route::resource('spjs', \App\Http\Controllers\Admin\SpjController::class)->except(['show']);
        Route::get('/spjs/{spj}/download', [\App\Http\Controllers\Admin\SpjController::class, 'download'])->name('spjs.download');
        Route::get('/spjs/{spj}/preview', [\App\Http\Controllers\Admin\SpjController::class, 'preview'])->name('spjs.preview');
    });

/*
|--------------------------------------------------------------------------
| Admin (Reviewer) Routes — Hanya untuk review surat
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard Reviewer
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/posisi-surat', [AdminLetterController::class, 'tracking'])->name('tracking.index');
        Route::get('/posisi-surat/{letter}', [AdminLetterController::class, 'trackingShow'])->name('tracking.show');
        Route::get('/reviews', [\App\Http\Controllers\Admin\LetterReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/history', [\App\Http\Controllers\Admin\LetterReviewController::class, 'history'])->name('reviews.history');

        Route::get('/letters/{letter}', [AdminLetterController::class, 'show'])->name('letters.show');
        Route::get('/letters/{letter}/download', [AdminLetterController::class, 'download'])->name('letters.download');
        Route::get('/letters/{letter}/preview', [AdminLetterController::class, 'preview'])->name('letters.preview');
        Route::get('/letters/{letter}/download-final', [AdminLetterController::class, 'downloadFinal'])->name('letters.download_final');

        // Aksi Review
        Route::post('/letters/{letter}/revision', [AdminLetterController::class, 'requestRevision'])->name('letters.revision');
        Route::post('/letters/{letter}/approve', [AdminLetterController::class, 'approve'])->name('letters.approve');
        Route::post('/letters/{letter}/manual-progress', [AdminLetterController::class, 'manualProgress'])->name('letters.manual_progress');

        // Template Surat (View & Download untuk Admin Umum, Full Access untuk Admin SETUM)
        Route::resource('letter-templates', \App\Http\Controllers\Admin\LetterTemplateController::class)
            ->parameters(['letter-templates' => 'letterTemplate'])
            ->names('letter_templates')
            ->except(['show']);
        Route::get('/letter-templates/{letterTemplate}/download', [\App\Http\Controllers\Admin\LetterTemplateController::class, 'download'])->name('letter_templates.download');

        // Manajemen Kategori (Full Access untuk Admin SETUM)
        Route::resource('categories', AdminCategoryController::class)->except(['show']);

        // Arsip Surat
        Route::get('/archives', [\App\Http\Controllers\Admin\ArchiveController::class, 'index'])->name('archives.index');
        Route::get('/archives/recap/pdf', [\App\Http\Controllers\Admin\ArchiveController::class, 'exportMonthlyPdf'])->name('archives.recap_pdf');
        Route::get('/archives/recap/excel', [\App\Http\Controllers\Admin\ArchiveController::class, 'exportMonthlyExcel'])->name('archives.recap_excel');
        Route::delete('/archives/{letter}', [\App\Http\Controllers\Admin\ArchiveController::class, 'destroy'])->name('archives.destroy');

        // Buku Agenda / Surat Masuk & Keluar (Full Access untuk Admin SETUM, Read-Only untuk admin lain)
        Route::get('/mail-registries/recap/pdf', [\App\Http\Controllers\Admin\MailRegistryController::class, 'exportMonthlyPdf'])->name('mail_registries.recap_pdf');
        Route::get('/mail-registries/recap/excel', [\App\Http\Controllers\Admin\MailRegistryController::class, 'exportMonthlyExcel'])->name('mail_registries.recap_excel');
        Route::resource('mail-registries', \App\Http\Controllers\Admin\MailRegistryController::class)
            ->names('mail_registries')
            ->except(['show']);
        Route::get('/mail-registries/{mailRegistry}/download', [\App\Http\Controllers\Admin\MailRegistryController::class, 'download'])->name('mail_registries.download');
        Route::get('/mail-registries/{mailRegistry}/disposition', [\App\Http\Controllers\Admin\MailRegistryController::class, 'dispositionForm'])->name('mail_registries.disposition');
        Route::post('/mail-registries/{mailRegistry}/disposition', [\App\Http\Controllers\Admin\MailRegistryController::class, 'storeDisposition'])->name('mail_registries.disposition.store');

        // Renbut Approval (For PROGAR)
        Route::resource('renbuts', \App\Http\Controllers\Admin\RenbutApprovalController::class)->only(['index', 'show', 'update']);
        Route::get('/renbuts/{renbut}/download-attachment', [\App\Http\Controllers\Admin\RenbutApprovalController::class, 'downloadAttachment'])->name('renbuts.download_attachment');

        // Upload & Berkas SPJ (Format PDF - Untuk Admin PROGAR & PEKAS)
        Route::resource('spjs', \App\Http\Controllers\Admin\SpjController::class)->except(['show']);
        Route::get('/spjs/{spj}/download', [\App\Http\Controllers\Admin\SpjController::class, 'download'])->name('spjs.download');
        Route::get('/spjs/{spj}/preview', [\App\Http\Controllers\Admin\SpjController::class, 'preview'])->name('spjs.preview');

        // Tong Sampah Admin
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SuperAdminTrashController::class, 'index'])->name('index');
            Route::put('/letters/{id}/restore', [\App\Http\Controllers\Admin\SuperAdminTrashController::class, 'restoreLetter'])->name('letters.restore');
            Route::delete('/letters/{id}', [\App\Http\Controllers\Admin\SuperAdminTrashController::class, 'forceDeleteLetter'])->name('letters.force_delete');
            Route::put('/templates/{id}/restore', [\App\Http\Controllers\Admin\SuperAdminTrashController::class, 'restoreTemplate'])->name('templates.restore');
            Route::delete('/templates/{id}', [\App\Http\Controllers\Admin\SuperAdminTrashController::class, 'forceDeleteTemplate'])->name('templates.force_delete');
            Route::put('/categories/{id}/restore', [\App\Http\Controllers\Admin\SuperAdminTrashController::class, 'restoreCategory'])->name('categories.restore');
            Route::delete('/categories/{id}', [\App\Http\Controllers\Admin\SuperAdminTrashController::class, 'forceDeleteCategory'])->name('categories.force_delete');
        });
    });


/*
|--------------------------------------------------------------------------
| Profile Routes (dari Breeze — bisa diakses semua role yang sudah login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Backward compatibility redirects untuk URL lama
    Route::redirect('/super-admin/profile', '/profile');
    Route::redirect('/admin/profile', '/profile');
    Route::redirect('/staff/profile', '/profile');

    // Notifications (sudah didefinisikan di grup global atas, hapus duplikat di sini)
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Login, Register, Reset Password — dari Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
