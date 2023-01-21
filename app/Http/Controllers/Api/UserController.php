<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\AuthFormRequest;
use App\Http\Requests\Api\User\ChangeEmailFormRequest;
use App\Http\Requests\Api\User\UserCreateFormRequest;

use App\Models\User;

class UserController extends Controller
{
    /**
     * Authenticate user with laravel sanctum
     */
    public function authenticate(AuthFormRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($request->device_name);

        return $token;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserCreateFormRequest $request)
    {
        $request->merge([
            'password' => bcrypt($request->password)
        ]);
        $user = User::create($request->all());

        return [
            'message' => 'User created successfully!',
            'user'    => $user
        ];
    }

    /**
     * Display the specified user.
     */
    public function me(Request $request)
    {
        return $request->user();
    }

    /**
     * Update email for the specified user
     */
    public function updateEmail(ChangeEmailFormRequest $request)
    {
         /** @var User */
         $user = $request->user();
         $user->email = $request->email;
         $user->save();

         return [
                     'message' => 'User e-mail updated successfully!',
                     'user'    => $user
                ];
    }
}
