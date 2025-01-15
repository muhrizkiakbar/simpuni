<?php

namespace App\Services;

use App\Models\FunctionBuilding;
use App\Repositories\FunctionBuildings;
use Illuminate\Http\Request;
use App\Services\ApplicationService;

class FunctionBuildingService extends ApplicationService
{
    protected $functionBuildingRepository;

    public function __construct()
    {
        $this->functionBuildingRepository = new FunctionBuildings;
    }

    public function function_buildings(Request $request)
    {
        $function_buildings = $this->functionBuildingRepository->filter($request->all());
        return $function_buildings;
    }

    public function show(string $id)
    {
        return FunctionBuilding::find($id);
    }

    public function create($request)
    {

        $function_building = new FunctionBuilding();
        $function_building->name = $request['name'];
        $function_building->state = $request['state'];
        $function_building->save();

        return $function_building;
    }

    public function update(FunctionBuilding $function_building, $request)
    {
        $function_building->name = $request['name'];
        $function_building->state = $request['state'];
        $function_building->save();

        return $function_building;
    }

    public function delete(FunctionBuilding $function_building)
    {
        if ($function_building->state == "active"){
            $function_building->state = "archived";
            $function_building->deleted_at = now();
        }else{
            $function_building->state = "active";
            $function_building->deleted_at = null;
        }
        $function_building->save();

        return $function_building;
    }
}


