<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stats;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    protected $stat;

    public function __construct()
    {
        $this->stat = new Stats();
    }

    public function test()
    {
        $this->stat->get_start_end_date_of_week(2018, 29);
    }
}
