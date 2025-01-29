<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Outputs\Admin\DenunciationOutput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\Denunciations\WarningLetterRequest;
use App\Models\Denunciation;
use App\Services\DenunciationService;
use App\Events\Duties\NewDutyEvent;

class DenunciationController extends Controller
{
    protected $denunciationService;

    public function __construct()
    {
        $this->denunciationService = new DenunciationService(Auth::user());
    }

    //
    public function index(Request $request)
    {
        $denunciations = $this->denunciationService->denunciations($request)->cursorPaginate(10);

        return $this->render_json_array(DenunciationOutput::class, "format", $denunciations);
    }

    public function show(string $id)
    {
        $denunciation = $this->denunciationService->show(decrypt($id));
        return $this->render_json(DenunciationOutput::class, "format", $denunciation);
    }

    public function update(WarningLetterRequest $request, string $id)
    {
        $denunciation = Denunciation::find(decrypt($id));
        list($denunciationn, $duty) = $this->denunciationService->warning_letter($denunciation, $request);

        if ($duty != null) {
            //broadcast(new NewDutyEvent($duty, $duty->user_petugas));
            broadcast(new NewDutyEvent($duty, $duty->user_petugas));
        }

        return $this->render_json(
            DenunciationOutput::class,
            "format",
            $denunciation->load('log_denunciations', 'attachments')
        );
    }

    public function denunciations_count(Request $request)
    {
        $denunciations_count = $this->denunciationService->denunciations($request)->count;
        return response()->json(
            [
                'params' => $request->all(),
                'result_count' => $denunciations_count,
            ]
        );
    }
}
