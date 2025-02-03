<?php

namespace App\Services;

use App\Exceptions\DenunciationException;
use App\Jobs\SendNotificationAdminNewDenunciation;
use Illuminate\Support\Facades\Storage;
use App\Models\Duty;
use Illuminate\Http\Request;
use App\Repositories\Denunciations;
use App\Services\ApplicationService;
use App\Models\Denunciation;
use App\Models\LogDenunciation;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class DenunciationService extends ApplicationService
{
    protected $denunciationRepository;
    protected $currentUser;

    public function __construct(User $user)
    {
        $this->currentUser = $user;
        $this->denunciationRepository = new Denunciations();
    }

    public function denunciations(Request $request)
    {
        $denunciations = $this->denunciationRepository->filter($request->all(), ["attachments", "function_building", "type_denunciation", "user_pelapor"]);
        return $denunciations;
    }

    public function create($request)
    {
        $request_input = $request->merge(
            [
                'user_pelapor_id' => $this->currentUser->id,
                'state' => 'sent'
            ]
        );
        $request_input = $request->except('attachments');

        $denunciation = new Denunciation();
        $denunciation = $denunciation->create(
            $request_input
        );

        if (!empty($request->attachments) && $request->hasFile('attachments')) {
            foreach ($request['attachments'] as $file) {
                $filePath = $file->store('denunciations', 'public');

                $denunciation->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        SendNotificationAdminNewDenunciation::dispatch($denunciation->id);

        return $denunciation;
    }

    public function update(Denunciation $denunciation, $request)
    {
        try {
            if ($denunciation->state != 'sent' && $request['state'] == 'cancel') {
                throw new DenunciationException("Tidak bisa membatalkan laporan.");
            }

            $request_input = $request->merge(
                [
                    'user_pelapor_id' => $this->currentUser->id,
                ]
            );
            $request_input = $request->except('attachments');


            $denunciation->update(
                $request_input
            );


            if (!empty($request->delete_attachment_ids)) {
                $denunciation->attachments()->where('attachable_type', 'App\Models\Denunciation')->whereIn('attachments.id', $request->delete_attachment_ids)->delete();
            }

            if (!empty($request->attachments) && $request->hasFile('attachments')) {

                foreach ($request['attachments'] as $file) {
                    $filePath = $file->store('denunciations', 'public');

                    $denunciation->attachments()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ]);
                }
            }

            return $denunciation;
        } catch (DenunciationException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (Exception $e) {
            throw new Exception('Something went wrong.');
        }
    }

    public function warning_letter(Denunciation $denunciation, $request)
    {
        if ($denunciation->state == "done" || $denunciation->state == 'reject') {
            return [$denunciation, null];
        }

        $currentState = $denunciation->state;

        if (!empty($request->state)) {
            if ($request->state == 'done' && $denunciation->state != 'sent') {
                $denunciation->state = 'done';
                $denunciation->save();

                $title = "Laporan Selesai.";
                $description = "Laporan telah selesai, dengan jenis laporan ". $denunciation->type_denunciation->name.".";
                $this->send_notification($denunciation, $denunciation->user_pelapor_id, $title, $description, "denunciation_done");

                $log_denunciation = $this->create_log_denunciation($denunciation, $currentState);

                $denunciation->duties
                    ->update(['state' => 'done']);

                return [$denunciation, null];
            } elseif ($request->state == 'reject' && $denunciation->state == 'sent') {
                $denunciation->state = 'reject';
                $denunciation->save();

                $title = "Laporan Ditolak.";
                $description = "Laporan telah ditolak, dengan jenis laporan ". $denunciation->type_denunciation->name.".";
                $this->send_notification($denunciation, $denunciation->user_pelapor_id, $title, $description, "denunciation_reject");


                $log_denunciation = $this->create_log_denunciation($denunciation, $currentState);

                return [$denunciation, null];
            } else {
                return [$denunciation, null];
            }
        }

        $denunciation->state = $this->evolve_state($denunciation->state);
        $denunciation->save();

        $log_denunciation = $this->create_log_denunciation($denunciation, $currentState);

        if ($denunciation->state == 'diterima') {
            return [$denunciation, null];
        }

        $duty = $this->create_duty($denunciation, $request);

        return [$denunciation, $duty];
    }

    public function show(string $id)
    {
        return Denunciation::find($id)->load(
            'user_pelapor',
            'type_denunciation',
            'function_building',
            'attachments',
            'log_denunciations',
            'duties.user_petugas',
            'duties.user_petugas'
        );
    }

    //API count pelaporan baru dan count pelaporan dalam proses (gabung)
    public function count_by_new_and_in_progress(Request $request)
    {
        $results = Denunciation::select(
            DB::raw("SUM(CASE WHEN state IN ('diterima', 'teguran_lisan', 'sp1','sp2', 'sp3', 'sk_bongkar') THEN 1 ELSE 0 END) as in_progress"),
            DB::raw("SUM(CASE WHEN state = 'sent' THEN 1 ELSE 0 END) as sent"),
        )
        ->get();

        return $results;
    }

    //API count index pelaporan per bulan berdasarkan status terakhir
    public function count_every_state_by_month_year(Request $request)
    {
        $results = Denunciation::select(
            DB::raw("SUM(CASE WHEN state = 'sent' THEN 1 ELSE 0 END) as sent"),
            DB::raw("SUM(CASE WHEN state = 'reject' THEN 1 ELSE 0 END) as reject"),
            DB::raw("SUM(CASE WHEN state = 'diterima' THEN 1 ELSE 0 END) as diterima"),
            DB::raw("SUM(CASE WHEN state = 'teguran_lisan' THEN 1 ELSE 0 END) as teguran_lisan"),
            DB::raw("SUM(CASE WHEN state = 'sp1' THEN 1 ELSE 0 END) as sp1"),
            DB::raw("SUM(CASE WHEN state = 'sp2' THEN 1 ELSE 0 END) as sp2"),
            DB::raw("SUM(CASE WHEN state = 'sp3' THEN 1 ELSE 0 END) as sp3"),
            DB::raw("SUM(CASE WHEN state = 'sk_bongkar' THEN 1 ELSE 0 END) as sk_bongkar"),
            DB::raw("SUM(CASE WHEN state = 'done' THEN 1 ELSE 0 END) as done"),
        )
        ->whereMonth('created_at', $request->month)
        ->whereYear('created_at', $request->year)
        ->get();

        return $results;
    }

    //API count statistik pelaporan pertahun setiap bulan
    public function count_done_by_year(Request $request)
    {

        $results = Denunciation::select(
            DB::raw("DATE_FORMAT(created_at, '%m') as month"),
            DB::raw("SUM(CASE WHEN state = 'done' THEN 1 ELSE 0 END) as done"),
        )
        ->whereYear('created_at', $request->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return $results;
    }

    public function count_denunciation_in_progress(Request $request)
    {
        $results = Denunciation::select(
            DB::raw('COUNT(*) AS total_rows'),
            DB::raw("SUM(CASE WHEN state IN ('diterima', 'teguran_lisan', 'sp1','sp2', 'sp3', 'sk_bongkar') THEN 1 ELSE 0 END) as in_progress"),
        )
            ->where('user_pelapor_id', $request->user_pelapor_id)
            ->get();

        return $results;
    }

    protected function evolve_state($state)
    {
        if ($state == 'sent') {
            return 'diterima';
        } elseif ($state == 'diterima') {
            return 'teguran_lisan';
        } elseif ($state == 'teguran_lisan') {
            return 'sp1';
        } elseif ($state == 'sp1') {
            return 'sp2';
        } elseif ($state == 'sp2') {
            return 'sp3';
        } elseif ($state == 'sp3') {
            return 'sk_bongkar';
        } elseif ($state == 'sk_bongkar') {
            return 'done';
        } elseif ($state == 'done') {
            return 'done';
        }
    }

    protected function create_log_denunciation($denunciation, $currentState)
    {
        $log_denunciation = new LogDenunciation();
        $log_denunciation->denunciation_id = $denunciation->id;
        $log_denunciation->user_admin_id = $this->currentUser->id;
        $log_denunciation->current_state = $currentState;
        $log_denunciation->new_state = $denunciation->state;
        $log_denunciation->save();

        return $log_denunciation;
    }

    protected function create_duty($denunciation, $request)
    {
        $duty = new Duty();
        $duty->denunciation_id = $denunciation->id;
        $duty->user_petugas_id = $request->user_petugas_id;
        $duty->user_admin_id = $this->currentUser->id;
        $duty->state_type = $denunciation->state;

        if (!empty($request->surat_tugas) && $request->hasFile('surat_tugas')) {
            if (!is_null($duty->surat_tugas)) {
                Storage::delete($duty->surat_tugas);
            }

            $file = $request->file('surat_tugas');
            $filePath = $file->store('duties/surat_tugas', 'public');

            $duty->surat_tugas = $filePath;
            $duty->save();
        }

        $duty_service = new DutyService(new User());
        $title = "Tugas Baru.";
        $description = "Tugas baru dengan jenis peringatan ". str_replace('_', ' ', strtoupper($duty->state_type)).".";
        $this->send_notification($denunciation, $denunciation->user_pelapor_id, $title, $description, "assignment_new");


        $duty->save();

        return $duty;
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
                "body" => "coba",
                "title" => "masuk"
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
