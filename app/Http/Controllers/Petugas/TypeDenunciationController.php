<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TypeDenunciationRequest;
use App\Models\TypeDenunciation;
use App\Outputs\Admin\TypeDenunciationOutput;
use App\Services\TypeDenunciationService;
use Illuminate\Http\Request;

class TypeDenunciationController extends Controller
{
    public function __construct(
        protected TypeDenunciationService $typeDenunciationService
    ) {
    }
    //
    public function index(Request $request)
    {
        $type_denunciations = $this->typeDenunciationService->type_denunciations($request)->cursorPaginate(10);

        return $this->render_json_array(TypeDenunciationOutput::class, "format", $type_denunciations);
    }
}
