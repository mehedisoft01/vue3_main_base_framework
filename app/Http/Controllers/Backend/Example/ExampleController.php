<?php

namespace App\Http\Controllers\Backend\Example;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Example\Example;
use App\Models\Example\ExampleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ExampleController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new Example();
    }

    public function index()
    {
        if (!can('example.index')) {
            return $this->notPermitted();
        }

        try {
            $keyword = request()->input('keyword');
            $perPage = request()->input('per_page');

            $data = $this->model
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('example_name', 'like', "%{$keyword}%");
                    });
                })
                ->orderBy('id', 'DESC')
                ->paginate($perPage);

            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }


    public function store(Request $request)
    {
        if (!can('example.store')) {
            return $this->notPermitted();
        }
        try {
            DB::beginTransaction();

            $input = $request->all();
            $input['user_id'] = auth()->id();

            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(3000, $validate->errors());
            }

            $this->model->fill($input);
            $this->model->save();

            foreach ($request->example_items as $item) {
                ExampleItem::create([
                    'example_id'       => $this->model->id,
                    'item_name'        => $item['item_name'] ?? null,
                    'quantity'         => $item['quantity'] ?? 0,
                    'rate'             => $item['rate'] ?? 0,
                    'amount'           => $item['amount'] ?? 0,
                    'remarks'          => $item['remarks'] ?? null,
                ]);
            }

            DB::commit();

            return returnData(2000, null, 'Successfully Inserted');

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }


    public function edit($id)
    {
        if (!can('example.edit')) {
            return $this->notPermitted();
        }

        try {
            $data = $this->model->findOrFail($id);
            $data->setAttribute('example_items', $data->exampleItems);

            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }


    public function update(Request $request, $id)
    {
        if (!can('example.update')) {
            return $this->notPermitted();
        }
        try {
            DB::beginTransaction();

            $input = $request->all();
            $input['user_id'] = auth()->id();

            $validation = $this->model->validate($input);
            if ($validation->fails()) {
                return response()->json(['status' => 3000, 'errors' => $validation->errors()], 300);
            }

            $data = $this->model->findOrFail($id);
            $data->update($input);

            ExampleItem::where('example_id', $id)->delete();
            foreach ($request->construction_activity_items ?? [] as $item) {
                ExampleItem::create([
                    'example_id'       => $id,
                    'item_name'        => $item['item_name'] ?? null,
                    'quantity'         => $item['quantity'] ?? 0,
                    'rate'             => $item['rate'] ?? 0,
                    'amount'           => $item['amount'] ?? 0,
                    'remarks'          => $item['remarks'] ?? null,
                ]);
            }

            DB::commit();

            return returnData(2000, null, 'Successfully Updated');
        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }


    public function show($id)
    {
        if (!can('example.show')) {
            return $this->notPermitted();
        }

        try {
            $data = $this->model->findOrFail($id);
            $data->setAttribute('example_items', $data->exampleItems);

            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }


    public function destroy($id)
    {
        if (!can('example.destroy')) {
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


    public function multiple(Request $request)
    {
        if (!can('example.destroy')) {
            return $this->notPermitted();
        }

        $selectedKeys = $request->input('selectedKeys', []);
        $ids = is_array($selectedKeys) ? $selectedKeys : [];

        if (empty($ids)) {
            return returnData(4000, null, 'No IDs provided');
        }

        $deleted = [];
        $errors = [];

        foreach ($ids as $id) {
            $data = Example::where('id', $id)->first();

            if (!$data) {
                $errors[] = "ID {$id} not found";
                continue;
            }

            $data->delete();
            $deleted[] = $id;
        }

        return returnData(2000, null,count($deleted)." Item Deleted and ".json_encode($errors)." Item Not Deleted");
    }
}
