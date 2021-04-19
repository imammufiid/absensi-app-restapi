<?php

use App\SubCriteria;
use Illuminate\Database\Seeder;

class SubCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataDisiplin = [
            [
                "name"  => "Tidak Disiplin",
                "point" => 1
            ],
            [
                "name"  => "Kurang Disiplin",
                "point" => 2
            ],
            [
                "name"  => "Cukup Disiplin",
                "point" => 3
            ],
            [
                "name"  => "Disiplin",
                "point" => 4
            ],
            [
                "name"  => "Sangat Disiplin",
                "point" => 5
            ],
        ];
        $dataKejujuran = [
            [
                "name"  => "Sangat Kotor",
                "point" => 1
            ],
            [
                "name"  => "Kotor",
                "point" => 2
            ],
            [
                "name"  => "Cukup Bersih",
                "point" => 3
            ],
            [
                "name"  => "Bersih",
                "point" => 4
            ],
            [
                "name"  => "Sangat Bersih",
                "point" => 5
            ],
        ];
        $dataKomunikasi = [
            [
                "name"  => "Tidak Baik",
                "point" => 1
            ],
            [
                "name"  => "Kurang Baik",
                "point" => 2
            ],
            [
                "name"  => "Cukup Baik",
                "point" => 3
            ],
            [
                "name"  => "Baik",
                "point" => 4
            ],
            [
                "name"  => "Sangat Baik",
                "point" => 5
            ],
        ];
        $dataKerjasama = [
            [
                "name"  => "Tidak Baik",
                "point" => 1
            ],
            [
                "name"  => "Kurang Baik",
                "point" => 2
            ],
            [
                "name"  => "Cukup Baik",
                "point" => 3
            ],
            [
                "name"  => "Baik",
                "point" => 4
            ],
            [
                "name"  => "Sangat Baik",
                "point" => 5
            ],
        ];
        $dataTanggungJawab = [
            [
                "name"  => "Tidak Bertanggungjawab",
                "point" => 1
            ],
            [
                "name"  => "Kurang",
                "point" => 2
            ],
            [
                "name"  => "Cukup",
                "point" => 3
            ],
            [
                "name"  => "Bertanggungjawab",
                "point" => 4
            ],
            [
                "name"  => "Sangat Bertanggungjawab",
                "point" => 5
            ],
        ];


        $kedisiplinan = new SubCriteria();
        $kedisiplinan->criteria_id = 1;
        $kedisiplinan->name = json_encode($dataDisiplin);
        $kedisiplinan->save();

        $kejujuran = new SubCriteria();
        $kejujuran->criteria_id = 2;
        $kejujuran->name = json_encode($dataKejujuran);
        $kejujuran->save();

        $komunikasi = new SubCriteria();
        $komunikasi->criteria_id = 3;
        $komunikasi->name = json_encode($dataKomunikasi);
        $komunikasi->save();

        $kerjasama = new SubCriteria();
        $kerjasama->criteria_id = 4;
        $kerjasama->name = json_encode($dataKerjasama);
        $kerjasama->save();

        $tanggungJawab = new SubCriteria();
        $tanggungJawab->criteria_id = 4;
        $tanggungJawab->name = json_encode($dataTanggungJawab);
        $tanggungJawab->save();
    }
}
