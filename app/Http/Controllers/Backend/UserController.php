<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use Helper;
    public function __construct()
    {
        if (!can(request()->route()->action['as'])) {
            return returnData(5001, null, 'You are not authorized to access this page');
        }
        $this->model = new User();
    }

    public function index()
    {
        $perPage = request()->input('perPage', 10);

        $data = User::paginate($perPage);

        return returnData(2000, $data);
    }
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        if (!can('users.store')) {
            return $this->notPermitted();
        }
        try{
            $data = $request->all();
            $data['is_superadmin'] = isset($data['is_superadmin']) && $data['is_superadmin'] ? 1 : 0;

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            $this->model->fill($data);
            $this->model->save();

            return returnData(2000, null, 'Successfully Inserted');
        }catch (\Exception $exception){
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }

    }

    public function show($id)
    {
        $perPage = request()->input('perPage');
        $data = $this->model->where('id', $id)->orderBy('id', 'DESC')->paginate($perPage);
        return returnData(2000, $data);
    }



    public function update(Request $request, $id)
    {
        if (!can('users.update')) {
            return $this->notPermitted();
        }
        $existingData = $this->model->find($id);
        if (!$existingData) {
            return returnData(5000, null, 'Data Not Found');
        }
        $data = $request->all();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $existingData->fill($data);
        $existingData->save();

        return returnData(2000, null, 'Successfully Updated');
    }

    public function destroy($id)
    {
        if (!can('users.destroy')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->where('id', $id)->first();
            if (!$data) {
                return returnData(5000, null, 'Data Not found');
            }

            $data->delete();

            return returnData(2000, $data, 'Successfully Deleted');

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
}
