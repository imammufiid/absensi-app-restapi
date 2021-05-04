<?php

namespace Tests\Unit\Attendance;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AttendanceIjinTest extends TestCase
{
    /**
     * absen ijin successfully.
     *
     * @return void
     */
    public function test_ijin_attendance_successfully()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create([
            'name'      => 'Imam',
            'email'     => "imam@gmail.com",
            'nik'       => "123123123",
            'qrcode'    => "code123",
            'is_admin'  => 1
        ]);

        $token = JWTAuth::fromUser($user);

        $data = [
            'id_employee'   => $user->id,
            "latitude"      => "-8.137035168730755",
            "longitude"     => "112.13864366932964",
            "attendance_type"   => 2, // ijin
            'information'    => "Ijin telat 10 menit"
        ];

        $this->json('POST', 'api/attendance/scan?token=' . $token, $data, ['Accept' => 'Application/json'])
            ->assertStatus(201);
    }
}
