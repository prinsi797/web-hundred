<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppUser as Table;
use App\Models\AppUser;
use App\Models\AppUserBenchpress;
use App\Models\AppUserDeadlift;
use App\Models\AppUserPowerclean;
use App\Models\AppUserSquat;
use Illuminate\Http\Request;
use Exception;

class StatController extends Controller
{
    protected $handle_name = "stats user";
    protected $handle_name_plural = "stats_users";

    public function __construct()
    {
        // Stripe::setApiKey(config('app.stripe_secret'));
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        //$all_count = Table::count();
        $all_count = AppUser::whereHas('userDeadlifts')
            ->orWhereHas('userPowerCleans')
            ->orWhereHas('userSquats')
            ->orWhereHas('userBenchpress')
            ->withCount(['userDeadlifts', 'userPowerCleans', 'userSquats', 'userBenchpress'])
            ->count();

        // $trashed_count = Table::onlyTrashed()->count();

        $trashed_count = AppUser::whereHas('userDeadlifts')
            ->orWhereHas('userPowerCleans')
            ->orWhereHas('userSquats')
            ->orWhereHas('userBenchpress')
            ->withCount(['userDeadlifts', 'userPowerCleans', 'userSquats', 'userBenchpress'])->onlyTrashed()->count();

        return kview($this->handle_name_plural . '.index', [
            'ajax_route' => route('admin.' . $this->handle_name_plural . '.ajax'),
            'delete_route' => route('admin.' . $this->handle_name_plural . '.delete'),
            'create_route' => route('admin.' . $this->handle_name_plural . '.create'),
            'table_status' => 'all',
            'all_count' => $all_count,
            'trashed_count' => $trashed_count,
        ]);
    }

    public function create(Request $request)
    {
        $users = AppUser::get();
        return kview($this->handle_name_plural . '.manage', [
            'form_action' => route('admin.' . $this->handle_name_plural . '.store'),
            'edit' => 0,
            'users' => $users,
        ]);
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $queryString = parse_url($id, PHP_URL_QUERY);
        parse_str($queryString, $queryParams);
        $date = isset($queryParams['date']) ? $queryParams['date'] : null;

        $user = AppUser::with('userDeadlifts', 'userPowerCleans', 'userSquats', 'userBenchpress')
            ->where('id', '=', $request->id)
            ->first();

        $deadlifts = $user->userDeadlifts()->where('date', $date)->pluck('deadlift', 'app_user_id');
        $powerCleans = $user->userPowerCleans()->where('date', $date)->pluck('power_clean', 'app_user_id');
        $squats = $user->userSquats()->where('date', $date)->pluck('squat', 'app_user_id');
        $benchpress = $user->userBenchpress()->where('date', $date)->pluck('bench_press', 'app_user_id');
        $dates = collect([$date]);

        $users = AppUser::get();

        return kview($this->handle_name_plural . '.manage', [
            'form_action' => route('admin.' . $this->handle_name_plural . '.update'),
            'cancel' => route('admin.' . $this->handle_name_plural . '.index'),
            'edit' => 1,
            'data' => $user,
            'users' => $users,
            'deadlifts' => $deadlifts,
            'powerCleans' => $powerCleans,
            'squats' => $squats,
            'benchpress' => $benchpress,
            'dates' => $dates,
        ]);
    }

    public function store(Request $request)
    {
        try {
            if ($request->filled('bench_press')) {
                AppUserBenchpress::create([
                    'app_user_id' => $request->app_user_id,
                    'bench_press' => $request->bench_press,
                    'date' => $request->date,
                ]);
            }
            if ($request->filled('deadlift')) {
                AppUserDeadlift::create([
                    'app_user_id' => $request->app_user_id,
                    'deadlift' => $request->deadlift,
                    'date' => $request->date,
                ]);
            }
            if ($request->filled('power_clean')) {
                AppUserPowerclean::create([
                    'app_user_id' => $request->app_user_id,
                    'power_clean' => $request->power_clean,
                    'date' => $request->date,
                ]);
            }
            if ($request->filled('squat')) {
                AppUserSquat::create([
                    'app_user_id' => $request->app_user_id,
                    'squat' => $request->squat,
                    'date' => $request->date,
                ]);
            }
            return redirect()->to(route('admin.' . $this->handle_name_plural . '.index'))->with('success', 'New ' . ucfirst($this->handle_name) . ' has been added.');
        } catch (Exception $e) {
            return $e->getMessage();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $table = Table::findOrFail($request->id);
            if ($request->filled('bench_press')) {
                $update_data = [
                    'app_user_id' => $request->app_user_id,
                    'date' => $request->date,
                    'bench_press' => $request->bench_press,
                ];
                $where = [
                    'id' => $request->id
                ];
                $benchPress = AppUserBenchpress::updateOrCreate($where, $update_data);
            }

            if ($request->filled('deadlift')) {
                $update_data = [
                    'app_user_id' => $request->app_user_id,
                    'date' => $request->date,
                    'deadlift' => $request->deadlift,
                ];
                $where = [
                    'id' => $request->id
                ];
                $deadlift = AppUserDeadlift::updateOrCreate($where, $update_data);
            }

            if ($request->filled('power_clean')) {
                $update_data = [
                    'app_user_id' => $request->app_user_id,
                    'date' => $request->date,
                    'power_clean' => $request->power_clean,
                ];
                $where = [
                    'id' => $request->id
                ];
                $powerClean = AppUserPowerClean::updateOrCreate($where, $update_data);
            }

            if ($request->filled('squat')) {
                $update_data = [
                    'app_user_id' => $request->app_user_id,
                    'date' => $request->date,
                    'squat' => $request->squat,
                ];
                $where = [
                    'id' => $request->id
                ];
                $squat = AppUserSquat::updateOrCreate($where, $update_data);
            }

            return redirect()->back()->with('success', ucfirst($this->handle_name) . ' has been updated');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function ajax(Request $request)
    {
        $edit_route = route('admin.' . $this->handle_name_plural . '.edit');
        $current_page = $request->page_number ?? 1; // Default to page 1 if not provided
        $limit = $request->limit ?? 10;
        $offset = ($current_page - 1) * $limit;
        $modalObject = AppUser::query();

        if (isset($request->string)) {
            $string = $request->string;
            $modalObject = $modalObject->where(function ($query) use ($string) {
                $query->where('name', 'like', "%" . $string . "%")
                    ->orWhere('username', 'like', "%" . $string . "%")
                    ->orWhere('dob', 'like', "%" . $string . "%")
                    ->orWhere('phone_number', 'like', "%" . $string . "%");
            });
        }

        $all_trashed = $request->all_trashed;
        if ($all_trashed == "trashed") {
            $modalObject = $modalObject->onlyTrashed();
        }

        $data = $modalObject
            ->with(['userDeadlifts', 'userPowerCleans', 'userSquats', 'userBenchpress'])
            ->orderBy('id', 'desc')
            ->get();

        $groupedData = [];

        foreach ($data as $v) {
            $v->appUser = optional($v->users)->name;

            if (count($v->userDeadlifts) > 0 || count($v->userPowerCleans) > 0 || count($v->userSquats) > 0 || count($v->userBenchpress) > 0) {
                $deadlifts = collect($v->userDeadlifts)->sortBy('date')->pluck('deadlift', 'date');
                $powerCleans = collect($v->userPowerCleans)->sortBy('date')->pluck('power_clean', 'date');
                $squats = collect($v->userSquats)->sortBy('date')->pluck('squat', 'date');
                $benchpress = collect($v->userBenchpress)->sortBy('date')->pluck('bench_press', 'date');
                $dates = $deadlifts->keys()->merge($powerCleans->keys())->merge($squats->keys())->merge($benchpress->keys())->unique();

                $v->dates = $dates;
                $v->benchpress = $benchpress;
                $v->deadlifts = $deadlifts;
                $v->powerCleans = $powerCleans;
                $v->squats = $squats;

                foreach ($dates as $date) {
                    $groupedData[$v->id . '_' . $date] = [
                        'app_user_id' => $v->id,
                        'date' => $date,
                        'name' => $v->name,
                        'benchpress' => $benchpress[$date] ?? '-',
                        'deadlift' => $deadlifts[$date] ?? '-',
                        'power_clean' => $powerCleans[$date] ?? '-',
                        'squat' => $squats[$date] ?? '-',
                        'created_at' => $v->created_at,
                        'deleted_at' => $v->deleted_at,
                    ];
                }
            }
        }

        $total_records = count($groupedData);
        $total_pages = ceil($total_records / $limit);
        $pagedGroupedData = array_slice($groupedData, $offset, $limit);

        $pagination = [
            "offset" => $offset,
            "total_records" => $total_records,
            "item_per_page" => $limit,
            "total_pages" => $total_pages,
            "current_page" => $current_page,
        ];

        return kview($this->handle_name_plural . '.ajax', compact('edit_route', 'pagedGroupedData', 'pagination'));
    }

    public function delete(Request $request)
    {
        if (isset($request->action)) {
            $action = $request->action;
            $is_bulk = $request->is_bulk;
            $data_id = $request->data_id;
            $date = $request->date;
            // \Log::info('date: ' . $date);
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
                        $users = AppUser::with('userDeadlifts', 'userPowerCleans', 'userSquats', 'userBenchpress')
                            ->whereIn('id', $data_id)
                            ->get();

                        foreach ($users as $user) {
                            foreach ($date as $singleDate) { // Loop through each date
                                $user->userDeadlifts()->where('date', $singleDate)->forceDelete();
                                $user->userPowerCleans()->where('date', $singleDate)->forceDelete();
                                $user->userSquats()->where('date', $singleDate)->forceDelete();
                                $user->userBenchpress()->where('date', $singleDate)->forceDelete();
                            }
                        }
                        return 1;
                    } else {
                        $user = AppUser::with('userDeadlifts', 'userPowerCleans', 'userSquats', 'userBenchpress')
                            ->where('id', $data_id)
                            ->first();

                        $user->userDeadlifts()->where('date', $date)->forceDelete();
                        $user->userPowerCleans()->where('date', $date)->forceDelete();
                        $user->userSquats()->where('date', $date)->forceDelete();
                        $user->userBenchpress()->where('date', $date)->forceDelete();
                        // \Log::info('dates: ' . $date);
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
                            $tbl->forceDelete();
                        }
                        return 1;
                    } else {

                        $table = Table::withTrashed()->find($data_id);
                        $data = $table->forceDelete();
                        // $table->forceDelete();
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
}
