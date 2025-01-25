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
    ) {
    }
    //
    public function index(Request $request)
    {
        $function_buildings = $this->functionBuildingService->function_buildings($request)->cursorPaginate(10);

        return $this->render_json_array(FunctionBuildingOutput::class, "format", $function_buildings);
    }
}
