<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        Auth::logout();
        $data['is_success'] = true;
        return response()->json([
            'meta' => object_meta(Response::HTTP_OK, "success", "Logout"),
            'data' => $data
        ], Response::HTTP_OK);
    }
}
