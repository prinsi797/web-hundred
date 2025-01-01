<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppUser;
use App\Models\AppUserBenchpress;
use App\Models\AppUserDeadlift;
use App\Models\AppUserFriend;
use App\Models\AppUserPowerclean;
use App\Models\AppUserSchool;
use App\Models\AppUserSquat;
use App\Models\School;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Repositories\ApiRepository;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class FitnessController extends Controller
{
    private ApiRepository $apiRepository;

    public function __construct(ApiRepository $apiRepository)
    {
        $this->apiRepository = $apiRepository;
    }
    /**
     * @OA\Get(
     *     path="/api/search-school",
     *     tags={"Hundred App"},
     *     summary="Get List of Schools or Search Schools",
     *     description="Retrieve a list of all schools or search for schools based on a provided query term.",
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         required=false,
     *         description="The search query term. If provided, schools containing the search term in their name will be returned.",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="List of schools retrieved successfully"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve schools")
     *         )
     *     )
     * )
     */
    public function searchSchool(Request $request)
    {
        try {
            $searchTerm = $request->input('q');
            $filteredSchools = $this->apiRepository->searchSchool($searchTerm);
            return response()->json([
                'success' => true,
                'message' => 'Get list of schools',
                'data' => [
                    'school' => $filteredSchools,
                ]

            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search School Faild'
            ]);
        }
    }


    public function schoolAdd(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'short_name' => 'nullable|string|max:255',
                'website' => 'nullable|string|max:255',
                'street' => 'nullable|string|max:255',
                'street2' => 'nullable|string|max:255',
                'zipcode' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 400);
            }

            $school = new School();
            $school->name = $request->name;
            $school->short_name = $request->short_name;
            $school->website = $request->website;
            $school->street = $request->street;
            $school->street2 = $request->street2;
            $school->zipcode = $request->zipcode;
            $school->state = $request->state;
            $school->city = $request->city;

            $school = $this->apiRepository->schoolAdd($school, $request);

            return response()->json([
                'success' => true,
                'message' => 'School added successfully',
                'data' => [
                    'school' => $school
                ]
            ]);
        } catch (\Exception $e) {
            // \Log::error('Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Add School Failed',
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/app-user-school",
     *     tags={"Hundred App"},
     *     summary="Store User Schools API",
     *     description="Store the schools associated with the authenticated user.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"schools"},
     *                 @OA\Property(
     *                     property="schools",
     *                     type="array",
     *                     description="An array of school IDs associated with the user.",
     *                     @OA\Items(type="integer", example="1"),
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Schools stored successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Schools not available")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="School Stored Failed")
     *         )
     *     )
     * )
     */
    public function appUserSchoolStore(Request $request)
    {
        try {
            $schools = $request->input('schools');
            if (!$schools) {
                return response()->json(['success' => false, 'message' => 'Schools not available'], 404);
            }
            $userId = Auth::id();
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }
            $this->apiRepository->appUserSchoolStore($userId, $schools);

            return response()->json([
                'success' => true,
                'message' => 'Schools stored successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'School Stored Faild',
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/bench-store",
     *     tags={"Hundred App"},
     *     summary="Store Bench Press Data API",
     *     description="Store the bench press data for the authenticated user.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Bench press data to be stored",
     *         @OA\JsonContent(
     *             required={"bench_press"},
     *             @OA\Property(property="bench_press", type="string", pattern="^\d{1,6}$", example="100"),
     *             @OA\Property(property="date", type="string", format="date", description="Date of the bench press record (YYYY-MM-DD)"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Bench press added successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="bench", type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="date", type="string", format="date", description="Date of the bench press record (YYYY-MM-DD)"),
     *                     @OA\Property(property="bench_press", type="integer", example="100")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Bench press add failed")
     *         )
     *     )
     * )
     */

    public function benchPressStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'bench_press' => 'regex:/^\d{1,6}$/',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 400);
            }
            $bench = $this->apiRepository->benchPressStore($request->all());
            $bench = [
                'date' => $bench->date,
                'id' => $bench->id,
                'bench_press' => $bench->bench_press,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Bench press add successfully',
                'data' => [
                    'bench' => $bench
                ]
            ]);
        } catch (\Exception $e) {
            // \Log::error('Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bench Add Faild',
            ]);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/deadlift-store",
     *     tags={"Hundred App"},
     *     summary="Store Deadlift Data API",
     *     description="Store the deadlift data for the authenticated user.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Deadlift data to be stored",
     *         @OA\JsonContent(
     *             required={"deadlift", "lift_type"},
     *             @OA\Property(property="deadlift", type="string", pattern="^\d{1,6}$", example="200"),
     *             @OA\Property(property="lift_type", type="string", enum={"Conventional", "Sumo"}, example="Conventional"),
     *             @OA\Property(property="date", type="string", format="date", description="Date of the deadlift record (YYYY-MM-DD)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Deadlift added successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="username", type="string"),
     *                     @OA\Property(property="dob", type="string", format="date", description="Date of Birth (YYYY-MM-DD)"),
     *                     @OA\Property(property="phone_number", type="string"),
     *                     @OA\Property(property="profile_photo_url", type="string"),
     *                     @OA\Property(property="lift_type", type="string", enum={"Conventional", "Sumo"})
     *                 ),
     *                 @OA\Property(property="latest_lifts", type="object",
     *                     @OA\Property(property="deadlift", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="date", type="string", format="date", description="Date of the deadlift record (YYYY-MM-DD)"),
     *                         @OA\Property(property="deadlift", type="integer", example="200")
     *                     ),
     *                     @OA\Property(property="total_count", type="integer", example="800")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Deadlift add failed")
     *         )
     *     )
     * )
     */
    public function deadliftStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'deadlift' => 'regex:/^\d{1,6}$/',
                'lift_type' => 'required',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 400);
            }

            $deadlift = $this->apiRepository->deadliftStore($request->all());
            $user = Auth::user();

            if ($user->profile_photo_url) {
                $fullUrl = Storage::disk('public')->url('ProfilePic/' . $user->profile_photo_url);
                $user->profile_photo_url = $fullUrl;
            }

            $latestDeadlift = $this->apiRepository->getLatestDeadlift($user->id);
            $latestPowerClean = $this->apiRepository->getLatestPowerClean($user->id);
            $latestBenchPress = $this->apiRepository->getLatestBenchPress($user->id);
            $latestSquat = $this->apiRepository->getLatestSquat($user->id);

            $totalCount = ($latestDeadlift ? $latestDeadlift->deadlift : 0) +
                ($latestPowerClean ? $latestPowerClean->power_clean : 0) +
                ($latestBenchPress ? $latestBenchPress->bench_press : 0) +
                ($latestSquat ? $latestSquat->squat : 0);

            return response()->json([
                'success' => true,
                'message' => 'Deadlift add successfully',
                'data' => [
                    'user' => [
                        'name' => $user->name,
                        'username' => $user->username,
                        'dob' => $user->dob,
                        'phone_number' => $user->phone_number,
                        'profile_photo_url' => $user->profile_photo_url,
                        'lift_type' => $user->lift_type,
                    ],
                    'latest_lifts' => [
                        'deadlift' => $latestDeadlift,
                        'power_clean' => $latestPowerClean,
                        'bench_press' => $latestBenchPress,
                        'squat' => $latestSquat,
                        'total_count' => $totalCount,
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            // \Log::error('Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Deadlift Add Faild',
            ]);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/squats-store",
     *     tags={"Hundred App"},
     *     summary="Store Squats Data API",
     *     description="Store the squat data for the authenticated user.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Squat data to be stored",
     *         @OA\JsonContent(
     *             required={"squat"},
     *             @OA\Property(property="squat", type="string", pattern="^\d{1,6}$", example="100"),
     *             @OA\Property(property="date", type="string", format="date", description="Date of the squat record (YYYY-MM-DD)"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Squat added successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="squat", type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="date", type="string", format="date", description="Date of the squat record (YYYY-MM-DD)"),
     *                     @OA\Property(property="squat", type="integer", example="100")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Squat add failed")
     *         )
     *     )
     * )
     */

    public function squatsStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'squat' => 'regex:/^\d{1,6}$/',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 400);
            }

            $squat = $this->apiRepository->squatsStore($request->all());

            $squat = [
                'date' => $squat->date,
                'id' => $squat->id,
                'squat' => $squat->squat,
            ];
            return response()->json([
                'success' => true,
                'message' => 'Squat add successfully',
                'data' => [
                    'squat' => $squat
                ]
            ]);
        } catch (\Exception $e) {
            // \Log::error('Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Squat Add Faild',
            ]);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/powercleans-store",
     *     tags={"Hundred App"},
     *     summary="Store Power Cleans Data API",
     *     description="Store the power cleans data for the authenticated user.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Power cleans data to be stored",
     *         @OA\JsonContent(
     *             required={"power_clean", "lift_type"},
     *             @OA\Property(property="power_clean", type="string", pattern="^\d{1,6}$", example="150"),
     *             @OA\Property(property="lift_type", type="string", enum={"Clean", "Hang Clean", "Power Clean"}, example="Power Clean"),
     *             @OA\Property(property="date", type="string", format="date", description="Date of the power clean record (YYYY-MM-DD)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Power clean added successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="username", type="string"),
     *                     @OA\Property(property="dob", type="string", format="date", description="Date of Birth (YYYY-MM-DD)"),
     *                     @OA\Property(property="phone_number", type="string"),
     *                     @OA\Property(property="profile_photo_url", type="string"),
     *                     @OA\Property(property="lift_type", type="string", enum={"Clean", "Hang Clean", "Power Clean"})
     *                 ),
     *                 @OA\Property(property="latest_lifts", type="object",
     *                     @OA\Property(property="power_clean", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="date", type="string", format="date", description="Date of the power clean record (YYYY-MM-DD)"),
     *                         @OA\Property(property="power_clean", type="integer", example="150")
     *                     ),
     *                     @OA\Property(property="total_count", type="integer", example="800")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Power clean add failed")
     *         )
     *     )
     * )
     */

    public function powercleansStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'power_clean' => 'regex:/^\d{1,6}$/',
                'lift_type' => 'required',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 400);
            }

            $powerclean = $this->apiRepository->powercleansStore($request->all());

            $user = Auth::user();

            if ($user->profile_photo_url) {
                $fullUrl = Storage::disk('public')->url('ProfilePic/' . $user->profile_photo_url);
                $user->profile_photo_url = $fullUrl;
            }

            $latestDeadlift = $this->apiRepository->getLatestDeadlift($user->id);
            $latestPowerClean = $this->apiRepository->getLatestPowerClean($user->id);
            $latestBenchPress = $this->apiRepository->getLatestBenchPress($user->id);
            $latestSquat = $this->apiRepository->getLatestSquat($user->id);

            $totalCount = ($latestDeadlift ? $latestDeadlift->deadlift : 0) +
                ($latestPowerClean ? $latestPowerClean->power_clean : 0) +
                ($latestBenchPress ? $latestBenchPress->bench_press : 0) +
                ($latestSquat ? $latestSquat->squat : 0);

            return response()->json([
                'success' => true,
                'message' => 'Powerclean add successfully',
                'data' => [
                    'user' => [
                        'name' => $user->name,
                        'username' => $user->username,
                        'dob' => $user->dob,
                        'phone_number' => $user->phone_number,
                        'profile_photo_url' => $user->profile_photo_url,
                        'lift_type' => $user->lift_type,
                    ],
                    'latest_lifts' => [
                        'power_clean' => $latestPowerClean,
                        'deadlift' => $latestDeadlift,
                        'bench_press' => $latestBenchPress,
                        'squat' => $latestSquat,
                        'total_count' => $totalCount,
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Powerclean Add Faild',
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/weight-store",
     *     tags={"Hundred App"},
     *     summary="Store Weight Data API",
     *     description="Store the weight data for the authenticated user.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Weight data to be stored",
     *         @OA\JsonContent(
     *             required={"weight"},
     *             @OA\Property(property="weight", type="string", pattern="^\d{1,6}$", example="70"),
     *             @OA\Property(property="date", type="string", format="date", description="Date of the weight record (YYYY-MM-DD)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Weight added successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="weight", type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="date", type="string", format="date", description="Date of the weight record (YYYY-MM-DD)"),
     *                     @OA\Property(property="weight", type="integer", example="70")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Weight add failed")
     *         )
     *     )
     * )
     */

    public function weightStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'weight' => 'regex:/^\d{1,6}$/',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 400);
            }

            $weight = $this->apiRepository->weightStore($request->all());
            $weight = [
                'date' => $weight->date,
                'id' => $weight->id,
                'weight' => $weight->weight,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Weight add successfully',
                'data' => [
                    'weight' => $weight
                ]
            ]);
        } catch (\Exception $e) {
            // \Log::error('Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Weight add faild',
            ]);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/height-store",
     *     tags={"Hundred App"},
     *     summary="Store User's Height API",
     *     description="Store the user's height information.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User's height data",
     *         @OA\JsonContent(
     *             required={"fit", "inch"},
     *             @OA\Property(property="fit", type="integer", example=5),
     *             @OA\Property(property="inch", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Height added successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="height", type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="date", type="string"),
     *                     @OA\Property(property="fit", type="integer"),
     *                     @OA\Property(property="inch", type="integer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The inch field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred while processing the request")
     *         )
     *     )
     * )
     */

    public function heightStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'inch' => 'regex:/^\d{1,6}$/',
                'fit' => 'regex:/^\d{1,6}$/',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 400);
            }

            $height = $this->apiRepository->heightStore($request->all());
            $height = [
                'id' => $height->id,
                'date' => $height->date,
                'fit' => $height->fit,
                'inch' => $height->inch,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Height add successfully',
                'data' => [
                    'height' => $height
                ]
            ]);
        } catch (\Exception $e) {
            // \Log::error('Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Height add faild',
            ]);
        }
    }

    /*
    List Type store api
    */

    public function liftTypeStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'lift_type' => 'required',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 400);
            }

            $liftType = $this->apiRepository->liftTypeStore($request->all());
            $liftType = [
                'id' => $liftType->id,
                'lift_type' => $liftType->lift_type,
            ];

            return response()->json([
                'success' => true,
                'message' => 'lift type add successfully',
                'data' => [
                    'lift_type' => $liftType
                ]
            ]);
        } catch (\Exception $e) {
            //            \Log::error('Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'lift type add faild',
            ]);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/user-contacts",
     *     tags={"Hundred App"},
     *     summary="Get User Contacts API",
     *     description="Retrieve the contacts of the authenticated user.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Get app user contacts"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user_contacts",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="app_user_id", type="integer"),
     *                         @OA\Property(property="contact_firstname", type="string"),
     *                         @OA\Property(property="contact_lastname", type="string"),
     *                         @OA\Property(property="contact_phone_number", type="string")
     *                     ),
     *                     description="List of user contacts"
     *                 ),
     *                 @OA\Property(
     *                     property="contact_in_app",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="app_user_id", type="integer"),
     *                         @OA\Property(property="user_id", type="integer"),
     *                         @OA\Property(property="contact_firstname", type="string"),
     *                         @OA\Property(property="contact_lastname", type="string"),
     *                         @OA\Property(property="contact_phone_number", type="string"),
     *                         @OA\Property(property="is_added", type="boolean")
     *                     ),
     *                     description="List of contacts in the app"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User Contacts Failed")
     *         )
     *     )
     * )
     */

    public function getUserContacts(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }
            $userContacts = $this->apiRepository->getUserContacts($user->id);
            $userAppContact = $this->apiRepository->getContactInApp($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Get app user contacts',
                'data' => [
                    'user_contacts' => $userContacts,
                    'contact_in_app' => $userAppContact,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User Contacts Faild' . $e->getMessage(),
            ]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/get-appuser-contacts",
     *     tags={"Hundred App"},
     *     summary="Get User Contacts API",
     *     description="Retrieve the contacts of the authenticated user.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Get app user contacts"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user_contacts",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="app_user_id", type="integer"),
     *                         @OA\Property(property="contact_firstname", type="string"),
     *                         @OA\Property(property="contact_lastname", type="string"),
     *                         @OA\Property(property="contact_phone_number", type="string")
     *                     ),
     *                     description="List of user contacts"
     *                 ),
     *                 @OA\Property(
     *                     property="contact_in_app",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="app_user_id", type="integer"),
     *                         @OA\Property(property="user_id", type="integer"),
     *                         @OA\Property(property="contact_firstname", type="string"),
     *                         @OA\Property(property="contact_lastname", type="string"),
     *                         @OA\Property(property="contact_phone_number", type="string"),
     *                         @OA\Property(property="is_added", type="boolean")
     *                     ),
     *                     description="List of contacts in the app"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User Contacts Failed")
     *         )
     *     )
     * )
     */
    public function GetUserContactList(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }
            $userContacts = $this->apiRepository->getAppUserContacts($user->id);
            $userAppContact = $this->apiRepository->getContactInApp($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Get app user contacts',
                'data' => [
                    'user_contacts' => $userContacts,
                    'contact_in_app' => $userAppContact,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User Contacts Faild' . $e->getMessage(),
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/access-contact",
     *     tags={"Hundred App"},
     *     summary="Store User Contacts API",
     *     description="Store the contacts of the authenticated user.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Array of contacts to be stored",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="contact_firstname", type="string", example="John"),
     *                 @OA\Property(property="contact_lastname", type="string", example="Doe"),
     *                 @OA\Property(property="contact_phone_number", type="string", example="1234567890")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User Contacts stored successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user_contacts",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="app_user_id", type="integer"),
     *                         @OA\Property(property="contact_firstname", type="string"),
     *                         @OA\Property(property="contact_lastname", type="string"),
     *                         @OA\Property(property="contact_phone_number", type="string")
     *                     ),
     *                     description="List of user contacts"
     *                 ),
     *                 @OA\Property(
     *                     property="contact_in_app",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="app_user_id", type="integer"),
     *                         @OA\Property(property="user_id", type="integer"),
     *                         @OA\Property(property="contact_firstname", type="string"),
     *                         @OA\Property(property="contact_lastname", type="string"),
     *                         @OA\Property(property="contact_phone_number", type="string"),
     *                         @OA\Property(property="is_added", type="boolean")
     *                     ),
     *                     description="List of contacts in the app"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User Contacts store Failed")
     *         )
     *     )
     * )
     */
    public function contactStore(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }
            $validator = Validator::make($request->all(), [
                '*.contact_firstname' => 'required|string',
                '*.contact_lastname' => 'required|string',
                '*.contact_phone_number' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        // Remove non-numeric characters from the phone number
                        $phoneNumber = preg_replace('/[^0-9]/', '', $value);
                        // Check if the resulting phone number has exactly 10 digits
                        // if (strlen($phoneNumber) !== 10) {
                        //     $fail('The ' . $attribute . ' must be 10 digits.');
                        // }
                    },
                ],
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 400);
            }

            $createdContacts = $this->apiRepository->storeUserContacts($user->id, $request->all(),$user->phone_number);

            $userContacts = $this->apiRepository->getUserContacts($user->id);
            $userAppContact = $this->apiRepository->getContactInApp($user->id);

            $formattedContacts = [];
            foreach ($createdContacts as $contact) {
                $formattedContacts[] = [
                    'id' => $contact->id,
                    'contact_firstname' => $contact->contact_firstname,
                    'contact_lastname' => $contact->contact_lastname,
                    'contact_phone_number' => $contact->contact_phone_number,
                ];
            }

            // $createdContacts = convertNullToEmptyStrings($createdContacts);
            return response()->json([
                'success' => true,
                'message' => 'User Contacts stored successfully',
                'data' => [
                    'user_contacts' => $userContacts,
                    'contact_in_app' => $userAppContact,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User Contacts store Faild' . $e->getMessage(),
            ]);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/add-friend",
     *     tags={"Hundred App"},
     *     summary="Add Friend API",
     *     description="Add a friend for the authenticated user.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"friend_id"},
     *                 @OA\Property(property="friend_id", type="integer", example=123, description="ID of the friend user to be added.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Add friend successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="add_friend",
     *                     type="object",
     *                     description="Details of the added friend"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     )
     * )
     */

    public function addFriend(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['sucess' => false, 'message' => 'User not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'friend_id' => 'required|exists:app_users,id',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 400);
            }
            $friendId = $request['friend_id'];

            $invitation = $this->apiRepository->addFriend($user->id, $friendId);

            return response()->json([
                'success' => true,
                'message' => 'Add friend successfully',
                'data' => [
                    'add_friend' => $invitation,
                ]
            ], 200);
        } catch (Exception $e) {
            // \Log::error('Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/profile-get",
     *     tags={"Hundred App"},
     *     summary="Get User Profile API",
     *     description="Retrieve the profile details of the authenticated user.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User details retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="dob", type="string", format="date", description="Date of birth (YYYY-MM-DD)"),
     *                     @OA\Property(property="phone_number", type="string"),
     *                     @OA\Property(property="username", type="string"),
     *                     @OA\Property(property="profile_photo_url", type="string"),
     *                     @OA\Property(property="lift_type", type="string")
     *                 ),
     *                 @OA\Property(
     *                     property="latest_lifts",
     *                     type="object",
     *                     @OA\Property(property="deadlift", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="date", type="string", format="date", description="Date of the record (YYYY-MM-DD)"),
     *                         @OA\Property(property="deadlift", type="integer")
     *                     ),
     *                     @OA\Property(property="power_clean", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="date", type="string", format="date", description="Date of the record (YYYY-MM-DD)"),
     *                         @OA\Property(property="power_clean", type="integer")
     *                     ),
     *                     @OA\Property(property="bench_press", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="date", type="string", format="date", description="Date of the record (YYYY-MM-DD)"),
     *                         @OA\Property(property="bench_press", type="integer")
     *                     ),
     *                     @OA\Property(property="squat", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="date", type="string", format="date", description="Date of the record (YYYY-MM-DD)"),
     *                         @OA\Property(property="squat", type="integer")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="school",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="image_url", type="string")
     *                 ),
     *                 @OA\Property(property="total_count", type="integer", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred while processing the request")
     *         )
     *     )
     * )
     */

    public function profileGet(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            if ($user->profile_photo_url) {
                $fullUrl = Storage::disk('public')->url('ProfilePic/' . $user->profile_photo_url);
                $user->profile_photo_url = $fullUrl;
            }

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'dob' => $user->dob,
                'phone_number' => $user->phone_number,
                'username' => $user->username,
                'profile_photo_url' => $user->profile_photo_url,
                'lift_type' => $user->lift_type,
            ];

            $latestDeadlift = null;
            $latestPowerClean = null;
            $latestBenchPress = $this->apiRepository->getLatestBenchPress($user->id);
            $latestSquat = $this->apiRepository->getLatestSquat($user->id);

            if ($user->lift_type === 'deadlift') {
                $latestDeadlift = $this->apiRepository->getLatestDeadlift($user->id);
            } elseif ($user->lift_type === 'power_clean') {
                $latestPowerClean = $this->apiRepository->getLatestPowerClean($user->id);
            }

            $schools = $this->apiRepository->getUserSchools($user->id);

            $schoolData = null;
            foreach ($schools as $school) {
                $schoolRecord = $this->apiRepository->getSchoolById($school->school_id);

                if ($schoolRecord->image_url) {
                    $fullUrl = Storage::disk('public')->url('SchoolImage/' . $schoolRecord->image_url);
                    $schoolRecord->image_url = $fullUrl;
                }
                if ($schoolRecord) {
                    $schoolData = [
                        'id' => $schoolRecord->id,
                        'name' => $schoolRecord->name,
                        'image_url' => $schoolRecord->image_url,
                    ];
                }
            }

            $totalCount = ($latestDeadlift ? $latestDeadlift->deadlift : 0) +
                ($latestPowerClean ? $latestPowerClean->power_clean : 0) +
                ($latestBenchPress ? $latestBenchPress->bench_press : 0) +
                ($latestSquat ? $latestSquat->squat : 0);

            $latestLiftsNull = $latestDeadlift === null && $latestPowerClean === null && $latestBenchPress === null && $latestSquat === null;

            $latest_lifts = null;

            if ($user->lift_type === 'deadlift') {
                $latest_lifts = $latestLiftsNull ? null : [
                    'deadlift' => $latestDeadlift,
                    'bench_press' => $latestBenchPress,
                    'squat' => $latestSquat,
                ];
            } elseif ($user->lift_type === 'power_clean') {
                $latest_lifts = $latestLiftsNull ? null : [
                    'power_clean' => $latestPowerClean,
                    'bench_press' => $latestBenchPress,
                    'squat' => $latestSquat,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'User details retrieved successfully',
                'data' => [
                    'user' => $userData,
                    'latest_lifts' => $latest_lifts,
                    'school' => $schoolData,
                    'total_count' => $totalCount === 0 ? null : $totalCount,
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/lift-graph",
     *     tags={"Hundred App"},
     *     summary="Get Lift Graph Data API",
     *     description="Retrieve the latest lift graph data for the authenticated user.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lift graph data retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="latest_lifts",
     *                     type="object",
     *                     @OA\Property(
     *                         property="deadlift",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="date", type="string", format="date", description="Date of the record (YYYY-MM-DD)"),
     *                             @OA\Property(property="deadlift", type="integer")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="bench_press",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="date", type="string", format="date", description="Date of the record (YYYY-MM-DD)"),
     *                             @OA\Property(property="bench_press", type="integer")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="squat",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="date", type="string", format="date", description="Date of the record (YYYY-MM-DD)"),
     *                             @OA\Property(property="squat", type="integer")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="power_clean",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="date", type="string", format="date", description="Date of the record (YYYY-MM-DD)"),
     *                             @OA\Property(property="power_clean", type="integer")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="total_counts",
     *                     type="object",
     *                     @OA\Property(property="deadlift", type="integer"),
     *                     @OA\Property(property="bench_press", type="integer"),
     *                     @OA\Property(property="squat", type="integer"),
     *                     @OA\Property(property="power_clean", type="integer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred while processing the request")
     *         )
     *     )
     * )
     */

    public function liftGraph(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }
            $latestLifts = $this->apiRepository->getLatestLifts($user->id);
            $latestLifts = $latestLifts ?? [];

            $latestCounts = null;

            foreach ($latestLifts as $liftType => $lifts) {
                if (!empty($lifts)) {
                    $latestRecord = $lifts[0] ?? null;
                    if ($latestRecord) {
                        $latestCount = $latestRecord[$liftType] ?? null;
                    } else {
                        $latestCount = null;
                    }
                } else {
                    $latestCount = null;
                }
                $latestCounts[$liftType] = $latestCount;

                if ($lifts->isEmpty()) {
                    $latestLifts[$liftType] = null;
                }
            }
            $latestLifts = !empty($latestLifts) ? $latestLifts : null;

            return response()->json([
                'success' => true,
                'message' => 'lift graph data get successfully',
                'data' => [
                    'latest_lifts' => $latestLifts,
                    'latest_counts' => $latestCounts,
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/leaderboard",
     *     tags={"Hundred App"},
     *     summary="Get Leaderboard Data API",
     *     description="Retrieve leaderboard data for the authenticated user's friends and users from the same school.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Friend details retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="school_friend_details",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="lift_type", type="string"),
     *                         @OA\Property(property="phone_number", type="string"),
     *                         @OA\Property(property="profile_photo_url", type="string"),
     *                         @OA\Property(
     *                             property="latest_lifts",
     *                             type="object",
     *                             oneOf={
     *                                 @OA\Schema(
     *                                     type="object",
     *                                     @OA\Property(property="deadlift", type="integer"),
     *                                     @OA\Property(property="bench_press", type="integer"),
     *                                     @OA\Property(property="squat", type="integer"),
     *                                 ),
     *                                 @OA\Schema(
     *                                     type="object",
     *                                     @OA\Property(property="power_clean", type="integer"),
     *                                     @OA\Property(property="bench_press", type="integer"),
     *                                     @OA\Property(property="squat", type="integer"),
     *                                 )
     *                             },
     *                             nullable=true
     *                         ),
     *                         @OA\Property(
     *                             property="school",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="name", type="string"),
     *                             @OA\Property(property="image_url", type="string"),
     *                         ),
     *                         @OA\Property(property="total_count", type="integer"),
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="friend_details",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="lift_type", type="string"),
     *                         @OA\Property(property="phone_number", type="string"),
     *                         @OA\Property(property="profile_photo_url", type="string"),
     *                         @OA\Property(
     *                             property="latest_lifts",
     *                             type="object",
     *                             oneOf={
     *                                 @OA\Schema(
     *                                     type="object",
     *                                     @OA\Property(property="deadlift", type="integer"),
     *                                     @OA\Property(property="bench_press", type="integer"),
     *                                     @OA\Property(property="squat", type="integer"),
     *                                 ),
     *                                 @OA\Schema(
     *                                     type="object",
     *                                     @OA\Property(property="power_clean", type="integer"),
     *                                     @OA\Property(property="bench_press", type="integer"),
     *                                     @OA\Property(property="squat", type="integer"),
     *                                 )
     *                             },
     *                             nullable=true
     *                         ),
     *                         @OA\Property(
     *                             property="school",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="name", type="string"),
     *                             @OA\Property(property="image_url", type="string"),
     *                         ),
     *                         @OA\Property(property="total_count", type="integer"),
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred while processing the request")
     *         )
     *     )
     * )
     */
    public function leaderBoard(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'dob' => $user->dob,
                'phone_number' => $user->phone_number,
                'username' => $user->username,
                'profile_photo_url' => $user->profile_photo_url,
                'lift_type' => $user->lift_type,
            ];
            $userFriends = AppUserFriend::where('app_user_id', $user->id)->get();

            $friendDetails = null;

            foreach ($userFriends as $friend) {
                $friendData = AppUser::find($friend->app_friend_id);
                if (!$friendData) {
                    continue;
                }

                $schools = AppUserSchool::where('app_user_id', $friendData->id)->get();;

                $schoolData = null;
                foreach ($schools as $school) {
                    $schoolRecord = $this->apiRepository->getSchoolById($school->school_id);

                    if ($schoolRecord->image_url) {
                        $fullUrl = Storage::disk('public')->url('SchoolImage/' . $schoolRecord->image_url);
                        $schoolRecord->image_url = $fullUrl;
                    }
                    if ($schoolRecord) {
                        $schoolData = [
                            'id' => $schoolRecord->id,
                            'name' => $schoolRecord->name,
                            'image_url' => $schoolRecord->image_url,
                        ];
                    }
                }
                if ($friendData->profile_photo_url) {
                    $fullUrl = Storage::disk('public')->url('ProfilePic/' . $friendData->profile_photo_url);
                    $friendData->profile_photo_url = $fullUrl;
                }

                $latestDeadlift = null;
                $latestPowerClean = null;
                $latestBenchPress = null;
                $latestSquat = null;

                if ($friendData->lift_type === 'deadlift') {
                    $latestDeadlift = AppUserDeadlift::where('app_user_id', $friendData->id)->latest('date')->value('deadlift');
                    $latestBenchPress = AppUserBenchpress::where('app_user_id', $friendData->id)->latest('date')->value('bench_press');
                    $latestSquat = AppUserSquat::where('app_user_id', $friendData->id)->latest('date')->value('squat');
                } elseif ($friendData->lift_type === 'power_clean') {
                    $latestPowerClean = AppUserPowerclean::where('app_user_id', $friendData->id)->latest('date')->value('power_clean');
                    $latestBenchPress = AppUserBenchpress::where('app_user_id', $friendData->id)->latest('date')->value('bench_press');
                    $latestSquat = AppUserSquat::where('app_user_id', $friendData->id)->latest('date')->value('squat');
                }

                $totalCount = ($latestDeadlift ?? 0) + ($latestPowerClean ?? 0) + ($latestBenchPress ?? 0) + ($latestSquat ?? 0);

                if ($friendData->lift_type === 'deadlift') {
                    $latest_lifts = [
                        'deadlift' => $latestDeadlift,
                        'bench_press' => $latestBenchPress,
                        'squat' => $latestSquat,
                    ];
                } elseif ($friendData->lift_type === 'power_clean') {
                    $latest_lifts = [
                        'power_clean' => $latestPowerClean,
                        'bench_press' => $latestBenchPress,
                        'squat' => $latestSquat,
                    ];
                }

                if ($latest_lifts !== null && array_filter($latest_lifts) === []) {
                    $latest_lifts = null;
                }

                $friendDetails[] = [
                    'name' => $friendData->name,
                    'lift_type' => $friendData->lift_type,
                    'phone_number' => $friendData->phone_number,
                    'profile_photo_url' => $friendData->profile_photo_url,
                    'school' => $schoolData ?? null,
                    'latest_lifts' => $latest_lifts,
                    'total_count' => $totalCount,
                ];
            }

            if ($friendDetails !== null) {
                addRank($friendDetails);
            }

            $userSchools = AppUserSchool::where('app_user_id', $user->id)->pluck('school_id');
            $schoolFriendDetails = null;
            // Fetch users who belong to the same school as the authenticated user
            foreach ($userSchools as $schoolId) {
                $schoolUsers = AppUserSchool::where('school_id', $schoolId)->pluck('app_user_id');

                foreach ($schoolUsers as $schoolUserId) {
                    $schoolUserData = AppUser::find($schoolUserId);

                    if (!$schoolUserData) {
                        continue;
                    }

                    $latestLifts = null;

                    if ($schoolUserData->lift_type === 'deadlift') {
                        $latestLifts = [
                            'deadlift' => AppUserDeadlift::where('app_user_id', $schoolUserId)->latest('date')->value('deadlift'),
                            'bench_press' => AppUserBenchpress::where('app_user_id', $schoolUserId)->latest('date')->value('bench_press'),
                            'squat' => AppUserSquat::where('app_user_id', $schoolUserId)->latest('date')->value('squat'),
                        ];
                    } elseif ($schoolUserData->lift_type === 'power_clean') {
                        $latestLifts = [
                            'power_clean' => AppUserPowerclean::where('app_user_id', $schoolUserId)->latest('date')->value('power_clean'),
                            'bench_press' => AppUserBenchpress::where('app_user_id', $schoolUserId)->latest('date')->value('bench_press'),
                            'squat' => AppUserSquat::where('app_user_id', $schoolUserId)->latest('date')->value('squat'),
                        ];
                    }

                    $allNull = true;
                    foreach ($latestLifts as $lift) {
                        if ($lift !== null) {
                            $allNull = false;
                            break;
                        }
                    }

                    $latestLifts = ($allNull) ? null : $latestLifts;

                    $totalCount = array_sum(array_filter($latestLifts ?? []));
                    $school = null;
                    $schools = AppUserSchool::where('app_user_id', $schoolUserId)->get();
                    foreach ($schools as $school) {
                        $schoolRecord = $this->apiRepository->getSchoolById($school->school_id);
                        if ($schoolRecord) {
                            $schoolData = [
                                'id' => $schoolRecord->id,
                                'name' => $schoolRecord->name,
                            ];
                            if ($schoolRecord->image_url) {
                                $fullUrl = Storage::disk('public')->url('SchoolImage/' . $schoolRecord->image_url);
                                $schoolData['image_url'] = $fullUrl;
                            }
                            $school = $schoolData;
                        }
                    }

                    if ($schoolUserData->profile_photo_url) {
                        $fullUrl = Storage::disk('public')->url('ProfilePic/' . $schoolUserData->profile_photo_url);
                        $schoolUserData->profile_photo_url = $fullUrl;
                    }

                    $schoolFriendDetails[] = [
                        'id' => $schoolUserData->id,
                        'name' => $schoolUserData->name,
                        'lift_type' => $schoolUserData->lift_type,
                        'phone_number' => $schoolUserData->phone_number,
                        'profile_photo_url' => $schoolUserData->profile_photo_url,
                        'latest_lifts' => $latestLifts,
                        'school' => $school,
                        'total_count' => $totalCount,
                    ];
                }
            }
            if ($schoolFriendDetails !== null) {
                addRank($schoolFriendDetails);
            }
            return response()->json([
                'success' => true,
                'message' => 'Friend details retrieved successfully',
                'data' => [
                    'user' => $userData,
                    'school_friend_details' => $schoolFriendDetails,
                    'friend_details' => $friendDetails,
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/feedback",
     *     tags={"Hundred App"},
     *     summary="Submit Feedback API",
     *     description="Submit feedback from the authenticated user.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Feedback message",
     *         @OA\JsonContent(
     *             required={"message"},
     *             @OA\Property(property="message", type="string", example="This is a feedback message.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Feedback sent successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="feedback", type="object",
     *                     @OA\Property(property="message", type="string"),
     *                     @OA\Property(property="id", type="integer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The message field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred while processing the request")
     *         )
     *     )
     * )
     */

    public function feedback(Request $request)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'message' => 'required',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 400);
            }

            $feedback = $this->apiRepository->createFeedback($user->id, $request->message);

            $feedbackData = [
                'message' => $feedback->message,
                'id' => $feedback->id,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Feedback Send successfully',
                'data' => [
                    'feedback' => $feedbackData,
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
