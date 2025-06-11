<?php

use App\Exports\DataExport;
use App\Exports\OrdersExport;
use App\Exports\SingleOrderExport;
use App\Livewire\PrivacyPolicy;
use Illuminate\Support\Facades\Route;
use App\Livewire\StoreShow;
use App\Livewire\ProductDetail;
use App\Livewire\ShoppingCart;
use App\Livewire\OrderPage;
use App\Livewire\OrderDetail;
use App\Livewire\Checkout;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\PaymentConfirmationPage;
use App\Livewire\Profile;
use App\Livewire\SearchPage;
use App\Exports\TemplateExport;
use App\Livewire\CreateRequest;
use App\Livewire\Request as AppRequest;
use App\Livewire\ShopDetail;
use App\Livewire\UpdateProfile;
use App\Models\Order;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

Route::middleware([])->group(function () {
    Route::get('/', StoreShow::class)->name('home');
    Route::get('/privacy-policy', PrivacyPolicy::class)->name('privacy.policy');
});

Route::middleware(['store.closed'])->group(function () {
    Route::get('/product/{slug}', ProductDetail::class)->name('product.detail');
    Route::get('/search', SearchPage::class)->name('search.page');
    Route::get('/shop/{slug}', ShopDetail::class)->name('shop.detail');
});


Route::middleware(['guest',])->group(function () {
    Route::get('login', Login::class)->name('login');
    Route::get('register', Register::class)->name('register');
});


Route::middleware(['auth', 'store.closed'])->group(function () {
    Route::get('/shopping-cart', ShoppingCart::class)->name('shopping-cart');

    Route::get('/payment-confirmation/{orderNumber}', PaymentConfirmationPage::class)->name('payment-confirmation');
});

Route::middleware(['auth',])->group(function () {
    Route::get('/profile', Profile::class)->name('profile');
    Route::get('/update-profile', UpdateProfile::class)->name('profile.update');

    Route::get('/permohonan', AppRequest::class)->name('permohonan');
    Route::get('/buat-permohonan', CreateRequest::class)->name('buat.permohonan');

    Route::get('/orders', OrderPage::class)->name('orders');
    Route::get('/order-detail/{orderNumber}', OrderDetail::class)->name('order-detail');

    Route::get('/download-template', function () {
        return Excel::download(new TemplateExport, 'template.xlsx');
    })->name('download-template');
    Route::get('/download-data', function () {
        return Excel::download(new DataExport, 'data.xlsx');
    })->name('download-data');

    Route::get('/download-rekap', function (Request $request) {
        $startDate = $request->query('start_date', now()->subMonth()->format('Y-m-d')); // Default ke bulan lalu jika kosong
        $endDate = $request->query('end_date', now()->format('Y-m-d')); // Default ke hari ini jika kosong

        return Excel::download(new OrdersExport($startDate, $endDate), 'rekap.xlsx');
    })->name('download-rekap');

    Route::get('/download-order', function (Request $request) {
        $orderId = $request->query('order_id');
        $order = Order::findOrFail($orderId);
        $santriName = str_replace(' ', '_', $order->nama_santri);
        return Excel::download(new SingleOrderExport($orderId), "pesanan_{$santriName}.xlsx");
    })->name('download-order');
});

Route::middleware(['auth', 'verified', 'store.closed'])->group(function () {
    Route::get('/checkout', Checkout::class)->name('checkout');
});


require __DIR__ . '/auth.php';
