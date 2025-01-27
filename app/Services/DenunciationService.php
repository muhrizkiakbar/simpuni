<?php

namespace App\Services;

use App\Exceptions\DenunciationException;
use App\Models\Duty;
use Illuminate\Http\Request;
use App\Repositories\Denunciations;
use App\Services\ApplicationService;
use App\Models\Denunciation;
use App\Models\LogDenunciation;
use App\Models\User;
use Exception;

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
        $currentState = $denunciation->state;

        if (!empty($request->state)) {
            if ($request->state == 'done') {
                $denunciation->state = 'done';
                $denunciation->save();

                $log_denunciation = new LogDenunciation();
                $log_denunciation->denunciation_id = $denunciation->id;
                $log_denunciation->user_admin_id = $this->currentUser->id;
                $log_denunciation->current_state = $currentState;
                $log_denunciation->new_state = $denunciation->state;
                $log_denunciation->save();

                $denunciation->duties
                    ->update(['state' => 'done']);

                return $denunciation;
            }
        }

        $denunciation->state = $this->evolve_state($denunciation->state);
        $denunciation->save();

        $log_denunciation = new LogDenunciation();
        $log_denunciation->denunciation_id = $denunciation->id;
        $log_denunciation->user_admin_id = $this->currentUser->id;
        $log_denunciation->current_state = $currentState;
        $log_denunciation->new_state = $denunciation->state;
        $log_denunciation->save();

        if ($denunciation->state == 'diterima') {
            return $denunciation;
        }

        $duty = new Duty();
        $duty->denunciation = $denunciation;
        $duty->user_petugas_id = $request->user_petugas_id;
        $duty->user_admin = $this->currentUser;
        $duty->state_type = $denunciation->state;

        if (!empty($request->surat_tugas) && $request->hasFile('surat_tugas')) {
            if (!is_null($duty->surat_tugas)) {
                Storage::delete($building->surat_tugas);
            }

            $file = $request->file('surat_tugas');
            $filePath = $file->store('duties/surat_tugas', 'public');

            $duty->surat_tugas = $filePath;
            $duty->save();
        }

        $duty->save();

        return $denunciation;
    }

    public function show(string $id)
    {
        return Denunciation::find($id)->with(
            'user_pelapor',
            'type_denunciation',
            'function_building',
            'attachments',
            'log_denunciations'
        );
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
        }
    }
}
