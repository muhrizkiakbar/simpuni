<?php

namespace App\Services;

use App\Models\Building;
use App\Models\User;
use App\Repositories\Buildings;
use Illuminate\Http\Request;
use App\Services\ApplicationService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class BuildingService extends ApplicationService
{
    protected $buildingRepository;
    protected $currentUser;

    public function __construct(User $user)
    {
        $this->currentUser = $user;
        $this->buildingRepository = new Buildings;
    }

    public function buildings(Request $request)
    {
        $buildings = $this->buildingRepository->filter(
            $request->all(),
            ['function_building', 'user_admin', 'user_superadmin']
        );
        return $buildings;
    }

    public function show(string $id)
    {
        return Building::find($id);
    }

    public function create($request)
    {

        $building = new Building();
        $building->nomor_bangunan = $request["nomor_bangunan"];
        $building->created_by_user_id = $this->currentUser->id;

        $building->function_building_id = $request["function_building_id"];
        $building->name = $request["name"];
        $building->alamat = $request["alamat"];
        $building->kecamatan_id = $request["kecamatan_id"];
        $building->kecamatan = $request["kecamatan"];
        $building->kelurahan_id = $request["kelurahan_id"];
        $building->kelurahan = $request["kelurahan"];
        $building->luas_bangunan = $request["luas_bangunan"];
        $building->banyak_lantai = $request["banyak_lantai"];
        $building->ketinggian = $request["ketinggian"];
        $building->longitude = $request["longitude"];
        $building->latitude = $request["latitude"];
        $building->save();

        return $building;
    }

    public function update(Building $building, $request)
    {

        $building->nomor_bangunan = $request["nomor_bangunan"];
        $building->updated_by_user = Auth::user()->id;
        $building->function_building_id = $request["function_building_id"];

        $building->name = $request["name"];
        $building->alamat = $request["alamat"];
        $building->kecamatan_id = $request["kecamatan_id"];
        $building->kecamatan = $request["kecamatan"];
        $building->kelurahan_id = $request["kelurahan_id"];
        $building->kelurahan = $request["kelurahan"];
        $building->luas_bangunan = $request["luas_bangunan"];
        $building->banyak_lantai = $request["banyak_lantai"];
        $building->ketinggian = $request["ketinggian"];
        $building->longitude = $request["longitude"];
        $building->latitude = $request["latitude"];
        $building->save();

        return $building;
    }

    public function delete(Building $building)
    {
        if ($building->state == "active"){
            $building->state = "archived";
            $building->deleted_at = now();
        }else{
            $building->state = "active";
            $building->deleted_at = null;
        }
        $building->save();

        return $building;
    }
}


