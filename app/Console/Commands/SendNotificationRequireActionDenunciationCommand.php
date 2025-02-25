<?php

namespace App\Console\Commands;

use App\Jobs\SendNotificationAdminRequireActionDenunciation;
use Illuminate\Console\Command;

class SendNotificationRequireActionDenunciationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-notification-require-action-denunciation-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Notification Require Action Denunciation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SendNotificationAdminRequireActionDenunciation::dispatch();
        $this->info('Job berhasil dijalankan.');
    }
}
