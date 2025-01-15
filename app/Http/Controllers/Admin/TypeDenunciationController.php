<?php

namespace App\Http\Controllers\Admin;

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
    ){}
    //
    public function index(Request $request) {
        $type_denunciations = $this->typeDenunciationService->type_denunciations($request)->cursorPaginate(10);

        return $this->render_json_array(TypeDenunciationOutput::class, "format", $type_denunciations);
    }

    public function store(TypeDenunciationRequest $request) {
        $type_denunciation = $this->typeDenunciationService->create($request->all());
        return $this->render_json(TypeDenunciationOutput::class, "format", $type_denunciation);
    }

    public function show(string $id) {
        $type_denunciation = $this->typeDenunciationService->show(decrypt($id));
        return $this->render_json(TypeDenunciationOutput::class, "format", $type_denunciation);
    }

    public function update(TypeDenunciationRequest $request, string $id) {
        $type_denunciation = TypeDenunciation::find(decrypt($id));
        $type_denunciation = $this->typeDenunciationService->update($type_denunciation, $request->all());

        return $this->render_json(TypeDenunciationOutput::class, "format", $type_denunciation);
    }

    public function destroy(string $id) {
        $type_denunciation = TypeDenunciation::find(decrypt($id));
        $type_denunciation = $this->typeDenunciationService->delete($type_denunciation);

        return $this->render_json(TypeDenunciationOutput::class, "format", $type_denunciation);
    }
}
