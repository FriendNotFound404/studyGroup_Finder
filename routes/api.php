<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudyGroupController;
use App\Http\Controllers\GroupMessageController;

Route::post('/groups', [StudyGroupController::class, 'create']);
Route::get('/groups', [StudyGroupController::class, 'index']);
Route::get('/groups/search', [StudyGroupController::class, 'search']);
Route::post('/groups/{id}/join', [StudyGroupController::class, 'join']);
Route::post('/groups/{id}/leave', [StudyGroupController::class, 'leave']);

Route::get('/groups/{id}/messages', [GroupMessageController::class, 'index']);
Route::post('/groups/{id}/messages', [GroupMessageController::class, 'send']);

Route::get('/test-api', function () {
    return ['status' => 'API OK'];
});
