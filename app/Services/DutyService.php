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
        $duty->start_latitude = $request->start_latitude;
        $duty->start_longitude = $request->start_longitude;
        $duty->state = "on_going";
        $duty->save();

        return $duty;
    }

    // selesai pengantaran
    public function submit(Duty $duty, $request)
    {
        $duty->update($request->except('attachments'));
        $duty->state = "done";
        $duty->tanggal_pengantaran = now();
        $duty->submit_latitude = $request->submit_latitude;
        $duty->submit_longitude = $request->submit_longitude;
        $duty->save();

        if (!empty($request->file('attachments')) && $request->hasFile('attachments')) {
            foreach ($request['attachments'] as $file) {
                $filePath = $file->store('duties', 'public');

                $duty->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
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
        $project_id = 'simpuni-banjarbaru';
        $fcm = $user->fcm_token;
        if ($fcm == null) {
            return;
        }

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
        } catch (\Throwable $e) {
            return 'Error: ' . $e->getMessage();
        }

    }
}
