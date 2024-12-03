<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect(to: route('create.document')));
Route::get('document/create', [DocumentController::class, 'create'])->name('create.document');
Route::post('document/generate', [DocumentController::class, 'generate'])->name('generate.document');
