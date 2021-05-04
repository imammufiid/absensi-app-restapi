<?php

use App\Config;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $configLocationOffice = new Config();

        $dataOfficeLocation = [
            "name"              => "Office",
            "address"           => "Blitar City",
            "latitude"          => "-8.137035168730755",
            "longitude"         => "112.13864366932964",
            "time"              => "07:30:00",
            "attendance_point"  => 2,
            "task_point"        => 2
        ];

        $configLocationOffice->name = "office";
        $configLocationOffice->configuration = json_encode($dataOfficeLocation);
        $configLocationOffice->status = 1;
        $configLocationOffice->save();
    }
}
