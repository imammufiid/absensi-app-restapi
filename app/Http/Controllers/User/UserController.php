<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get by user
        try {
            $userId = request('user_id');

            if ($userId == null) {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_BAD_REQUEST,
                        "failed",
                        "Failed Bad Request"
                    ),
                    'data' => null
                ], Response::HTTP_BAD_REQUEST);
            } else {
                

                $user = User::where("id", $userId)->first();
                if (empty($user)) {
                    return response()->json([
                        'meta' => object_meta(
                            Response::HTTP_NOT_FOUND,
                            "failed",
                            "User Not Found"
                        ),
                        'data' => null
                    ], Response::HTTP_NOT_FOUND);
                } else {
                    return response()->json([
                        'meta' => object_meta(
                            Response::HTTP_OK,
                            "success",
                            "This data user"
                        ),
                        'data' => $user
                    ], Response::HTTP_OK);
                }
            }
        } catch(Exception $e) {
            return response()->json([
                'meta' => object_meta(
                    Response::HTTP_EXPECTATION_FAILED,
                    "error",
                    "Error Server "
                ),
                'data' => null
            ], Response::HTTP_EXPECTATION_FAILED);
        }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
