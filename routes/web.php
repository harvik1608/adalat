<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PdfController;

Route::get('/', [AuthController::class, 'index'])->name('admin.login');
Route::post('/sign-in', [AuthController::class, 'sign_in'])->name('admin.signin');
Route::get('/admin-logout', [AuthController::class, 'logout'])->name('admin.logout');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::resource('pdfs', PdfController::class);
Route::get('/load-pdfs', [PdfController::class, 'load'])->name('admin.pdf.load');
Route::get('/settings', [DashboardController::class, 'settings'])->name('admin.settings');
Route::get('/pdfs/remove/{id}', [PdfController::class, 'pdf_remove']);
Route::get('/split-pdf/{id}', [PdfController::class, 'splitPdf'])->name("admin.split.pdf");
Route::get('/rename-pdf/{id}', [PdfController::class, 'renamePdf'])->name("admin.rename.pdf");
Route::get('/download-pdf/{id}', [PdfController::class, 'downloadPdf'])->name("admin.download.pdf");
