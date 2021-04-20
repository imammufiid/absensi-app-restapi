<?php

namespace App\Http\Controllers\Attendance;

use App\Attendence;
use App\Http\Controllers\Controller;
use App\MyCons;
use App\SawScore;
use App\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the data attedance.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $validator = Validator::make(request()->all(), [
            "user_id"    => "required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'meta' => object_meta(
                    Response::HTTP_BAD_REQUEST,
                    "failed",
                    "Failed"
                ),
                'data' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $dataAttendance = Attendence::where("user_id", request("user_id"))
            ->orderBy('date', 'DESC')
            ->get();

        if ($dataAttendance != null) {
            return response()->json([
                "meta" => object_meta(
                    Response::HTTP_OK,
                    "success",
                    "List of Data Attendance"
                ),
                "data" => $dataAttendance
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                "meta" => object_meta(
                    Response::HTTP_NOT_FOUND,
                    "failed",
                    "Failed for Attendance"
                ),
                "data" => null
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Display the specified data attendance.
     *
     * @param  \App\Attendence  $attendence
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_employee' => "required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_BAD_REQUEST,
                        "failed",
                        "Failed"
                    ),
                    'data' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }


            $idEmploye = request("id_employee");
            $today = date("d-m-Y");

            $attendanceToday = Attendence::where('user_id', $idEmploye)
                ->where('date', $today)->first();

            if ($attendanceToday == null) {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_NOT_FOUND,
                        "failed",
                        "Belum Absen hari ini"
                    ),
                    'data' => null
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_OK,
                        "success",
                        "Attendance Today"
                    ),
                    'data' => $attendanceToday
                ], Response::HTTP_OK);
            }
        } catch (Throwable $e) {
            $data = [
                "error" => $e
            ];
            return response()->json([
                "meta" => object_meta(
                    Response::HTTP_EXPECTATION_FAILED,
                    "error",
                    "Error Handling"
                ),
                "data" => $data
            ], Response::HTTP_EXPECTATION_FAILED);
        }
    }

    /**
     * Employe has been go home.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Attendence  $attendence
     * @return \Illuminate\Http\Response
     */
    public function scan(Request $request)
    {
        try {
            // validation
            $validator = Validator::make($request->all(), [
                "id_employee"   => "required",
                "qr_code"       => "required",
                "latitude"      => "required",
                "longitude"      => "required",
            ]);

            // validation error
            if ($validator->fails()) {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_BAD_REQUEST,
                        "failed",
                        "Failed"
                    ),
                    'data' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            // data request
            $idEmploye  = request("id_employee");
            $qrCode     = request("qr_code");
            $time       = time();

            // get date this day
            $currentDate = date('d-m-Y');

            // check name of qr code
            if (!$this->checkValidationQrCode($qrCode, $idEmploye)) {
                return response()->json([
                    "meta" => object_meta(
                        Response::HTTP_BAD_REQUEST,
                        "failed",
                        "Qr Code Not Valid"
                    ),
                    "data" => null
                ], Response::HTTP_BAD_REQUEST);
            }

            // calculate distance location employee and office
            $distance = $this->calculateDistance(request("latitude"), request("longitude"));
            if ($distance > 50) {
                return response()->json([
                    "meta" => object_meta(
                        Response::HTTP_BAD_REQUEST,
                        "failed",
                        "Jarak anda masih " . number_format($distance, 1, '.', ',') . " M dari kantor"
                    ),
                    "data" => null
                ], Response::HTTP_BAD_REQUEST);
            }

            // check when come or go home
            $checkerAttendance = Attendence::where('date', $currentDate)
                ->where('user_id', $idEmploye)
                ->first();

            if ($checkerAttendance == null) {
                // is comming
                $time = time();
                $attendance = Attendence::create([
                    "user_id"       => $idEmploye,
                    "date"          => date("d-m-Y", $time),
                    "time_comes"    => date("H:i:s", $time),
                    "time_gohome"   => 0
                ]);

                /**
                 * store point kedisiplinan to saw score table
                 */
                $point = MyCons::SangatTinggi; // on-time  
                if ($this->checkLateTime(date("H:i:s", $time))) {
                    $point = MyCons::Rendah; // late
                }
                SawScore::create([
                    "user_id"       => $idEmploye,
                    "criteria_id"   => 1,
                    "point"         => $point,
                    "date"          => date('Y-m-d')
                ]);

                return response()->json([
                    "meta" => object_meta(
                        Response::HTTP_CREATED,
                        "success",
                        "Success for Attendance Comming"
                    ),
                    "data" => $attendance
                ], Response::HTTP_CREATED);
            } else {
                // is go home
                if ($checkerAttendance->time_gohome != 0) {
                    // already home
                    return response()->json([
                        "meta" => object_meta(
                            Response::HTTP_BAD_REQUEST,
                            "failed",
                            "Anda Sudah Absen Pulang"
                        ),
                        "data" => null
                    ], Response::HTTP_BAD_REQUEST);
                }

                // scan for attendance go home
                $data = [
                    "time_gohome" => date("H:i:s", $time),
                ];
                $attendance = Attendence::where('date', $currentDate)
                    ->where('user_id', $idEmploye)
                    ->update($data);

                if ($attendance > 0) {
                    return response()->json([
                        "meta" => object_meta(
                            Response::HTTP_CREATED,
                            "success",
                            "Success for Go Home Attendance"
                        ),
                        "data" => $attendance
                    ], Response::HTTP_CREATED);
                } else {
                    return response()->json([
                        "meta" => object_meta(
                            Response::HTTP_BAD_REQUEST,
                            "failed",
                            "Failed for Go Home Attendance"
                        ),
                        "data" => $attendance
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
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

    private function checkValidationQrCode($qrCode = "", $idEmploye = 0)
    {
        $data = User::where("id", $idEmploye)
            ->where("qrcode", $qrCode)
            ->first();

        if ($data != null) {
            return true;
        }

        return false;
    }

    private function calculateDistance($latitude = 0, $longitude = 0, $unit = "M")
    {
        $dataConfiguration = data_app_configuration('office');
        $officeLocation = json_decode($dataConfiguration->configuration);

        if (($latitude == $officeLocation->latitude) && ($longitude == $officeLocation->longitude)) {
            return 0;
        } else {
            $theta = $longitude - $officeLocation->longitude;
            $dist = sin(deg2rad($latitude))
                * sin(deg2rad($officeLocation->latitude))
                + cos(deg2rad($latitude))
                * cos(deg2rad($officeLocation->latitude))
                * cos(deg2rad($theta));

            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            switch ($unit) {
                case "K":
                    return $miles * 1.609344;
                    break;
                case "N":
                    return $miles * 0.8684;
                    break;
                case "M":
                    return ($miles * 1.609344) * 1000;
                    break;
                default:
                    return $miles;
                    break;
            }
        }
    }

    private function checkLateTime($timeFrom = "")
    {
        $dataConfiguration = data_app_configuration('office');
        $config = json_decode($dataConfiguration->configuration);

        $timeTo = DateTime::createFromFormat('H:i:s', $config->time);
        $from = DateTime::createFromFormat('H:i:s', $timeFrom);

        if ($from > $timeTo) { // late
            return true;
        }

        return false;
    }

    private function checkYesterday($userId = 0)
    {
        $yesterday = date('d-m-Y', strtotime("-1 days"));
        $isComming = true;
        $result = Attendence::where("user_id", $userId)
            ->where("date", $yesterday)
            ->first();

        if ($result == null) {
            $isComming = false;
        }

        return $isComming;
    }
}
