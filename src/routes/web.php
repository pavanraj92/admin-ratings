<?php

use Illuminate\Support\Facades\Route;
use admin\ratings\Controllers\RatingManagerController;

Route::name('admin.')->middleware(['web','admin.auth'])->group(function () {  
    Route::resource('ratings', RatingManagerController::class)->only(['show', 'index','destroy']);
    Route::post('ratings/updateStatus', [RatingManagerController::class, 'updateStatus'])->name('ratings.updateStatus');

});
