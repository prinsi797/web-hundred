<?php

namespace Tests\Feature\Controllers\Admin;

use App\Http\Controllers\Admin\AppUserController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Requests\AppUserRequests\AddUser;
use App\Http\Requests\AppUserRequests\UpdateUser;
use App\Http\Requests\BookRequests\AddBook;
use App\Http\Requests\BookRequests\UpdateBook;
use App\Models\AppUser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Tests\TestCase;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mockery;

// use App\Models\Book;
// use App\Http\Controllers\Admin\BookController;

class AppUserControllerTest extends TestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    // Create and authenticate a user
    $user = User::factory()->create();
    $this->actingAs($user);
  }

  /** @test */
  public function testIndexMethodReturnIndexView()
  {
    // Invoke the index method of the AppUserController
    $response = $this->get(route('admin.app_users.index'));

    // Assert that the response is successful
    $response->assertStatus(200);

    // Assert that the view returned is 'app_users.index'
    $response->assertViewIs('theme.app_users.index');
  }

  /** @test */
  public function testCreateMethodReturnManageView()
  {
    // Invoke the index method of the AppUserController
    $response = $this->get(route('admin.app_users.create'));

    // Assert that the view returned is 'app_user.create'
    $response->assertViewIs('theme.app_users.manage');
  }

  public function testEditMethodReturnManageViewWithData()
  {
    // Create an AppUser instance
    // $appUser = AppUser::factory()->count(2)->create();

    $appUser = AppUser::first();
    $this->assertNotNull($appUser, 'No App users found in the database.');

    // Create an instance of the AppUserController
    $controller = new AppUserController();
    $request = new Request();
    $request['id'] = $appUser->id;

    // Call the edit method
    $response = $controller->edit($request);

    // Assert that the response is an instance of a View
    $this->assertInstanceOf(View::class, $response);

    // Assert that the view name is 'theme.app_users.manage'
    $this->assertEquals('theme.app_users.manage', $response->name());

    // Assert that the view data contains the necessary variables
    $this->assertEquals(route('admin.app_users.update'), $response->getData()['form_action']);
    $this->assertEquals(route('admin.app_users.index'), $response->getData()['cancel']);
    $this->assertEquals(1, $response->getData()['edit']);

    // Assert that specific fields match between the expected and actual data
    $this->assertEquals($appUser->name, $response->getData()['data']['name']);
    $this->assertEquals($appUser->dob, $response->getData()['data']['dob']);
    $this->assertEquals($appUser->phone_number, $response->getData()['data']['phone_number']);
    $this->assertEquals($appUser->profile_photo_url, $response->getData()['data']['profile_photo_url']);
    $this->assertEquals($appUser->username, $response->getData()['data']['username']);
    $this->assertEquals($appUser->lift_type, $response->getData()['data']['lift_type']);
  }

  /** @test */
  public function testStoreMethodShouldStoreData()
  {
    // Mock the AddUser request object with fake data
    $request = new AddUser([
      'name' => "Testing",
      'username' => "Hello",
      'profile_photo_url' => 'test.jpg', // Replace with the file path
      'dob' => '2001-09-10',
      'phone_number' => '6543209876',
      'lift_type' => 'power_clean',
    ]);

    // Call the store method
    $controller = new AppUserController();
    $response = $controller->store($request);

    // Assert that the response is a RedirectResponse
    $this->assertInstanceOf(RedirectResponse::class, $response);

    // Assert that the response redirects to the correct route
    $this->assertEquals(route('admin.app_users.index'), $response->getTargetUrl());

    // Assert that the session contains a success message
    $this->assertTrue(session()->has('success'));
  }

  /** @test */
  public function testUpdateMethodShouldUpdateData()
  {
    $this->get(route('admin.app_users.edit'));
    // Mock the AddRequest object with fake data
    $request = new UpdateUser([
      'id' => 29,
      'name' => "Testing1 update",
      'username' => "Hello2 update",
      'profile_photo_url' => 'test2.jpg',
      'dob' => '2001-09-10',
      'phone_number' => '6543209870',
      'lift_type' => 'deadlift',
    ]);

    // Call the update method
    $controller = new AppUserController();
    $response = $controller->update($request);

    // Assert that the response is a RedirectResponse
    $this->assertInstanceOf(RedirectResponse::class, $response);

    // Assert that the response redirects to the correct route
    $this->assertEquals(route('admin.app_users.edit'), $response->getTargetUrl());

    // Assert that the session contains a success message
    $this->assertTrue(session()->has('success'));
  }

  /** @test */
  public function testAjaxMethodShouldReturnAjaxViewWithData() {
    
    $users = AppUser::factory()->count(50)->create();
        // Create the controller and request instances
        $controller = new AppUserController();
        $request = new Request([
            'page_number' => 1,
        ]);

        // Call the ajax method
        $response = $controller->ajax($request);

        // Assert that the response is an instance of View
        $this->assertInstanceOf(View::class, $response);

        // Assert that the view name is correct
        $this->assertEquals('theme.app_users.ajax', $response->name());

        // Define expected pagination variables
        $offset = 0;
        $limit = 10;
        $current_page = 1;
        $total_records = AppUser::count();
        $appUsers = AppUser::orderBy('id', 'desc')->skip($offset)->take($limit)->get();

        $pagination = [
            "offset" => $offset,
            "total_records" => $total_records,
            "item_per_page" => $limit,
            "total_pages" => ceil($total_records / $limit),
            "current_page" => $current_page,
        ];

        // Assert that the view data contains the necessary variables
        $viewData = $response->getData();

        $this->assertEquals(route('admin.app_users.edit'), $viewData['edit_route']);
        $this->assertEquals($current_page, $viewData['page_number']);
        $this->assertEquals($limit, $viewData['limit']);
        $this->assertEquals($offset, $viewData['offset']);
        $this->assertEquals($pagination, $viewData['pagination']);
        $this->assertEquals($appUsers->toArray(), $viewData['data']->toArray());
    }

  /** @test */
  public function testDeleteMethodShouldTrashSingleRecord()
  {
    $controller = new AppUserController();

    $appUserId = AppUser::first()->id;

    $request = new Request([
      'action' => 'trash',
      'is_bulk' => 0,
      'data_id' => $appUserId,
    ]);

    $response = $controller->delete($request);
    $this->assertEquals(1, $response);

  }
}
