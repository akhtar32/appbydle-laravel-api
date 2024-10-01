<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ControllerExpenses extends Controller
{

    function index(Request $request)
    {
        $id = Auth::guard('user-api')->user()->id;
        $query=Expense::query();
        $days=$request->input('days');
        if(!empty($days)){
          $query->whereBetween('created_at', [Carbon::now()->subDays($days), Carbon::now()]);
        }
        $data = $query->where("user_id", $id)->latest('id')->get();
        $data->each(function ($row) {
            $row['image'] = url("uploads/" . $row->image);
        });
        return response()->json(["status" => true, "message" => "success", "data" => $data], 200);

    }


    function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "expenses_name" => "required",
            "date" => "required",
            'currency' => 'required',
            'total' => 'required',
            'category' => 'required',
            'description' => 'required',
            "image" => "required|mimes:jpeg,png,jpg,webp",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $data = new Expense();
            $data->user_id = Auth::guard('user-api')->user()->id;
            $data->email = $request->email;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $file->move(public_path('uploads/'), $fileName);
                $data->image = $fileName;
            }
            $data->expenses_name = $request->expenses_name;
            $data->date = $request->date;
            $data->currency = $request->currency;
            $data->total = $request->total;
            $data->category = $request->category;
            $data->description = $request->description;
            $data->save();
            return response()->json(["status" => true, "message" => "success"], 201);
        } catch (\Exception $e) {
            return response()->json(["status" => false, "message" => "Error" . $e->getMessage()], 500);
        }


    }

    function delete($id)
    {
        try {
            $data = Expense::find($id);
            if (!$data) {
                return response()->json(["status" => false, "message" => "recored not found"], 404);
            }
            $data->delete();
            return response()->json(["status" => true, "message" => "recored delete successfully"], 200);
        } catch (\Exception $e) {
            return response()->json(["status" => false, "message" => "Error" . $e->getMessage()], 500);
        }

    }
}
