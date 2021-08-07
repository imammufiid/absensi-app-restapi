<?php

namespace App\Http\Controllers\Attendance;

use App\AttendanceDetail;
use App\Attendence;
use App\Http\Controllers\Controller;
use App\LocationDetail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AttendanceController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api');
    // }

    /**
     * Display a listing of the data attedance.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $validator = Validator::make(request()->all(), [
        //     "user_id"    => "required"
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'meta' => object_meta(
        //             Response::HTTP_BAD_REQUEST,
        //             "failed",
        //             "Failed"
        //         ),
        //         'data' => $validator->errors()
        //     ], Response::HTTP_OK);
        // }

        if (request("is_admin") != null) {
            $dataAttendance = Attendence::whereRaw('created_at >=DATE_ADD(NOW(),INTERVAL -1 MONTH)')
                ->orderBy('date', 'ASC')
                ->orderBy('time_comes', 'ASC')
                ->get();
        } else {
            $dataAttendance = Attendence::whereRaw('created_at >=DATE_ADD(NOW(),INTERVAL -1 MONTH)')
                ->where("user_id", request("user_id"))
                ->orderBy('date', 'ASC')
                ->orderBy('time_comes', 'ASC')
                ->get();
        }


        if ($dataAttendance == null) {
            return response()->json([
                "meta" => object_meta(
                    Response::HTTP_NOT_FOUND,
                    "failed",
                    "Failed for Attendance"
                ),
                "data" => null
            ], Response::HTTP_NOT_FOUND);
        }

        foreach ($dataAttendance as $key => $value) {
            if ($dataAttendance[$key]->file_information != null) {
                $dataAttendance[$key]->file_information = URL::to($value->file_information);
            }
        }

        return response()->json([
            "meta" => object_meta(
                Response::HTTP_OK,
                "success",
                "List of Data Attendance"
            ),
            "data" => $dataAttendance
        ], Response::HTTP_OK);
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
                ], Response::HTTP_OK);
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
                if ($attendanceToday->file_information != null) {
                    $attendanceToday->file_information = URL::to($attendanceToday->file_information);
                }

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
     * Display the specified data attendance.
     *
     * @param  \App\Attendence  $attendence
     * @return \Illuminate\Http\Response
     */
    public function showByIdAttendance(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'attendance_id' => "required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_BAD_REQUEST,
                        "failed",
                        "Failed"
                    ),
                    'data' => $validator->errors()
                ], Response::HTTP_OK);
            }


            $id = request("attendance_id");

            $attendanceToday = Attendence::where('id', $id)->first();

            if ($attendanceToday == null) {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_NOT_FOUND,
                        "failed",
                        "Not Found"
                    ),
                    'data' => null
                ], Response::HTTP_OK);
            } else {
                if ($attendanceToday->file_information != null) {
                    $attendanceToday->file_information = URL::to($attendanceToday->file_information);
                }

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
                "latitude"      => "required",
                "longitude"      => "required",
                "attendance_type"   => "required"
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
                ], Response::HTTP_OK);
            }

            // data request
            $idEmploye  = request("id_employee");
            $attendanceType = request("attendance_type");
            $time = time();

            // get date this day
            $currentDate = date('d-m-Y');

            // check when come or go home
            $checkerAttendance = Attendence::where('date', $currentDate)->where("user_id", $idEmploye)->first();

            // check attendance type
            switch ($attendanceType) {
                case '1':
                    # hadir...
                    $qrCode     = request("qr_code");

                    // check name of qr code
                    if (!$this->checkValidationQrCode($qrCode, $idEmploye)) {
                        return response()->json([
                            "meta" => object_meta(
                                Response::HTTP_BAD_REQUEST,
                                "failed",
                                "Qr Code Not Valid"
                            ),
                            "data" => null
                        ], Response::HTTP_OK);
                    }

                    // calculate distance location employee and office
                    $calculateDistance = $this->calculateDistance(request("latitude"), request("longitude"));
                    $distance = (float) ($calculateDistance * 1.609344) * 1000;
                    if ($distance > 50) {
                        return response()->json([
                            "meta" => object_meta(
                                Response::HTTP_BAD_REQUEST,
                                "failed",
                                "Jarak anda masih " . number_format($distance, 1, '.', ',') . " M dari kantor"
                            ),
                            "data" => null
                        ], Response::HTTP_OK);
                    }

                    // check when come or go home
                    $checkerAttendance = Attendence::where('date', $currentDate)
                        ->first();

                    if ($checkerAttendance == null) {
                        // is comming
                        $time = time();
                        $attendance = Attendence::create([
                            "user_id"       => $idEmploye,
                            "date"          => date("d-m-Y", $time),
                            "time_comes"    => date("H:i:s", $time),
                            "time_gohome"   => 0,
                            "attendance_type"   => $attendanceType,
                            "information"   => "Hadir"
                        ]);

                        $attendanceLocation = LocationDetail::create([
                            'attendance_id' => $attendance->id,
                            'latitude'  => request('latitude'),
                            'longitude'  => request('longitude'),
                            'lbs'  => $calculateDistance,
                        ]);

                        return response()->json([
                            "meta" => object_meta(
                                Response::HTTP_CREATED,
                                "success",
                                "Success for Attendance Comming"
                            ),
                            "data" => [
                                "attendance"    => $attendance,
                                "location_attendance"   => $attendanceLocation
                            ]
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
                            ], Response::HTTP_OK);
                        }

                        // scan for attendance go home
                        $data = [
                            "time_gohome" => date("H:i:s", $time),
                            "information"   => "Pulang"
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
                            ], Response::HTTP_OK);
                        }
                    }

                    break;

                case '2':
                    # ijin...
                    if ($checkerAttendance != null) {
                        return response()->json([
                            "meta" => object_meta(
                                Response::HTTP_OK,
                                "failed",
                                "Anda Sudah Absen"
                            ),
                            "data" => null
                        ], Response::HTTP_OK);
                    }
                    $information = request("information");
                    $attendance = Attendence::create([
                        "user_id"       => $idEmploye,
                        "date"          => date("d-m-Y", $time),
                        "time_comes"    => date("H:i:s", $time),
                        "time_gohome"   => 0,
                        "attendance_type"   => $attendanceType,
                        "information"   => $information
                    ]);

                    return response()->json([
                        "meta" => object_meta(
                            Response::HTTP_CREATED,
                            "success",
                            "Success for Attendance Comming"
                        ),
                        "data" => $attendance
                    ], Response::HTTP_CREATED);

                    break;

                case '3':
                    # sakit...
                    $folder = "img/file_attendance/";
                    if ($checkerAttendance != null) {
                        return response()->json([
                            "meta" => object_meta(
                                Response::HTTP_OK,
                                "failed",
                                "Anda Sudah Absen"
                            ),
                            "data" => null
                        ], Response::HTTP_OK);
                    }
                    $information = request("information");
                    $fileInformation = request("file_information");
                    $dataUser = User::where("id", $idEmploye)->first();
                    $nameOfFolder = $folder . $dataUser->nik . "/";

                    $dir = "";
                    // check folder if exist
                    if (check_folder_public_if_not_exist($nameOfFolder)) {
                        $dir = create_folder_public($nameOfFolder);
                    } else {
                        $dir = public_path($nameOfFolder);
                    }
                    // save file into directory
                    $fileName = time() . "_" . $fileInformation->getClientOriginalName();
                    $fileInformation->move($dir, $fileName);

                    $attendance = Attendence::create([
                        "user_id"           => $idEmploye,
                        "date"              => date("d-m-Y", $time),
                        "time_comes"        => date("H:i:s", $time),
                        "time_gohome"       => 0,
                        "attendance_type"   => $attendanceType,
                        "information"       => $information,
                        "file_information"  => $nameOfFolder . $fileName
                    ]);

                    // replace file directory
                    $attendance->file_information = URL::to($folder) . $nameOfFolder . $fileName;

                    return response()->json([
                        "meta" => object_meta(
                            Response::HTTP_CREATED,
                            "success",
                            "Success for Attendance Comming"
                        ),
                        "data" => $attendance
                    ], Response::HTTP_CREATED);

                    break;

                default:
                    # code...
                    return response()->json([
                        "meta" => object_meta(
                            Response::HTTP_BAD_REQUEST,
                            "failed",
                            "Request Not Valid"
                        ),
                        "data" => null
                    ], Response::HTTP_OK);
                    break;
            }
        } catch (Throwable $e) {
            $data = [
                "error" => $e->getMessage()
            ];
            return response()->json([
                "meta" => object_meta(
                    Response::HTTP_CONFLICT,
                    "error",
                    "Failed for Attendance"
                ),
                "data" => $data
            ], Response::HTTP_CONFLICT);
        }
    }

    /**
     * Display the specified data attendance.
     *
     * @param  \App\Attendence  $attendence
     * @return \Illuminate\Http\Response
     */
    public function getLocationAttendance(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'attendance_id' => "required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_BAD_REQUEST,
                        "failed",
                        "Failed"
                    ),
                    'data' => $validator->errors()
                ], Response::HTTP_OK);
            }


            $id = request("attendance_id");

            $locationDetail = LocationDetail::where('attendance_id', $id)->first();

            if ($locationDetail == null) {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_NOT_FOUND,
                        "failed",
                        "Not Found"
                    ),
                    'data' => null
                ], Response::HTTP_OK);
            } else {
                if ($locationDetail->lbs != null) {
                    $distance = (float) ($locationDetail->lbs * 1.609344) * 1000;
                    $locationDetail->lbs = number_format($distance, 2, '.', ',');
                }

                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_OK,
                        "success",
                        "Attendance Location Detail"
                    ),
                    'data' => $locationDetail
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

    public function validateAttendance(Request $request)
    {
        try {
            // validation
            $validator = Validator::make($request->all(), [
                "attendance_id"     => "required",
                "is_admin"          => "required"
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
                ], Response::HTTP_OK);
            }

            // * get attendance by id
            $attendance = Attendence::where("id", request("attendance_id"))->first();

            if ($attendance == null) {
                return response()->json([
                    "meta" => object_meta(
                        Response::HTTP_NOT_FOUND,
                        "failed",
                        "Attendance Not Found"
                    ),
                    "data" => null
                ], Response::HTTP_NOT_FOUND);
            }

            $data["is_validate"] = 1;
            $attendanceUpdate = Attendence::where("id", request("attendance_id"))->update($data);

            if ($attendanceUpdate <= 0) {
                return response()->json([
                    "meta" => object_meta(
                        Response::HTTP_OK,
                        "failed",
                        "Attendance failed validation"
                    ),
                    "data" => null
                ], Response::HTTP_OK);
            }
            return response()->json([
                "meta" => object_meta(
                    Response::HTTP_OK,
                    "success",
                    "Attendance has been validation"
                ),
                "data" => $data
            ], Response::HTTP_OK);
        } catch (Throwable $e) {
            $data = [
                "error" => $e->getMessage()
            ];
            return response()->json([
                "meta" => object_meta(
                    Response::HTTP_CONFLICT,
                    "error",
                    "Failed for Validation Attendance"
                ),
                "data" => $data
            ], Response::HTTP_CONFLICT);
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

            return $miles;
            // switch ($unit) {
            //     case "K":
            //         return $miles * 1.609344;
            //         break;
            //     case "N":
            //         return $miles * 0.8684;
            //         break;
            //     case "M":
            //         return ($miles * 1.609344) * 1000;
            //         break;
            //     default:
            //         return $miles;
            //         break;
            // }
        }
    }
}
