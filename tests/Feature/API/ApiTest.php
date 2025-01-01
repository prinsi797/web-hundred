<?php

namespace Tests\Feature;

use App\Models\AppUser;
use App\Models\AppUserBenchpress;
use App\Models\AppUserContact;
use App\Models\AppUserDeadlift;
use App\Models\AppUserFriend;
use App\Models\AppUserHeight;
use App\Models\AppUserPowerclean;
use App\Models\AppUserSchool;
use App\Models\AppUserSquat;
use App\Models\AppUserWeight;
use App\Models\Feedback;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Twilio\Rest\Client;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Repositories\ApiRepository;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    protected $token = null;
    protected function setUp(): void
    {
        parent::setUp();

        AppUser::factory()->count(2)->create();

        // Generate JWT token for authentication
        $this->token = JWTAuth::fromUser(AppUser::first());
    }

    public function testUserRegistration()
    {
        $userData = [
            'name' => 'test',
            'dob' => '1997-07-18',
            'phone_number' => '9979202377',
            'security_code' => '54322',
            'profile_photo_url' => 'test.jpg',
            'username' => 'DER3232',
            'lift_type' => 'power_clean'

        ];
        $response = $this->postJson(route('user.register'), $userData);
        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'user',
            ],
        ]);
    }

    public function testUserLogin()
    {
        $user = AppUser::first();

        $this->assertNotNull($user, 'No users found in the database.');
        $verification = $this->app->make(ApiRepository::class)->loginProcess($user->phone_number);

        $response = $this->postJson(route('user.login'), [
            'phone_number' => $verification->phone_number,
        ]);
        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'user',
            ],
        ]);
    }

    public function testVerifyOTP()
    {
        $user = AppUser::first();

        $this->assertNotNull($user, 'No users found in the database.');
        $verification = $this->app->make(ApiRepository::class)->loginProcess($user->phone_number);

        $response = $this->postJson(route('user.verify'), [
            'phone_number' => $verification->phone_number,
            'security_code' => $verification->security_code,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'token',
                'user',
            ],
        ]);
    }

    public function testProfileImageUpdate()
    {
        // Create a user
        $user = AppUser::first();

        $this->assertNotNull($user, 'No users found in the database.');

        Storage::fake('public');
        $file = UploadedFile::fake()->image('profile-photo.jpg');

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('profile.image.update'), [
            'profile_photo_url' => $file,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'user',
            ],
        ]);
        $this->assertNotNull($user->fresh()->profile_photo_url);
    }

    public function testProfileNameUpdate()
    {
        $user = AppUser::first();
        $this->assertNotNull($user, 'No users found in the database.');
        $token = JWTAuth::fromUser($user);
        $newName = 'New Name';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('profile.name.update'), [
            'name' => $newName,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'user',
            ],
        ]);

        // Optionally, assert that the user's name has been updated in the database
        $this->assertEquals($newName, $user->fresh()->name);
    }

    public function testDeleteUserAccount()
    {
        $user = AppUser::first();
        $this->assertNotNull($user, 'No users found in the database.');
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('delete.user.account'));

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure();
    }

    public function testSearchSchool()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get(route('search.school'));

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'school',
            ],
        ]);
    }

    public function testAppUserSchoolStore()
    {
        // Create a user
        $user = AppUser::first();
        $this->assertNotNull($user, 'No users found in the database.');
        // $token = JWTAuth::fromUser($user);

        // $user = AppUser::factory()->count(2)->create();

        // Create a school
        $school = School::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson(
            route('store.school'),
            ['schools' => $school->id]
        );
        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure();

        $count = DB::table('app_user_schools')
            ->where('app_user_id', $user->id)
            ->where('school_id', $school->id)
            ->count();
    }

    public function testGetUserContacts()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get(route('user.contacts'));

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'user_contacts',
                'contact_in_app',
            ],
        ]);
    }

    public function testContactStore()
    {
        $ContactUser = AppUserContact::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson(route('conatct.store'));

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'user_contacts',
                'contact_in_app',
            ],
        ]);
    }

    public function testAddFriend()
    {
        $appUserFriend = AppUserFriend::factory()->create();

        $friendId = $appUserFriend->id;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson(route('invite.friend'), [
            'friend_id' => $friendId,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'add_friend',
            ],
        ]);
    }

    public function testbenchPressStore()
    {
        $benchpress = AppUserBenchpress::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson(route('bench.store'), [
            'bench_press' => $benchpress->bench_press,
            'date' => $benchpress->date,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'bench',
            ],
        ]);
    }

    public function testdeadliftStore()
    {
        $deadlifts = AppUserDeadlift::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson(route('deadlift.store'), [
            'deadlift' => $deadlifts->deadlift,
            'date' => $deadlifts->date,
            'lift_type' =>'power_clean',
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'user',
                'latest_lifts',
            ],
        ]);
    }

    public function testsquatsStore()
    {
        $squats = AppUserSquat::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson(route('squats.store'), [
            'squat' => $squats->squat,
            'date' => $squats->date,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'squat',
            ],
        ]);
    }

    public function testpowercleansStore()
    {
        $powercleans = AppUserPowerclean::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson(route('powercleans.store'), [
            'power_clean' => $powercleans->power_clean,
            'date' => $powercleans->date,
            'lift_type' =>'power_clean',
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'user',
                'latest_lifts',
            ],
        ]);
    }

    public function testweightStore()
    {
        $weights = AppUserWeight::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson(route('weight.store'), [
            'weight' => $weights->weight,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'weight',
            ],
        ]);
    }

    public function testheightStore()
    {
        $heights = AppUserHeight::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson(route('height.store'), [
            'fit' => $heights->fit,
            'inch' => $heights->inch,
            'date' => $heights->date,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'height',
            ],
        ]);
    }

    public function testprofileGet()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get(route('user.profile'));

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'user',
                'latest_lifts',
                'school',
                'total_count',
            ],
        ]);
    }

    public function testliftGraph()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get(route('lift.graph'));

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'latest_lifts',
                'latest_counts',
            ],
        ]);
    }

    public function testleaderBoard()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get(route('leaderboard'));

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'user',
                'school_friend_details',
                'friend_details',
            ],
        ]);
    }

    public function testfeedback()
    {
        $feedbacks = Feedback::factory()->create();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson(route('feedback'),[
            'message' => $feedbacks->message,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'feedback',
            ],
        ]);
    }

    public function testGetUserContactList()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get(route('get.user.contact'));

        $response->assertStatus(200);
        $this->assertTrue($response['success']);
        $response->assertJsonStructure([
            'data' => [
                'user_contacts',
                'contact_in_app',
            ],
        ]);
    }
}
