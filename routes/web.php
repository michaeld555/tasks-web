<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/login', '/tasks/login', 301)->name('tasks.login');

Route::redirect('/', '/tasks', 301)->name('home');
