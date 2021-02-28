<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Task;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
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
            // data datetime db d-M-Y H:i:s (data type datetime)
            // data date filter d-M-Y
            $task = Task::where("DATE_FORMAT(datetime, '%d-%m-%Y')", '\'' . $filterDate . '\'')
                ->get();
        } else {
            // employe
            $task = Task::where("user_id", $userId)
                ->where("DATE_FORMAT(datetime, '%d-%m-%Y')", '\'' . $filterDate . '\'', false)
                ->get();
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
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
        //
    }
}
