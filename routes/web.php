<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{
    RegisteredUserController,
    SessionsController,
    GuestLoginController,
    LogoutController
};
use App\Http\Controllers\Admin\{
    DashboardController as AdminDashboardController,
    MaterialController as AdminMaterialController,
    AdminStudentController,
    QuestionController as AdminQuestionController,
    AdminUserController,
    PendingApprovalController,
    LogoutController as AdminLogoutController,
    UeqSurveyController,
    QuestionBankController,
    UeqSurveyController as AdminUeqSurveyController
};
use App\Http\Controllers\Mahasiswa\{
    DashboardController as MahasiswaDashboardController,
    MaterialController as MahasiswaMaterialController,
    ProfileController as MahasiswaProfileController,
    QuestionController as MahasiswaQuestionController,
    MahasiswaController,
    MaterialQuestionController,
    UeqSurveyController as MahasiswaUeqSurveyController
};
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to login or materials page based on authentication
Route::get('/login', function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        if ($user->role_id <= 2) {
            return redirect()->route('admin.dashboard');
        } else if ($user->role_id == 3) {
            return redirect()->route('mahasiswa.dashboard');
        } else {
            return redirect()->route('mahasiswa.materials.index');
        }
    }
    
    // return redirect()->route('login');
// })->name('mahasiswa.materials.index');
});

// Guest Routes (Unauthenticated)
Route::middleware('guest')->group(function () {
    // Authentication
    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/login', [SessionsController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    
    // Guest login
    Route::get('/guest-login', [GuestLoginController::class, 'login'])->name('guest.login');
});

// Logout routes (for all authenticated users)
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
Route::post('/mahasiswa/logout', [LogoutController::class, 'logout'])->name('mahasiswa.logout');

// Verification route
Route::get('/verify', [SessionsController::class, 'verify'])->name('verify');

Route::get('/', [MahasiswaMaterialController::class, 'index'])->name('mahasiswa.materials.index');
Route::get('materials/{material}', [MahasiswaMaterialController::class, 'show'])->name('mahasiswa.materials.show');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Pending Approval Route - accessible by any authenticated user
    Route::get('admin/pending-approval', [PendingApprovalController::class, 'index'])
        ->name('admin.pending-approval');
        
    // Admin Routes (role 1 = superadmin, role 2 = admin)
    Route::middleware(['role:1|2', 'admin.approved'])->name('admin.')->prefix('admin')->group(function () {
        // Dashboard
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Materials & Questions
        Route::resource('materials', AdminMaterialController::class);
        Route::resource('questions', AdminQuestionController::class);
        Route::resource('materials.questions', AdminQuestionController::class)->except(['show']);
        
        // Students management
        Route::controller(AdminStudentController::class)->group(function () {
            Route::get('students', 'index')->name('students.index');
            Route::get('students/{student}/progress', 'progress')->name('students.progress');
            Route::delete('students/{student}', 'destroy')->name('students.destroy');
            Route::get('students/import', 'showImportForm')->name('students.import');
            Route::post('students/import', 'processImport')->name('students.process-import');
            Route::get('students/download-template', 'downloadTemplate')->name('students.download-template');
        });

        // Admin management routes (only for superadmin - role 1)
        Route::middleware(['superadmin'])->group(function () {
            // Admin approval
            Route::get('/pending-admins', [AdminUserController::class, 'pendingAdmins'])->name('pending-admins');
            Route::post('/users/{user}/approve', [AdminUserController::class, 'approveAdmin'])->name('users.approve');
            Route::post('/users/{user}/reject', [AdminUserController::class, 'rejectAdmin'])->name('users.reject');
            
            // User import
            Route::get('/users/import', [AdminUserController::class, 'showImportForm'])->name('users.import');
            Route::post('/users/import', [AdminUserController::class, 'processImport'])->name('users.process-import');
            Route::get('/users/download-template', [AdminUserController::class, 'downloadTemplate'])->name('users.download-template');
            
            // User management
            Route::resource('users', AdminUserController::class)->except(['show']);
        });

        // Question Banks routes
        Route::resource('question-banks', QuestionBankController::class);
        Route::get('question-banks/{questionBank}/manage-questions', [QuestionBankController::class, 'manageQuestions'])
            ->name('question-banks.manage-questions');
        Route::post('question-banks/{questionBank}/add-question/{question}', [QuestionBankController::class, 'addQuestion'])
            ->name('question-banks.add-question');
        Route::delete('question-banks/{questionBank}/remove-question/{question}', [QuestionBankController::class, 'removeQuestion'])
            ->name('question-banks.remove-question');
        Route::get('question-banks/{questionBank}/configure', [QuestionBankController::class, 'configureBank'])
            ->name('question-banks.configure');
        Route::post('question-banks/{questionBank}/configure', [QuestionBankController::class, 'storeConfig'])
            ->name('question-banks.store-config');
        Route::delete('question-bank-configs/{config}', [QuestionBankController::class, 'deleteConfig'])
            ->name('question-bank-configs.delete');

        // Media routes
        Route::get('/media/delete/{id}', [AdminMaterialController::class, 'deleteMedia'])
            ->name('media.delete');
    });

    // Mahasiswa & Guest Routes (role 3 = mahasiswa, role 4 = guest)
    Route::middleware(['role:3|4'])->name('mahasiswa.')->prefix('mahasiswa')->group(function () {
        // Dashboard (only for mahasiswa)
        Route::middleware(['role:3'])->group(function () {
            Route::get('dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
            Route::get('dashboard/in-progress', [MahasiswaDashboardController::class, 'inProgress'])->name('dashboard.in-progress');
            Route::get('dashboard/completed', [MahasiswaDashboardController::class, 'complete'])->name('dashboard.completed');
            
            // Profile
            Route::get('profile', [MahasiswaProfileController::class, 'show'])->name('profile');
            Route::put('profile', [MahasiswaProfileController::class, 'update'])->name('profile.update');
            
            // UEQ Survey routes
            Route::get('/ueq-survey', [MahasiswaUeqSurveyController::class, 'create'])->name('ueq.create');
            Route::post('/ueq-survey', [MahasiswaUeqSurveyController::class, 'store'])->name('ueq.store');
            Route::get('/ueq-survey/thankyou', [MahasiswaUeqSurveyController::class, 'thankyou'])->name('ueq.thankyou');
        });
        
        // Materials (for both mahasiswa and guest)
        // Route::get('materials', [MahasiswaMaterialController::class, 'index'])->name('materials.index');

        // Questions index route (must come before the materials/{material} route)
        Route::get('materials/questions', [MaterialQuestionController::class, 'index'])
            ->name('materials.questions.index')
            ->withoutMiddleware('auth');

        // Material show route
        // Route::get('materials/{material}', [MahasiswaMaterialController::class, 'show'])->name('materials.show');

        // Material questions review route (must come before the questions show route)
        Route::get('materials/{material}/questions/review', [MaterialQuestionController::class, 'review'])
            ->name('materials.questions.review');

        // Material questions show route
        Route::get('materials/{material}/questions', [MaterialQuestionController::class, 'show'])
            ->name('materials.questions.show');

        
        // Reset (conditional based on role)
        Route::post('materials/{material}/reset', function($material) {
            if (auth()->user()->role_id == 3) {
                $controller = app()->make(App\Http\Controllers\Mahasiswa\MaterialController::class);
                return $controller->reset($material);
            } else {
                $controller = app()->make(App\Http\Controllers\Mahasiswa\MaterialController::class);
                return $controller->guestReset($material);
            }
        })->name('materials.reset');
        
        // Questions
        Route::post('/questions/check-answer', [MahasiswaQuestionController::class, 'checkAnswer'])
            ->name('questions.check-answer');

        // Mahasiswa-specific routes
        Route::get('/leaderboard', [MahasiswaController::class, 'leaderboard'])->name('leaderboard');

        // Questions routes
        Route::prefix('materials/{material}/questions')->name('materials.questions.')->group(function () {
            Route::get('/', [MaterialQuestionController::class, 'show'])->name('show');
            Route::post('/{question}/check', [MaterialQuestionController::class, 'checkAnswer'])->name('check');
            Route::get('/{question}/attempts', [MaterialQuestionController::class, 'getAttempts'])->name('attempts');
            Route::get('/levels', [MaterialQuestionController::class, 'showLevels'])->name('levels');
        });
    });

    // Admin logout route - keep this one
    Route::post('/admin/logout', [AdminLogoutController::class, 'logout'])
        ->name('admin.logout')
        ->middleware('auth'); // Only require authentication
});
// ✅ TAMU bisa akses materi
Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('materials', [MahasiswaMaterialController::class, 'index'])->name('materials.index');
    Route::get('materials/{material}', [MahasiswaMaterialController::class, 'show'])->name('materials.show');
    
    // Tambahan untuk latihan soal yang bisa diakses tamu
    Route::get('materials/questions', [MaterialQuestionController::class, 'index'])->name('materials.questions.index')
        ->withoutMiddleware('auth');
    
    // PERBAIKAN: Tambahkan withoutMiddleware('auth') pada semua route soal
    Route::get('materials/{material}/questions', [MaterialQuestionController::class, 'show'])
        ->name('materials.questions.show')
        ->withoutMiddleware('auth');
    
    Route::get('materials/{material}/questions/levels', [MaterialQuestionController::class, 'showLevels'])
        ->name('materials.questions.levels')
        ->withoutMiddleware('auth');
    
    Route::get('materials/{material}/questions/review', [MaterialQuestionController::class, 'review'])
        ->name('materials.questions.review')
        ->withoutMiddleware('auth');
});
// Tambahkan route baru yang dapat diakses tanpa middleware
Route::post('/questions/check-answer', [MahasiswaQuestionController::class, 'checkAnswer'])
    ->name('questions.check-answer')
    ->withoutMiddleware('auth');

// UEQ Survey routes for mahasiswa - tambahkan middleware auth
Route::prefix('mahasiswa')->name('mahasiswa.')->middleware(['auth'])->group(function () {
    Route::get('/ueq-survey', [MahasiswaUeqSurveyController::class, 'create'])->name('ueq.create');
    Route::post('/ueq-survey', [MahasiswaUeqSurveyController::class, 'store'])->name('ueq.store');
    Route::get('/ueq-survey/thankyou', [MahasiswaUeqSurveyController::class, 'thankyou'])->name('ueq.thankyou');
});

// Fallback route for 404 errors
Route::fallback(function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        if ($user->role_id <= 2) {
            return redirect()->route('admin.dashboard');
        } else if ($user->role_id == 3) {
            return redirect()->route('mahasiswa.dashboard');
        } else {
            // Guest users (role_id = 4)
            return redirect()->route('mahasiswa.materials.index')
                ->with('info', 'Halaman yang Anda cari tidak tersedia untuk akun tamu.');
        }
    }
    
    return redirect()->route('login');
});

// Add this with your other guest routes
Route::post('/guest-logout', [GuestLoginController::class, 'logout'])->name('guest.logout');


Route::get('/mahasiswa/materials/{material}/questions/{question}/attempts', [MaterialQuestionController::class, 'getAttempts'])
    ->name('mahasiswa.materials.questions.attempts');

// Mahasiswa routes
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    // Remove these lines
    // Route::get('/ueq-survey', [MahasiswaUeqSurveyController::class, 'create'])->name('mahasiswa.ueq.create');
    // Route::post('/ueq-survey', [MahasiswaUeqSurveyController::class, 'store'])->name('mahasiswa.ueq.store');
});

// Admin UEQ Survey routes
Route::middleware(['auth', 'role:1|2'])->group(function () {
    Route::get('/admin/ueq-survey', [UeqSurveyController::class, 'index'])->name('admin.ueq.index');
    Route::get('/admin/ueq-survey/export', [UeqSurveyController::class, 'export'])->name('admin.ueq.export');
    Route::get('/admin/ueq/{user}/detail', [UeqSurveyController::class, 'detail'])->name('admin.ueq.detail');
});

