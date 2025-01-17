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

        $user = User::create([
            'type_user' => $request->type_user,
            'instansi' => $request->instansi,
            'posisi' => $request->posisi,
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $user;
    }

    public function update($id, $request)
    {
        $user = User::find(decrypt($id));
        $user->update([
            'type_user' => $request->type_user,
            'instansi' => $request->instansi,
            'posisi' => $request->posisi,
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();


        return $user;
    }

    public function delete($id)
    {
        $user = User::find(decrypt($id));
        $user->update([
            'state' => 'archived',
            'deleted_at'=> now()
        ]);

        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return $user;
    }
}



