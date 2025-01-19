<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Outputs\Admin\UserOutput;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    //
    public function index(Request $request) {
        $users = $this->userService->users($request)->cursorPaginate(10);
        return $this->render_json_array(UserOutput::class, "format", $users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([ 'type_user' => 'required|string|max:15',
            'instansi' => 'required|string|max:255',
            'posisi' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $user = $this->userService->create($request);

        return $this->render_json(UserOutput::class, "format", $user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        return $this->render_json(UserOutput::class, "format", User::find(decrypt($id)));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'type_user' => 'required|string|max:15',
            'instansi' => 'required|string|max:255',
            'posisi' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $user = $this->userService->update($id, $request);

        return $this->render_json(UserOutput::class, "format", $user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = $this->userService->delete($id);
        return $this->render_json(UserOutput::class, "format", $user);
    }
}
