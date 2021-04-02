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
            "name"  => "Office",
            "address"   => "Blitar City",
            "latitude"  => "0.0000000",
            "longitude"  => "000.0000000",
        ];

        $configLocationOffice->name = "office location";
        $configLocationOffice->configuration = json_encode($dataOfficeLocation);
        $configLocationOffice->status = 1;
        $configLocationOffice->save();
    }
}
