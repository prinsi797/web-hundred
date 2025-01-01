<?php

namespace Tests\Feature\Controllers\Admin;

use App\Http\Controllers\Admin\BenchPressController;
use App\Models\AppUserBenchpress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Tests\TestCase;
use App\Models\User;
use App\Http\Requests\AppUserBenchpressRequests\AddBenchpress;
use App\Http\Requests\AppUserBenchpressRequests\UpdateBenchpress;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BenchPressControllerTest extends TestCase {
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
    // Invoke the index method of the BenchpressController
    $response = $this->get(route('admin.benchpress.index'));

    // Assert that the response is successful
    $response->assertStatus(200);

    // Assert that the view returned is 'benchpress.index'
    $response->assertViewIs('theme.benchpress.index');
  }

  /** @test */
  public function testCreateMethodReturnManageView() {
    // Invoke the index method of the BenchpressController
    $response = $this->get(route('admin.benchpress.create'));

    // Assert that the view returned is 'benchpress.create'
    $response->assertViewIs('theme.benchpress.manage');
  }

  public function testEditMethodReturnManageViewWithData() {
    $benchpress = AppUserBenchpress::first();
    $this->assertNotNull($benchpress, 'No Benchpress found in the database.');

    // Create an instance of the BenchpressController
    $controller = new BenchPressController();
    $request = new Request();
    $request['id'] = $benchpress->id;

    // Call the edit method
    $response = $controller->edit($request);

    // Assert that the response is an instance of a View
    $this->assertInstanceOf(View::class, $response);

    // Assert that the view name is 'theme.benchpress.manage'
    $this->assertEquals('theme.benchpress.manage', $response->name());

    // Assert that the view data contains the necessary variables
    $this->assertEquals(route('admin.benchpress.update'), $response->getData()['form_action']);
    $this->assertEquals(route('admin.benchpress.index'), $response->getData()['cancel']);
    $this->assertEquals(1, $response->getData()['edit']);
    $this->assertEquals($benchpress->toArray(), $response->getData()['data']->toArray());
  }

  public function testStoreMethodShouldStoreData()
{
    // Mock the AddBenchpress request object with fake data
    $request = new AddBenchpress([
      'app_user_id' => 1,
      'date' => '2022-07-12',
      'bench_press' => '43',
  ]);

    // Call the store method
    $controller = new BenchPressController();
    $response = $controller->store($request);

    // Assert that the response is a RedirectResponse
    $this->assertInstanceOf(RedirectResponse::class, $response);

    // Assert that the response redirects to the correct route
    $this->assertEquals(route('admin.benchpress.index'), $response->getTargetUrl());

    // Assert that the session contains a success message
    $this->assertTrue(session()->has('success'));

    // Assert that the data is stored in the database
    $this->assertDatabaseHas('app_user_benchpresses', [
        'app_user_id' => 1,
        'date' => '2022-07-12',
        'bench_press' => '43',
    ]);
  }

 /** @test */
 public function testUpdateMethodShouldUpdateData() {
  $this->get(route('admin.benchpress.edit'));

  AppUserBenchpress::factory()->create([
    'id' => 4,
    'app_user_id' => 1,
    'date' => '2022-07-13',
    'bench_press' => '430',
  ]);

  // Mock the AddRequest object with fake data
  $request = new UpdateBenchpress([
    'id' => 4,
    'app_user_id' => 1,
    'date' => '2022-07-13',
    'bench_press' => '4300',
  ]);

  // Call the update method
  $controller = new BenchPressController();
  $response = $controller->update($request);

  // Assert that the response is a RedirectResponse
  $this->assertInstanceOf(RedirectResponse::class, $response);

  // Assert that the response redirects to the correct route
  $this->assertEquals(route('admin.benchpress.edit'), $response->getTargetUrl());

  // Assert that the session contains a success message
  $this->assertTrue(session()->has('success'));

  // Assert that the data is stored in the database
  $this->assertDatabaseHas('app_user_benchpresses', [
    'app_user_id' => 1,
    'date' => '2022-07-13',
    'bench_press' => '4300',
  ]);
}


/** @test */
public function testAjaxMethodShouldReturnAjaxViewWithData() {

  // Seed the database with test records
  AppUserBenchpress::factory()->count(5)->create([
    'app_user_id'  =>  1,
  ]);

  // Create an instance of the BenchpressController
  $controller = new BenchPressController();
  $request = new Request([
    'page_number' => 1,
  ]);

  // Call the ajax method
  $response = $controller->ajax($request);

  // Assert that the response is an instance of a View
  $this->assertInstanceOf(View::class, $response);

  // Assert that the view name is 'theme.benchpress.manage'
  $this->assertEquals('theme.benchpress.ajax', $response->name());

  $offset = 0;
  $limit = 10;
  $current_page = 1;
  $total_records = AppUserBenchpress::count();
  $benchpress = AppUserBenchpress::orderBy('id', 'desc')->get();

  $pagination = [
    "offset" => $offset,
    "total_records" => $total_records,
    "item_per_page" => $limit,
    "total_pages" => ceil($total_records / $limit),
    "current_page" => $current_page,
  ];

  // Assert that the view data contains the necessary variables
  $this->assertEquals(route('admin.benchpress.edit'), $response->getData()['edit_route']);
  $this->assertEquals($current_page, $response->getData()['page_number']);
  $this->assertEquals($limit, $response->getData()['limit']);
  $this->assertEquals($offset, $response->getData()['offset']);
  $this->assertEquals($pagination, $response->getData()['pagination']);
  $this->assertEquals($benchpress->toArray(), $response->getData()['data']->toArray());
}

/** @test */
public function testDeleteMethodShouldTrashSingleRecord() {
  // Seed the database with test records
  // AppUserBenchpress::factory()->count(2)->create([
  //   'app_user_id'  =>  1,
  // ]);

  // Create an instance of the BenchpressController
  $controller = new BenchPressController();

  $benchpressId = AppUserBenchpress::first()->id;
 
  $request = new Request([
    'action' => 'trash',
    'is_bulk' => 0,
    'data_id' => $benchpressId,
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

  // Create an instance of the BenchpressController
  $controller = new BenchPressController();
  $total_benchpress = AppUserBenchpress::count();
  $this->assertEquals($total_benchpress, 7);

  $benchpressIds = AppUserBenchpress::pluck('id')->toArray();
  $benchpressIdsExceptFirstTwo = array_slice($benchpressIds, 2);
  $benchpress_ids = implode(",", $benchpressIdsExceptFirstTwo);
  $request = new Request([
    'action' => 'trash',
    'is_bulk' => 1,
    'data_id' => $benchpress_ids,
  ]);

  // Call the delete method
  $response = $controller->delete($request);
  $this->assertEquals(1, $response);

  $total_benchpress = AppUserBenchpress::count();
  $this->assertEquals($total_benchpress, 2);
}

/** @test */
public function testDeleteMethodShouldDeleteSingleRecord() {
  // Seed the database with test records
  // AppUserBenchpress::factory()->count(2)->create([
  //   'app_user_id'  =>  1,
  //   'deleted_at' => now(),
  // ]);

  // Create an instance of the BenchpressController
  $controller = new BenchPressController();

  $benchpressId = AppUserBenchpress::withTrashed()->first()->id;
  $request = new Request([
    'action' => 'delete',
    'is_bulk' => 0,
    'data_id' => $benchpressId,
  ]);

  // Call the delete method
  $response = $controller->delete($request);
  $this->assertEquals(1, $response);

  $total_benchpress = AppUserBenchpress::withTrashed()->count();
  $this->assertEquals($total_benchpress, 7);
}

// /** @test */
// public function testDeleteMethodShouldDeleteMultipleRecord() {
//   // Seed the database with test records
//   AppUserBenchpress::factory()->count(5)->create([
//     'app_user_id'  => 1,
//     'deleted_at' => now(),
//   ]);

//   // Create an instance of the BenchpressController
//   $controller = new BenchPressController();
//   $total_benchpress = AppUserBenchpress::withTrashed()->count();
//   $this->assertEquals($total_benchpress, 13);

//   $benchpressIds = AppUserBenchpress::withTrashed()->pluck('id')->toArray();
//   $benchpressIdsExceptFirstTwo = array_slice($benchpressIds, 2);
//   $benchpress_ids = implode(",", $benchpressIdsExceptFirstTwo);
//   $request = new Request([
//     'action' => 'delete',
//     'is_bulk' => 1,
//     'data_id' => $benchpress_ids,
//   ]);

//   // Call the delete method
//   $response = $controller->delete($request);
//   $this->assertEquals(3, $response);

//   $total_benchpress = AppUserBenchpress::withTrashed()->count();
//   $this->assertEquals($total_benchpress, 2);
// }

}
