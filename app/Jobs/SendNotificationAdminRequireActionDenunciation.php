<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use App\Models\Denunciation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class SendNotificationAdminRequireActionDenunciation implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Ambil semua user dengan type_user = 'admin'
        $admin_users = User::where('type_user', 'admin')->whereNotNull('fcm_token')->get();
        echo storage_path('app/json/account_google.json');
        $denunciations = Denunciation::where('updated_at', '<', Carbon::now()->subDays(14))->whereNotIn('state', ['done', 'reject'])->get();

        $laporan = 1;
        if ($denunciations->count() > 0) {
            Log::info('SendNotificationAdminRequireActionDenunciation memulai pada ' . now());
            Log::info('Banyak Laporan :'. $denunciations->count());
            foreach ($admin_users as $user) {
                foreach ($denunciations as $denunciation) {
                    $title = 'Pelaporan Perlu Tindakan';
                    $description = 'Ada laporan dengan jenis laporan '.$denunciation->type_denunciation->name.' yang perlu tindakan lanjutan.';
                    $this->send_notification($denunciation, $user, $title, $description, "denunciation_need_action");
                    $laporan = $laporan + 1;
                }
            }
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
                'sent_at' => "a",
            ], // optional
            'data' => [
                'sent_at' => "a",
                'user_id' => $user->id,
                'slug' => encrypt($data->id),
                'notification_type' => $topic
            ], // optional
        ]);

        $result = $messaging->send($message);
    }
}
