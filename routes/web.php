<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', 'IndexController@index')->name('home');
Route::get('/', [IndexController::class, 'index']);

Auth::routes([
  'register' => false, // Registration Routes...
  'reset' => true, // Password Reset Routes...
  'verify' => true, // Email Verification Routes...
]);

Route::get('/test', "IndexController@test")->name('admin.test');

Route::get('/pages/{slug}', 'PageController@showPage')->name('show.page');
Route::get('/user/{username}', [IndexController::class, 'userPublicProfile'])->name('user.profile.public');
Route::get('/foresite-help', function () {
  return Redirect::to('https://www.titleivsite.com/faq.html');
})->name('foresite.help');
Route::get('verify/resend', 'Auth\TwoFactorController@resend')->name('verify.resend');
Route::get('/user/upgrade', 'UserController@upgrade')->name('user.upgrade');
// Route::post('/user/upgrade/subscription', 'UserController@upgradeMembership')->name('user.upgrade.subscription');

Route::resource('verify', 'Auth\TwoFactorController')->only(['index', 'store']);
Route::middleware(['auth', 'twofactor'])->prefix('admin')->group(function () {
  // Test Route

  // For Settings
  Route::get('/settings/edit_profile', "UserController@edit_profile")->name('admin.settings.edit_profile');
  Route::post('/settings/update_profile', "UserController@update_profile")->name('admin.settings.update_profile');

  // For Dashboard
  Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
  // Route::get('/dashboard', 'HomeController@index')->name('admin.dashboard');

  // For Users

  Route::get('/users', 'UserController@index')->name('admin.users.index');
  Route::get('/users/add', "UserController@create")->name('admin.users.create');
  Route::get('/users/edit', "UserController@edit")->name('admin.users.edit');
  Route::post('/users/store', "UserController@store")->name('admin.users.store');
  Route::post('/users/update', "UserController@update")->name('admin.users.update');
  Route::get('/users/ajax', "UserController@ajax")->name('admin.users.ajax');
  Route::post('/users/delete', "UserController@delete")->name('admin.users.delete');


  Route::get('/app-users', 'Admin\AppUserController@index')->name('admin.app_users.index');
  Route::get('/app-users/add', "Admin\AppUserController@create")->name('admin.app_users.create');
  Route::get('/app-users/edit', "Admin\AppUserController@edit")->name('admin.app_users.edit');
  Route::post('/app-users/store', "Admin\AppUserController@store")->name('admin.app_users.store');
  Route::post('/app-users/update', "Admin\AppUserController@update")->name('admin.app_users.update');
  Route::get('/app-users/ajax', "Admin\AppUserController@ajax")->name('admin.app_users.ajax');
  Route::post('/app-users/delete', "Admin\AppUserController@delete")->name('admin.app_users.delete');
  Route::get('/app-users/contacts', "Admin\AppUserController@getContacts")->name('admin.app_users.contacts');
  Route::get('/app-users/schools', 'Admin\AppUserController@getSchools')->name('admin.app_users.schools');
  Route::get('/app-users/friends', 'Admin\AppUserController@getFriends')->name('admin.app_users.friends');
  Route::get('/app-users/status', 'Admin\AppUserController@getStatus')->name('admin.app_users.status');

  // For Roles
  Route::get('/roles', 'RoleController@index')->name('admin.roles.index');
  Route::get('/roles/add', "RoleController@create")->name('admin.roles.create');
  Route::get('/roles/edit', "RoleController@edit")->name('admin.roles.edit');
  Route::post('/roles/store', "RoleController@store")->name('admin.roles.store');
  Route::post('/roles/update', "RoleController@update")->name('admin.roles.update');
  Route::get('/roles/ajax', "RoleController@ajax")->name('admin.roles.ajax');
  Route::post('/roles/delete', "RoleController@delete")->name('admin.roles.delete');

  //For App User BenchPress

  Route::get('/benchpress', 'Admin\BenchPressController@index')->name('admin.benchpress.index');
  Route::get('/benchpress/add', "Admin\BenchPressController@create")->name('admin.benchpress.create');
  Route::get('/benchpress/edit', "Admin\BenchPressController@edit")->name('admin.benchpress.edit');
  Route::post('/benchpress/store', "Admin\BenchPressController@store")->name('admin.benchpress.store');
  Route::post('/benchpress/update', "Admin\BenchPressController@update")->name('admin.benchpress.update');
  Route::get('/benchpress/ajax', "Admin\BenchPressController@ajax")->name('admin.benchpress.ajax');
  Route::post('/benchpress/delete', "Admin\BenchPressController@delete")->name('admin.benchpress.delete');

  //For dedlifts

  Route::get('/deadlifts', 'Admin\DeadliftController@index')->name('admin.deadlifts.index');
  Route::get('/deadlifts/add', "Admin\DeadliftController@create")->name('admin.deadlifts.create');
  Route::get('/deadlifts/edit', "Admin\DeadliftController@edit")->name('admin.deadlifts.edit');
  Route::post('/deadlifts/store', "Admin\DeadliftController@store")->name('admin.deadlifts.store');
  Route::post('/deadlifts/update', "Admin\DeadliftController@update")->name('admin.deadlifts.update');
  Route::get('/deadlifts/ajax', "Admin\DeadliftController@ajax")->name('admin.deadlifts.ajax');
  Route::post('/deadlifts/delete', "Admin\DeadliftController@delete")->name('admin.deadlifts.delete');

  //For Power cleans

  Route::get('/powercleans', 'Admin\PowerCleanController@index')->name('admin.powercleans.index');
  Route::get('/powercleans/add', "Admin\PowerCleanController@create")->name('admin.powercleans.create');
  Route::get('/powercleans/edit', "Admin\PowerCleanController@edit")->name('admin.powercleans.edit');
  Route::post('/powercleans/store', "Admin\PowerCleanController@store")->name('admin.powercleans.store');
  Route::post('/powercleans/update', "Admin\PowerCleanController@update")->name('admin.powercleans.update');
  Route::get('/powercleans/ajax', "Admin\PowerCleanController@ajax")->name('admin.powercleans.ajax');
  Route::post('/powercleans/delete', "Admin\PowerCleanController@delete")->name('admin.powercleans.delete');

  //For Squat
  Route::get('/squats', 'Admin\SquatController@index')->name('admin.squats.index');
  Route::get('/squats/add', "Admin\SquatController@create")->name('admin.squats.create');
  Route::get('/squats/edit', "Admin\SquatController@edit")->name('admin.squats.edit');
  Route::post('/squats/store', "Admin\SquatController@store")->name('admin.squats.store');
  Route::post('/squats/update', "Admin\SquatController@update")->name('admin.squats.update');
  Route::get('/squats/ajax', "Admin\SquatController@ajax")->name('admin.squats.ajax');
  Route::post('/squats/delete', "Admin\SquatController@delete")->name('admin.squats.delete');

  //For Heights

  Route::get('/add-users-heights', 'Admin\HeightController@index')->name('admin.app_user_heights.index');
  Route::get('/add-users-heights/add', "Admin\HeightController@create")->name('admin.app_user_heights.create');
  Route::get('/add-users-heights/edit', "Admin\HeightController@edit")->name('admin.app_user_heights.edit');
  Route::post('/add-users-heights/store', "Admin\HeightController@store")->name('admin.app_user_heights.store');
  Route::post('/add-users-heights/update', "Admin\HeightController@update")->name('admin.app_user_heights.update');
  Route::get('/add-users-heights/ajax', "Admin\HeightController@ajax")->name('admin.app_user_heights.ajax');
  Route::post('/add-users-heights/delete', "Admin\HeightController@delete")->name('admin.app_user_heights.delete');

  //For Weights
  Route::get('/add-users-weights', 'Admin\WeightController@index')->name('admin.app_user_weights.index');
  Route::get('/add-users-weights/add', "Admin\WeightController@create")->name('admin.app_user_weights.create');
  Route::get('/add-users-weights/edit', "Admin\WeightController@edit")->name('admin.app_user_weights.edit');
  Route::post('/add-users-weights/store', "Admin\WeightController@store")->name('admin.app_user_weights.store');
  Route::post('/add-users-weights/update', "Admin\WeightController@update")->name('admin.app_user_weights.update');
  Route::get('/add-users-weights/ajax', "Admin\WeightController@ajax")->name('admin.app_user_weights.ajax');
  Route::post('/add-users-weights/delete', "Admin\WeightController@delete")->name('admin.app_user_weights.delete');

  // For Schools
  Route::get('/schools', 'Admin\SchoolController@index')->name('admin.schools.index');
  Route::get('/schools/add', "Admin\SchoolController@create")->name('admin.schools.create');
  Route::get('/schools/edit', "Admin\SchoolController@edit")->name('admin.schools.edit');
  Route::post('/schools/store', "Admin\SchoolController@store")->name('admin.schools.store');
  Route::post('/schools/update', "Admin\SchoolController@update")->name('admin.schools.update');
  Route::get('/schools/ajax', "Admin\SchoolController@ajax")->name('admin.schools.ajax');
  Route::post('/schools/delete', "Admin\SchoolController@delete")->name('admin.schools.delete');

  // For Pages
  Route::get('/pages', 'PageController@index')->name('admin.pages.index');
  Route::get('/pages/add', "PageController@create")->name('admin.pages.create');
  Route::get('/pages/edit', "PageController@edit")->name('admin.pages.edit');
  Route::post('/pages/store', "PageController@store")->name('admin.pages.store');
  Route::post('/pages/update', "PageController@update")->name('admin.pages.update');
  Route::get('/pages/ajax', "PageController@ajax")->name('admin.pages.ajax');
  Route::post('/pages/delete', "PageController@delete")->name('admin.pages.delete');

  //For Feedbacks

  Route::get('/feedbacks', 'Admin\FeedbackController@index')->name('admin.app_user_feedbacks.index');
  Route::get('/feedbacks/add', "Admin\FeedbackController@create")->name('admin.app_user_feedbacks.create');
  Route::get('/feedbacks/edit', "Admin\FeedbackController@edit")->name('admin.app_user_feedbacks.edit');
  Route::post('/feedbacks/store', "Admin\FeedbackController@store")->name('admin.app_user_feedbacks.store');
  Route::post('/feedbacks/update', "Admin\FeedbackController@update")->name('admin.app_user_feedbacks.update');
  Route::get('/feedbacks/ajax', "Admin\FeedbackController@ajax")->name('admin.app_user_feedbacks.ajax');
  Route::post('/feedbacks/delete', "Admin\FeedbackController@delete")->name('admin.app_user_feedbacks.delete');

  //For Stats
  Route::get('/stats', 'Admin\StatController@index')->name('admin.stats_users.index');
  Route::get('/stats/add', "Admin\StatController@create")->name('admin.stats_users.create');
  Route::get('/stats/edit', "Admin\StatController@edit")->name('admin.stats_users.edit');
  Route::post('/stats/store', "Admin\StatController@store")->name('admin.stats_users.store');
  Route::post('/stats/update', "Admin\StatController@update")->name('admin.stats_users.update');
  Route::get('/stats/ajax', "Admin\StatController@ajax")->name('admin.stats_users.ajax');
  Route::post('/stats/delete', "Admin\StatController@delete")->name('admin.stats_users.delete');

});
