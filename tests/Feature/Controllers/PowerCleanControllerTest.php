<?php

namespace Tests\Feature\Controllers\Admin;

use App\Http\Controllers\Admin\PowerCleanController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Tests\TestCase;
use App\Models\User;
use App\Http\Requests\AppUserPowercleansRequests\AddPowerCleans;
use App\Http\Requests\AppUserPowercleansRequests\UpdatePowerCleans;
use App\Models\AppUserPowerclean;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PowerCleanControllerTest extends TestCase {
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
    // Invoke the index method of the PowercleanController
    $response = $this->get(route('admin.powercleans.index'));

    // Assert that the response is successful
    $response->assertStatus(200);

    // Assert that the view returned is 'powercleans.index'
    $response->assertViewIs('theme.powercleans.index');
  }

  /** @test */
  public function testCreateMethodReturnManageView() {
    // Invoke the index method of the PowercleanController
    $response = $this->get(route('admin.powercleans.create'));

    // Assert that the view returned is 'powercleans.create'
    $response->assertViewIs('theme.powercleans.manage');
  }

  public function testEditMethodReturnManageViewWithData() {
    $powercleans = AppUserPowerclean::first();
    $this->assertNotNull($powercleans, 'No Powercleans found in the database.');

    // Create an instance of the PowercleanController
    $controller = new PowerCleanController();
    $request = new Request();
    $request['id'] = $powercleans->id;

    // Call the edit method
    $response = $controller->edit($request);

    // Assert that the response is an instance of a View
    $this->assertInstanceOf(View::class, $response);

    // Assert that the view name is 'theme.powercleans.manage'
    $this->assertEquals('theme.powercleans.manage', $response->name());

    // Assert that the view data contains the necessary variables
    $this->assertEquals(route('admin.powercleans.update'), $response->getData()['form_action']);
    $this->assertEquals(route('admin.powercleans.index'), $response->getData()['cancel']);
    $this->assertEquals(1, $response->getData()['edit']);
    $this->assertEquals($powercleans->toArray(), $response->getData()['data']->toArray());
  }

  public function testStoreMethodShouldStoreData()
  {
      $request = new AddPowerCleans([
        'app_user_id' => 1,
        'date' => '2022-07-13',
        'power_clean' => '43',
    ]);
  
      // Call the store method
      $controller = new PowerCleanController();
      $response = $controller->store($request);
  
      // Assert that the response is a RedirectResponse
      $this->assertInstanceOf(RedirectResponse::class, $response);
      // Assert that the response redirects to the correct route
      $this->assertEquals(route('admin.powercleans.index'), $response->getTargetUrl());
  
      // Assert that the session contains a success message
      $this->assertTrue(session()->has('success'));
  
      // Assert that the data is stored in the database
      $this->assertDatabaseHas('app_user_powercleans', [
        'app_user_id' => 1,
        'date' => '2022-07-13',
        'power_clean' => '43',
      ]);
    }

/** @test */
 public function testUpdateMethodShouldUpdateData() {
    $this->get(route('admin.powercleans.edit'));
  
    AppUserPowerclean::factory()->create([
      'id' => 4,
      'app_user_id' => 1,
      'date' => '2022-07-13',
      'power_clean' => '430',
    ]);
  
    // Mock the AddRequest object with fake data
    $request = new UpdatePowerCleans([
      'id' => 4,
      'app_user_id' => 1,
      'date' => '2022-07-20',
      'power_clean' => '4300',
    ]);
  
    // Call the update method
    $controller = new PowerCleanController();
    $response = $controller->update($request);
  
    // Assert that the response is a RedirectResponse
    $this->assertInstanceOf(RedirectResponse::class, $response);
  
    // Assert that the response redirects to the correct route
    $this->assertEquals(route('admin.powercleans.edit'), $response->getTargetUrl());
  
    // Assert that the session contains a success message
    $this->assertTrue(session()->has('success'));
  
    // // Assert that the data is stored in the database
    $this->assertDatabaseHas('app_user_powercleans',[
      'app_user_id' => 1,
      'date' => "2022-07-20",
      'power_clean' => '4300',
    ]);
  }

/** @test */
public function testAjaxMethodShouldReturnAjaxViewWithData() {

    // Seed the database with test records
    AppUserPowerclean::factory()->count(5)->create([
      'app_user_id'  =>  1,
    ]);
  
    // Create an instance of the PowercleanController
    $controller = new PowerCleanController();
    $request = new Request([
      'page_number' => 1,
    ]);
  
    // Call the ajax method
    $response = $controller->ajax($request);
  
    // Assert that the response is an instance of a View
    $this->assertInstanceOf(View::class, $response);
  
    // Assert that the view name is 'theme.powercleans.manage'
    $this->assertEquals('theme.powercleans.ajax', $response->name());
  
    $offset = 0;
    $limit = 10;
    $current_page = 1;
    $total_records = AppUserPowerclean::count();
    $powercleans = AppUserPowerclean::orderBy('id', 'desc')->get();
  
    $pagination = [
      "offset" => $offset,
      "total_records" => $total_records,
      "item_per_page" => $limit,
      "total_pages" => ceil($total_records / $limit),
      "current_page" => $current_page,
    ];
  
    // Assert that the view data contains the necessary variables
    $this->assertEquals(route('admin.powercleans.edit'), $response->getData()['edit_route']);
    $this->assertEquals($current_page, $response->getData()['page_number']);
    $this->assertEquals($limit, $response->getData()['limit']);
    $this->assertEquals($offset, $response->getData()['offset']);
    $this->assertEquals($pagination, $response->getData()['pagination']);
    $this->assertEquals($powercleans->toArray(), $response->getData()['data']->toArray());
  }

  /** @test */
public function testDeleteMethodShouldTrashSingleRecord() {
    
    $controller = new PowerCleanController();
  
    $powercleansId = AppUserPowerclean::first()->id;
   
    $request = new Request([
      'action' => 'trash',
      'is_bulk' => 0,
      'data_id' => $powercleansId,
    ]);
  
    // Call the delete method
    $response = $controller->delete($request);
    $this->assertEquals(1, $response);
  
  }
  
  /** @test */
public function testDeleteMethodShouldTrashMultipleRecord() {
    // Create an instance of the BenchpressController
    $controller = new PowerCleanController();
    $total_powercleans = AppUserPowerclean::count();
    $this->assertEquals($total_powercleans, 7);
  
    $powercleansIds = AppUserPowerclean::pluck('id')->toArray();
    $powercleanIdsExceptFirstTwo = array_slice($powercleansIds, 2);
    $powercleans_ids = implode(",", $powercleanIdsExceptFirstTwo);
    $request = new Request([
      'action' => 'trash',
      'is_bulk' => 1,
      'data_id' => $powercleans_ids,
    ]);
  
    // Call the delete method
    $response = $controller->delete($request);
    $this->assertEquals(1, $response);
  
    $total_powercleans = AppUserPowerclean::count();
    $this->assertEquals($total_powercleans, 2);
  }
  
  /** @test */
public function testDeleteMethodShouldDeleteSingleRecord() {
   
    $controller = new PowerCleanController();
  
    $powercleansId = AppUserPowerclean::withTrashed()->first()->id;
    $request = new Request([
      'action' => 'delete',
      'is_bulk' => 0,
      'data_id' => $powercleansId,
    ]);
  
    // Call the delete method
    $response = $controller->delete($request);
    $this->assertEquals(1, $response);
  
    $total_powercleans = AppUserPowerclean::withTrashed()->count();
    $this->assertEquals($total_powercleans, 7);
  }
  
}
