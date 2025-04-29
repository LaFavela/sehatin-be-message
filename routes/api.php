<?php

use App\Http\Controllers\UserDetailController;
use Illuminate\Support\Facades\Route;


Route::apiResource('users/detail', UserDetailController::class);
