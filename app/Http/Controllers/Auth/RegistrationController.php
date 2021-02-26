<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(RegisterRequest $request)
    {
        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'nik' => request('nik'),
            'is_admin' => 0,
            'password' => bcrypt(request('password'))
        ]);

        return response()->json([
            'meta' => object_meta(Response::HTTP_CREATED, "success", "Account has ben Registered"),
            'data' => $user
        ], Response::HTTP_CREATED);
    }
}
