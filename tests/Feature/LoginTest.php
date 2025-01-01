<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\Recaptcha;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\WithFaker;

class LoginTest extends TestCase {
  use RefreshDatabase, WithFaker;

  /**
   * Test that a user can login with valid credentials.
   *
   * @return void
   */
  public function testUserCantLoginWithoutValidRecaptcha() {

    $user = User::factory()->create();
    $response = $this->post('/login', [
      'email' => $user->email,
      'password' => 'password',
    ]);

    $this->assertGuest();

    $response->assertRedirect();

    $response->assertSessionHasErrors('g-recaptcha-response');
  }

  public function testUserCanLoginWithValidRecaptcha() {

    $user = User::factory()->create();

    $response = $this->post('/login', [
      'email' => $user->email,
      'password' => 'password',
      'g-recaptcha-response' => base64_encode('valid-recaptcha-response'),
    ]);

    $this->assertAuthenticatedAs($user);

    $response->assertRedirect(RouteServiceProvider::HOME); // Update with the actual redirect URL after successful login
  }
}
