<?php

namespace App\Jobs;

use App\Models\Denunciation;
use App\Models\User;
use App\Services\DenunciationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationAdminNewDenunciation implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    protected $denunciationService;
    protected $denunciation;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public $denunciation_id
    ) {
        //
        $this->denunciationService = new DenunciationService(new User());
        $this->denunciation_id = $denunciation_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $admin_users =
            User::where('type_user', 'admin')->get();

        $denunciation = Denunciation::find($denunciation_id);

        $title = 'Tugas Baru.';
        $description = 'Ada laporan baru nih dari laporan dengan jenis laporan '.$this->denunciation->type_denunciation->name.'., semangat yaa !';
        dd($title);

        foreach ($admin_users as $user) {

            //$this->denunciationService->send_notification($user, $title, $description);
        }
    }
}
