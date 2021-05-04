<?php

namespace Tests\Unit\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Config;
use App\User;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AttendanceHadirTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    /**
     * scan qr code hadir successfully.
     *
     * @return void
     */
    public function test_scan_present_attendance_successfully()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create([
            'name'      => 'Imam',
            'email'     => "imam@gmail.com",
            'nik'       => "123123123",
            'qrcode'    => "code123",
            'is_admin'  => 1
        ]);

        factory(Config::class)->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'id_employee'       => $user->id,
            "latitude"          => "-8.137035168730755",
            "longitude"         => "112.13864366932964",
            'qr_code'           => $user->qrcode,
            'attendance_type'   => 1 // hadir
        ];

        $this->json('POST', 'api/attendance/scan?token=' . $token, $data, ['Accept' => 'Application/json'])
            ->assertStatus(201);
    }

    /**
     * scan qr code hadir successfully.
     *
     * @return void
     */
    public function test_qrcode_notvalid_attendance_successfully()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create([
            'name'      => 'Imam',
            'email'     => "imam@gmail.com",
            'nik'       => "123123123",
            'qrcode'    => "code123",
            'is_admin'  => 1
        ]);

        factory(Config::class)->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'id_employee'   => $user->id,
            "latitude"      => "-8.137035168730755",
            "longitude"     => "112.13864366932964",
            'qr_code'       => "123code",
            'attendance_type'   => 1
        ];

        $this->json('POST', 'api/attendance/scan?token=' . $token, $data, ['Accept' => 'Application/json'])
            ->assertStatus(400);
    }
}
