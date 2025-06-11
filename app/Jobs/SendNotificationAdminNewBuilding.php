<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use App\Models\Building;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class SendNotificationAdminNewBuilding implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $building_id;

    /**
     * Create a new job instance.
     */
    public function __construct($building_id)
    {
        $this->building_id = $building_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('SendNotificationAdminNewBuilding memulai pada ' . now());
        // Ambil semua user dengan type_user = 'admin'
        $admin_users = User::where('type_user', 'admin')->whereNotNull('fcm_token')->get();

        //echo($this->denunciation_id);
        //// Ambil data laporan berdasarkan ID
        $building = Building::find($this->building_id);

        //// Buat instance service di dalam handle()

        $title = 'Bangunan Baru';
        $description = 'Ada bangunan baru dengan fungsi bangunan '.$building->function_building->name ?? null.' yang perlu dikonfirmasi. Mohon segera ditinjau.';

        foreach ($admin_users as $user) {
            $this->send_notification($building, $user, $title, $description, "building_new");
        }
    }

    public function send_notification($data, $user, $title, $description, $topic)
    {
        $project_id = 'simpuni-banjarbaru';
        $fcm = $user->fcm_token;
        if ($fcm == null) {
            return;
        }

        $firebase = (new Factory())->withServiceAccount(env('FIREBASE_CREDENTIAL'));

        $messaging = $firebase->createMessaging();

        $message = CloudMessage::new()
        ->toToken($fcm);

        $message = CloudMessage::fromArray([
            'token' => $fcm,
            'notification' => [
                "body2" => $description,
                "body" => $description,
                "title" => $title
            ], // optional
            'data' => [
                'user_id2' => $user->id,
                'user_id' => $user->id,
                'slug' => encrypt($data->id),
                'notification_type' => $topic
            ], // optional
        ]);

        $result = $messaging->send($message);
    }
}
