<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FitnessController;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register',[AuthController::class,'register'])->name('user.register');

Route::post('/login', [AuthController::class, 'login'])->name('user.login');
Route::post('/verify-otp', [AuthController::class, 'verifyOTP'])->name('user.verify');
Route::post('/resend-otp',[AuthController::class,'resendOTP'])->name('resend.opt');

// Route::middleware('auth:app_user')->group(function () {
Route::middleware(['jwt.verify','auth:app_user'])->group(function () {
    Route::post('/profile-name-update', [AuthController::class, 'profileNameUpdate'])->name('profile.name.update');
    Route::post('/profile-image-update', [AuthController::class, 'profileImageUpdate'])->name('profile.image.update');
    // school
    Route::get('/search-school',[FitnessController::class,'searchSchool'])->name('search.school');
    Route::post('/add-school',[FitnessController::class,'schoolAdd'])->name('school.add');
    Route::post('/app-user-school',[FitnessController::class,'appUserSchoolStore'])->name('store.school');

    // app user contact
    Route::get('/user-contacts',[FitnessController::class,'getUserContacts'])->name('user.contacts');
    Route::post('/access-contact',[FitnessController::class,'contactStore'])->name('conatct.store');

    // app user Add Friend
    Route::post('/add-friend',[FitnessController::class,'addFriend'])->name('invite.friend');

    // bench store api
    Route::post('/bench-store',[FitnessController::class,'benchPressStore'])->name('bench.store');
    Route::post('/deadlift-store',[FitnessController::class,'deadliftStore'])->name('deadlift.store');
    Route::post('/squats-store',[FitnessController::class,'squatsStore'])->name('squats.store');
    Route::post('/powercleans-store',[FitnessController::class,'powercleansStore'])->name('powercleans.store');
    Route::post('/weight-store',[FitnessController::class,'weightStore'])->name('weight.store');
    Route::post('/height-store',[FitnessController::class,'heightStore'])->name('height.store');
    Route::post('/lift-type',[FitnessController::class,'liftTypeStore'])->name('list.type.store');

    // Route::get('/get-fitnesh',[MerchantController::class,'fitneshInfoGet'])->name('fitnesh.auth.get');
    Route::get('/profile-get',[FitnessController::class,'profileGet'])->name('user.profile');
    Route::get('/lift-graph',[FitnessController::class,'liftGraph'])->name('lift.graph');
    Route::get('/leaderboard',[FitnessController::class,'leaderBoard'])->name('leaderboard');
    Route::post('/feedback',[FitnessController::class,'feedback'])->name('feedback');

    //get contact in app user
    Route::get('get-appuser-contacts',[FitnessController::class,'GetUserContactList'])->name('get.user.contact'); 

    //delete a account
    Route::post('deleteAccount',[AuthController::class,'deleteUserAccount'])->name('delete.user.account');
});
