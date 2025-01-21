<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Outputs\Admin\DenunciationOutput;
use App\Services\DenunciationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\Denunciations\InputRequest;
use App\Http\Requests\Admin\Denunciations\WarningLetterRequest;
use App\Models\Denunciation;

class DenunciationController extends Controller
{
    protected $denunciationService;

    public function __construct()
    {
        $this->denunciationService = new DenunciationService(Auth::user());
    }

    //
    public function index(Request $request) {
        $denunciations = $this->denunciationService->denunciations($request)->cursorPaginate(10);

        return $this->render_json_array(DenunciationOutput::class, "format", $denunciations);
    }

    public function show(string $id) {
        $denunciation = $this->denunciationService->show(decrypt($id));
        return $this->render_json(DenunciationOutput::class, "format", $denunciation);
    }

    public function update(WarningLetterRequest $request, string $id) {
        $denunciation = Denunciation::find(decrypt($id));
        $denunciation = $this->denunciationService->warning_letter($denunciation, $request);

        return $this->render_json(DenunciationOutput::class, "format", $denunciation);
    }
}
