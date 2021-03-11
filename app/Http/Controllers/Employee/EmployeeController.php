<?php
namespace App\Http\Controllers\Employee;
use App\Http\Controllers\Controller;
use App\User;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth:api');
	}

	public function index()
	{
	  $dataAttendance = User::where("is_admin", 0)->get();

	  if($dataAttendance != null) {
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
}
