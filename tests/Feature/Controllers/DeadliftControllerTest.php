<?php

namespace Tests\Feature\Controllers\Admin;

use App\Http\Controllers\Admin\DeadliftController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Tests\TestCase;
use App\Models\User;
use App\Http\Requests\AppUserDeadliftsRequests\AddDeadlifts;
use App\Http\Requests\AppUserDeadliftsRequests\UpdateDeadlifts;
use App\Models\AppUserDeadlift;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeadliftControllerTest extends TestCase {
  use RefreshDatabase;

  public function setUp(): void
    {
        parent::setUp();

        // Create and authenticate a user
        $user = User::factory()->create();
        $this->actingAs($user);
    }

  /** @test */
  public function testIndexMethodReturnIndexView() {
    // Invoke the index method of the DeadliftController
    $response = $this->get(route('admin.deadlifts.index'));

    // Assert that the response is successful
    $response->assertStatus(200);

    // Assert that the view returned is 'deadlifts.index'
    $response->assertViewIs('theme.deadlifts.index');
  }

  /** @test */
  public function testCreateMethodReturnManageView() {
    // Invoke the index method of the DeadliftController
    $response = $this->get(route('admin.deadlifts.create'));

    // Assert that the view returned is 'deadlifts.create'
    $response->assertViewIs('theme.deadlifts.manage');
  }

  public function testEditMethodReturnManageViewWithData() {
    $deadlifts = AppUserDeadlift::first();
    $this->assertNotNull($deadlifts, 'No Deadlift found in the database.');

    // Create an instance of the DeadliftController
    $controller = new DeadliftController();
    $request = new Request();
    $request['id'] = $deadlifts->id;

    // Call the edit method
    $response = $controller->edit($request);

    // Assert that the response is an instance of a View
    $this->assertInstanceOf(View::class, $response);

    // Assert that the view name is 'theme.deadlifts.manage'
    $this->assertEquals('theme.deadlifts.manage', $response->name());

    // Assert that the view data contains the necessary variables
    $this->assertEquals(route('admin.deadlifts.update'), $response->getData()['form_action']);
    $this->assertEquals(route('admin.deadlifts.index'), $response->getData()['cancel']);
    $this->assertEquals(1, $response->getData()['edit']);
    $this->assertEquals($deadlifts->toArray(), $response->getData()['data']->toArray());
  }

  public function testStoreMethodShouldStoreData()
  {
      // Mock the AddDeadlift request object with fake data
      $request = new AddDeadlifts([
        'app_user_id' => 1,
        'date' => '2022-07-13',
        'deadlift' => '43',
    ]);
  
      // Call the store method
      $controller = new DeadliftController();
      $response = $controller->store($request);
  
      // Assert that the response is a RedirectResponse
      $this->assertInstanceOf(RedirectResponse::class, $response);
  
      // Assert that the response redirects to the correct route
      $this->assertEquals(route('admin.deadlifts.index'), $response->getTargetUrl());
  
      // Assert that the session contains a success message
      $this->assertTrue(session()->has('success'));
  
      // Assert that the data is stored in the database
      $this->assertDatabaseHas('app_user_deadlifts', [
        'app_user_id' => 1,
        'date' => '2022-07-13',
        'deadlift' => '43',
      ]);
    }

/** @test */
 public function testUpdateMethodShouldUpdateData() {
    $this->get(route('admin.deadlifts.edit'));
  
    AppUserDeadlift::factory()->create([
      'id' => 4,
      'app_user_id' => 1,
      'date' => '2022-07-13',
      'deadlift' => '430',
    ]);
  
    // Mock the AddRequest object with fake data
    $request = new UpdateDeadlifts([
      'id' => 4,
      'app_user_id' => 1,
      'date' => '2022-07-20',
      'deadlift' => '4300',
    ]);
  
    // Call the update method
    $controller = new DeadliftController();
    $response = $controller->update($request);
  
    // Assert that the response is a RedirectResponse
    $this->assertInstanceOf(RedirectResponse::class, $response);
  
    // Assert that the response redirects to the correct route
    $this->assertEquals(route('admin.deadlifts.edit'), $response->getTargetUrl());
  
    // Assert that the session contains a success message
    $this->assertTrue(session()->has('success'));
  
    // // Assert that the data is stored in the database
    $this->assertDatabaseHas('app_user_deadlifts',[
      'app_user_id' => 1,
      'date' => "2022-07-20",
      'deadlift' => '4300',
    ]);
  }

  /** @test */
public function testAjaxMethodShouldReturnAjaxViewWithData() {
    // Seed the database with test records
    AppUserDeadlift::factory()->count(5)->create([
      'app_user_id'  =>  1,
    ]);
  
    // Create an instance of the DeadliftController
    $controller = new DeadliftController();
    $request = new Request([
      'page_number' => 1,
    ]);
  
    // Call the ajax method
    $response = $controller->ajax($request);
  
    // Assert that the response is an instance of a View
    $this->assertInstanceOf(View::class, $response);
  
    // Assert that the view name is 'theme.deadlifts.manage'
    $this->assertEquals('theme.deadlifts.ajax', $response->name());
  
    $offset = 0;
    $limit = 10;
    $current_page = 1;
    $total_records = AppUserDeadlift::count();
    $deadlifts = AppUserDeadlift::orderBy('id', 'desc')->get();
  
    $pagination = [
      "offset" => $offset,
      "total_records" => $total_records,
      "item_per_page" => $limit,
      "total_pages" => ceil($total_records / $limit),
      "current_page" => $current_page,
    ];
  
    // Assert that the view data contains the necessary variables
    $this->assertEquals(route('admin.deadlifts.edit'), $response->getData()['edit_route']);
    $this->assertEquals($current_page, $response->getData()['page_number']);
    $this->assertEquals($limit, $response->getData()['limit']);
    $this->assertEquals($offset, $response->getData()['offset']);
    $this->assertEquals($pagination, $response->getData()['pagination']);
    $this->assertEquals($deadlifts->toArray(), $response->getData()['data']->toArray());
  }
  
/** @test */
public function testDeleteMethodShouldTrashSingleRecord() {
    // Seed the database with test records
    // AppUserBenchpress::factory()->count(2)->create([
    //   'app_user_id'  =>  1,
    // ]);
  
    // Create an instance of the DeadliftController
    $controller = new DeadliftController();
  
    $deadliftsId = AppUserDeadlift::first()->id;
   
    $request = new Request([
      'action' => 'trash',
      'is_bulk' => 0,
      'data_id' => $deadliftsId,
    ]);
  
    // Call the delete method
    $response = $controller->delete($request);
    $this->assertEquals(1, $response);
  
    // $total_books = AppUserBenchpress::count();
    // $this->assertEquals($total_books, 1);
  }
  
  /** @test */
public function testDeleteMethodShouldTrashMultipleRecord() {
    // Seed the database with test records
    // AppUserBenchpress::factory()->count(5)->create([
    //   'app_user_id'  =>  1,
    // ]);
  
    // Create an instance of the DeadliftController
    $controller = new DeadliftController();
    $total_deadlifts = AppUserDeadlift::count();
    $this->assertEquals($total_deadlifts, 7);
  
    $benchpressIds = AppUserDeadlift::pluck('id')->toArray();
    $deadliftIdsExceptFirstTwo = array_slice($benchpressIds, 2);
    $deadlifts_ids = implode(",", $deadliftIdsExceptFirstTwo);
    $request = new Request([
      'action' => 'trash',
      'is_bulk' => 1,
      'data_id' => $deadlifts_ids,
    ]);
  
    // Call the delete method
    $response = $controller->delete($request);
    $this->assertEquals(1, $response);
  
    $total_deadlifts = AppUserDeadlift::count();
    $this->assertEquals($total_deadlifts, 2);
  }
  
  /** @test */
public function testDeleteMethodShouldDeleteSingleRecord() {
    // Seed the database with test records
    // AppUserBenchpress::factory()->count(2)->create([
    //   'app_user_id'  =>  1,
    //   'deleted_at' => now(),
    // ]);
  
    // Create an instance of the DeadliftController
    $controller = new DeadliftController();
  
    $deadliftsId = AppUserDeadlift::withTrashed()->first()->id;
    $request = new Request([
      'action' => 'delete',
      'is_bulk' => 0,
      'data_id' => $deadliftsId,
    ]);
  
    // Call the delete method
    $response = $controller->delete($request);
    $this->assertEquals(1, $response);
  
    $total_deadlifts = AppUserDeadlift::withTrashed()->count();
    $this->assertEquals($total_deadlifts, 7);
  }

}
