<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Requests\Admin\Duties\SubmitRequest;
use App\Outputs\Admin\DutyOutput;
use App\Http\Controllers\Controller;
use App\Services\DutyService;
use Illuminate\Http\Request;
use App\Models\Duty;
use Illuminate\Support\Facades\Auth;

class DutyController extends Controller
{
    protected $dutyService;

    public function __construct()
    {
        $this->dutyService = new DutyService(Auth::user());
    }

    //
    public function index(Request $request)
    {
        $request_input = $request->merge(
            [
                'user_petugas_id' => Auth::user()
            ]
        );
        $duties = $this->dutyService->duties($request_input, [
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

    public function start(string $id)
    {
        $duty = Duty::find(decrypt($id));
        $duty = $this->dutyService->start($duty);
        return $this->render_json(DutyOutput::class, "format", $duty);
    }

    public function submit(SubmitRequest $request, string $id)
    {
        $duty = Duty::find(decrypt($id));
        $duty = $this->dutyService->submit($duty, $request);
        return $this->render_json(DutyOutput::class, "format", $duty);
    }
}
