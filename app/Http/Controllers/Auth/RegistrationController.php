<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class RegistrationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        // validation 
        $validation = Validator::make($request->all(), [
            'name' => 'required|min:3|max:25',
            'nik' => 'required|unique:users,nik',
            'email' => 'email|required|unique:users,email',
            'password' => 'required|min:8'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'meta' => object_meta(
                    Response::HTTP_BAD_REQUEST,
                    "failed",
                    "Failed"
                ),
                'data' => $validation->errors()
            ], Response::HTTP_BAD_REQUEST);
        }
        $nameOfCode = "KR" . "_" . request('nik') . "_" . $this->_generateRandomString();

        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'nik' => request('nik'),
            'is_admin' => 0,
            'qrcode'    => $nameOfCode,
            'password' => bcrypt(request('password'))
        ]);

        $this->_generateQrCode($nameOfCode);


        return response()->json([
            'meta' => object_meta(
                Response::HTTP_CREATED,
                "success",
                "Account has ben Registered"
            ),
            'data' => $user
        ], Response::HTTP_CREATED);
    }

    /**
     * QrCode Generator.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    private function _generateQrCode($code = "")
    {
        $qrCode = new QrCode($code);

        $qrCode->setSize(500);
        $qrCode->setMargin(20);

        // Set advanced options
        $qrCode->setWriterByName('png');
        $qrCode->setEncoding('UTF-8');
        $qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_MARGIN);

        // Save it to a file
        $path = public_path('img/qrcode_user/' . $code . '.png');
        $qrCode->writeFile($path);
    }

    private function _generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
