<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use App\Models\Denunciation;
use App\Models\User;
use App\Services\DenunciationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class SendNotificationAdminNewDenunciation implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $denunciation_id;

    /**
     * Create a new job instance.
     */
    public function __construct($denunciation_id)
    {
        $this->denunciation_id = $denunciation_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('SendNotificationAdminNewDenunciation memulai pada ' . now());
        // Ambil semua user dengan type_user = 'admin'
        $admin_users = User::where('type_user', 'admin')->whereNotNull('fcm_token')->get();

        //echo($this->denunciation_id);
        //// Ambil data laporan berdasarkan ID
        $denunciation = Denunciation::find($this->denunciation_id);

        //// Buat instance service di dalam handle()

        $title = 'Pelaporan Baru';
        $description = 'Ada laporan baru dengan jenis laporan '.$denunciation->type_denunciation->name.'. Harap segera ditinjau.';

        foreach ($admin_users as $user) {
            $this->send_notification($denunciation, $user, $title, $description, "denunciation_new");
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
                "body" => $description,
                "title" => $title,
                "title2" => $title
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
