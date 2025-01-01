<?php

namespace Tests\Unit\Models;

use App\Models\AppUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppUserTest extends TestCase {
  use RefreshDatabase;

  public function testUserHasNameAndEmail() {
    $user = AppUser::factory()->create([
      'name' => 'Akram',
      'dob' => '1997-07-28',
      'phone_number' => '7869054323',
      'security_code' => 45343,
      'profile_photo_url' => 'test.jpg',
      'username' => 'akramchohan',
      'lift_type' => 'deadlift',
    ]);

    $this->assertEquals('Akram', $user->name);
    $this->assertEquals('1997-07-28', $user->dob);
    $this->assertEquals('7869054323', $user->phone_number);
    $this->assertEquals('45343', $user->security_code);
    $this->assertEquals('test.jpg', $user->profile_photo_url);
    $this->assertEquals('akramchohan', $user->username);
    $this->assertEquals('deadlift', $user->lift_type);
  }
}
