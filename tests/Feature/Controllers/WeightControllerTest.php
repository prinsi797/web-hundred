<?php

namespace Tests\Feature\Controllers\Admin;

use App\Http\Controllers\Admin\WeightController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Tests\TestCase;
use App\Models\User;
use App\Http\Requests\AppUserWeightsRequests\AddWeights;
use App\Http\Requests\AppUserWeightsRequests\UpdateWeights;
use App\Models\AppUserWeight;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WeightControllerTest extends TestCase {
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
    $response = $this->get(route('admin.app_user_weights.index'));

    // Assert that the response is successful
    $response->assertStatus(200);

    // Assert that the view returned is 'benchpress.index'
    $response->assertViewIs('theme.app_user_weights.index');
  }

  /** @test */
  public function testCreateMethodReturnManageView() {
    // Invoke the index method of the BenchpressController
    $response = $this->get(route('admin.app_user_weights.create'));

    // Assert that the view returned is 'benchpress.create'
    $response->assertViewIs('theme.app_user_weights.manage');
  }

  public function testEditMethodReturnManageViewWithData() {
    $weights = AppUserWeight::first();
    $this->assertNotNull($weights, 'No Weight found in the database.');

    // Create an instance of the BenchpressController
    $controller = new WeightController();
    $request = new Request();
    $request['id'] = $weights->id;

    // Call the edit method
    $response = $controller->edit($request);

    // Assert that the response is an instance of a View
    $this->assertInstanceOf(View::class, $response);

    // Assert that the view name is 'theme.benchpress.manage'
    $this->assertEquals('theme.app_user_weights.manage', $response->name());

    // Assert that the view data contains the necessary variables
    $this->assertEquals(route('admin.app_user_weights.update'), $response->getData()['form_action']);
    $this->assertEquals(route('admin.app_user_weights.index'), $response->getData()['cancel']);
    $this->assertEquals(1, $response->getData()['edit']);
    $this->assertEquals($weights->toArray(), $response->getData()['data']->toArray());
  }

  public function testStoreMethodShouldStoreData()
  {
      // Mock the AddBenchpress request object with fake data
      $request = new AddWeights([
        'app_user_id' => 1,
        'date' => '2022-07-13',
        'weight' => '43',
    ]);
  
      // Call the store method
      $controller = new WeightController();
      $response = $controller->store($request);
  
      // Assert that the response is a RedirectResponse
      $this->assertInstanceOf(RedirectResponse::class, $response);
  
      // Assert that the response redirects to the correct route
      $this->assertEquals(route('admin.app_user_weights.index'), $response->getTargetUrl());
  
      // Assert that the session contains a success message
      $this->assertTrue(session()->has('success'));
  
      // Assert that the data is stored in the database
      $this->assertDatabaseHas('app_user_weights', [
        'app_user_id' => 1,
        'date' => '2022-07-13',
        'weight' => '43',
      ]);
    }

/** @test */
 public function testUpdateMethodShouldUpdateData() {
    $this->get(route('admin.app_user_weights.edit'));
  
    AppUserWeight::factory()->create([
      'id' => 4,
      'app_user_id' => 1,
      'date' => '2022-07-13',
      'weight' => '43',
    ]);
  
    // Mock the AddRequest object with fake data
    $request = new UpdateWeights([
      'id' => 4,
      'app_user_id' => 1,
      'date' => '2022-07-20',
      'weight' => '45',
    ]);
  
    // Call the update method
    $controller = new WeightController();
    $response = $controller->update($request);
  
    // Assert that the response is a RedirectResponse
    $this->assertInstanceOf(RedirectResponse::class, $response);
  
    // Assert that the response redirects to the correct route
    $this->assertEquals(route('admin.app_user_weights.edit'), $response->getTargetUrl());
  
    // Assert that the session contains a success message
    $this->assertTrue(session()->has('success'));
  
    // // Assert that the data is stored in the database
    $this->assertDatabaseHas('app_user_weights',[
      'app_user_id' => 1,
      'date' => "2022-07-20",
      'weight' => '45',
    ]);
  }

  /** @test */
public function testAjaxMethodShouldReturnAjaxViewWithData() {

    // Seed the database with test records
    AppUserWeight::factory()->count(5)->create([
      'app_user_id'  =>  1,
    ]);
  
    // Create an instance of the BenchpressController
    $controller = new WeightController();
    $request = new Request([
      'page_number' => 1,
    ]);
  
    // Call the ajax method
    $response = $controller->ajax($request);
  
    // Assert that the response is an instance of a View
    $this->assertInstanceOf(View::class, $response);
  
    // Assert that the view name is 'theme.benchpress.manage'
    $this->assertEquals('theme.app_user_weights.ajax', $response->name());
  
    $offset = 0;
    $limit = 10;
    $current_page = 1;
    $total_records = AppUserWeight::count();
    $weights = AppUserWeight::orderBy('id', 'desc')->get();
  
    $pagination = [
      "offset" => $offset,
      "total_records" => $total_records,
      "item_per_page" => $limit,
      "total_pages" => ceil($total_records / $limit),
      "current_page" => $current_page,
    ];
  
    // Assert that the view data contains the necessary variables
    $this->assertEquals(route('admin.app_user_weights.edit'), $response->getData()['edit_route']);
    $this->assertEquals($current_page, $response->getData()['page_number']);
    $this->assertEquals($limit, $response->getData()['limit']);
    $this->assertEquals($offset, $response->getData()['offset']);
    $this->assertEquals($pagination, $response->getData()['pagination']);
    $this->assertEquals($weights->toArray(), $response->getData()['data']->toArray());
  }
  
/** @test */
public function testDeleteMethodShouldTrashSingleRecord() {
    // Seed the database with test records
    // AppUserBenchpress::factory()->count(2)->create([
    //   'app_user_id'  =>  1,
    // ]);
  
    // Create an instance of the BenchpressController
    $controller = new WeightController();
  
    $weightsId = AppUserWeight::first()->id;
   
    $request = new Request([
      'action' => 'trash',
      'is_bulk' => 0,
      'data_id' => $weightsId,
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
    $controller = new WeightController();
    $total_weights = AppUserWeight::count();
    $this->assertEquals($total_weights, 7);
  
    $benchpressIds = AppUserWeight::pluck('id')->toArray();
    $weightsIdsExceptFirstTwo = array_slice($benchpressIds, 2);
    $weights_ids = implode(",", $weightsIdsExceptFirstTwo);
    $request = new Request([
      'action' => 'trash',
      'is_bulk' => 1,
      'data_id' => $weights_ids,
    ]);
  
    // Call the delete method
    $response = $controller->delete($request);
    $this->assertEquals(1, $response);
  
    $total_weights = AppUserWeight::count();
    $this->assertEquals($total_weights, 2);
  }
  
  /** @test */
public function testDeleteMethodShouldDeleteSingleRecord() {
    // Seed the database with test records
    // AppUserBenchpress::factory()->count(2)->create([
    //   'app_user_id'  =>  1,
    //   'deleted_at' => now(),
    // ]);
  
    // Create an instance of the BenchpressController
    $controller = new WeightController();
  
    $weightsId = AppUserWeight::withTrashed()->first()->id;
    $request = new Request([
      'action' => 'delete',
      'is_bulk' => 0,
      'data_id' => $weightsId,
    ]);
  
    // Call the delete method
    $response = $controller->delete($request);
    $this->assertEquals(1, $response);
  
    $total_weights = AppUserWeight::withTrashed()->count();
    $this->assertEquals($total_weights, 7);
  }

}
