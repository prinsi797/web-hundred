<?php

namespace Tests\Feature\Controllers\Admin;

use App\Http\Controllers\Admin\FeedbackController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Tests\TestCase;
use App\Models\User;
use App\Http\Requests\AppUserFeedbacksRequests\AddFeedbacks;
use App\Http\Requests\AppUserFeedbacksRequests\UpdateFeedbacks;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackControllerTest extends TestCase {
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
    // Invoke the index method of the FeedbackController
    $response = $this->get(route('admin.app_user_feedbacks.index'));

    // Assert that the response is successful
    $response->assertStatus(200);

    // Assert that the view returned is 'app_user_feedbacks.index'
    $response->assertViewIs('theme.app_user_feedbacks.index');
  }

  /** @test */
  public function testCreateMethodReturnManageView() {
    // Invoke the index method of the FeedbackController
    $response = $this->get(route('admin.app_user_feedbacks.create'));

    // Assert that the view returned is 'app_user_feedbacks.create'
    $response->assertViewIs('theme.app_user_feedbacks.manage');
  }

  public function testEditMethodReturnManageViewWithData() {
    $feedbacks = Feedback::first();
    $this->assertNotNull($feedbacks, 'No Feedback found in the database.');

    // Create an instance of the FeedbackController
    $controller = new FeedbackController();
    $request = new Request();
    $request['id'] = $feedbacks->id;

    // Call the edit method
    $response = $controller->edit($request);

    // Assert that the response is an instance of a View
    $this->assertInstanceOf(View::class, $response);

    // Assert that the view name is 'theme.app_user_feedbacks.manage'
    $this->assertEquals('theme.app_user_feedbacks.manage', $response->name());

    // Assert that the view data contains the necessary variables
    $this->assertEquals(route('admin.app_user_feedbacks.update'), $response->getData()['form_action']);
    $this->assertEquals(route('admin.app_user_feedbacks.index'), $response->getData()['cancel']);
    $this->assertEquals(1, $response->getData()['edit']);
    $this->assertEquals($feedbacks->toArray(), $response->getData()['data']->toArray());
  }

  public function testStoreMethodShouldStoreData()
  {
      // Mock the AddFeedbacks request object with fake data
      $request = new AddFeedbacks([
        'app_user_id' => 1,
        'message' => 'Good',
    ]);
  
      // Call the store method
      $controller = new FeedbackController();
      $response = $controller->store($request);
  
      // Assert that the response is a RedirectResponse
      $this->assertInstanceOf(RedirectResponse::class, $response);
  
      // Assert that the response redirects to the correct route
      $this->assertEquals(route('admin.app_user_feedbacks.index'), $response->getTargetUrl());
  
      // Assert that the session contains a success message
      $this->assertTrue(session()->has('success'));
  
      // Assert that the data is stored in the database
      $this->assertDatabaseHas('feedbacks', [
        'app_user_id' => 1,
        'message' => 'Good',
      ]);
    }

/** @test */
 public function testUpdateMethodShouldUpdateData() {
    $this->get(route('admin.app_user_feedbacks.edit'));
  
    Feedback::factory()->create([
      'id' => 4,
      'app_user_id' => 1,
      'message' => "osm",
    ]);
  
    // Mock the AddRequest object with fake data
    $request = new UpdateFeedbacks([
        'id' => 4,
        'app_user_id' => 1,
        'message' => "osm!!!",
    ]);
  
    // Call the update method
    $controller = new FeedbackController();
    $response = $controller->update($request);
  
    // Assert that the response is a RedirectResponse
    $this->assertInstanceOf(RedirectResponse::class, $response);
  
    // Assert that the response redirects to the correct route
    $this->assertEquals(route('admin.app_user_feedbacks.edit'), $response->getTargetUrl());
  
    // Assert that the session contains a success message
    $this->assertTrue(session()->has('success'));
  
    // // Assert that the data is stored in the database
    $this->assertDatabaseHas('feedbacks',[
        'id' => 4,
        'app_user_id' => 1,
        'message' => "osm!!!",
    ]);
  }

  /** @test */
public function testAjaxMethodShouldReturnAjaxViewWithData() {

    // Seed the database with test records
    Feedback::factory()->count(5)->create([
      'app_user_id'  =>  1,
    ]);
  
    // Create an instance of the FeedbackController
    $controller = new FeedbackController();
    $request = new Request([
      'page_number' => 1,
    ]);
  
    // Call the ajax method
    $response = $controller->ajax($request);
  
    // Assert that the response is an instance of a View
    $this->assertInstanceOf(View::class, $response);
  
    // Assert that the view name is 'theme.app_user_feedbacks.manage'
    $this->assertEquals('theme.app_user_feedbacks.ajax', $response->name());
  
    $offset = 0;
    $limit = 10;
    $current_page = 1;
    $total_records = Feedback::count();
    $feedbacks = Feedback::orderBy('id', 'desc')->get();
  
    $pagination = [
      "offset" => $offset,
      "total_records" => $total_records,
      "item_per_page" => $limit,
      "total_pages" => ceil($total_records / $limit),
      "current_page" => $current_page,
    ];
  
    // Assert that the view data contains the necessary variables
    $this->assertEquals(route('admin.app_user_feedbacks.edit'), $response->getData()['edit_route']);
    $this->assertEquals($current_page, $response->getData()['page_number']);
    $this->assertEquals($limit, $response->getData()['limit']);
    $this->assertEquals($offset, $response->getData()['offset']);
    $this->assertEquals($pagination, $response->getData()['pagination']);
    $this->assertEquals($feedbacks->toArray(), $response->getData()['data']->toArray());
  }
  
/** @test */
public function testDeleteMethodShouldTrashSingleRecord() {
   
    $controller = new FeedbackController();
    $feedbacksId = Feedback::first()->id;
    $request = new Request([
      'action' => 'trash',
      'is_bulk' => 0,
      'data_id' => $feedbacksId,
    ]);
  
    // Call the delete method
    $response = $controller->delete($request);
    $this->assertEquals(1, $response);
  }
  
  /** @test */
public function testDeleteMethodShouldTrashMultipleRecord() {
  
    $controller = new FeedbackController();
    $total_deadlifts = Feedback::count();
    $this->assertEquals($total_deadlifts, 7);
  
    $benchpressIds = Feedback::pluck('id')->toArray();
    $feedbacksIdsExceptFirstTwo = array_slice($benchpressIds, 2);
    $feedbacks_ids = implode(",", $feedbacksIdsExceptFirstTwo);
    $request = new Request([
      'action' => 'trash',
      'is_bulk' => 1,
      'data_id' => $feedbacks_ids,
    ]);
  
    // Call the delete method
    $response = $controller->delete($request);
    $this->assertEquals(1, $response);
  
    $total_feedbacks = Feedback::count();
    $this->assertEquals($total_feedbacks, 2);
  }
  
  /** @test */
public function testDeleteMethodShouldDeleteSingleRecord() {
   
    $controller = new FeedbackController();
  
    $deadliftsId = Feedback::withTrashed()->first()->id;
    $request = new Request([
      'action' => 'delete',
      'is_bulk' => 0,
      'data_id' => $deadliftsId,
    ]);
  
    // Call the delete method
    $response = $controller->delete($request);
    $this->assertEquals(1, $response);
  
    $total_feedbacks = Feedback::withTrashed()->count();
    $this->assertEquals($total_feedbacks, 7);
  }

}
