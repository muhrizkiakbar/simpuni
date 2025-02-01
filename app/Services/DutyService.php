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
    public function start(Duty $duty)
    {
        $duty->tanggal_pengantaran = now();
        $duty->state = "on_going";
        $duty->save();

        return $duty;
    }

    // selesai pengantaran
    public function submit(Duty $duty, $request)
    {
        $duty = $duty->update($request->except('foto'));
        $duty->state = "done";
        $duty->save();

        if (!empty($request->file('foto')) && $request->hasFile('foto')) {
            $file = $request->file('foto');
            $filePath = $file->store('buildings/foto', 'public');

            $duty->foto = $filePath;
            $duty->save();
        }

        $denunciation = $duty->denunciation;
        $denunciation->upadated_at = now();
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

    public function send_notification($user, $title, $description)
    {
        $procject_id = 'simpuni-banjarbaru';
        $fcm = $user->fcm_token;

        //$credentialsFilePath = storage_path('app/public/app/json/google-services.json');

        $firebase = (new Factory())->withServiceAccount(storage_path('app/public/app/json/google-services.json'));

        $messaging = $firebase->createMessaging();

        $message = CloudMessage::fromArray([
            'notification' => [
                'title' => 'Hello from Firebase!',
                'body' => 'This is a test notification.'
            ],
            'topic' => 'global'
        ]);

        $messaging->send($message);
    }
}
