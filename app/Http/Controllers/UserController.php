<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{


    function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::updateOrCreate(
            ['email' => $request->email], // Search condition
            [
                'name' => $request->givenName,
                'family_name' => $request->familyName,
                'google_id' => $request->id,
                'photo' => $request->photo,
            ]
        );
        $token=$user->createToken('User')->plainTextToken;
        $user['token']=$token;
        return response()->json(["status" => true, "message" => "success", "user" => $user], 200);
    }
    function guest_login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'mobile_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $data = User::updateOrCreate(
            ['email' => $request->mobile_id],
        );
        $user=User::where("email",$request->mobile_id)->first();

        $token=$user->createToken('User')->plainTextToken;
        $user['token']=$token;
        return response()->json(["status" => true, "message" => "success", "user" => $user], 200);
    }
}
