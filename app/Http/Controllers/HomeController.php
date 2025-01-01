<?php

namespace App\Http\Controllers;

use App\Models\Advertiser;
use App\Models\AppUser;
use App\Models\Book;
use App\Models\Coupon;
use App\Models\Feedback;
use Illuminate\Http\Request;
use \Stripe\Stripe;
use App\Models\Report;
use App\Models\School;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct() {
    // Stripe::setApiKey(config('app.stripe_secret'));
    $this->middleware('auth');
  }

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function index() {
    $user = Auth::user();
    $users = AppUser::count();
    // $stats = Stat::count();
    $schools = School::count();
    $feedbacks = Feedback::count();

    $dashboard_cards = [
      ['App Users', $users, Route('admin.app_users.index'), 'fa fa-users'],
      // ['Stats', $stats, Route('admin.stats.index'), 'fa fa-bullhorn'],
      ['Schools', $schools, Route('admin.schools.index'), 'fa fa-university'],
      ['Feedbacks', $feedbacks, Route('admin.app_user_feedbacks.index'), 'fa fa-comment'],
    ];

    return kview('home', [
      'user' => $user,
      'dashboard_cards' => $dashboard_cards,
    ]);
  }
}
