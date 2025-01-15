<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FunctionBuildingRequest;
use App\Models\FunctionBuilding;
use App\Outputs\Admin\FunctionBuildingOutput;
use App\Services\FunctionBuildingService;
use Illuminate\Http\Request;

class FunctionBuildingController extends Controller
{
    public function __construct(
        protected FunctionBuildingService $functionBuildingService
    ){}
    //
    public function index(Request $request) {
        $function_buildings = $this->functionBuildingService->function_buildings($request)->cursorPaginate(10);

        return $this->render_json_array(FunctionBuildingOutput::class, "format", $function_buildings);
    }

    public function store(FunctionBuildingRequest $request) {
        $function_building = $this->functionBuildingService->create($request->all());
        return $this->render_json(FunctionBuildingOutput::class, "format", $function_building);
    }

    public function show(string $id) {
        $function_building = $this->functionBuildingService->show(decrypt($id));
        return $this->render_json(FunctionBuildingOutput::class, "format", $function_building);
    }

    public function update(FunctionBuildingRequest $request, string $id) {
        $function_building = FunctionBuilding::find(decrypt($id));
        $function_building = $this->functionBuildingService->update($function_building, $request->all());

        return $this->render_json(FunctionBuildingOutput::class, "format", $function_building);
    }

    public function destroy(string $id) {
        $function_building = FunctionBuilding::find(decrypt($id));
        $function_building = $this->functionBuildingService->delete($function_building);

        return $this->render_json(FunctionBuildingOutput::class, "format", $function_building);
    }
}
