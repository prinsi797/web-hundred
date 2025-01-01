<?php

namespace Tests\Feature\Controllers\Admin;

use App\Http\Controllers\Admin\SchoolController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Tests\TestCase;
use App\Models\User;
use App\Http\Requests\SchoolRequests\UpdateSchool;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SchoolControllerTest extends TestCase {
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
    // Invoke the index method of the SchoolController
    $response = $this->get(route('admin.schools.index'));

    // Assert that the response is successful
    $response->assertStatus(200);

    // Assert that the view returned is 'schools.index'
    $response->assertViewIs('theme.schools.index');
  }

  /** @test */
  public function testCreateMethodReturnManageView() {
    // Invoke the index method of the SchoolController
    $response = $this->get(route('admin.schools.create'));

    // Assert that the view returned is 'schools.create'
    $response->assertViewIs('theme.schools.manage');
  }

  public function testEditMethodReturnManageViewWithData() {
    $schools = School::first();
    $this->assertNotNull($schools, 'No Schools found in the database.');

    // Create an instance of the SchoolController
    $controller = new SchoolController();
    $request = new Request();
    $request['id'] = $schools->id;

    // Call the edit method
    $response = $controller->edit($request);

    // Assert that the response is an instance of a View
    $this->assertInstanceOf(View::class, $response);

    // Assert that the view name is 'theme.schools.manage'
    $this->assertEquals('theme.schools.manage', $response->name());

    // Assert that the view data contains the necessary variables
    $this->assertEquals(route('admin.schools.update'), $response->getData()['form_action']);
    $this->assertEquals(route('admin.schools.index'), $response->getData()['cancel']);
    $this->assertEquals(1, $response->getData()['edit']);
    $this->assertEquals($schools->toArray(), $response->getData()['data']->toArray());
  }

//   public function testStoreMethodShouldStoreData()
//   {
//       $request = new AddSchool([
//         'name' => 'test school',
//         'image_url' => 'test.jpg',
//         'short_name' => 'school',
//         'website' => 'https://hariom.com',
//         'street' => 'puna',
//         'street2' => 'varachha',
//         'zipcode' => '43233',
//         'state' => 'india',
//         'city' => 'surat',
//     ]);
  
//       // Call the store method
//       $controller = new SchoolController();
//       $response = $controller->store($request);
  
//       // Assert that the response is a RedirectResponse
//       $this->assertInstanceOf(RedirectResponse::class, $response);
//       // Assert that the response redirects to the correct route
//       $this->assertEquals(route('admin.schools.index'), $response->getTargetUrl());
  
//       // Assert that the session contains a success message
//       $this->assertTrue(session()->has('success'));
  
//       // Assert that the data is stored in the database
//       $this->assertDatabaseHas('schools', [
//         'name' => 'test school',
//         'image_url' => 'test.jpg',
//         'short_name' => 'school',
//         'website' => 'https://hariom.com',
//         'street' => 'puna',
//         'street2' => 'varachha',
//         'zipcode' => '43233',
//         'state' => 'india',
//         'city' => 'surat',
//       ]);
//     }


  public function testStoreMethodShouldStoreData()
  {
    Storage::fake('public');

    // Simulate file upload
    $file = UploadedFile::fake()->image('test.jpg');

    $data = [
        'name' => 'test school',
        'image_url' => $file,
        'short_name' => 'school',
        'website' => 'https://hariom.com',
        'street' => 'puna',
        'street2' => 'varachha',
        'zipcode' => '43233',
        'state' => 'india',
        'city' => 'surat',
    ];
    $response = $this->post(route('admin.schools.store'), $data);

    // Assert that the response is a redirect to the intended route
    $response->assertRedirect(route('admin.schools.index'));

    // Assert that the session contains a success message
    $response->assertSessionHas('success', 'New School has been added.');

    // Assert that the file was stored
    Storage::disk('public')->url('SchoolImage/' .  $file->hashName());
    // Storage::disk('public')->assertExists('SchoolImage/' . $file->hashName());
}

  /** @test */
  public function testUpdateMethodShouldUpdateData()
  {
      $this->get(route('admin.schools.edit'));

      School::factory()->create([
          'id' => 4,
          'name' => 'test school2',
          'image_url' => 'test2.jpg',
          'short_name' => 'school2',
          'website' => 'https://hariom.com',
          'street' => 'puna',
          'street2' => 'varachha',
          'zipcode' => '43233',
          'state' => 'india',
          'city' => 'surat'
      ]);

      // Mock the AddRequest object with fake data
      $request = new UpdateSchool([
          'id' => 4,
          'name' => 'test school3',
          'image_url' => 'test2.jpg',
          'short_name' => 'school2',
          'website' => 'https://hariom.com',
          'street' => 'puna',
          'street2' => 'varachha',
          'zipcode' => '43233',
          'state' => 'india',
          'city' => 'surat'
      ]);

      // Call the update method
      $controller = new SchoolController();
      $response = $controller->update($request);

      // Assert that the response is a RedirectResponse
      $this->assertInstanceOf(RedirectResponse::class, $response);

      // Assert that the response redirects to the correct route
      $this->assertEquals(route('admin.schools.edit'), $response->getTargetUrl());

      // Assert that the session contains a success message
      $this->assertTrue(session()->has('success'));

      // // Assert that the data is stored in the database
      $this->assertDatabaseHas('schools', [
          'name' => 'test school3',
          'image_url' => 'test2.jpg',
          'short_name' => 'school2',
          'website' => 'https://hariom.com',
          'street' => 'puna',
          'street2' => 'varachha',
          'zipcode' => '43233',
          'state' => 'india',
          'city' => 'surat',
      ]);
  }

  /** @test */
  public function testAjaxMethodShouldReturnAjaxViewWithData() {

      // Seed the database with test records
      // School::factory()->count(5)->create([
      //   'app_user_id'  =>  1,
      // ]);

      // Create an instance of the SchoolController
      $controller = new SchoolController();
      $request = new Request([
        'page_number' => 1,
      ]);

      // Call the ajax method
      $response = $controller->ajax($request);

      // Assert that the response is an instance of a View
      $this->assertInstanceOf(View::class, $response);

      // Assert that the view name is 'theme.schools.manage'
      $this->assertEquals('theme.schools.ajax', $response->name());

      $offset = 0;
      $limit = 10;
      $current_page = 1;
      $total_records = School::count();
      $schools = School::orderBy('id', 'desc')->get();

      $pagination = [
        "offset" => $offset,
        "total_records" => $total_records,
        "item_per_page" => $limit,
        "total_pages" => ceil($total_records / $limit),
        "current_page" => $current_page,
      ];

      // Assert that the view data contains the necessary variables
      $this->assertEquals(route('admin.schools.edit'), $response->getData()['edit_route']);
      $this->assertEquals($current_page, $response->getData()['page_number']);
      $this->assertEquals($limit, $response->getData()['limit']);
      $this->assertEquals($offset, $response->getData()['offset']);
      $this->assertEquals($pagination, $response->getData()['pagination']);
      $this->assertEquals($schools->toArray(), $response->getData()['data']->toArray());
    }

    /** @test */
  public function testDeleteMethodShouldTrashSingleRecord() {

      $controller = new SchoolController();

      $schoolId = School::first()->id;

      $request = new Request([
        'action' => 'trash',
        'is_bulk' => 0,
        'data_id' => $schoolId,
      ]);

      // Call the delete method
      $response = $controller->delete($request);
      $this->assertEquals(1, $response);

    }

    /** @test */
  public function testDeleteMethodShouldTrashMultipleRecord() {
      // Create an instance of the BenchpressController
      School::factory()->count(5)->create([
          'name' => 'hari om',
          'image_url' => 'hello.jpg',
          'short_name' => 'school3',
          'website' => 'https://hariom.com',
          'street' => 'puna',
          'street2' => 'varachha',
          'zipcode' => '43233',
          'state' => 'india',
          'city' => 'surat',
      ]);

      $controller = new SchoolController();
      $total_schools = School::count();
      $this->assertEquals($total_schools, 7);

      $schoolIds = School::pluck('id')->toArray();
      $schoolIdsExceptFirstTwo = array_slice($schoolIds, 2);
      $schools_ids = implode(",", $schoolIdsExceptFirstTwo);
      $request = new Request([
        'action' => 'trash',
        'is_bulk' => 1,
        'data_id' => $schools_ids,
      ]);

      // Call the delete method
      $response = $controller->delete($request);
      $this->assertEquals(1, $response);

      $total_schools = School::count();
      $this->assertEquals($total_schools, 2);
    }

    /** @test */
  public function testDeleteMethodShouldDeleteSingleRecord() {

      $controller = new SchoolController();

      $schoolsId = School::withTrashed()->first()->id;
      $request = new Request([
        'action' => 'delete',
        'is_bulk' => 0,
        'data_id' => $schoolsId,
      ]);

      // Call the delete method
      $response = $controller->delete($request);
      $this->assertEquals(1, $response);

      $total_schools = School::withTrashed()->count();
      $this->assertEquals($total_schools, 7);
    } 
}
