<?php

namespace App\Http\Controllers\Pelapor;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DenunciationsExport;
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

    public function export_excel(Request $request)
    {
        $request_input = $request->except(['start_date', 'end_date']);
        $denunciations = $this->denunciationService->denunciations(new Request($request_input))
            ->whereBetween('created_at', [$request->start_date, $request->end_date])->get();

        return Excel::download(
            new DenunciationsExport($denunciations),
            'pelaporan.xlsx',
            \Maatwebsite\Excel\Excel::XLSX,
            [
                'Content-Type' => 'application/xlsx',
            ]
        );
    }

    public function cluster(Request $request)
    {
        $denunciations = $this->denunciationService->denunciations($request)->get();

        $denunciationOutput = new DenunciationOutput();
        return response()->json(['data' => $denunciationOutput->renderJson($denunciations, "format", [ "mode" => "raw_many_data"])]);
    }
}
