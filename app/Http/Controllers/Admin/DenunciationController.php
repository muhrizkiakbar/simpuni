<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DenunciationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return $this->render_json_array(Denun::class, "format", $denunciations);
    }

    public function store(BuildingRequest $request) {
        $building = $this->buildingService->create($request->all());
        return $this->render_json(BuildingOutput::class, "format", $building);
    }

    public function show(string $id) {
        $building = $this->buildingService->show(decrypt($id));
        return $this->render_json(BuildingOutput::class, "format", $building);
    }

    public function update(BuildingRequest $request, string $id) {
        $building = Building::find(decrypt($id));
        $building = $this->buildingService->update($building, $request->all());

        return $this->render_json(BuildingOutput::class, "format", $building);
    }

    public function destroy(string $id) {
        $building = Building::find(decrypt($id));
        $building = $this->buildingService->delete($building);

        return $this->render_json(BuildingOutput::class, "format", $building);
    }
}
