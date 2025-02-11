<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// test api
Route::get('/vcc', [ApiController::class, 'SaveCourseRotationSetsByClub']);
Route::get('/markers', [ApiController::class, 'getPinRotationMarkersByClub']);
Route::get('/test', [TestController::class, 'syncPinRotationsByClub']);
Route::get('/dbfix', [TestController::class, 'dbFix']);

// real app api
// Route::post('/saveCourseRotationSets', [ApiController::class, 'SaveCourseRotationSets']);
Route::post('/SaveCourseRotationSetsByClub', [ApiController::class, 'SaveCourseRotationSetsByClub']);
Route::post('/getPinRotationMarkersByClub', [ApiController::class, 'getPinRotationMarkersByClub']);
Route::any('/getTransformPoint', [ApiController::class, 'getTransformPoint']);
Route::any('/getActiveRotation', [ApiController::class, 'getActiveRotation']);
Route::any('/getClubImageEdge', [ApiController::class, 'getClubImageEdge']);
Route::any('/getCourseRotationPositionsByClubPin', [ApiController::class, 'getCourseRotationPositionsByClubPin']);
// Email Subscription
Route::any('/sendSubscriptionEmails', [ApiController::class, 'sendSubscriptionEmails']);

// Admin Api
Route::group(['prefix' => 'admin'], function() {
	Route::post('loginAdmin', [AdminController::class, 'loginAdmin']);
	// vcc credentials
	Route::post('addClub', [AdminController::class, 'addClub']);
	Route::post('getCourses', [AdminController::class, 'getCourses']);
	Route::post('removeClub', [AdminController::class, 'removeClub']);
	// vcc tables
	Route::post('resetTable', [AdminController::class, 'resetTable']);
	Route::post('syncClubTables', [AdminController::class, 'syncClubTables']);
	Route::post('syncPCPTablesOfClub', [AdminController::class, 'syncPCPTablesOfClub']);
});