<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Projects\ListProjectsController;
use App\Http\Controllers\Api\Projects\ProjectController;
use App\Http\Controllers\Api\Projects\ProjectMembersController;
use App\Http\Controllers\Api\Projects\ProjectVersionsController;
use App\Http\Controllers\Api\Tasks\ListTasksController;
use App\Http\Controllers\Api\Tasks\TaskAppointmentsController;
use App\Http\Controllers\Api\Tasks\TaskController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\User\UpdatePreferencesController;
use App\Http\Controllers\Api\User\UpdateSettingsController;
use Illuminate\Support\Facades\Route;

// Autenticação do Usuário

Route::post('/login', LoginController::class)->name('api.login');

// Rotas autenticadas do Usuário

Route::group(['middleware' => 'jwt.verify'], function () {

    Route::get('/profile', ProfileController::class)->name('api.profile');

    Route::get('/profile/settings', UpdateSettingsController::class)->name('api.settings');

    Route::get('/profile/preferences', UpdatePreferencesController::class)->name('api.preferences');

    Route::get('/projects', ListProjectsController::class)->name('api.projects');

    Route::get('/project/{project}', ProjectController::class)->name('api.project');

    Route::get('/project/{project}/members', ProjectMembersController::class)->name('api.project-members');

    Route::get('/project/{project}/versions', ProjectVersionsController::class)->name('api.project-versions');

    Route::get('/project/{project}/tasks', ListTasksController::class)->name('api.tasks');

    //Route::get('/task/{taskId}', TaskController::class)->name('api.task');//

    Route::get('/task/{taskId}/appointments', TaskAppointmentsController::class)->name('api.task-appointments');

});
