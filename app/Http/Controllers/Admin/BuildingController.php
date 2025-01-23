<?php

namespace App\Http\Controllers\Admin;

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

        return $this->render_json(BuildingOutput::class, "format", $building->load('attachments'));
    }

    public function destroy(string $id)
    {
        $building = Building::find(decrypt($id));
        $building = $this->buildingService->delete($building);

        return $this->render_json(BuildingOutput::class, "format", $building);
    }

    public function buildings_count(Request $request)
    {
        $buildings_count = $this->buildingService->buildings($request)->count();
        return response()->json(
            [
                'params' => $request->all(),
                'result_count' => $buildings_count,
            ]
        );
    }
}
