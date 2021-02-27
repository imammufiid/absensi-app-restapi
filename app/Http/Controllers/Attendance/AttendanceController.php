<?php

namespace App\Http\Controllers\Absensi;

use App\Attendence;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $idEmploye = request("id_employe");
            $time = time();
            $attendance = Attendence::create([
                "user_id"       => $idEmploye,
                "date"          => date("d-M-Y", $time),
                "time_comes"    => date("H:i:s", $time),
                "time_gohome"   => 0
            ]);

            return response()->json([
                "meta" => object_meta(
                    Response::HTTP_CREATED,
                    "success",
                    "Success for Attendance"
                ),
                "data" => $attendance
            ], Response::HTTP_CREATED);
        } catch (Throwable $e) {
            $data = [
                "error" => $e
            ];
            return response()->json([
                "meta" => object_meta(
                    Response::HTTP_BAD_REQUEST,
                    "error",
                    "Failed for Attendance"
                ),
                "data" => $data
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Attendence  $attendence
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Attendence  $attendence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attendence $attendence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Attendence  $attendence
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attendence $attendence)
    {
        //
    }
}
