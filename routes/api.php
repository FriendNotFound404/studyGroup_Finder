<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupMessageController;
use App\Http\Controllers\StudyGroupController;
use App\Http\Controllers\GroupJoinRequestController;
use App\Http\Controllers\GroupSessionController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // study groups (REST)
    Route::get('/groups', [StudyGroupController::class, 'index']);
    Route::post('/groups', [StudyGroupController::class, 'store']);
    Route::get('/groups/{id}', [StudyGroupController::class, 'show']);
    Route::patch('/groups/{id}', [StudyGroupController::class, 'update']);
    Route::delete('/groups/{id}', [StudyGroupController::class, 'destroy']);
    Route::post('/groups/{groupId/sessions', [GroupSessionController::class, 'createSession']);

    // study group lifecycle
    Route::post('/groups/{id}/archive', [StudyGroupController::class, 'archive']);
    Route::patch('/groups/{id}/retention', [StudyGroupController::class, 'updateRetention']);

    // membership
    Route::post('/groups/{id}/join', [StudyGroupController::class, 'join']);
    Route::post('/groups/{id}/leave', [StudyGroupController::class, 'leave']);
    Route::post('/groups/{id}/join-request',[GroupJoinRequestController::class, 'requestJoin']);
    Route::post('/join-requests/{id}/approve',[GroupJoinRequestController::class, 'approve']);

    // group messages
    Route::get('/groups/{groupId}/messages', [GroupMessageController::class, 'index']);
    Route::patch('/groups/{groupId}/messages/{messageId}',[GroupMessageController::class, 'update']);
    Route::post('/groups/{groupId}/messages', [GroupMessageController::class, 'send']);
    Route::post('/groups/messages/{id}/pin',[GroupMessageController::class, 'pinnedmessage']);
    Route::delete('/groups/{groupId}/messages/{messageId}',[GroupMessageController::class, 'destroy']);
});

