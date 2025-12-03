<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\OaiHarvestController;
use App\Http\Controllers\LocIslamicController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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

// Public Routes
Route::get('/', function () {
    return view('welcome');
});
// routes/web.php

// Public repository routes (accessible without admin auth)
Route::group(['middleware' => ['web']], function () {
    Route::get('/repository', [RepositoryController::class, 'index'])->name('repository.index');
    Route::get('/repository/browse', [RepositoryController::class, 'browse'])->name('repository.browse');
    Route::get('/repository/statistics', [RepositoryController::class, 'statistics'])->name('repository.statistics');
    Route::get('/repository/communities/{id}', [RepositoryController::class, 'showCommunity'])->name('repository.community');
    Route::get('/repository/collections/{id}', [RepositoryController::class, 'showCollection'])->name('repository.collection');
    Route::get('/repository/items/{id}', [RepositoryController::class, 'showItem'])->name('items.show');
});

// Admin repository management (keep your existing auth)
Route::group(['middleware' => ['auth', 'web'], 'prefix' => 'admin'], function () {
    Route::get('/repository/settings', [RepositoryController::class, 'settings'])->name('admin.repository.settings');
    Route::put('/repository/settings', [RepositoryController::class, 'updateSettings'])->name('admin.repository.settings.update');
});
// Breeze Authentication Routes
// Add this with your other routes in routes/web.php

// Dashboard Route
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

// Optional: Make dashboard the home page
Route::get('/home', function () {
    return redirect()->route('dashboard');
});

// Or if you want dashboard as the default landing for logged-in users:
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
});

// OAI-PMH Protocol Route
    Route::get('/oai', [App\Http\Controllers\OAIController::class, 'index'])->name('oai-pmh');
    // Simple test route
    Route::get('/oai-test', function() {
        return response('OAI Test Works!', 200);
    });

Route::middleware('auth')->group(function () {
    // Profile Routes (from Breeze) - KEEP THESE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // DCMS Protected Routes
    Route::redirect('/', '/dashboard');

    // Communities - Admin and Manager only
    Route::resource('communities', CommunityController::class)->middleware('role:admin,manager');

    // Collections - Admin and Manager only  
    Route::resource('collections', CollectionController::class)->middleware('role:admin,manager');

    // Categories - Admin and Manager only
    Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit'])->middleware('role:admin,manager');

    // Items - All authenticated users can access
    Route::resource('items', ItemController::class);
    Route::post('/items/{item}/publish', [ItemController::class, 'publish'])->name('items.publish');
    Route::post('/items/{item}/submit-review', [ItemController::class, 'submitForReview'])->name('items.submit-review');
    Route::get('/bitstreams/{bitstream}/download', [ItemController::class, 'downloadBitstream'])->name('bitstreams.download');
    Route::delete('/bitstreams/{bitstream}', [ItemController::class, 'deleteBitstream'])->name('bitstreams.destroy');

    Route::put('items/{item}/status', [ItemController::class, 'status'])->name('items.status');
    Route::patch('items/{item}/approve', [ItemController::class, 'approve'])->name('items.approve');
    Route::patch('items/{item}/archive', [ItemController::class, 'archive'])->name('items.archive');
    Route::patch('items/{item}/unarchive', [ItemController::class, 'unarchive'])->name('items.unarchive');
    Route::patch('items/{item}/feature', [ItemController::class, 'feature'])->name('items.feature');
    // Workflow routes
    Route::get('/items/{item}/workflow', [WorkflowController::class, 'show'])->name('workflow.show');
    Route::post('/items/{item}/submit', [WorkflowController::class, 'submit'])->name('workflow.submit');
    // In routes/web.php
    Route::post('/items/{item}/workflow/history', [WorkflowController::class, 'addHistory'])->name('workflow.add-history');

    // Workflow review routes
    Route::post('/items/{item}/technical-review', [WorkflowController::class, 'technicalReview'])->name('workflow.technical-review');
    Route::post('/items/{item}/content-review', [WorkflowController::class, 'contentReview'])->name('workflow.content-review');

    // Enhanced workflow routes
    Route::post('/items/{item}/final-approve', [WorkflowController::class, 'finalApprove'])->name('workflow.final-approve');
    Route::post('/items/{item}/quick-approve', [WorkflowController::class, 'quickApprove'])->name('workflow.quick-approve');

    Route::get('items/{item}/download', [ItemController::class, 'downloadFile'])->name('items.download');
    Route::post('items/bulk-action', [ItemController::class, 'bulkAction'])->name('items.bulk-action');
    
    // routes/web.php

    // Item version routes
    Route::prefix('items/{item}')->group(function () {
        Route::get('/versions', [ItemController::class, 'versions'])->name('items.versions');
        Route::get('/versions/compare/{version1}/{version2?}', [ItemController::class, 'compareVersions'])->name('items.versions.compare');
        Route::post('/versions/{version}/restore', [ItemController::class, 'restoreVersion'])->name('items.versions.restore');
        Route::delete('/versions/{version}', [ItemController::class, 'destroyVersion'])->name('items.versions.destroy');
    });

    // Version-specific routes
    Route::get('/item-versions/{version}/download', [ItemController::class, 'downloadVersion'])->name('items.versions.download');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/usage', [ReportController::class, 'usageStats'])->name('reports.usage');
    Route::get('/reports/collections', [ReportController::class, 'collectionReport'])->name('reports.collections');
    Route::get('/reports/export', [ReportController::class, 'exportReport'])->name('reports.export');

    // Advanced Search
    Route::get('/search', [ItemController::class, 'search'])->name('items.search');

    // User Management Routes - ADMIN ONLY
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit'); // ← ADD THIS LINE
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // routes/web.php

    // Public routes
    Route::get('/repository', [RepositoryController::class, 'index'])->name('repository.index');
    Route::get('/repository/browse', [RepositoryController::class, 'browse'])->name('repository.browse');
    Route::get('/repository/statistics', [RepositoryController::class, 'statistics'])->name('repository.statistics');
    Route::get('/repository/communities/{id}', [RepositoryController::class, 'showCommunity'])->name('repository.community');
    Route::get('/repository/collections/{id}', [RepositoryController::class, 'showCollection'])->name('repository.collection');
    Route::get('/repository/items/{id}', [RepositoryController::class, 'showItem'])->name('items.show');

    // Admin routes
    Route::get('/admin/repository/settings', [RepositoryController::class, 'settings'])->name('admin.repository.settings');
    Route::put('/admin/repository/settings', [RepositoryController::class, 'updateSettings'])->name('admin.repository.settings.update');


    // routes/web.php
    // Batch Operations Routes
    // Batch Operations Routes
    Route::prefix('batch')->name('batch.')->group(function () {
        // Dashboard
        Route::get('/', [BatchController::class, 'index'])->name('index');
        
        // Export routes
        Route::get('/export', [BatchController::class, 'exportForm'])->name('export.form');
        Route::post('/export', [BatchController::class, 'export'])->name('export.process');
        
        // Import routes  
        Route::get('/import', [BatchController::class, 'importForm'])->name('import.form');
        Route::post('/import', [BatchController::class, 'import'])->name('import.process');
        
        // Bulk update routes
        Route::get('/bulk-update', [BatchController::class, 'bulkUpdateForm'])->name('bulk-update.form');
        Route::post('/bulk-update', [BatchController::class, 'bulkUpdate'])->name('bulk-update.process');
        
        // AJAX routes
        Route::get('/get-items', [BatchController::class, 'getItems'])->name('get-items');
        
        // 🔥 QUICK ACTION ROUTES - ADD THESE
        Route::post('/quick-publish/{collectionId}', [BatchController::class, 'quickPublishCollection'])->name('quick.publish');
        Route::post('/quick-unpublish/{collectionId}', [BatchController::class, 'quickUnpublishCollection'])->name('quick.unpublish');
        Route::post('/quick-approve', [BatchController::class, 'quickApproveAll'])->name('quick.approve');
        Route::post('/quick-stats', [BatchController::class, 'quickStatsUpdate'])->name('quick.stats');
    });

    Route::get('/debug-import', function(Request $request) {
        return view('debug-import');
    });

    Route::post('/debug-import', function(Request $request) {
        dd([
            'has_file' => $request->hasFile('file'),
            'file_object' => $request->file('file'),
            'file_name' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'no file',
            'file_size' => $request->file('file') ? $request->file('file')->getSize() : 0,
            'file_path' => $request->file('file') ? $request->file('file')->getPathname() : 'no path',
            'all_request_data' => $request->all(),
            'files_data' => $request->allFiles(),
        ]);
    });

    Route::get('/php-info', function() {
        return response()->json([
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
        ]);
    });

    // routes/web.php
    Route::middleware(['auth'])->prefix('oai-harvest')->name('oai.harvest.')->group(function () {
        Route::get('/', [OaiHarvestController::class, 'index'])->name('index');
        Route::get('/history', [OaiHarvestController::class, 'history'])->name('history');
        Route::get('/harvest/{id}', [OaiHarvestController::class, 'showHarvest'])->name('show');
        Route::get('/resume/{id}', [OaiHarvestController::class, 'resume'])->name('resume');
        
        Route::post('/test-connection', [OaiHarvestController::class, 'testConnection'])->name('test-connection');
        Route::post('/get-sets', [OaiHarvestController::class, 'getSets'])->name('get-sets');
        Route::post('/preview', [OaiHarvestController::class, 'preview'])->name('preview');
        Route::post('/harvest', [OaiHarvestController::class, 'harvest'])->name('harvest');
        Route::get('/search', [OaiHarvestController::class, 'search'])->name('search');
        Route::post('/search', [OaiHarvestController::class, 'performSearch'])->name('search-perform');
        Route::post('/select-records', [OaiHarvestController::class, 'selectRecords'])->name('select-records');
        Route::post('/harvest-selected', [OaiHarvestController::class, 'harvestSelected'])->name('harvest-selected');
    });

    // routes/web.php
    Route::middleware(['auth'])->prefix('loc')->name('loc.')->group(function () {
        Route::get('/islamic', [LocIslamicController::class, 'index'])->name('islamic.index');
        Route::post('/islamic/test-connection', [LocIslamicController::class, 'testConnection'])->name('islamic.test-connection');
        Route::post('/islamic/search', [LocIslamicController::class, 'search'])->name('islamic.search');
        Route::get('/islamic/preview/{collection?}', [LocIslamicController::class, 'preview'])->name('islamic.preview');
        Route::post('/islamic/import', [LocIslamicController::class, 'import'])->name('islamic.import');
        Route::post('/islamic/sets', [LocIslamicController::class, 'getSets'])->name('islamic.sets');
        Route::post('/islamic/preview-selected', [LocIslamicController::class, 'previewSelected'])->name('islamic.preview-selected');
        Route::get('/harvest/{id}', [LocIslamicController::class, 'show'])->name('harvest.show');
        Route::get('/islamic/quick/{type}', [LocIslamicController::class, 'quickSearch'])->name('loc.islamic.quick-search');
    });

    // System Management Routes - ADMIN ONLY
    // Enhanced system routes
    Route::prefix('system')->name('system.')->middleware('auth')->group(function () {
        Route::get('/settings', [SystemController::class, 'settings'])->name('settings');
        Route::put('/settings', [SystemController::class, 'updateSettings'])->name('settings.update');
        Route::get('/logs', [SystemController::class, 'logs'])->name('logs');
        Route::post('/clear-logs', [SystemController::class, 'clearLogs'])->name('clear-logs');
        Route::post('/clear-cache', [SystemController::class, 'clearCache'])->name('clear-cache');
        Route::get('/info', [SystemController::class, 'systemInfo'])->name('info');
        Route::get('/health', [SystemController::class, 'health'])->name('health');
        Route::post('/backup', [SystemController::class, 'backup'])->name('backup');
    });

    // Test route for workflow status component - REMOVE IN PRODUCTION
    Route::get('/test-workflow-status', function () {
        // Get or create a test user
        $user = \App\Models\User::first();
        if (!$user) {
            $user = \App\Models\User::create([
                'name' => 'Demo User',
                'email' => 'demo@dcms.test',
                'password' => bcrypt('password'),
                'role' => 'user'
            ]);
        }

        // Get or create test items in different states
        $items = \App\Models\Item::all();
        
        if ($items->isEmpty()) {
            // Create items in different workflow states
            $states = ['draft', 'submitted', 'under_review', 'approved', 'published', 'rejected'];
            
            foreach ($states as $state) {
                \App\Models\Item::create([
                    'title' => ucfirst($state) . ' Item Demo',
                    'description' => 'This is a ' . $state . ' item for workflow demonstration',
                    'content' => 'This content shows how the ' . $state . ' state looks in the workflow system.',
                    'category' => 'demo',
                    'workflow_state' => $state,
                    'submitter_id' => $user->id,
                    'submitted_at' => in_array($state, ['submitted', 'under_review', 'approved', 'published', 'rejected']) ? now() : null,
                    'published_at' => $state === 'published' ? now() : null,
                    'is_published' => $state === 'published',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            $items = \App\Models\Item::all();
        }

        // Let user select which state to view
        return view('test-workflow-demo', [
            'items' => $items,
            'currentItem' => $items->first()
        ]);
    })->name('test.workflow.status');

    // Route to show specific item state
    Route::get('/test-workflow-status/{item}', function (\App\Models\Item $item) {
        $items = \App\Models\Item::all();
        return view('test-workflow-demo', [
            'items' => $items,
            'currentItem' => $item
        ]);
    })->name('test.workflow.status.item');
});

Route::get('/test-jquery', function() {
    return view('test-jquery');
});
// CSS Route (keep public for styling)
Route::get('/css/custom.css', function () {
    $cssPath = resource_path('css/custom.css');
    
    if (!file_exists($cssPath)) {
        abort(404);
    }
    
    $content = file_get_contents($cssPath);
    
    return response($content, 200, [
        'Content-Type' => 'text/css',
    ]);
});

// routes/web.php
Route::get('/check-upload-config', function() {
    return [
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'max_file_uploads' => ini_get('max_file_uploads'),
        'upload_tmp_dir' => ini_get('upload_tmp_dir'),
        'temp_dir_exists' => is_dir(ini_get('upload_tmp_dir')),
        'temp_dir_writable' => is_writable(ini_get('upload_tmp_dir')),
        'storage_path' => storage_path('app/public'),
        'storage_writable' => is_writable(storage_path('app/public')),
    ];
});
// Breeze Auth Routes (included automatically, but we can see them here)
// Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
// Route::post('/register', [RegisteredUserController::class, 'store']);
// Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
// Route::post('/login', [AuthenticatedSessionController::class, 'store']);
// Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
// Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
// Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
// Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
// Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
// Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');
// Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
// Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
// Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
// Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store']);

require __DIR__.'/auth.php';