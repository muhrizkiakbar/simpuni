<?php

namespace App\Services;

use App\Models\TypeDenunciation;
use App\Repositories\TypeDenunciations;
use Illuminate\Http\Request;
use App\Services\ApplicationService;

class TypeDenunciationService extends ApplicationService
{
    protected $typeDenunciationRepository;

    public function __construct()
    {
        $this->typeDenunciationRepository = new TypeDenunciations;
    }

    public function type_denunciations(Request $request)
    {
        $type_denunciations = $this->typeDenunciationRepository->filter($request->all());
        return $type_denunciations;
    }

    public function show(string $id)
    {
        return TypeDenunciation::find($id);
    }

    public function create($request)
    {

        $type_denunciation = new TypeDenunciation();
        $type_denunciation->name = $request['name'];
        $type_denunciation->state = $request['state'];
        $type_denunciation->save();

        return $type_denunciation;
    }

    public function update(TypeDenunciation $type_denunciation, $request)
    {
        $type_denunciation->name = $request['name'];
        $type_denunciation->state = $request['state'];
        $type_denunciation->save();

        return $type_denunciation;
    }

    public function delete(TypeDenunciation $type_denunciation)
    {
        if ($type_denunciation->state == "active"){
            $type_denunciation->state = "archived";
            $type_denunciation->deleted_at = now();
        }else{
            $type_denunciation->state = "active";
            $type_denunciation->deleted_at = null;
        }
        $type_denunciation->save();

        return $type_denunciation;
    }
}


