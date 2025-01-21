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
use Illuminate\Support\Facades\Storage;
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
            ['function_building', 'updated_by_user', 'created_by_user']
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
        $building->nomor_izin_bangunan = $request["nomor_izin_bangunan"];
        $building->nomor_bangunan = $request["nomor_bangunan"];
        $building->rw = $request["rw"];
        $building->rt = $request["rt"];
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


        //komentar dlu gasan pelaporan
        //if (!empty($request->attachments) && $request->hasFile('attachments')) {
            //foreach ($request['attachments'] as $file) {
                //$filePath = $file->store('buildings','public');

                //$building->attachments()->create([
                    //'file_name' => $file->getClientOriginalName(),
                    //'file_path' => $filePath,
                    //'mime_type' => $file->getMimeType(),
                    //'size' => $file->getSize(),
                //]);
            //}
        //}

        if (!empty($request->file('foto')) && $request->hasFile('foto')) {
            $file = $request->file('foto');
            $filePath = $file->store('buildings/foto','public');

            $building->foto = $filePath;
            $building->save();
        }

        if (!empty($request->dokumen) && $request->hasFile('dokumen')) {
            $file = $request->file('dokumen');
            $filePath = $file->store('buildings/dokumen','public');

            $building->dokumen = $filePath;
            $building->save();
        }

        $building->save();

        return $building;
    }

    public function update(Building $building, $request)
    {
        $building->nomor_izin_bangunan = $request["nomor_izin_bangunan"];
        $building->rw = $request["rw"];
        $building->rt = $request["rt"];

        $building->nomor_bangunan = $request["nomor_bangunan"];
        $building->updated_by_user = $this->currentUser->id;
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


        // gasan laporan
        //$building->attachments()->where('attachable_type', 'App\Models\Building')->whereIn('attachments.id', $request["delete_attachment_ids"])->delete();

        //if (!empty($request->attachments) && $request->hasFile('attachments')) {

            //foreach ($request['attachments'] as $file) {
                //$filePath = $file->store('buildings','public');

                //$building->attachments()->create([
                    //'file_name' => $file->getClientOriginalName(),
                    //'file_path' => $filePath,
                    //'mime_type' => $file->getMimeType(),
                    //'size' => $file->getSize(),
                //]);
            //}
        //}

        if (!empty($request->foto) && $request->hasFile('foto')) {
            Storage::disk('public/buildings/foto')->delete($building->foto);

            $file = $request->file('foto');
            $filePath = $file->store('foto','buildings','public');

            $building->foto = $filePath;
            $building->save();
        }

        if (!empty($request->dokumen) && $request->hasFile('dokumen')) {
            Storage::disk('public/buildings/dokumen')->delete($building->dokumen);

            $file = $request->file('dokumen');
            $filePath = $file->store('dokumen','buildings','public');

            $building->dokumen = $filePath;
            $building->save();
        }

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


