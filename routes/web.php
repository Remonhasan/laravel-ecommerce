<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Frontend\ClientController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Frontend - home
Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/search-product', 'searchProduct')->name('search.product');
});

/*
   Frontend - customer section
   public routes
*/
Route::controller(ClientController::class)->group(function () {
    Route::get('/category/{id}/{slug}', 'getCategory')->name('customer.category');
    Route::get('/product/{id}/{slug}', 'getProduct')->name('customer.product');
    Route::get('/new-release', 'newRelease')->name('new.release');
});

/*
   Frontend - customer section
   Protected routes
*/
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::controller(ClientController::class)->group(function () {
        // cart
        Route::get('/cart', 'addCart')->name('customer.cart');
        Route::post('/cart-product', 'addProductCart')->name('add.productCart');
        Route::get('/cart/remove/{id}', 'cartRemove')->name('remove.cart');
        // shipping 
        Route::get('/shipping-address', 'getShippingAddress')->name('shipping.address');
        Route::post('/shipping/add', 'addShippingAddress')->name('add.shipping');
        // checkout
        Route::get('/checkout', 'checkout')->name('customer.checkout');
        // order
        Route::post('/place-order', 'placeOrder')->name('place.order');
        // payment - stripe
        Route::get('/payment/stripe', 'stripePayment')->name('stripe.payment');
        Route::get('/payment/stripe/success/{order_id}', 'stripePaymentSuccess')->name('stripe.payment.success');
        // user profile 
        Route::get('/user-profile', 'userProfile')->name('user.profile');
        Route::get('/user-profile/order', 'pendingOrder')->name('user.pendingOrder');
        Route::get('/user-profile/approved-order', 'approvedOrder')->name('user.approvedOrder');
        Route::get('/user-profile/history', 'userHistory')->name('user.history');
        // others
        Route::get('/todays-deal', 'todaysDeal')->name('todays.deal');
        Route::get('/customer-service', 'customerService')->name('customer.service');
    });
});

// Admin
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/admin/dashboard', 'index')->name('admin.dashboard');
    });

    // Category
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/admin/category/all', 'index')->name('all.category');
        Route::get('/admin/category/add', 'addCategory')->name('add.category');
        Route::post('/admin/category/store', 'store')->name('store.category');
        Route::get('/admin/category/edit/{id}', 'edit')->name('edit.category');
        Route::post('/admin/category/update', 'update')->name('update.category');
        Route::get('/admin/category/delete/{id}', 'delete')->name('delete.category');
    });

    // Subcategory
    Route::controller(SubCategoryController::class)->group(function () {
        Route::get('/admin/subcategory/all', 'index')->name('all.subcategory');
        Route::get('/admin/subcategory/add', 'addSubCategory')->name('add.subcategory');
        Route::post('/admin/subcategory/store', 'store')->name('store.subcategory');
        Route::get('/admin/subcategory/edit/{id}', 'edit')->name('edit.subcategory');
        Route::post('/admin/subcategory/update', 'update')->name('update.subcategory');
        Route::get('/admin/subcategory/delete/{id}', 'delete')->name('delete.subcategory');
    });

    // Product
    Route::controller(ProductController::class)->group(function () {

        // Get corresponding subcategory by selecting category
        Route::get('/get-subcategories/{categoryId}', 'getSubcategories');

        // core routes
        Route::get('/admin/product/all', 'index')->name('all.product');
        Route::get('/admin/product/add', 'addProduct')->name('add.product');
        Route::post('/admin/product/store', 'store')->name('store.product');
        Route::get('/admin/product/edit/{id}', 'edit')->name('edit.product');
        Route::post('/admin/product/update', 'update')->name('update.product');
        Route::get('/admin/product/delete/{id}', 'delete')->name('delete.product');
    });

    // Order
    Route::controller(OrderController::class)->group(function () {
        Route::get('/admin/order/pending', 'pendingOrder')->name('pending.order');
        Route::get('/admin/order/approve/{id}', 'approvePendingOrder')->name('approve.order');
        Route::get('/admin/order/cancle/{id}', 'cancledPendingOrder')->name('cancled.order');
        Route::get('/admin/order/all-complete', 'allCompletedOrder')->name('all.completed.order');
        Route::get('/admin/order/all-cancle', 'allCancledOrder')->name('all.cancled.order');
    });

     // Order
     Route::controller(ReportController::class)->group(function () {
        Route::get('/admin/report/total-sales', 'totalSaleReport')->name('report.total.sale');
    });
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
