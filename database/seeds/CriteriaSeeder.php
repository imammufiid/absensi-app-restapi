<?php

use App\Criteria;
use Illuminate\Database\Seeder;

class CriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $configLocationOffice = new Criteria();
        $configLocationOffice->name = "Kedisiplinan";
        $configLocationOffice->type = "B";
        $configLocationOffice->weight = 25;
        $configLocationOffice->save();

        $configLocationOffice2 = new Criteria();
        $configLocationOffice2->name = "Kejujuran";
        $configLocationOffice2->type = "B";
        $configLocationOffice2->weight = 15;
        $configLocationOffice2->save();
        
        $configLocationOffice3 = new Criteria();
        $configLocationOffice3->name = "Komunikasi";
        $configLocationOffice3->type = "B";
        $configLocationOffice3->weight = 15;
        $configLocationOffice3->save();

        $configLocationOffice4 = new Criteria();
        $configLocationOffice4->name = "kerjasama";
        $configLocationOffice4->type = "B";
        $configLocationOffice4->weight = 15;
        $configLocationOffice4->save();

        $configLocationOffice5 = new Criteria();
        $configLocationOffice5->name = "Tanggung Jawab";
        $configLocationOffice5->type = "B";
        $configLocationOffice5->weight = 30;
        $configLocationOffice5->save();
    }
}
