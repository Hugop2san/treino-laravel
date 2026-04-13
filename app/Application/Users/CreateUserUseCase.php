<?php

namespace App\Application\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUserUseCase
{
    /**
     * @param array{name:string,email:string} $payload
     */
    public function execute(array $payload): User
    {
        $payload['password'] = Hash::make(Str::random(16));

        return User::query()->create($payload);
    }
}
