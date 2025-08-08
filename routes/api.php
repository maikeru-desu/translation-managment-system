<?php

declare(strict_types=1);

use App\Http\Controllers\Api\LocaleController;
use Illuminate\Support\Facades\Route;

Route::apiResource('locales', LocaleController::class);


