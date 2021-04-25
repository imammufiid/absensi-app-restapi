<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\MyCons;
use App\SawScore;
use App\Task;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $isAdmin    = request('is_admin');
        $userId     = request('user_id');

        $filterDate = (request('date') == null)
            ? date('d-m-Y')
            : request('date');

        if ($isAdmin != null) {
            // admin
            $task = Task::where('datetime', 'LIKE', '%' . $filterDate . '%')
                ->where('user_id', $userId)
                ->get();
        } else {
            // employe
            $task = Task::where('datetime', 'LIKE', '%' . $filterDate . '%')
                ->where('user_id', $userId)
                ->get();
        }

        foreach ($task as $key => $value) {
            $task[$key]['file'] = URL::to($value->file);
        }

        if ($task == null) {
            return response()->json([
                'meta' => object_meta(
                    Response::HTTP_NOT_FOUND,
                    "failed",
                    "Failed"
                ),
                'data' => $task
            ], Response::HTTP_NOT_FOUND);
        } else {
            return response()->json([
                'meta' => object_meta(
                    Response::HTTP_OK,
                    "success",
                    "This all data task"
                ),
                'data' => $task
            ], Response::HTTP_OK);
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
        try {
            $validator = Validator::make($request->all(), [
                'user_id'   => "required",
                'task'      => "required"
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

            if (request('is_admin') == 1) {

                $task = Task::create([
                    'user_id'       => request('user_id'),
                    'task'          => request('task'),
                    'is_complete'   => 0,
                    'datetime'      => date('d-m-Y H:i:s')
                ]);

                /**
                 * insert default point saw for task
                 */
                // 1. cek apakah hari itu di saw score sudah ada?
                // 2. jika belum -> insert, jika sudah berarti update
                $sawScoreTask = $this->checkSawScoreTask(request('user_id'));
                if (!$sawScoreTask['is_there']) {
                    // no there
                    SawScore::create([
                        'user_id'   => request('user_id'),
                        'criteria_id'    => MyCons::TanggungJawab,
                        'point'         => MyCons::SangatRendah,
                        'date'          => date('Y-m-d')
                    ]);
                }

                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_CREATED,
                        "success",
                        "Task has been Added"
                    ),
                    'data' => $task
                ], Response::HTTP_CREATED);
            } else {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_FORBIDDEN,
                        "failed",
                        "You must be admin"
                    ),
                    'data' => null
                ], Response::HTTP_FORBIDDEN);
            }
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try {
            $idTask = request('id_task');
            if ($idTask == null) {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_BAD_REQUEST,
                        "failed",
                        "Failed request"
                    ),
                    'data' => null
                ], Response::HTTP_BAD_REQUEST);
            } else {

                $task = Task::where("id", $idTask)->get();
                if (empty($task)) {
                    return response()->json([
                        'meta' => object_meta(
                            Response::HTTP_OK,
                            "success",
                            "This data Task"
                        ),
                        'data' => $task
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'meta' => object_meta(
                            Response::HTTP_NOT_FOUND,
                            "failed",
                            "Data Task Not Found"
                        ),
                        'data' => $task
                    ], Response::HTTP_NOT_FOUND);
                }
            }
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function markComplete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_task'   => "required",
                "user_id"   => "required",
                "file"      => "required|mimes:doc,docx,pdf,jpg,jpeg,png,xlsx,xls"
            ]);

            // request validation
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

            $idTask = request('id_task');
            if ($idTask == null) {
                return response()->json([
                    'meta' => object_meta(
                        Response::HTTP_BAD_REQUEST,
                        "failed",
                        "Failed request"
                    ),
                    'data' => null
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $dataUser = User::where("id", request("user_id"))->first();
                $nameOfFolder = "document_task/" . $dataUser->nik . "/";
                $dir = "";
                // check folder if exist
                if (check_folder_public_if_not_exist($nameOfFolder)) {
                    $dir = create_folder_public($nameOfFolder);
                } else {
                    $dir = public_path($nameOfFolder);
                }

                // save file into directory
                $fileName = time() . "_" . request("file")->getClientOriginalName();
                request("file")->move($dir, $fileName);

                // data 
                $data = [
                    'is_complete'   => 1,
                    'file'          => $nameOfFolder . $fileName
                ];

                // action db
                $task = Task::where("id", $idTask)->update($data);
                $taskResult = Task::where("id", $idTask)->first();

                // set Score Task SAW algorithm
                $point = $this->checkTaskComplete(request('user_id'));

                SawScore::where('user_id', request('user_id'))
                    ->where('date', date('Y-m-d'))
                    ->update([
                        'point' => (int) $point,
                    ]);

                // get task point
                // $config = data_app_configuration("office");
                // $dataConfig  = json_decode($config->configuration);
                // $taskPoint = $dataConfig->task_point;

                // list of task ----------------------------
                // $listTask = Task::where('datetime', 'LIKE', '%' . date("d-m-Y") . '%')
                //     ->where('user_id', request("user_id"))
                //     ->get();
                // -----------------------------------------

                $data = [
                    "id"            => $taskResult->id,
                    "user_id"       => $taskResult->user_id,
                    "task"          => $taskResult->task,
                    "is_complete"   => $taskResult->is_complete,
                    "file"          => public_path() . $taskResult->file,
                    "datetime"      => $taskResult->datetime,
                ];
                if ($task > 0) {
                    return response()->json([
                        'meta' => object_meta(
                            Response::HTTP_OK,
                            "success",
                            "Task has completed"
                        ),
                        'data' => $data
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'meta' => object_meta(
                            Response::HTTP_NOT_FOUND,
                            "failed",
                            "Data Task Not Found"
                        ),
                        'data' => $taskResult
                    ], Response::HTTP_NOT_FOUND);
                }
            }
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

    private function checkSawScoreTask($userId = 0)
    {
        $isThere = SawScore::where('user_id', $userId)
            ->where('criteria_id', MyCons::TanggungJawab)
            ->where('date', date('Y-m-d'))
            ->first();

        $result = [];

        if ($isThere == null) {
            $result = [
                'is_there'  => false,
                'data_id'   => null
            ];
        } else {
            $result = [
                'is_there'  => true,
                'data_id'   => $isThere->id
            ];
        }

        return $result;
    }

    private function checkTaskComplete($userId = 0)
    {
        $filterDate = date('d-m-Y');
        $allTask = Task::where('datetime', 'LIKE', '%' . $filterDate . '%')
            ->where('user_id', $userId)
            ->get();

        $taskIsComplete = Task::where('datetime', 'LIKE', '%' . $filterDate . '%')
            ->where('user_id', $userId)
            ->where("is_complete", 1)
            ->get();

        $countTask = MyCons::Cukup;
        if ($taskIsComplete->count() == $allTask->count()) {
            $countTask = MyCons::SangatTinggi;
        }

        return $countTask;
    }
}
