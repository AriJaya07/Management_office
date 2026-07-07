<?php

namespace App\Actions\Auth;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterUser
{
    /**
     * Register a new user account with the default employee role.
     *
     * @param  array{name: string, email: string, password: string}  $input
     */
    public function handle(array $input): User
    {
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $user->assignRole(RoleName::Employee->value);

        return $user;
    }
}
