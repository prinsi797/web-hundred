<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase {
  use RefreshDatabase;

  public function testUserHasNameAndEmail() {
    $user = User::factory()->create([
      'first_name' => 'Akram',
      'last_name' => 'Chauhan',
      'email' => 'akram.chauhan@example.com',
    ]);

    $this->assertEquals('Akram', $user->first_name);
    $this->assertEquals('Chauhan', $user->last_name);
    $this->assertEquals('akram.chauhan@example.com', $user->email);
  }
}
