<?php

namespace App\Commands;

use App\Models\User;

class CreateUserHandler
{
    public function __invoke(CreateUserCommand $command)
    {
        $user = new User();
        $user->email = $command->getEmail();
        $user->first_name = $command->getFirstName();
        $user->last_name = $command->getLastName();
        $user->save();

        return $user;
    }
}
