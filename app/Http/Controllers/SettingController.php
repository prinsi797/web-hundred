<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting as Table;
use Exception;
use App\Models\StripeProduct;
use Stripe\Stripe;
use Stripe\Subscription;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRequests\Profile as UpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
      }
    
      public function index() {
        $settings = getSettings();
        return kview('settings.general', [
          'form_action' => route('admin.settings.update'),
          'edit' => 1,
          'settings' => $settings,
        ]);
      }
      public function edit_profile() {
        // Stripe::setApiKey(config('app.stripe_secret'));
        $subscription_info = [];
        $user = Auth::user();
        $roles = Role::get();
        $payment_methods = null;
        if (!$user->isAdmin) {
          try {
            $payment_methods = $user->paymentMethods();
            // $subscription = DB::table('subscriptions')->where([
            //   'user_id' => $user->id,
            //   'stripe_status' => 'active'
            // ])->orderBy('id', 'desc')->first();
    
            // $subscription_stripe = \Stripe\Subscription::retrieve($subscription->stripe_id);
            // $is_cancel_period = $subscription_stripe->cancel_at_period_end;
            // $cancel_at = $subscription_stripe->cancel_at;
    
            // $product = $subscription_stripe->plan->product;
            // $subscription_product = StripeProduct::where('product_id', $product)->first();
    
            // $subscription_info = [
            //   'stripe_id' => $subscription_stripe->id,
            //   'name' => $subscription_product->name,
            //   'price' => $subscription_product->unit_amount > 0 ?  $subscription_product->unit_amount / 100 : 0,
            //   'current_period_end' => $subscription_stripe->current_period_end,
            //   'current_period_start' => $subscription_stripe->current_period_start,
            //   'cancel_at' => $cancel_at,
            //   'is_cancel_period' => $is_cancel_period,
            // ];
            //code...
          } catch (Exception $e) {
            Log::info("Couldn't fetch subscriptionf or the user -> " . $user->name, ['error' => $e->getMessage()]);
          }
        }
    
        return kview('users.manage', [
          'form_action' => route('admin.settings.update_profile'),
          'cancel' => route('admin.dashboard'),
          'edit' => 1,
          'data' => $user,
          'payment_methods' => $payment_methods,
          'subscription_info' => $subscription_info,
          'roles' => $roles,
        ]);
      }
    
      public function update_profile(UpdateRequest $request){
        try {
          $update_data = [
              'first_name' => $request->first_name,
              'last_name' => $request->last_name,
          ];

          if (isset($request->old_password)) {
              // $password=  Hash::make($request->password);
              $userObj = User::where([
                  'id' => $request->id,
              ])->first();
              if (Hash::check($request->old_password, $userObj->password)) {
                  $update_data['password'] = bcrypt($request->password);
              } else {
                  return redirect()->back()->with('error', "Old password is incorrect.");
              }
          }
          if (isset($request->password)) {
              $update_data['password'] = bcrypt($request->password);
          }
          $where = [
              'id' => $request->id
          ];

          $user = User::updateOrCreate($where, $update_data);
          if (isset($request->role)) {
              $user->syncRoles($request->role);
          }

          return redirect()->back()->with('success','User has been updated');
      } catch (Exception $e) {
          return redirect()->back()->with('error', $e->getMessage());
      }
    }

      public function update(Request $request) {
        if (isset($request->SITE_NAME)) {
          $this->updateSetting('SITE_NAME', $request->SITE_NAME);
        }
        if (isset($request->site_url)) {
          $this->updateSetting('site_url', $request->site_url);
        }
        if (isset($request->tagline)) {
          $this->updateSetting('tagline', $request->tagline);
        }
        if (isset($request->theme)) {
          $this->updateSetting('theme', $request->theme);
        }
        return redirect()->back()->with('success', 'Settings has been updated');
      }
      public function updateSetting($key, $value) {
        $where = [
          'key' => $key,
        ];
        $update_array = [
          'value' => $value,
        ];
        Table::updateOrCreate($where, $update_array);
      }
}
