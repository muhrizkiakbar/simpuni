<?php

namespace App\Http\Controllers\Admin;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DenunciationsExport;
use App\Http\Controllers\Controller;
use App\Outputs\Admin\DenunciationOutput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\Denunciations\WarningLetterRequest;
use App\Models\Denunciation;
use App\Services\DenunciationService;
use App\Events\Duties\NewDutyEvent;
use App\Services\DutyService;

class DenunciationController extends Controller
{
    protected $denunciationService;
    protected $dutyService;

    public function __construct()
    {
        $this->denunciationService = new DenunciationService(Auth::user());
        $this->dutyService = new DutyService(Auth::user());
    }

    //
    public function index(Request $request)
    {
        $denunciations = $this->denunciationService->denunciations($request)->orderBy('updated_at', 'asc')->cursorPaginate(10);

        return $this->render_json_array(DenunciationOutput::class, "format", $denunciations);
    }

    public function show(string $id)
    {
        $denunciation = $this->denunciationService->show(decrypt($id));
        return $this->render_json(DenunciationOutput::class, "detail_format", $denunciation);
    }

    public function update(WarningLetterRequest $request, string $id)
    {
        $denunciation = Denunciation::find(decrypt($id));
        list($denunciation, $duty) = $this->denunciationService->warning_letter($denunciation, $request);

        return $this->render_json(
            DenunciationOutput::class,
            "detail_format",
            $denunciation->load('log_denunciations', 'attachments')
        );
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

    //API count pelaporan baru dan count pelaporan dalam proses (gabung)
    public function count_by_new_and_in_progress(Request $request)
    {
        $denunciations_count = $this->denunciationService->count_by_new_and_in_progress($request);
        return response()->json($denunciations_count);
    }

    //API count index pelaporan per bulan berdasarkan status terakhir
    public function count_every_state_by_month_year(Request $request)
    {
        $denunciations_count = $this->denunciationService->count_every_state_by_month_year($request);
        return response()->json($denunciations_count);
    }
    //
    //API count statistik pelaporan pertahun setiap bulan
    public function count_done_by_year(Request $request)
    {
        $denunciations_count = $this->denunciationService->count_done_by_year($request);
        return response()->json($denunciations_count);
    }

    public function cluster(Request $request)
    {
        $denunciations = $this->denunciationService->denunciations($request)->get();

        $denunciationOutput = new DenunciationOutput();
        return response()->json(['data' => $denunciationOutput->renderJson($denunciations, "format", [ "mode" => "raw_many_data"])]);
    }
}
