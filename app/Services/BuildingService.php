<?php

namespace App\Services;

use App\Jobs\SendNotificationAdminNewBuilding;
use App\Models\Building;
use App\Models\User;
use App\Repositories\Buildings;
use Illuminate\Http\Request;
use App\Services\ApplicationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class BuildingService extends ApplicationService
{
    protected $buildingRepository;
    protected $currentUser;

    public function __construct(User $user)
    {
        $this->currentUser = $user;
        $this->buildingRepository = new Buildings();
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

        if ($this->currentUser->type_user == 'konsultan') {
            $building->state = 'waiting';
            $building->save();

            SendNotificationAdminNewBuilding::dispatch($building->id);
        }


        if (!empty($request->file('foto')) && $request->hasFile('foto')) {
            $file = $request->file('foto');
            $filePath = $file->store('buildings/foto', 'public');

            $building->foto = $filePath;
            $building->save();
        }

        if (!empty($request->dokumen) && $request->hasFile('dokumen')) {
            $file = $request->file('dokumen');
            $filePath = $file->store('buildings/dokumen', 'public');

            $building->dokumen = $filePath;
            $building->save();
        }

        $building->save();

        return $building;
    }

    public function update(Building $building, $request)
    {

        $building->update(
            $request->except(['foto', 'dokumen'])
        );

        $building->updated_by_user_id = $this->currentUser->id;

        if ($this->currentUser->type_user == 'konsultan') {
            $building->state = 'waiting';
        }

        $building->save();


        if (!empty($request->foto) && $request->hasFile('foto')) {
            if (!is_null($building->foto)) {
                Storage::delete($building->foto);
            }

            $file = $request->file('foto');
            $filePath = $file->store('buildings/foto', 'public');

            $building->foto = $filePath;
            $building->save();
        }

        if (!empty($request->dokumen) && $request->hasFile('dokumen')) {
            if (!is_null($building->dokumen)) {
                Storage::delete($building->dokumen);
            }

            $file = $request->file('dokumen');
            $filePath = $file->store('buildings/dokumen', 'public');

            $building->dokumen = $filePath;
            $building->save();
        }


        if ($building->state == "active" && $building->created_by_user->type_user == 'konsultan') {
            $title = "Bangunan Diaktifkan.";
            $description = "Bangunan dengan fungsi bangunan ".$building->function_building->name.".";
            $this->send_notification($building, $building->created_by_user_id, $title, $description, "building_confirmation");
        }
        return $building;
    }

    public function delete(Building $building)
    {
        if ($building->state == "active") {
            $building->state = "archived";
            $building->deleted_at = now();
        } else {
            $building->state = "active";
            $building->deleted_at = null;
        }
        $building->save();

        return $building;
    }

    public function count_building_permit(Request $request)
    {
        $results = Building::select(
            DB::raw('COUNT(*) AS total_rows'),
            DB::raw('COUNT(nomor_izin_bangunan) AS berizin'),
            DB::raw('COUNT(*) - COUNT(nomor_izin_bangunan) AS tidak_berizin')
        )
        ->first();

        return $results;
    }

    public function send_notification($data, $user, $title, $description, $topic)
    {
        $procject_id = 'simpuni-banjarbaru';
        $fcm = $user->fcm_token;

        $firebase = (new Factory())->withServiceAccount(storage_path('app/json/account_google.json'));

        $messaging = $firebase->createMessaging();

        $message = CloudMessage::new()
        ->toToken($fcm);

        $message = CloudMessage::fromArray([
            'token' => $fcm,
            'notification' => [
                "body" => "coba",
                "title" => "masuk"
            ], // optional
            'data' => [
                'user_id' => $user->id,
                'slug' => encrypt($data->id),
                'notification_type' => $topic
            ], // optional
        ]);

        try {
            $result = $messaging->send($message);
            echo 'Notification sent successfully!';
        } catch (\Throwable $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
