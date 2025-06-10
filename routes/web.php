<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CronJobController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/test', 'TestController@index');
Route::get('/', [LoginController::class, 'showLoginForm'])->name('home');

Route::get('cron/backup-db', [CronJobController::class, 'BackupDB'])->name('cron.backup_db');
Route::get('cron/clean-backup', [CronJobController::class, 'CleanBackup'])->name('cron.clean_backup');

Auth::routes();

/*** ----------------ADMIN ROUTES START---------------------------------***/
require __DIR__.'/admin.php';

/*** ----------------USER ROUTES START---------------------------------***/
require __DIR__.'/user.php';
