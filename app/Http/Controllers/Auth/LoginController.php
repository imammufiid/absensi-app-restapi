<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\UserSessions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
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
            'email' => 'email|required',
            'password' => 'required|min:8'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'meta' => object_meta(
                    Response::HTTP_BAD_REQUEST,
                    "failed",
                    "Failed"
                ),
                'data' => $validation->errors()
            ], Response::HTTP_OK);
        }

        // check session
        $session = UserSessions::where("email", request("email"))->first();
        if ($session != null && $session->is_login == 1) {
            // $device = json_decode($session->detail_login);
            return response()->json([
                'meta' => object_meta(
                    Response::HTTP_FORBIDDEN,
                    "failed",
                    "Akun anda masih login pada device lain"
                ),
                'data' => null
            ], Response::HTTP_OK);
        } else {
            if (!$token =  Auth::attempt(request()->only('email', 'password'))) {
                $data['is_success'] = 'false';
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_UNAUTHORIZED,
                        "failed",
                        "Username/password salah"
                    ),
                    'data' => $data
                ], Response::HTTP_OK);
            }

            $user = User::where('email', request('email'))->first();
            $data = json_decode($user, true);
            $data['token'] = $token;

            UserSessions::create([
                "user_id"   => $user->id,
                "email"     => $user->email,
                "detail_login"  => "",
                "is_login"  => 1
            ]);
        }

        return response()->json([
            'meta' => object_meta(
                Response::HTTP_OK,
                "success",
                "Login Successfuly"
            ),
            'data' => $data
        ], Response::HTTP_OK);
    }
}
