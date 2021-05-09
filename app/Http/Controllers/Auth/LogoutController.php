<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\UserSessions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // validation 
        $validation = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'meta' => object_meta(
                    Response::HTTP_BAD_REQUEST,
                    "failed",
                    "Failed"
                ),
                'data' => $validation->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        // delete session login
        $sessionDelete = UserSessions::where("user_id", request("user_id"))->delete();

        if ($sessionDelete <= 0) {
            return response()->json([
                'meta' => object_meta(
                    Response::HTTP_OK, 
                    "failed", 
                    "Gagal delete session"),
                'data' => null
            ], Response::HTTP_OK);
        }

        // Auth::logout();
        $data['is_success'] = true;
        return response()->json([
            'meta' => object_meta(
                Response::HTTP_OK, 
                "success", 
                "Logout"),
            'data' => $data
        ], Response::HTTP_OK);
    }
}
