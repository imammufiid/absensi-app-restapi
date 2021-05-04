<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Config;
use App\User;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;


class AttendanceTest extends TestCase
{
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

        factory(Config::class)->create([
            'configuration'      => json_encode([
                "name"              => "office",
                "address"           => "Blitar City",
                "latitude"          => "-8.137035168730755",
                "longitude"         => "112.13864366932964",
                "time"              => "07:30:00",
                "attendance_point"  => 2,
                "task_point"        => 2
            ])
        ]);

        $token = JWTAuth::fromUser($user);

        $data = [
            'id_employee'   => $user->id,
            "latitude"      => "-8.137035168730755",
            "longitude"     => "112.13864366932964",
            'qr_code'       => $user->qrcode,
            'type_attendance'   => 1 // hadir
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

        factory(Config::class)->create([
            'configuration'      => json_encode([
                "name"              => "office",
                "address"           => "Blitar City",
                "latitude"          => "-8.137035168730755",
                "longitude"         => "112.13864366932964",
                "time"              => "07:30:00",
                "attendance_point"  => 2,
                "task_point"        => 2
            ])
        ]);

        $token = JWTAuth::fromUser($user);

        $data = [
            'id_employee'   => $user->id,
            "latitude"      => "-8.137035168730755",
            "longitude"     => "112.13864366932964",
            'qr_code'       => "123code",
            'type_attendance'   => 1
        ];

        $this->json('POST', 'api/attendance/scan?token=' . $token, $data, ['Accept' => 'Application/json'])
            ->assertStatus(400);
    }

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
            "type_attendance"   => 2, // ijin
            'keterangan'    => "Ijin telat 10 menit"
        ];

        $this->json('POST', 'api/attendance/scan?token=' . $token, $data, ['Accept' => 'Application/json'])
            ->assertStatus(201);
    }

    /**
     * absen ijin successfully.
     *
     * @return void
     */
    public function test_sakit_attendance_successfully()
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
            "type_attendance"   => 3, // sakit
            'keterangan'    => "Sakit perut",
            'file_keterangan'   => "file_surat.png"
        ];

        $this->json('POST', 'api/attendance/scan?token=' . $token, $data, ['Accept' => 'Application/json'])
            ->assertStatus(201);
    }
}
