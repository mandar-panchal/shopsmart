<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppsController;
use App\Http\Controllers\UserInterfaceController;
use App\Http\Controllers\CardsController;
use App\Http\Controllers\ComponentsController;
use App\Http\Controllers\ExtensionController;
use App\Http\Controllers\PageLayoutController;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\MiscellaneousController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ChartsController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\authorization\PermissionController;
use App\Http\Controllers\authorization\RolesController;
use App\Http\Controllers\Notify\NotificationController;
use Laravel\Sanctum\Sanctum;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\TaskProductController;
use App\Http\Controllers\CustomerAuthController;

use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\CustomerProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Main Page Route
// Route::middleware(['auth'])->group(function () {
//     Route::get('/', [DashboardController::class, 'dashboardEcommerce'])->name('dashboard');
// });

Route::get('/test', function(){
    return view('test/test');
});


Route::get('/', [DashboardController::class, 'dashboardEcommerce'])->middleware(['auth'])->name('dashboard-ecommerce');

/* Theme Switcher */
Route::post('/update-theme-mode', [UserController::class, 'updateThemeMode'])->middleware(['auth'])->name('update.theme.mode');
Route::get('/get-current-theme', [UserController::class, 'getCurrentTheme'])->middleware(['auth'])->name('get.current.theme');
/* Theme Switcher */

/* Notification Controller */
Route::get('/markasreadall', [NotificationController::class, 'markAsReadAll'])->middleware(['auth']);
Route::get('/markasread/{notificationId}', [NotificationController::class, 'markAsRead'])->middleware(['auth']);
/* Notification Controller  */   

/* Route User */
Route::post('/users', [UserController::class, 'getUsers'])->middleware(['auth'])->name('user.getUsers');
Route::post('/users/create', [UserController::class, 'createuser'])->middleware(['auth'])->name('user.create');
Route::post('/update-role/{user}', [UserController::class, 'updateRole'])->middleware(['auth'])->name('update.role');
/* Route User */

/* Route Dashboards */
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth']], function () {
    Route::get('analytics', [DashboardController::class, 'dashboardAnalytics'])->name('dashboard-analytics');
    Route::get('ecommerce', [DashboardController::class, 'dashboardEcommerce'])->name('dashboard-ecommerce');
});
/* Route Dashboards */

/* Route Authorisation */

Route::group(['prefix' => 'authorization'], function () {
    Route::get('roles', [RolesController::class, 'index'])->middleware(['auth', 'can:role_access'])->name('authorization-roles');
    Route::post('roles/create', [RolesController::class,'create'])->middleware(['auth', 'can:role_create'])->name('authorization-roles-create');
    Route::post('roles/update', [RolesController::class,'update'])->middleware(['auth', 'can:role_update'])->name('authorization-roles-update');
    Route::get('get-roles', [RolesController::class, 'getRoles'])->middleware(['auth', 'can:role_access']);
    Route::get('roles/{id}', [RolesController::class, 'getRoleDetails'])->middleware(['auth', 'can:role_access']);
    Route::post('roles/update/{id}', [RolesController::class, 'edit'])->middleware(['auth', 'can:role_access'])->name('roles.edit');
 
    Route::get('permission', [PermissionController::class, 'index'])->middleware(['auth', 'can:permission_access'])->name('authorization-permission');
    Route::post('permissions/create', [PermissionController::class,'create'])->middleware(['auth', 'can:permission_create'])->name('authorization-permission-create');
    // Route::get('permissions/{permissionId}/edit', [PermissionController::class, 'edit']);
    // Route::post('permissions/{permissionId}/update', [PermissionController::class, 'update']);
    // Route::delete('permissions/{permissionId}/delete', [PermissionController::class, 'delete']);
    Route::get('permission/getdata', [PermissionController::class,'getdata'])->middleware(['auth', 'can:permission_access'])->name('authorization-permission-getdata');
    Route::get('permission/getpermissions', [PermissionController::class,'getPermissions'])->middleware(['auth', 'can:permission_access'])->name('authorization-permission-getdata');
    Route::post('permission/update', [PermissionController::class,'update'])->name('authorization-permission-update');
    Route::post('permission/delete', [PermissionController::class,'delete'])->name('authorization-permission-delete');  
    // Route::resource('permissions', PermissionController::class)->only(['index', 'store', 'update', 'destroy']);  
});
// Route::middleware(['auth'])->group(function () {
//     Route::get('/executive/processes', [ExecutiveProcessController::class, 'index'])->name('executive.processes');
//     Route::post('/executive/upload-document', [ExecutiveProcessController::class, 'uploadDocument'])->name('task.upload');
//     Route::get('/executive/task/{taskId}', [ExecutiveProcessController::class, 'getTaskDetails'])->name('task.details');
//     Route::get('/executive/project/{projectId}/tasks', [ExecutiveProcessController::class, 'getProjectTasks'])->name('project.tasks');
//     Route::get('/executive/document/{taskId}/download', [ExecutiveProcessController::class, 'downloadDocument'])->name('document.download');
//     Route::put('/task/update-document', [ExecutiveProcessController::class, 'updateDocument'])->name('task.update-document');
// });

Route::group(['prefix' => 'schedule', 'middleware' => ['auth']], function () {
    Route::get('index', [ScheduleController::class, 'index'])->middleware(['auth', 'can:scheduled_calls'])->name('schedule-index');
});
Route::get('/api/tasks/{id}', [EmailController::class, 'getTaskDetails']);
Route::get('/api/users/executives', [EmailController::class, 'getExecutives']);
Route::get('/api/projects/{id}/contacts', [EmailController::class, 'getProjectContacts']);
Route::post('/api/send-mail', [EmailController::class, 'sendMail']);
/* Route Authorisation */
/* Route Apps */
Route::group(['prefix' => 'app', 'middleware' => ['auth']], function () {
    Route::get('email', [AppsController::class, 'emailApp'])->name('app-email');
    Route::get('chat', [AppsController::class, 'chatApp'])->name('app-chat');
    Route::get('todo', [AppsController::class, 'todoApp'])->name('app-todo');
    Route::get('calendar', [AppsController::class, 'calendarApp'])->name('app-calendar');
    Route::get('kanban', [AppsController::class, 'kanbanApp'])->name('app-kanban');
    Route::get('invoice/list', [AppsController::class, 'invoice_list'])->name('app-invoice-list');
    Route::get('invoice/preview', [AppsController::class, 'invoice_preview'])->name('app-invoice-preview');
    Route::get('invoice/edit', [AppsController::class, 'invoice_edit'])->name('app-invoice-edit');
    Route::get('invoice/add', [AppsController::class, 'invoice_add'])->name('app-invoice-add');
    Route::get('invoice/print', [AppsController::class, 'invoice_print'])->name('app-invoice-print');
    Route::get('ecommerce/shop', [AppsController::class, 'ecommerce_shop'])->name('app-ecommerce-shop');
    Route::get('ecommerce/details', [AppsController::class, 'ecommerce_details'])->name('app-ecommerce-details');
    Route::get('ecommerce/wishlist', [AppsController::class, 'ecommerce_wishlist'])->name('app-ecommerce-wishlist');
    Route::get('ecommerce/checkout', [AppsController::class, 'ecommerce_checkout'])->name('app-ecommerce-checkout');
    Route::get('file-manager', [AppsController::class, 'file_manager'])->name('app-file-manager');
    Route::get('access-roles', [AppsController::class, 'access_roles'])->name('app-access-roles');
    Route::get('access-permission', [AppsController::class, 'access_permission'])->name('app-access-permission');
    Route::get('user/list', [AppsController::class, 'user_list'])->name('app-user-list');
    Route::get('user/view/account', [AppsController::class, 'user_view_account'])->name('app-user-view-account');
    Route::get('user/view/security', [AppsController::class, 'user_view_security'])->name('app-user-view-security');
    Route::get('user/view/billing', [AppsController::class, 'user_view_billing'])->name('app-user-view-billing');
    Route::get('user/view/notifications', [AppsController::class, 'user_view_notifications'])->name('app-user-view-notifications');
    Route::get('user/view/connections', [AppsController::class, 'user_view_connections'])->name('app-user-view-connections');
});
/* Route Apps */

/* Route UI */
Route::group(['prefix' => 'ui', 'middleware' => ['auth']], function () {
    Route::get('typography', [UserInterfaceController::class, 'typography'])->name('ui-typography');
});
/* Route UI */

/* Route Icons */
Route::group(['prefix' => 'icons', 'middleware' => ['auth']], function () {
    Route::get('feather', [UserInterfaceController::class, 'icons_feather'])->name('icons-feather');
});
/* Route Icons */

/* Route Cards */
Route::group(['prefix' => 'card', 'middleware' => ['auth']], function () {
    Route::get('basic', [CardsController::class, 'card_basic'])->name('card-basic');
    Route::get('advance', [CardsController::class, 'card_advance'])->name('card-advance');
    Route::get('statistics', [CardsController::class, 'card_statistics'])->name('card-statistics');
    Route::get('analytics', [CardsController::class, 'card_analytics'])->name('card-analytics');
    Route::get('actions', [CardsController::class, 'card_actions'])->name('card-actions');
});
/* Route Cards */

/* Route Components */
Route::group(['prefix' => 'component', 'middleware' => ['auth']], function () {
    Route::get('accordion', [ComponentsController::class, 'accordion'])->name('component-accordion');
    Route::get('alert', [ComponentsController::class, 'alert'])->name('component-alert');
    Route::get('avatar', [ComponentsController::class, 'avatar'])->name('component-avatar');
    Route::get('badges', [ComponentsController::class, 'badges'])->name('component-badges');
    Route::get('breadcrumbs', [ComponentsController::class, 'breadcrumbs'])->name('component-breadcrumbs');
    Route::get('buttons', [ComponentsController::class, 'buttons'])->name('component-buttons');
    Route::get('carousel', [ComponentsController::class, 'carousel'])->name('component-carousel');
    Route::get('collapse', [ComponentsController::class, 'collapse'])->name('component-collapse');
    Route::get('divider', [ComponentsController::class, 'divider'])->name('component-divider');
    Route::get('dropdowns', [ComponentsController::class, 'dropdowns'])->name('component-dropdowns');
    Route::get('list-group', [ComponentsController::class, 'list_group'])->name('component-list-group');
    Route::get('modals', [ComponentsController::class, 'modals'])->name('component-modals');
    Route::get('pagination', [ComponentsController::class, 'pagination'])->name('component-pagination');
    Route::get('navs', [ComponentsController::class, 'navs'])->name('component-navs');
    Route::get('offcanvas', [ComponentsController::class, 'offcanvas'])->name('component-offcanvas');
    Route::get('tabs', [ComponentsController::class, 'tabs'])->name('component-tabs');
    Route::get('timeline', [ComponentsController::class, 'timeline'])->name('component-timeline');
    Route::get('pills', [ComponentsController::class, 'pills'])->name('component-pills');
    Route::get('tooltips', [ComponentsController::class, 'tooltips'])->name('component-tooltips');
    Route::get('popovers', [ComponentsController::class, 'popovers'])->name('component-popovers');
    Route::get('pill-badges', [ComponentsController::class, 'pill_badges'])->name('component-pill-badges');
    Route::get('progress', [ComponentsController::class, 'progress'])->name('component-progress');
    Route::get('spinner', [ComponentsController::class, 'spinner'])->name('component-spinner');
    Route::get('toast', [ComponentsController::class, 'toast'])->name('component-bs-toast');
});
/* Route Components */

/* Route Extensions */
Route::group(['prefix' => 'ext-component', 'middleware' => ['auth']], function () {
    Route::get('sweet-alerts', [ExtensionController::class, 'sweet_alert'])->name('ext-component-sweet-alerts');
    Route::get('block-ui', [ExtensionController::class, 'block_ui'])->name('ext-component-block-ui');
    Route::get('toastr', [ExtensionController::class, 'toastr'])->name('ext-component-toastr');
    Route::get('sliders', [ExtensionController::class, 'sliders'])->name('ext-component-sliders');
    Route::get('drag-drop', [ExtensionController::class, 'drag_drop'])->name('ext-component-drag-drop');
    Route::get('tour', [ExtensionController::class, 'tour'])->name('ext-component-tour');
    Route::get('clipboard', [ExtensionController::class, 'clipboard'])->name('ext-component-clipboard');
    Route::get('plyr', [ExtensionController::class, 'plyr'])->name('ext-component-plyr');
    Route::get('context-menu', [ExtensionController::class, 'context_menu'])->name('ext-component-context-menu');
    Route::get('swiper', [ExtensionController::class, 'swiper'])->name('ext-component-swiper');
    Route::get('tree', [ExtensionController::class, 'tree'])->name('ext-component-tree');
    Route::get('ratings', [ExtensionController::class, 'ratings'])->name('ext-component-ratings');
    Route::get('locale', [ExtensionController::class, 'locale'])->name('ext-component-locale');
});
/* Route Extensions */

/* Route Page Layouts */
Route::group(['prefix' => 'page-layouts', 'middleware' => ['auth']], function () {
    Route::get('collapsed-menu', [PageLayoutController::class, 'layout_collapsed_menu'])->name('layout-collapsed-menu');
    Route::get('full', [PageLayoutController::class, 'layout_full'])->name('layout-full');
    Route::get('without-menu', [PageLayoutController::class, 'layout_without_menu'])->name('layout-without-menu');
    Route::get('empty', [PageLayoutController::class, 'layout_empty'])->name('layout-empty');
    Route::get('blank', [PageLayoutController::class, 'layout_blank'])->name('layout-blank');
});
/* Route Page Layouts */

/* Route Forms */
Route::group(['prefix' => 'form', 'middleware' => ['auth']], function () {
    Route::get('input', [FormsController::class, 'input'])->name('form-input');
    Route::get('input-groups', [FormsController::class, 'input_groups'])->name('form-input-groups');
    Route::get('input-mask', [FormsController::class, 'input_mask'])->name('form-input-mask');
    Route::get('textarea', [FormsController::class, 'textarea'])->name('form-textarea');
    Route::get('checkbox', [FormsController::class, 'checkbox'])->name('form-checkbox');
    Route::get('radio', [FormsController::class, 'radio'])->name('form-radio');
    Route::get('custom-options', [FormsController::class, 'custom_options'])->name('form-custom-options');
    Route::get('switch', [FormsController::class, 'switch'])->name('form-switch');
    Route::get('select', [FormsController::class, 'select'])->name('form-select');
    Route::get('number-input', [FormsController::class, 'number_input'])->name('form-number-input');
    Route::get('file-uploader', [FormsController::class, 'file_uploader'])->name('form-file-uploader');
    Route::get('quill-editor', [FormsController::class, 'quill_editor'])->name('form-quill-editor');
    Route::get('date-time-picker', [FormsController::class, 'date_time_picker'])->name('form-date-time-picker');
    Route::get('layout', [FormsController::class, 'layouts'])->name('form-layout');
    Route::get('wizard', [FormsController::class, 'wizard'])->name('form-wizard');
    Route::get('validation', [FormsController::class, 'validation'])->name('form-validation');
    Route::get('repeater', [FormsController::class, 'form_repeater'])->name('form-repeater');
});
/* Route Forms */

/* Route Tables */
Route::group(['prefix' => 'table', 'middleware' => ['auth']], function () {
    Route::get('', [TableController::class, 'table'])->name('table');
    Route::get('datatable/basic', [TableController::class, 'datatable_basic'])->name('datatable-basic');
    Route::get('datatable/advance', [TableController::class, 'datatable_advance'])->name('datatable-advance');
});
/* Route Tables */

/* Route Pages */
Route::group(['prefix' => 'page', 'middleware' => ['auth']], function () {
    Route::get('account-settings-account', [PagesController::class, 'account_settings_account'])->name('page-account-settings-account');
    Route::get('account-settings-security', [PagesController::class, 'account_settings_security'])->name('page-account-settings-security');
    Route::get('account-settings-billing', [PagesController::class, 'account_settings_billing'])->name('page-account-settings-billing');
    Route::get('account-settings-notifications', [PagesController::class, 'account_settings_notifications'])->name('page-account-settings-notifications');
    Route::get('account-settings-connections', [PagesController::class, 'account_settings_connections'])->name('page-account-settings-connections');
    Route::get('profile', [PagesController::class, 'profile'])->name('page-profile');
    Route::get('faq', [PagesController::class, 'faq'])->name('page-faq');
    Route::get('knowledge-base', [PagesController::class, 'knowledge_base'])->name('page-knowledge-base');
    Route::get('knowledge-base/category', [PagesController::class, 'kb_category'])->name('page-knowledge-base');
    Route::get('knowledge-base/category/question', [PagesController::class, 'kb_question'])->name('page-knowledge-base');
    Route::get('pricing', [PagesController::class, 'pricing'])->name('page-pricing');
    Route::get('api-key', [PagesController::class, 'api_key'])->name('page-api-key');
    Route::get('blog/list', [PagesController::class, 'blog_list'])->name('page-blog-list');
    Route::get('blog/detail', [PagesController::class, 'blog_detail'])->name('page-blog-detail');
    Route::get('blog/edit', [PagesController::class, 'blog_edit'])->name('page-blog-edit');

    // Miscellaneous Pages With Page Prefix
    Route::get('coming-soon', [MiscellaneousController::class, 'coming_soon'])->name('misc-coming-soon');
    Route::get('not-authorized', [MiscellaneousController::class, 'not_authorized'])->name('misc-not-authorized');
    Route::get('maintenance', [MiscellaneousController::class, 'maintenance'])->name('misc-maintenance');
    Route::get('license', [PagesController::class, 'license'])->name('page-license');
});

/* Modal Examples */
Route::get('/modal-examples', [PagesController::class, 'modal_examples'])->middleware(['auth'])->name('modal-examples');

/* Route Pages */
Route::get('/error', [MiscellaneousController::class, 'error'])->middleware(['auth'])->name('error');

/* Route Authentication Pages */
Route::group(['prefix' => 'auth', 'middleware' => ['auth']], function () {
    Route::get('login-basic', [AuthenticationController::class, 'login_basic'])->name('auth-login-basic');
    Route::get('login-cover', [AuthenticationController::class, 'login_cover'])->name('auth-login-cover');
    Route::get('register-basic', [AuthenticationController::class, 'register_basic'])->name('auth-register-basic');
    Route::get('register-cover', [AuthenticationController::class, 'register_cover'])->name('auth-register-cover');
    Route::get('forgot-password-basic', [AuthenticationController::class, 'forgot_password_basic'])->name('auth-forgot-password-basic');
    Route::get('forgot-password-cover', [AuthenticationController::class, 'forgot_password_cover'])->name('auth-forgot-password-cover');
    Route::get('reset-password-basic', [AuthenticationController::class, 'reset_password_basic'])->name('auth-reset-password-basic');
    Route::get('reset-password-cover', [AuthenticationController::class, 'reset_password_cover'])->name('auth-reset-password-cover');
    Route::get('verify-email-basic', [AuthenticationController::class, 'verify_email_basic'])->name('auth-verify-email-basic');
    Route::get('verify-email-cover', [AuthenticationController::class, 'verify_email_cover'])->name('auth-verify-email-cover');
    Route::get('two-steps-basic', [AuthenticationController::class, 'two_steps_basic'])->name('auth-two-steps-basic');
    Route::get('two-steps-cover', [AuthenticationController::class, 'two_steps_cover'])->name('auth-two-steps-cover');
    Route::get('register-multisteps', [AuthenticationController::class, 'register_multi_steps'])->name('auth-register-multisteps');
    Route::get('lock-screen', [AuthenticationController::class, 'lock_screen'])->name('auth-lock_screen');
});
/* Route Authentication Pages */

/* Route Charts */
Route::group(['prefix' => 'chart', 'middleware' => ['auth']], function () {
    Route::get('apex', [ChartsController::class, 'apex'])->name('chart-apex');
    Route::get('chartjs', [ChartsController::class, 'chartjs'])->name('chart-chartjs');
    Route::get('echarts', [ChartsController::class, 'echarts'])->name('chart-echarts');
});
/* Route Charts */

// map leaflet
Route::get('/maps/leaflet', [ChartsController::class, 'maps_leaflet'])->middleware(['auth'])->name('map-leaflet');

// locale Route
Route::get('lang/{locale}', [LanguageController::class, 'swap'])->middleware(['auth']);
// Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
// Route::post('/login', [AuthController::class, 'loginUser'])->name('login.post');
// Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// User Management Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users/create', [AuthController::class, 'showCreateForm'])->name('users.create');
    Route::post('/users/create', [AuthController::class, 'createUser'])->name('users.store');
    Route::get('/users', [AuthController::class, 'index'])->name('users.index');
});

// Authorization Routes

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'role:admin'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


Route::middleware(['auth'])->group(function () {
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [TaskProductController::class, 'index'])->name('index');
        Route::get('/fetch', [TaskProductController::class, 'fetch'])->name('fetch');
        Route::post('/store', [TaskProductController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [TaskProductController::class, 'edit'])->name('edit');
        Route::post('/update', [TaskProductController::class, 'update'])->name('update');
        Route::get('/view/{id}', [TaskProductController::class, 'view'])->name('view');
        Route::delete('/delete/{id}', [TaskProductController::class, 'destroy'])->name('destroy');
        Route::post('/toggle-status/{id}', [TaskProductController::class, 'toggleStatus'])->name('toggle-status');
        
    });
});




Route::prefix('customer')->name('customer.')->group(function () {
    
    // Guest Routes
    Route::middleware('customer.guest')->group(function () {
        Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [CustomerAuthController::class, 'register'])->name('register.submit');
        
        Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login/send-otp', [CustomerAuthController::class, 'sendOtp'])->name('send.otp');
        
        Route::get('/verify-otp', [CustomerAuthController::class, 'showVerifyOtpForm'])->name('verify.otp');
        Route::post('/verify-otp', [CustomerAuthController::class, 'verifyOtp'])->name('verify.otp.submit');
        Route::post('/resend-otp', [CustomerAuthController::class, 'resendOtp'])->name('resend.otp');
    });

    // Authenticated Routes
    Route::middleware('customer.auth')->group(function () {
        // Dashboard
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/analytics', [CustomerDashboardController::class, 'getAnalytics'])->name('analytics');
        
        // Products
        Route::get('/products', [CustomerProductController::class, 'index'])->name('products');
        Route::get('/products/fetch', [CustomerProductController::class, 'fetchProducts'])->name('products.fetch');
        Route::get('/products/{id}', [CustomerProductController::class, 'show'])->name('products.show');
        
    // Cart
        Route::get('/cart', [CartController::class, 'index'])->name('cart');
        Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
        Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
        Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
        Route::get('/cart/count', [CartController::class, 'getCount'])->name('cart.count');
  
        
        // Wishlist
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
        Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
        Route::delete('/wishlist/{id}', [WishlistController::class, 'remove'])->name('wishlist.remove');
        
        // Orders
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/orders/fetch', [OrderController::class, 'fetchOrders'])->name('orders.fetch');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

          // Checkout
        Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
        
        
        // Logout
        Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
    });
});


// Admin Routes (your existing routes)
Route::middleware(['auth'])->group(function () {
    // Your existing admin routes
});

Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart.data');
