<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Users;
use Illuminate\Http\Request;
use App\Services\ApplicationService;
use Illuminate\Support\Facades\Hash;

class UserService extends ApplicationService
{
    protected $userRepository;

    public function __construct()
    {
        $this->userRepository = new Users;
    }

    public function users(Request $request)
    {
        $users = $this->userRepository->filter($request->all());
        return $users;
    }

    public function show(string $id)
    {
        return User::find($id);
    }

    public function create($request)
    {

        $user = new User();
        $user->type_user = $request->type_user;
        $user->instansi = $request->instansi;
        $user->posisi = $request->posisi;
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();


        return $user;
    }

    public function update($id, $request)
    {
        $user = User::find(decrypt($id));
        $user->type_user = $request->type_user;
        $user->instansi = $request->instansi;
        $user->posisi = $request->posisi;
        $user->name = $request->name;

        if (!empty($request->email)) {
            $user->email = $request->email;
        }

        if (!empty($request->username)) {
            $user->username = $request->username;
        }

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        $user->tokens()->where('tokenable_id', $user->id)->delete();

        return $user;
    }

    public function delete($id)
    {
        $user = User::find(decrypt($id));
        $user->state = 'archived';
        $user->deleted_at = now();

        $user->update([
            'state' => 'archived',
        ]);

        $user->tokens()->where('tokenable_id', $user->id)->delete();

        return $user;
    }
}



