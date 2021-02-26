<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        request()->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        if (!$token =  Auth::attempt(request()->only('email', 'password'))) {
            $is_success = 'false';
            return response()->json([
                'meta' => object_meta(Response::HTTP_UNAUTHORIZED, "failed", "Login Failed"),
                'data' => $is_success
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::where('email', request('email'))->first();
        $data = json_decode($user, true);
        $data['token'] = $token;

        return response()->json([
            'meta' => object_meta(Response::HTTP_OK, "success", "Login Successfuly"),
            'data' => $data
        ], Response::HTTP_OK);
    }
}
