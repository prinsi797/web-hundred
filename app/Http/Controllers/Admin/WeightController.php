<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppUserWeight as Table;
use App\Models\AppUser;
use App\Http\Requests\AppUserWeightsRequests\UpdateWeights as UpdateRequest;
use App\Http\Requests\AppUserWeightsRequests\AddWeights as AddRequest;
use Exception;
use Illuminate\Support\Carbon;

class WeightController extends Controller
{
    protected $handle_name = "app user weights";
    protected $handle_name_plural = "app_user_weights";

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
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
        try {
            $weights = Table::where('id', '=', $request->id)->first();
            $weights = Table::findOrFail($request->id);
            $users = AppUser::get();

            return kview($this->handle_name_plural . '.manage', [
                'form_action' => route('admin.' . $this->handle_name_plural . '.update'),
                'cancel' => route('admin.' . $this->handle_name_plural . '.index'),
                'edit' => 1,
                'data' => $weights,
                'users' => $users,
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function store(AddRequest $request)
    {
        try {
            $date = $request->filled('date') ? $request->date : Carbon::now()->toDateString();
            $table = Table::create([
                'app_user_id' => $request->app_user_id,
                'date' => $date,
                'weight' => $request->weight,
            ]);

            return redirect()->route('admin.' . $this->handle_name_plural . '.index')
                ->with('success', 'New ' . ucfirst($this->handle_name) . ' has been added.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(UpdateRequest $request)
    {
        try {
            $date = $request->filled('date') ? $request->date : Carbon::now()->toDateString();
            $update_data = [
                'app_user_id' => $request->app_user_id,
                'date' => $date,
                'weight' => $request->weight,
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

    public function ajax(Request $request)
    {
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
            $modalObject = $modalObject->where('date', 'like', "%" . $request->string . "%")
                ->orWhereHas('users', function ($query) use ($string) {
                    $query->where('name', 'like', '%' . $string . '%');
                });
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
    public function delete(Request $request)
    {
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
                        $table = Table::whereIn('id', $data_id);
                        $table->delete();
                        return 1;
                    } else {
                        $table = Table::find($data_id);
                        // Log::info('trsha data',$data_id);
                        $table->delete();
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
