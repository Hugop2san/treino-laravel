<?php

namespace App\Application\Users;

use App\Models\User;

class UpdateUserUseCase
{
    /**
     * @param array{name:string,email:string} $payload
     */
    public function execute(User $user, array $payload): User
    {
        $user->update($payload);

        return $user->refresh();
    }
}
