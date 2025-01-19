<?php

namespace App\Services;

use App\Exceptions\DenunciationException;
use Illuminate\Http\Request;
use App\Repositories\Denunciations;
use App\Services\ApplicationService;
use App\Models\Denunciation;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class DenunciationService extends ApplicationService
{
    protected $denunciationRepository;
    protected $currentUser;

    public function __construct(User $user)
    {
        $this->currentUser = $user;
        $this->denunciationRepository = new Denunciations;
    }

    public function denunciations(Request $request)
    {
        $denunciations = $this->denunciationRepository->filter($request->all());
        return $denunciations;
    }

    public function create($request)
    {
        $denunciation = new Denunciation();
        $denunciation->user_pelapor_id = $this->currentUser->id;
        $denunciation->alamat = $request['alamat'];
        $denunciation->kecamatan_id = $request['kecamatan_id'];
        $denunciation->kecamatan = $request['kecamatan'];
        $denunciation->kelurahan_id = $request['kelurahan_id'];
        $denunciation->kelurahan = $request['kelurahan'];
        $denunciation->longitude = $request['longitude'];
        $denunciation->latitude = $request['latitude'];
        $denunciation->catatan = $request['catatan'];
        $denunciation->save();

        return $denunciation;
    }

    public function update(Denunciation $denunciation, $request)
    {
        try {
            if ($denunciation->state != 'sent' && $request['state'] == 'cancel') {
                throw new DenunciationException("Tidak bisa membatalkan laporan.");
            }
            $denunciation->alamat = $request['alamat'];
            $denunciation->kecamatan_id = $request['kecamatan_id'];
            $denunciation->kecamatan = $request['kecamatan'];
            $denunciation->kelurahan_id = $request['kelurahan_id'];
            $denunciation->kelurahan = $request['kelurahan'];
            $denunciation->longitude = $request['longitude'];
            $denunciation->latitude = $request['latitude'];
            $denunciation->catatan = $request['catatan'];

            $denunciation->state = $request['state'];
            $denunciation->save();

            return $denunciation;
        } catch (DenunciationException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (Exception $e) {
            throw new Exception('Something went wrong.');
        }
    }

    public function warning_letter(Denunciation $denunciation, Request $request)
    {
        $denunciation->state = $this->evolve_state($denunciation->state);
        $denunciation->save();

        return $denunciation;
    }

    protected function evolve_state($state){
        if ($state == 'sent') {
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


