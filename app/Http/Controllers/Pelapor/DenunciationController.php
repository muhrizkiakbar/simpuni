<?php

namespace App\Http\Controllers\Pelapor;

use App\Http\Controllers\Controller;
use App\Outputs\Admin\DenunciationOutput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\Denunciations\InputRequest;
use App\Models\Denunciation;
use App\Services\DenunciationService;

class DenunciationController extends Controller
{
    protected $denunciationService;

    public function __construct()
    {
        $this->denunciationService = new DenunciationService(Auth::user());
    }

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

    public function store(InputRequest $request)
    {
        $denunciation = $this->denunciationService->create($request);
        return $this->render_json(DenunciationOutput::class, "format", $denunciation->load('attachments'));
    }

    public function update(InputRequest $request, string $id)
    {
        $denunciation = Denunciation::find(decrypt($id));
        $denunciation = $this->denunciationService->update($denunciation, $request);

        return $this->render_json(DenunciationOutput::class, "format", $denunciation->load('attachments'));
    }

    public function count_denunciation_in_progress(Request $request)
    {
        $denunciations_count = $this->denunciationService->count_denunciation_in_progress(
            $request->merge(
                ['user_pelapor_id' => Auth::user()->id]
            )
        );
        return response()->json($denunciations_count);
    }
}
