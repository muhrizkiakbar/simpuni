<?php

namespace App\Http\Controllers\Admin;

use App\Outputs\Admin\DutyOutput;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DutyService;

class DutyController extends Controller
{
    protected $dutyService;

    public function __construct()
    {
        $this->dutyService = new DutyService(Auth::user());
    }

    public function index(Request $request)
    {
        $buildings = $this->dutyService->duties($request, [
            'denunciation',
            'user_petugas',
            'user_admin',
        ])->cursorPaginate(10);

        return $this->render_json_array(BuildingOutput::class, "format", $buildings);
    }

    public function show(Request $request)
    {

    }
}
