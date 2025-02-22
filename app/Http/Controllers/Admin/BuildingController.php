<?php

namespace App\Http\Controllers\Admin;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BuildingsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BuildingRequest;
use App\Models\Building;
use App\Outputs\Admin\BuildingOutput;
use App\Services\BuildingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuildingController extends Controller
{
    protected $buildingService;

    public function __construct()
    {
        $this->buildingService = new BuildingService(Auth::user());
    }

    //
    public function index(Request $request)
    {
        $buildings = $this->buildingService->buildings($request, ['attachments'])->cursorPaginate(10);

        return $this->render_json_array(BuildingOutput::class, "format", $buildings);
    }

    public function store(BuildingRequest $request)
    {
        $building = $this->buildingService->create($request);
        return $this->render_json(BuildingOutput::class, "format", $building);
    }

    public function show(string $id)
    {
        $building = $this->buildingService->show(decrypt($id));
        return $this->render_json(BuildingOutput::class, "format", $building);
    }

    public function update(BuildingRequest $request, string $id)
    {
        $building = Building::find(decrypt($id));
        $building = $this->buildingService->update($building, $request);

        return $this->render_json(BuildingOutput::class, "format", $building);
    }

    public function destroy(string $id)
    {
        $building = Building::find(decrypt($id));
        $building = $this->buildingService->delete($building);

        return $this->render_json(BuildingOutput::class, "format", $building);
    }

    public function export_excel(Request $request)
    {
        $request_input = $request->except(['start_date', 'end_date']);
        $buildings = $this->buildingService->buildings(new Request($request_input))
            ->whereBetween('created_at', [$request->start_date, $request->end_date])->get();

        return Excel::download(
            new BuildingsExport($buildings),
            'bangunan.xlsx',
            \Maatwebsite\Excel\Excel::XLSX,
            [
                'Content-Type' => 'application/xlsx',
            ]
        );
    }

    public function count_building_permit(Request $request)
    {
        $buildings_count = $this->buildingService->count_building_permit($request);
        return response()->json($buildings_count);
    }

    public function cluster(Request $request)
    {
        $buildings = $this->buildingService->buildings($request)->get();

        $buildingOutput = new BuildingOutput();
        return response()->json(['data' => $buildingOutput->renderJson($buildings, "format", [ "mode" => "raw_many_data"])]);
    }
}
