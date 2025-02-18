<?php

namespace App\Http\Controllers\Admin;

use App\Outputs\Admin\DutyOutput;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DutyService;
use Illuminate\Support\Facades\Auth;

class DutyController extends Controller
{
    protected $dutyService;

    public function __construct()
    {
        $this->dutyService = new DutyService(Auth::user());
    }

    public function index(Request $request)
    {
        $duties = $this->dutyService->duties($request, [
            'denunciation',
            'user_petugas',
            'user_admin',
        ])->cursorPaginate(10);

        return $this->render_json_array(DutyOutput::class, "format", $duties);
    }

    public function show(string $id)
    {
        $duty = $this->dutyService->show(decrypt($id));

        return $this->render_json(DutyOutput::class, "format", $duty);
    }
}
