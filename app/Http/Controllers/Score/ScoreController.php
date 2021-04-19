<?php

namespace App\Http\Controllers\Score;

use App\Attendence;
use App\Http\Controllers\Controller;
use App\Task;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ScoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "user_id"   => "required",
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
            // get point from task today


            // get point from attendance
            $pointOfAttendanceToday = 0;
            $pointOfAttendanceToday = $this->countPointFromAttendance(request("user_id"));

            return response()->json([
                'meta' => object_meta(
                    Response::HTTP_OK,
                    "success",
                    "your point"
                ),
                'data' => [
                    "point" => $pointOfAttendanceToday
                ]
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'meta' => object_meta(
                    Response::HTTP_SERVICE_UNAVAILABLE,
                    "error",
                    $e->getMessage()
                ),
                'data' => null
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    private function countPointFromAttendance($userId = 0)
    {
        $totalPoint = 0;
        $pointAttendance = Attendence::select('time_comes')
            ->where('user_id', $userId)
            ->where("date", date("d-m-Y"))
            ->first();

        // get data point from config
        $config = data_app_configuration("office");
        $dataConfig  = json_decode($config->configuration);
        $attendancePoint = (int) $dataConfig->attendance_point;
        $taskPoint = (int) $dataConfig->task_point;

        if ($pointAttendance != null) {
            // point attendance
            if (!$this->checkLateTime($pointAttendance->time_comes)) {
                $totalPoint += $attendancePoint;
            }
        }

        // point task
        $taskComplete = $this->checkTaskComplete(request("user_id")) * $taskPoint;
        $totalPoint += $taskComplete;


        return $totalPoint;
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

    private function checkTaskComplete($userId = 0)
    {
        $filterDate = date('d-m-Y');
        $taskIsComplete = Task::where('datetime', 'LIKE', '%' . $filterDate . '%')
            ->where('user_id', $userId)
            ->where("is_complete", 1)
            ->get();

        $countTask = $taskIsComplete->count();
        return $countTask;
    }
}
