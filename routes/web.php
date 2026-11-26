<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EditorialController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MoUController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/destinasi', [HomeController::class, 'packages'])->name('packages.index');
Route::get('/paket', [HomeController::class, 'packages'])->name('packages.legacy');
Route::get('/paket/{paket:slug}', [HomeController::class, 'package'])->name('packages.show');
Route::get('/jelajah', [EditorialController::class, 'explore'])->name('editorial.explore');
Route::get('/budaya-sejarah', [EditorialController::class, 'culture'])->name('editorial.culture');
Route::get('/kuliner', [EditorialController::class, 'culinary'])->name('editorial.culinary');
Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/advertorial', [EditorialController::class, 'advertorial'])->name('editorial.advertorial');
Route::get('/berita', [EditorialController::class, 'news'])->name('editorial.news');
Route::get('/artikel/{slug}', [EditorialController::class, 'article'])->name('editorial.show');

Route::middleware('guest')->group(function () {
    Route::get('/daftar', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->name('register.store');
    Route::get('/masuk', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login.legacy');
});

Route::post('/keluar', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/dokumen/pembayaran/{payment}', [DocumentController::class, 'paymentProof'])->middleware('auth')->name('documents.payment-proof');
Route::get('/dokumen/mou/{mou}/{kind}', [DocumentController::class, 'mouFile'])->middleware('auth')->name('documents.mou');

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/dashboard', [BookingController::class, 'index'])->name('customer.dashboard');
    Route::get('/akun', [CustomerProfileController::class, 'edit'])->name('customer.profile.edit');
    Route::put('/akun', [CustomerProfileController::class, 'update'])->name('customer.profile.update');
    Route::post('/paket/{paket:slug}/pesan', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/invoice/{invoice}', [PaymentController::class, 'show'])->name('invoices.show');
    Route::post('/invoice/{invoice}/pembayaran', [PaymentController::class, 'submit'])->name('payments.submit');
    Route::post('/invoice/{invoice}/simulasi-pembayaran', [PaymentController::class, 'simulatePayment'])->name('payments.simulate');
    Route::get('/invoice/{invoice}/cetak', [PaymentController::class, 'receipt'])->name('invoices.receipt');
    Route::get('/mou', [MoUController::class, 'index'])->name('mous.index');
    Route::post('/mou', [MoUController::class, 'store'])->name('mous.store');
});

Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/paket', [AdminController::class, 'packages'])->name('packages.index');
    Route::get('/konten', [AdminController::class, 'editorial'])->name('editorial.index');
    Route::post('/konten', [AdminController::class, 'storeEditorial'])->name('editorial.store');
    Route::put('/konten/{article}', [AdminController::class, 'updateEditorial'])->name('editorial.update');
    Route::delete('/konten/{article}', [AdminController::class, 'deleteEditorial'])->name('editorial.delete');
    Route::post('/paket', [AdminController::class, 'storePackage'])->name('packages.store');
    Route::put('/paket/{paket}', [AdminController::class, 'updatePackage'])->name('packages.update');
    Route::delete('/paket/{paket}', [AdminController::class, 'deletePackage'])->name('packages.delete');
    Route::get('/wisata', [AdminController::class, 'destinations'])->name('destinations.index');
    Route::post('/wisata', [AdminController::class, 'storeDestination'])->name('destinations.store');
    Route::put('/wisata/{wisata}', [AdminController::class, 'updateDestination'])->name('destinations.update');
    Route::delete('/wisata/{wisata}', [AdminController::class, 'deleteDestination'])->name('destinations.delete');
    Route::get('/pesanan', [AdminController::class, 'bookings'])->name('bookings.index');
    Route::put('/pesanan/{booking}/negosiasi', [AdminController::class, 'negotiate'])->name('bookings.negotiate');
    Route::put('/pembayaran/{payment}', [AdminController::class, 'reviewPayment'])->name('payments.review');
    Route::get('/konsumen', [AdminController::class, 'customers'])->name('customers.index');
    Route::get('/mou', [AdminController::class, 'mous'])->name('mous.index');
    Route::put('/mou/{mou}', [AdminController::class, 'reviewMou'])->name('mous.review');
    Route::get('/pengaturan', [AdminController::class, 'settings'])->name('settings.index');
    Route::put('/profil-perusahaan', [AdminController::class, 'updateProfile'])->name('profile.update');
    Route::post('/galeri-profil', [AdminController::class, 'storeGalleryImage'])->name('gallery.store');
    Route::put('/galeri-profil/{galleryImage}', [AdminController::class, 'updateGalleryImage'])->name('gallery.update');
    Route::delete('/galeri-profil/{galleryImage}', [AdminController::class, 'deleteGalleryImage'])->name('gallery.delete');
    Route::post('/manajemen', [AdminController::class, 'storeManager'])->name('managers.store');
    Route::put('/manajemen/{manager}', [AdminController::class, 'updateManager'])->name('managers.update');
    Route::delete('/manajemen/{manager}', [AdminController::class, 'deleteManager'])->name('managers.delete');
    Route::post('/metode-pembayaran', [AdminController::class, 'storePaymentMethod'])->name('payment-methods.store');
    Route::put('/metode-pembayaran/{paymentMethod}', [AdminController::class, 'updatePaymentMethod'])->name('payment-methods.update');
    Route::delete('/metode-pembayaran/{paymentMethod}', [AdminController::class, 'deletePaymentMethod'])->name('payment-methods.delete');
});
