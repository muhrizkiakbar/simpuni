<?php

namespace App\Services;

use App\Models\Duty;
use Illuminate\Http\Request;
use App\Repositories\Duties;
use App\Services\ApplicationService;
use App\Models\User;
use Exception;
use Google\Client as GoogleClient;
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

        $credentialsFilePath = storage_path('app/public/app/json/google-services.json');
        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->fetchAccessTokenWithAssertion();
        $token = $client->getAccessToken();

        $access_token = $token['access_token'];

        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];

        $data = [
            "message" => [
                "token" => $fcm,
                "notification" => [
                    "title" => $title,
                    "body" => $description,
                ],
            ]
        ];
        $payload = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$procject_id}/messages:send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        return $err;
    }
}
