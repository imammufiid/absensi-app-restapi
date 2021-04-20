<?php

namespace App\Http\Controllers\SAW;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SAWController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function calculateAlgorithm()
    {
        /**
         * 1. get all alternative
         * 2. delete all row on saw table
         * 3. get all criteria data
         * 4. get score saw from saw score table
         * 5. get all column type from criteria data
         * 6. get min max value by type of criteria
         * 7. change data by type criteria
         * 8. calculate weight with criteria weight
         * 9. result rank of employee
         */
    }
}
