<?php

namespace Tests\Feature\Controllers\Admin;

use App\Http\Controllers\Admin\SquatController;
use App\Http\Requests\AppUserSquatsRequests\AddSquats;
use App\Http\Requests\AppUserSquatsRequests\UpdateSquats;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Tests\TestCase;
use App\Models\User;
use App\Models\AppUserSquat;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SquatControllerTest extends TestCase
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
        // Invoke the index method of the SchoolController
        $response = $this->get(route('admin.squats.index'));

        // Assert that the response is successful
        $response->assertStatus(200);

        // Assert that the view returned is 'squats.index'
        $response->assertViewIs('theme.squats.index');
    }

    /** @test */
    public function testCreateMethodReturnManageView()
    {
        // Invoke the index method of the SchoolController
        $response = $this->get(route('admin.squats.create'));

        // Assert that the view returned is 'squats.create'
        $response->assertViewIs('theme.squats.manage');
    }

    public function testEditMethodReturnManageViewWithData()
    {
        $squats = AppUserSquat::first();
        $this->assertNotNull($squats, 'No Squats found in the database.');

        // Create an instance of the SchoolController
        $controller = new SquatController();
        $request = new Request();
        $request['id'] = $squats->id;

        // Call the edit method
        $response = $controller->edit($request);

        // Assert that the response is an instance of a View
        $this->assertInstanceOf(View::class, $response);

        // Assert that the view name is 'theme.squats.manage'
        $this->assertEquals('theme.squats.manage', $response->name());

        // Assert that the view data contains the necessary variables
        $this->assertEquals(route('admin.squats.update'), $response->getData()['form_action']);
        $this->assertEquals(route('admin.squats.index'), $response->getData()['cancel']);
        $this->assertEquals(1, $response->getData()['edit']);
        $this->assertEquals($squats->toArray(), $response->getData()['data']->toArray());
    }

    public function testStoreMethodShouldStoreData()
    {
        $request = new AddSquats([
            'app_user_id' => 2,
            'date' => '2023-04-12',
            'squat' => 23,
        ]);

        // Call the store method
        $controller = new SquatController();
        $response = $controller->store($request);

        // Assert that the response is a RedirectResponse
        $this->assertInstanceOf(RedirectResponse::class, $response);
        // Assert that the response redirects to the correct route
        $this->assertEquals(route('admin.squats.index'), $response->getTargetUrl());

        // Assert that the session contains a success message
        $this->assertTrue(session()->has('success'));

        // Assert that the data is stored in the database
        $this->assertDatabaseHas('app_user_squats', [
            'app_user_id' => 2,
            'date' => '2023-04-12',
            'squat' => 23,
        ]);
    }

    /** @test */
    public function testUpdateMethodShouldUpdateData()
    {
        $this->get(route('admin.squats.edit'));

        AppUserSquat::factory()->create([
            'id' => 9,
            'app_user_id' => 1,
            'date' => '2023-04-12',
            'squat' => 230,
        ]);

        // Mock the AddRequest object with fake data
        $request = new UpdateSquats([
            'id' => 9,
            'app_user_id' => 1,
            'date' => '2023-04-12',
            'squat' => 2300,
        ]);

        // Call the update method
        $controller = new SquatController();
        $response = $controller->update($request);

        // Assert that the response is a RedirectResponse
        $this->assertInstanceOf(RedirectResponse::class, $response);

        // Assert that the response redirects to the correct route
        $this->assertEquals(route('admin.squats.edit'), $response->getTargetUrl());

        // Assert that the session contains a success message
        $this->assertTrue(session()->has('success'));

        // // Assert that the data is stored in the database
        $this->assertDatabaseHas('app_user_squats', [
            'app_user_id' => 1,
            'date' => '2023-04-12',
            'squat' => 2300,
        ]);
    }

    /** @test */
    public function testAjaxMethodShouldReturnAjaxViewWithData()
    {

        // Seed the database with test records
        // School::factory()->count(5)->create([
        //   'app_user_id'  =>  1,
        // ]);

        // Create an instance of the SquatController
        $controller = new squatController();
        $request = new Request([
            'page_number' => 1,
        ]);

        // Call the ajax method
        $response = $controller->ajax($request);

        // Assert that the response is an instance of a View
        $this->assertInstanceOf(View::class, $response);

        // Assert that the view name is 'theme.squats.manage'
        $this->assertEquals('theme.squats.ajax', $response->name());

        $offset = 0;
        $limit = 10;
        $current_page = 1;
        $total_records = AppUserSquat::count();
        $squats = AppUserSquat::orderBy('id', 'desc')->get();

        $pagination = [
            "offset" => $offset,
            "total_records" => $total_records,
            "item_per_page" => $limit,
            "total_pages" => ceil($total_records / $limit),
            "current_page" => $current_page,
        ];

        // Assert that the view data contains the necessary variables
        $this->assertEquals(route('admin.squats.edit'), $response->getData()['edit_route']);
        $this->assertEquals($current_page, $response->getData()['page_number']);
        $this->assertEquals($limit, $response->getData()['limit']);
        $this->assertEquals($offset, $response->getData()['offset']);
        $this->assertEquals($pagination, $response->getData()['pagination']);
        $this->assertEquals($squats->toArray(), $response->getData()['data']->toArray());
    }

    /** @test */
    public function testDeleteMethodShouldTrashSingleRecord()
    {

        $controller = new SquatController();

        $squatsId = AppUserSquat::first()->id;

        $request = new Request([
            'action' => 'trash',
            'is_bulk' => 0,
            'data_id' => $squatsId,
        ]);

        // Call the delete method
        $response = $controller->delete($request);
        $this->assertEquals(1, $response);
    }

    /** @test */
    public function testDeleteMethodShouldTrashMultipleRecord()
    {
        // Create an instance of the BenchpressController
        AppUserSquat::factory()->count(5)->create([
            'app_user_id' => 2,
            'date' => '2023-04-12',
            'squat' => 4300,
        ]);

        $controller = new SquatController();
        $total_squats = AppUserSquat::count();
        $this->assertEquals($total_squats, 7);

        $squatIds = AppUserSquat::pluck('id')->toArray();
        $squatIdsExceptFirstTwo = array_slice($squatIds, 2);
        $squats_ids = implode(",", $squatIdsExceptFirstTwo);
        $request = new Request([
            'action' => 'trash',
            'is_bulk' => 1,
            'data_id' => $squats_ids,
        ]);

        // Call the delete method
        $response = $controller->delete($request);
        $this->assertEquals(1, $response);

        $total_squats = AppUserSquat::count();
        $this->assertEquals($total_squats, 2);
    }

    /** @test */
    public function testDeleteMethodShouldDeleteSingleRecord()
    {
        $controller = new SquatController();

        $squatsId = AppUserSquat::withTrashed()->first()->id;
        $request = new Request([
            'action' => 'delete',
            'is_bulk' => 0,
            'data_id' => $squatsId,
        ]);

        // Call the delete method
        $response = $controller->delete($request);
        $this->assertEquals(1, $response);

        $total_squats = AppUserSquat::withTrashed()->count();
        $this->assertEquals($total_squats, 7);
    }
}
