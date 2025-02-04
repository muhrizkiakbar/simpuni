<?php

namespace App\Services;

use App\Models\Duty;
use Illuminate\Http\Request;
use App\Repositories\Duties;
use App\Services\ApplicationService;
use App\Models\User;
use Exception;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Support\Facades\Storage;

class DutyService extends ApplicationService
{
    protected $dutyRepository;
    protected $currentUser;

    public function __construct(User $user)
    {
        $this->currentUser = $user;
        $this->dutyRepository = new Duties();
    }

    public function duties(Request $request)
    {
        $duties = $this->dutyRepository->filter($request->all(), ["user_petugas", "denunciation", "user_admin"]);
        return $duties;
    }

    public function create($request)
    {
    }

    public function update(Duty $duty, $request)
    {
    }

    // mulai pengantaran
    public function start(Request $request, Duty $duty)
    {
        $duty->tanggal_pengantaran = now();
        $duty->start_latitude = $request->start_latitude;
        $duty->start_longitude = $request->start_longitude;
        $duty->state = "on_going";
        $duty->save();

        return $duty;
    }

    // selesai pengantaran
    public function submit(Duty $duty, $request)
    {
        $duty->update($request->except('foto'));
        $duty->state = "done";
        $duty->submit_latitude = $request->submit_latitude;
        $duty->submit_longitude = $request->submit_longitude;
        $duty->save();

        if (!empty($request->file('foto')) && $request->hasFile('foto')) {
            $file = $request->file('foto');
            $filePath = $file->store('buildings/foto', 'public');

            $duty->foto = $filePath;
            $duty->save();
        }

        $denunciation = $duty->denunciation;
        $denunciation->updated_at = now();
        $denunciation->save();

        return $duty;
    }

    public function show(string $id)
    {
        return Duty::find($id)->load(
            'denunciation',
            'user_petugas',
            'user_admin',
        );
    }

    public function delete(string $id)
    {

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
                "body" => $description,
                "title" => $title
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
