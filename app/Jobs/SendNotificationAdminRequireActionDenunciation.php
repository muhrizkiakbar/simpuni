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
        $denunciations = Denunciation::where('updated_at', '<', Carbon::now()->subDays(14))->get();

        if ($denunciations->count() > 0) {
            Log::info('SendNotificationAdminRequireActionDenunciation memulai pada ' . now());
            foreach ($admin_users as $user) {
                foreach ($denunciations as $denunciation) {
                    $title = 'Ada Laporan Yang Perlu Ditindak.';
                    $description = 'Ada laporan yang perlu ditindak, laporan dengan jenis laporan '.$denunciation->type_denunciation->name.'. Semangat yaa !';
                    $this->send_notification($denunciation, $user, $title, $description, "denunciation_need_action");
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

        $json = [
            "type" => "service_account",
            "project_id" => "simpuni-banjarbaru",
            "private_key_id" => "4dfdedb44e025686e39b3c2964cb0240b60b0b9d",
            "private_key" => "-----begin private key-----\nmiievqibadanbgkqhkig9w0baqefaascbkcwggsjageaaoibaqctdzgj/zdoick6\nqn4fu3lhcfw0ipvvsncutjufj6qg5l8osdjvqgjsogdtxgsntfmqx2ziqsgtmhfj\ngywtetl6qlm5/1ztltmghzwvqhm6pvfru3quhytfncjpelgks0euunqrcaqn6+f2\ntwarwyiyzz0urdadcjpulnclx7xthsqh2w0asdppioklfzcd8e5sdi3ovzcxx6o3\nr55xoacae5jadtfn07ka92qdcyylctzj8byebfhrrmdoxctnpvahoqsmrnu0lq5k\nmb7togk1bruyoh8lsk1cnoegxowwhyqzpaqslcafp9up+zpuqnbamsoh25a0fdl5\nr5rzwiidagmbaaecggeai9b3pe/mqf3rcztx/2/asvg6a7ktndmwsevjsek+9vel\njrmrmqlcrej+tgqw7xwb6gvy+xijwjn8dtyaadglkqqhrnjn2945hpjk/hjdz7ng\nnurota/5jh3bcovubdmkxice8r7oketpog3la9fbfyl8qpdux7ae5ljlmrjpfp5u\n3u7iphzfuji+ib5ugohvymxof4g0c8m6cqbafuo9w/9a4e6nus/g4ritsojfvcni\ntu+wfrwsnbjef0e38gbg0slv3gt+vytm5oouunbufqla48j094bwocvf1vid37op\nl0fusiixowfzr7s3/bgdzexmmccwbbvdnuzz3r9xiqkbgqdsxpm0rnob6up6fu+w\nsvzdzotoex5dofpu25mb2h/keft+tflcc0a5ic2rpgbz0qpwj/7b3j0tsqj2dnaf\nklh+bwvaxzxuxjfgnrfewbk+xg88i9zhrm970d0kltxcp3yjt5xx9cklo81lulec\nnlkgckpnjw6h6oieu0xlxdjlmqkbgqdsr0l5vu2qxmtfcqb/dz/fbkto+b30i9t1\n7y4zdkhhnlcnywqamtzh1shcrqqtqy1m4cufvrmub5zl88njzjq0oiyyilpiftbp\njnkvwyu3rwieqk8fozrkucbltp/wyqlhhs+oh9l+tqkrou3hgsxgnayaxkhhauvi\naflcvo8vpqkbgqdon+qeirylymjnxxyw+ybylw0ociuqttqr+47dnqqvnfxmp054\nbtprd6ne/duadosh3jcfm/sy4ohc+gki/perikvgdqf/b7+zyv8nunr6r91wacab\nwekf49xxiahssnuvznd8sj59uo3zp+6le8q3llqkrt01q+xprzmce6epqqkbgh61\nh2scg75i+ujrksge+rkr34l88ob8cj4yhicti7slesvn8jyrajjmesvfjjlyooco\nseywepyxjdshq1appiyslmi8iqttvbtqm0im5xdq2rvdb/fvhtge5x8nvwv8ewef\nk1zi3uuxdu7xmubx3fewfm/cznbyejfjgo9jx+u9aogaubq3sx516gbtze56ijiu\nzrvaxyxcn73fmyaflra8slcr3fpzpyweuqwi0ge98vglhoryp0fjrkco7aksc7li\nm/kukljyejkxjrqfczi42fvlckog0orn1uzizcquuraezo3g2fvwutggzgjyy8wj\nm0v9jrrv7fehmdunlce1hd4=\n-----end private key-----\n",
            "client_email" => "firebase-adminsdk-fbsvc@simpuni-banjarbaru.iam.gserviceaccount.com",
            "client_id" => "103450508887842280313",
            "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
            "token_uri" => "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url" => "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-fbsvc%40simpuni-banjarbaru.iam.gserviceaccount.com",
            "universe_domain" => "googleapis.com"
        ];


        $firebase = (new Factory())->withServiceAccount($json);

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

        $result = $messaging->send($message);
    }
}
