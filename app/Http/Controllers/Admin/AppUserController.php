<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppUser as Table;
use App\Http\Requests\AppUserRequests\UpdateUser as UpdateRequest;
use App\Http\Requests\AppUserRequests\AddUser as AddRequest;
use App\Models\AppUser;
use App\Models\AppUserBenchpress;
use App\Models\AppUserContact;
use App\Models\AppUserDeadlift;
use App\Models\AppUserFriend;
use App\Models\AppUserPowerclean;
use App\Models\AppUserSchool;
use App\Models\AppUserSquat;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class AppUserController extends Controller {
    protected $handle_name = "app_user";
    protected $handle_name_plural = "app_users";

    public function __construct() {
        // Stripe::setApiKey(config('app.stripe_secret'));
        $this->middleware('auth');
    }

    public function index(Request $request) {
        $all_count = Table::count();
        $trashed_count = Table::onlyTrashed()->count();
        return kview($this->handle_name_plural . '.index', [
            'ajax_route' => route('admin.' . $this->handle_name_plural . '.ajax'),
            'delete_route' => route('admin.' . $this->handle_name_plural . '.delete'),
            'create_route' => route('admin.' . $this->handle_name_plural . '.create'),
            'table_status' => 'all', //all , trashed
            'all_count' => $all_count,
            'trashed_count' => $trashed_count,
        ]);
    }

    public function create(Request $request) {
        return kview($this->handle_name_plural . '.manage', [
            'form_action' => route('admin.' . $this->handle_name_plural . '.store'),
            'edit' => 0,
        ]);
    }
    public function edit(Request $request) {
        $user = Table::where('id', '=', $request->id)->first();

        return kview($this->handle_name_plural . '.manage', [
            'form_action' => route('admin.' . $this->handle_name_plural . '.update'),
            'cancel' => route('admin.' . $this->handle_name_plural . '.index'),
            'edit' => 1,
            'data' => $user,
        ]);
    }
    public function store(AddRequest $request) {
        try {
            $formattedPhoneNumber = formatPhoneNumber($request->phone_number);

            $existingUser = Table::where('phone_number', $formattedPhoneNumber)
                ->where('id', '!=', $request->id)
                ->first();

            if ($existingUser) {
                return redirect()->back()->with('error', 'Phone number already exists.');
            }

            $profilePhoto = null;
            if ($request->hasFile('profile_photo_url')) {
                $fileName = date("YmdHis") . rand(100, 900);
                $file = $request->file('profile_photo_url');
                $extension = $file->getClientOriginalExtension();
                $profileImage = $fileName . "." . $extension;
                $profilePhoto =  $profileImage;
                Storage::disk('public')->putFileAs('ProfilePic', $file, $profileImage);
            }

            $username = generateRandomUsername();
            $otp = generateOTP();

            $table = Table::create([
                'name' => $request->name,
                'dob' => $request->dob,
                'phone_number' => formatPhoneNumber($request->phone_number),
                'security_code' => $otp,
                'profile_photo_url' => $profilePhoto,
                'username' => $username,
                'lift_type' => $request->lift_type,
            ]);
            return redirect()->to(route('admin.' . $this->handle_name_plural . '.index'))->with('success', 'New ' . ucfirst($this->handle_name) . ' has been added.');
        } catch (Exception $e) {
            return $e->getMessage();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(UpdateRequest $request) {
        try {
            $table = Table::findOrFail($request->id);
            $formattedPhoneNumber = formatPhoneNumber($request->phone_number);

            $existingUser = Table::where('phone_number', $formattedPhoneNumber)
                ->where('id', '!=', $request->id)
                ->first();

            if ($existingUser) {
                return redirect()->back()->with('error', 'Phone number already exists.');
            }

            if ($request->hasFile('profile_photo_url')) {
                $fileName = date("YmdHis") . rand(100, 900);
                $file = $request->file('profile_photo_url');
                $extension = $file->getClientOriginalExtension();
                $profileImage = $fileName . "." . $extension;
                $profilePhoto =  $profileImage;
                Storage::disk('public')->putFileAs('ProfilePic', $file, $profileImage);

                if ($table->profile_photo_url) {
                    $oldImagePath = $file . $table->profile_photo_url;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            } else {
                $profilePhoto = $table->profile_photo_url;
            }
            $update_data = [
                'name' => $request->name,
                'dob' => $request->dob,
                'phone_number' => formatPhoneNumber($request->phone_number),
                'profile_photo_url' => $profilePhoto,
                'lift_type' => $request->lift_type,
            ];

            $where = [
                'id' => $request->id
            ];

            $user = Table::updateOrCreate($where, $update_data);
            return redirect()->back()->with('success', ucfirst($this->handle_name) . ' has been updated');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function ajax(Request $request) {
        $edit_route = route('admin.' . $this->handle_name_plural . '.edit');
        $current_page = $request->page_number;
        if (isset($request->limit)) {
            $limit = $request->limit;
        } else {
            $limit = 10;
        }
        $offset = (($current_page - 1) * $limit);
        $modalObject = new Table();
        if (isset($request->string)) {
            $string = $request->string;
            $modalObject = $modalObject->where('name', 'like', "%" . $request->string . "%");
            $modalObject = $modalObject->orWhere('username', 'like', "%" . $request->string . "%");
            $modalObject = $modalObject->orWhere('dob', 'like', "%" . $request->string . "%");
            $modalObject = $modalObject->orWhere('phone_number', 'like', "%" . $request->string . "%");
        }

        $all_trashed = $request->all_trashed;
        if ($all_trashed == "trashed") {
            $modalObject = $modalObject->onlyTrashed();
        }

        $total_records = $modalObject->count();
        $modalObject = $modalObject->offset($offset);
        $modalObject = $modalObject->take($limit);
        $modalObject = $modalObject->orderBy('id', 'desc');
        $data = $modalObject->get();

        if (isset($request->page_number) && $request->page_number != 1) {
            $page_number = $request->page_number + $limit - 1;
        } else {
            $page_number = 1;
        }
        $pagination = array(
            "offset" => $offset,
            "total_records" => $total_records,
            "item_per_page" => $limit,
            "total_pages" => ceil($total_records / $limit),
            "current_page" => $current_page,
        );

        return kview($this->handle_name_plural . '.ajax', compact('edit_route', 'data', 'page_number', 'limit', 'offset', 'pagination'));
    }
    public function delete(Request $request) {
        if (isset($request->action)) {
            $action = $request->action;
            $is_bulk = $request->is_bulk;
            $data_id = $request->data_id;
        }
        switch ($action) {
            case 'restore':
                try {
                    if ($is_bulk == 1) {
                        $data_id = explode(",", $data_id);
                        $table = Table::onlyTrashed()->whereIn('id', $data_id);
                        $table->restore();
                        return 1;
                    } else {
                        $table = Table::onlyTrashed()->find($data_id);
                        $table->restore();
                        return 1;
                    }
                } catch (Exception $e) {
                    return redirect()->back()->with('error', $e->getMessage());
                }
                break;
            case 'trash':
                try {
                    if ($is_bulk == 1) {
                        $data_id = explode(",", $data_id);
                        $table = Table::whereIn('id', $data_id)->get();
                        foreach ($table as $tbl) {
                            $tbl->deleteSquats();
                            $tbl->deleteDeadlift();
                            $tbl->deleteSchools();
                            $tbl->deletePoweCleans();
                            $tbl->deleteBenchpress();
                            $tbl->deletefriends();
                            $tbl->deleteWeights();
                            $tbl->deleteHeights();
                            $tbl->deleteContacts();
                            $tbl->deleteFeedbacks();
                            $tbl->deleteStatus();
                            $tbl->forceDelete();
                        }
                        // $table->forceDelete();

                        return 1;
                    } else {
                        $table = Table::find($data_id);
                        $table->deleteSquats();
                        $table->deleteDeadlift();
                        $table->deleteSchools();
                        $table->deletePoweCleans();
                        $table->deleteBenchpress();
                        $table->deletefriends();
                        $table->deleteWeights();
                        $table->deleteHeights();
                        $table->deleteContacts();
                        $table->deleteFeedbacks();
                        $table->deleteStatus();
                        $table->forceDelete();
                        // $table->delete();
                        return 1;
                    }
                } catch (Exception $e) {
                    return redirect()->back()->with('error', $e->getMessage());
                }
                break;
            case 'delete':
                try {
                    if ($is_bulk == 1) {
                        $data_id = explode(",", $data_id);
                        $table = Table::withTrashed()->whereIn('id', $data_id)->get();
                        foreach ($table as $tbl) {
                            $tbl->deleteSquats();
                            $tbl->deleteDeadlift();
                            $tbl->deleteSchools();
                            $tbl->deletePoweCleans();
                            $tbl->deleteBenchpress();
                            $tbl->deletefriends();
                            $tbl->deleteWeights();
                            $tbl->deleteHeights();
                            $tbl->deleteContacts();
                            $tbl->deleteFeedbacks();
                            $tbl->deleteStatus();
                            $tbl->forceDelete();
                        }
                        return 1;
                    } else {
                        $table = Table::withTrashed()->find($data_id);
                        $table->deleteSquats();
                        $table->deleteDeadlift();
                        $table->deleteSchools();
                        $table->deletePoweCleans();
                        $table->deleteBenchpress();
                        $table->deletefriends();
                        $table->deleteWeights();
                        $table->deleteHeights();
                        $table->deleteContacts();
                        $table->deleteFeedbacks();
                        $table->deleteStatus();
                        $table->forceDelete();
                        return 1;
                    }
                } catch (Exception $e) {
                    dd($e->getMessage());
                    return redirect()->back()->with('error', $e->getMessage());
                }
                break;
            default:
                return 0;
        }
    }

    public function getContacts(Request $request) {
        $userId = $request->input('app_user_id');
        $contacts = AppUserContact::with('appUser')->where('app_user_id', $userId)->get();
        return response()->json($contacts);
    }

    public function getSchools(Request $request) {
        $userId = $request->input('app_user_id');
        $schools = AppUserSchool::where('app_user_id', $userId)->with('school')->get();
        return response()->json($schools);
    }

    public function getFriends(Request $request) {
        $userId = $request->input('app_user_id');
        $friends = AppUserFriend::with('friend')
            ->where('app_user_id', $userId)
            ->get();
        return response()->json($friends);
    }

    public function getStatus(Request $request) {

        $userId = $request->input('app_user_id');
        $user = AppUser::find($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $liftType = $user->lift_type;

        if ($liftType === 'deadlift') {
            $latestDeadlift = AppUserDeadlift::where('app_user_id', $userId)->get();
            $latestBenchPress = AppUserBenchpress::where('app_user_id', $userId)->get();
            $latestSquat = AppUserSquat::where('app_user_id', $userId)->get();
        } elseif ($liftType === 'power_clean') {
            $latestPowerClean = AppUserPowerclean::where('app_user_id', $userId)->get();
            $latestBenchPress = AppUserBenchpress::where('app_user_id', $userId)->get();
            $latestSquat = AppUserSquat::where('app_user_id', $userId)->get();
        }

        return response()->json([
            'user' => $user,
            'latest_power_clean' => $liftType === 'deadlift' ? [] : $latestPowerClean->toArray(),
            'latest_deadlift' => $liftType == 'power_clean' ? [] : $latestDeadlift->toArray(),
            'latest_bench_press' => $latestBenchPress->toArray(),
            'latest_squat' => $latestSquat->toArray(),
        ]);

    }
}
