<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SmsController;
use App\Models\AppUser;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Exception;
use App\Rules\HeifValidationRule;
use App\Repositories\ApiRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller {
    private ApiRepository $apiRepository;
    private $dunkyPhoneNumbers;

    public function __construct(ApiRepository $apiRepository) {
        $this->apiRepository = $apiRepository;
        $this->dunkyPhoneNumbers = dunkyPhoneNumbers();
    }
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Hundred App"},
     *     summary="Register API",
     *     description="Store App User Information.",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"phone_number", "dob"},
     *                 @OA\Property(property="phone_number", type="string", example="4343434233"),
     *                 @OA\Property(property="dob", type="date", example="2023-05-12"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP sent successfully!"),
     *            @OA\Property(property="data", type="object"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User registration failed."),
     *             @OA\Property(property="data", type="object")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="failed!"),
     *             @OA\Property(property="data", type="object"),
     *         ),
     *     ),
     * )
     */

    public function register(Request $request) {
        try {
            $existingUser = AppUser::where('phone_number', $request->phone_number)->where('is_verify', false)->first();

            if ($existingUser) {
                // $otp = generateOTP();
                if (in_array($request->phone_number, $this->dunkyPhoneNumbers)) {
                    $otp = '00000';
                } else {
                    $otp = generateOTP();
                }
                $existingUser->security_code = $otp;
                $existingUser->is_verify = 0;
                $existingUser->dob = $request->dob;
                $existingUser->save();

                $smsController = new SmsController();
                $smsController->sendVerificationCode($existingUser->phone_number);

                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully',
                    'data' => [
                        'user' => $existingUser,
                    ]
                ]);
            }

            $validator = Validator::make($request->all(), [
                'dob' => 'required',
                'phone_number' => 'required|unique:app_users,phone_number',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 400);
            }

            $username = generateRandomUsername();
            // $otp = generateOTP();
            if (in_array($request->phone_number, $this->dunkyPhoneNumbers)) {
                $otp = '00000';
            } else {
                $otp = generateOTP();
            }
            $userData = $request->only(['dob', 'phone_number']);
            $userData['phone_number'] = formatPhoneNumber($request->phone_number);
            $userData['security_code'] = $otp;
            $userData['is_verify'] = 0;
            $userData['lift_type'] = "power_clean";
            $userData['username'] = $username;

            $user = $this->apiRepository->userRegister($userData);

            $smsController = new SmsController();
            $smsController->sendVerificationCode($user->phone_number);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'data' => [
                    'user' => $user,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User registration failed: ' . $e->getMessage(),
            ]);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Hundred App"},
     *     summary="Login API",
     *     description="Login User Information.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"phone_number"},
     *                 @OA\Property(
     *                     property="phone_number",
     *                     type="string",
     *                     example="4343434233",
     *                     description="The phone number of the user."
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="OTP sent successfully!",
     *                 description="A message indicating the result of the operation."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Additional data related to the user."
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Phone number not found",
     *                 description="A message indicating the error."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Additional data related to the error."
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed!",
     *                 description="A message indicating the error."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Additional data related to the error."
     *             ),
     *         ),
     *     ),
     * )
     */

    public function login(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 400);
            }

            $formattedPhoneNumber = formatPhoneNumber($request->phone_number);

            $verification = $this->apiRepository->loginProcess($formattedPhoneNumber);

            if (!$verification) {
                return response()->json(['success' => false, 'message' => 'Phone number not found']);
            }

            $otp = generateOTP();
            $verification->security_code = $otp;
            $verification->save();

            $smsController = new SmsController();
            $smsController->sendVerificationCode($verification->phone_number);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'data' => [
                    'user' => $verification,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User login failed.',
            ]);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/verify-otp",
     *     tags={"Hundred App"},
     *     summary="Verify OTP API",
     *     description="Verify the OTP for user login.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"phone_number", "security_code"},
     *                 @OA\Property(
     *                     property="phone_number",
     *                     type="string",
     *                     example="4343434233",
     *                     description="The phone number of the user."
     *                 ),
     *                 @OA\Property(
     *                     property="security_code",
     *                     type="string",
     *                     example="123456",
     *                     description="The OTP code received by the user."
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Login successfully",
     *                 description="A message indicating the result of the operation."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Additional data related to the user."
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Validation error message",
     *                 description="A message indicating the error."
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invalid OTP",
     *                 description="A message indicating the error."
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User login otp verification failed",
     *                 description="A message indicating the error."
     *             ),
     *         ),
     *     ),
     * )
     */

    public function verifyOTP(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required',
                'security_code' => 'required',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 404);
            }

            $phoneNumber = formatPhoneNumber($request->phone_number);

            $smsController = new SmsController();
            $result = $smsController->verifyCode($request->phone_number, $request->security_code);
            if (isset($result) && isset($result->status)) {
                $verification = $this->apiRepository->loginProcess($request->phone_number);
                $verification->security_code = $request->security_code;
                $verification->save();
            }

            $verification = $this->apiRepository->verifyOTP($phoneNumber, $request->security_code);

            if (!$verification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP'
                ], 404);
            }

            $verification->is_verify = true;
            $verification->save();

            $token = JWTAuth::fromUser($verification);

            return response()->json([
                'success' => true,
                'message' => 'Login successfully',
                'data' => [
                    'token' => $token,
                    'user' => $verification
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User login otp vertification failed',
            ]);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/resend-otp",
     *     tags={"Hundred App"},
     *     summary="Resend OTP API",
     *     description="Resend OTP to the user's phone number.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"phone_number"},
     *                 @OA\Property(
     *                     property="phone_number",
     *                     type="string",
     *                     example="4343434233",
     *                     description="The phone number of the user to resend OTP."
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="OTP resent successfully",
     *                 description="A message indicating the result of the operation."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Additional data related to the user."
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Validation error message",
     *                 description="A message indicating the error."
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User not found",
     *                 description="A message indicating the error."
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to resend OTP",
     *                 description="A message indicating the error."
     *             ),
     *         ),
     *     ),
     * )
     */
    public function resendOTP(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 400);
            }

            $user = $this->apiRepository->loginProcess($request->phone_number);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $otp = generateOTP();

            $user->security_code = $otp;
            $user->save();

            $smsController = new SmsController();
            $smsController->sendVerificationCode($request->phone_number);

            return response()->json([
                'success' => true,
                'message' => 'OTP resent successfully',
                'data' => [
                    'user' => $user,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP',
            ]);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/profile-image-update",
     *     tags={"Hundred App"},
     *     summary="Update Profile Image API",
     *     description="Update the profile image of the authenticated user.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"profile_photo_url"},
     *                 @OA\Property(
     *                     property="profile_photo_url",
     *                     type="string",
     *                     format="binary",
     *                     description="The new profile image to be uploaded."
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Profile pic updated successfully",
     *                 description="A message indicating the result of the operation."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Additional data related to the user's profile."
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Validation error message",
     *                 description="A message indicating the error."
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated",
     *                 description="A message indicating the error."
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User update profile failed.",
     *                 description="A message indicating the error."
     *             ),
     *         ),
     *     ),
     * )
     */

    public function profileImageUpdate(Request $request) {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            // $user = FacadesJWTAuth::parseToken()->authenticate();
            $user = Auth::user();
            $user = AppUser::findOrFail($request->user()->id);

            $validator = Validator::make($request->all(), [
                'profile_photo_url' => ['image', new HeifValidationRule],
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 400);
            }

            if ($request->hasFile('profile_photo_url')) {
                $uploadedFile = $request->file('profile_photo_url');
                $filename = $uploadedFile->getClientOriginalName();
                $path = $uploadedFile->storeAs('ProfilePic', $filename, 'public');
                $user->profile_photo_url = $filename;
            }
            $user->save();
            if ($user->profile_photo_url) {
                $fullUrl = Storage::disk('public')->url('ProfilePic/' . $user->profile_photo_url);
                $user->profile_photo_url = $fullUrl;
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile pic updated successfully',
                'data' => [
                    'user' => $user
                ]
            ]);
        } catch (\Exception $e) {
            //            \Log::error('Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'User update profile failed.',
            ]);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/profile-name-update",
     *     tags={"Hundred App"},
     *     summary="Update Profile Name API",
     *     description="Update the name of the authenticated user's profile.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="John Doe",
     *                     description="The new name of the user's profile."
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Profile updated successfully",
     *                 description="A message indicating the result of the operation."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Additional data related to the user's profile."
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Validation error message",
     *                 description="A message indicating the error."
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated",
     *                 description="A message indicating the error."
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false,
     *                 description="Indicates whether the request was successful."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User update profile failed.",
     *                 description="A message indicating the error."
     *             ),
     *         ),
     *     ),
     * )
     */
    public function profileNameUpdate(Request $request) {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }
            // $user = JWTAuth::parseToken()->authenticate();
            $user = Auth::user();
            // $user = Auth::guard('app_user')->user();
            $user = AppUser::findOrFail($request->user()->id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'success' => false,
                    'message' => $firstError
                ], 400);
            }

            $user->name = $request->input('name');
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => $user
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'User update profile failed. Error: ' . $e->getMessage(),
            ]);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/deleteAccount",
     *     tags={"Hundred App"},
     *     summary="Delete User Account API",
     *     description="Delete the authenticated user account and associated data.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User account deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="An error occurred while deleting the user account")
     *         )
     *     )
     * )
     */
    public function deleteUserAccount(Request $request) {
        try {
            $user = Auth::user();
            $user = AppUser::findOrFail($user->id);
            $user->deleteContacts();
            $user->userSquats()->forceDelete();
            $user->userDeadlifts()->forceDelete();
            $user->userSchools()->delete();
            $user->userPowerCleans()->forceDelete();
            $user->userBenchpress()->forceDelete();
            $user->userFriends()->forceDelete();
            $user->userWeights()->forceDelete();
            $user->userHeights()->forceDelete();
            $user->userFeedbacks()->forceDelete();
            $user->forceDelete();
            return response()->json(['success' => true, 'message' => 'User account deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'user account deleted failed'], 500);
        }
    }
}
