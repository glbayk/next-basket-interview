<?php

namespace App\Queries;

use App\Models\User;

class UserCreateQuery
{
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getData(): array
    {
        $user = User::query()->findOrFail($this->email);

        return [
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
        ];
    }
}
