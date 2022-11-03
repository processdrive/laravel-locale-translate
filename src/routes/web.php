<?php

use Illuminate\Support\Facades\Route;
use ProcessDrive\LaravelFileTranslate\controllers\LaravelFileTranslateController;

Route::get('translation/index', [LaravelFileTranslateController::class, 'index'])->name('translation.index');
Route::get('translation/get-data', [LaravelFileTranslateController::class, 'getTranslation'])->name('translation.getdata');
Route::post('translation/get-data', [LaravelFileTranslateController::class, 'getTranslation'])->name('translation.getdata');
Route::post('translation/store', [LaravelFileTranslateController::class, 'store'])->name('translation.store');
Route::post('translation/update', [LaravelFileTranslateController::class, 'update'])->name('translation.update');
Route::post('translation/delete', [LaravelFileTranslateController::class, 'destory'])->name('translation.delete');
Route::post('translation/new-language', [LaravelFileTranslateController::class, 'storeNewLanguage'])->name('translation.newlanguage');