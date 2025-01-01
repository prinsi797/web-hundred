<?php

namespace Tests\Feature\Controllers\Admin;

use App\Http\Controllers\Admin\HeightController;
use App\Http\Requests\AppUserHeightsRequests\AddHeights;
use App\Http\Requests\AppUserHeightsRequests\UpdateHeights;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Tests\TestCase;
use App\Models\User;
use App\Models\AppUserHeight;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HeightControllerTest extends TestCase {
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
    // Invoke the index method of the HeightController
    $response = $this->get(route('admin.app_user_heights.index'));

    // Assert that the response is successful
    $response->assertStatus(200);

    // Assert that the view returned is 'app_user_heights.index'
    $response->assertViewIs('theme.app_user_heights.index');
  }

  /** @test */
  public function testCreateMethodReturnManageView() {
    // Invoke the index method of the HeightController
    $response = $this->get(route('admin.app_user_heights.create'));

    // Assert that the view returned is 'app_user_heights.create'
    $response->assertViewIs('theme.app_user_heights.manage');
  }

  public function testEditMethodReturnManageViewWithData() {
    $heights = AppUserHeight::first();
    $this->assertNotNull($heights, 'No Heights found in the database.');

    // Create an instance of the PowercleanController
    $controller = new HeightController();
    $request = new Request();
    $request['id'] = $heights->id;

    // Call the edit method
    $response = $controller->edit($request);

    // Assert that the response is an instance of a View
    $this->assertInstanceOf(View::class, $response);

    // Assert that the view name is 'theme.app_user_heights.manage'
    $this->assertEquals('theme.app_user_heights.manage', $response->name());

    // Assert that the view data contains the necessary variables
    $this->assertEquals(route('admin.app_user_heights.update'), $response->getData()['form_action']);
    $this->assertEquals(route('admin.app_user_heights.index'), $response->getData()['cancel']);
    $this->assertEquals(1, $response->getData()['edit']);
    $this->assertEquals($heights->toArray(), $response->getData()['data']->toArray());
  }

  public function testStoreMethodShouldStoreData()
  {
      $request = new AddHeights([
        'app_user_id' => 1,
        'date' => '2022-07-13',
        'fit' => '43',
        'inch' => '4',
    ]);
  
      // Call the store method
      $controller = new HeightController();
      $response = $controller->store($request);
  
      // Assert that the response is a RedirectResponse
      $this->assertInstanceOf(RedirectResponse::class, $response);
  
      // Assert that the response redirects to the correct route
      $this->assertEquals(route('admin.app_user_heights.index'), $response->getTargetUrl());
  
      // Assert that the session contains a success message
      $this->assertTrue(session()->has('success'));
  
      // Assert that the data is stored in the database
      $this->assertDatabaseHas('app_user_heights', [
        'app_user_id' => 1,
        'date' => '2022-07-13',
        'fit' => '43',
        'inch' => '4',
      ]);
    }

/** @test */
 public function testUpdateMethodShouldUpdateData() {
    $this->get(route('admin.app_user_heights.edit'));
  
    AppUserHeight::factory()->create([
      'id' => 4,
      'app_user_id' => 1,
      'date' => '2022-07-13',
      'fit' => '43',
      'inch' => '4',
    ]);
  
    // Mock the AddRequest object with fake data
    $request = new UpdateHeights([
      'id' => 4,
      'app_user_id' => 1,
      'date' => '2022-07-20',
      'fit' => '44',
      'inch' => '3',
    ]);
  
    // Call the update method
    $controller = new HeightController();
    $response = $controller->update($request);
  
    // Assert that the response is a RedirectResponse
    $this->assertInstanceOf(RedirectResponse::class, $response);
  
    // Assert that the response redirects to the correct route
    $this->assertEquals(route('admin.app_user_heights.edit'), $response->getTargetUrl());
  
    // Assert that the session contains a success message
    $this->assertTrue(session()->has('success'));
  
    // // Assert that the data is stored in the database
    $this->assertDatabaseHas('app_user_heights',[
      'app_user_id' => 1,
      'date' => "2022-07-20",
      'fit' => '44',
      'inch' => '3',
    ]);
  }

/** @test */
public function testAjaxMethodShouldReturnAjaxViewWithData() {

    // Seed the database with test records
    AppUserHeight::factory()->count(5)->create([
      'app_user_id'  =>  1,
    ]);
  
    // Create an instance of the PowercleanController
    $controller = new HeightController();
    $request = new Request([
      'page_number' => 1,
    ]);
  
    // Call the ajax method
    $response = $controller->ajax($request);
  
    // Assert that the response is an instance of a View
    $this->assertInstanceOf(View::class, $response);
  
    // Assert that the view name is 'theme.powercleans.manage'
    $this->assertEquals('theme.app_user_heights.ajax', $response->name());
  
    $offset = 0;
    $limit = 10;
    $current_page = 1;
    $total_records = AppUserHeight::count();
    $heights = AppUserHeight::orderBy('id', 'desc')->get();
  
    $pagination = [
      "offset" => $offset,
      "total_records" => $total_records,
      "item_per_page" => $limit,
      "total_pages" => ceil($total_records / $limit),
      "current_page" => $current_page,
    ];
  
    // Assert that the view data contains the necessary variables
    $this->assertEquals(route('admin.app_user_heights.edit'), $response->getData()['edit_route']);
    $this->assertEquals($current_page, $response->getData()['page_number']);
    $this->assertEquals($limit, $response->getData()['limit']);
    $this->assertEquals($offset, $response->getData()['offset']);
    $this->assertEquals($pagination, $response->getData()['pagination']);
    $this->assertEquals($heights->toArray(), $response->getData()['data']->toArray());
  }

  /** @test */
public function testDeleteMethodShouldTrashSingleRecord() {
    
    $controller = new HeightController();
  
    $heightsId = AppUserHeight::first()->id;
   
    $request = new Request([
      'action' => 'trash',
      'is_bulk' => 0,
      'data_id' => $heightsId,
    ]);
  
    // Call the delete method
    $response = $controller->delete($request);
    $this->assertEquals(1, $response);
  
  }
  
  /** @test */
public function testDeleteMethodShouldTrashMultipleRecord() {
    // Create an instance of the BenchpressController
    $controller = new HeightController();
    $total_heights = AppUserHeight::count();
    $this->assertEquals($total_heights, 7);
  
    $heightIds = AppUserHeight::pluck('id')->toArray();
    $heightsIdsExceptFirstTwo = array_slice($heightIds, 2);
    $heights_ids = implode(",", $heightsIdsExceptFirstTwo);
    $request = new Request([
      'action' => 'trash',
      'is_bulk' => 1,
      'data_id' => $heights_ids,
    ]);
  
    // Call the delete method
    $response = $controller->delete($request);
    $this->assertEquals(1, $response);
  
    $total_heights = AppUserHeight::count();
    $this->assertEquals($total_heights, 2);
  }
  
  /** @test */
public function testDeleteMethodShouldDeleteSingleRecord() {
   
    $controller = new HeightController();
  
    $heightsId = AppUserHeight::withTrashed()->first()->id;
    $request = new Request([
      'action' => 'delete',
      'is_bulk' => 0,
      'data_id' => $heightsId,
    ]);
  
    // Call the delete method
    $response = $controller->delete($request);
    $this->assertEquals(1, $response);
  
    $total_heights = AppUserHeight::withTrashed()->count();
    $this->assertEquals($total_heights, 7);
  }
  
}
