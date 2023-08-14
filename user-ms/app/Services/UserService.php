<?php

namespace App\Services;

use App\CommandBus;
use App\Commands\CreateUserCommand;
use App\Models\User;

class UserService
{
    private CommandBus $commandBus;
    private RabbitMQService $messageBroker;

    public function __construct(RabbitMQService $messageBroker, CommandBus $commandBus)
    {
        $this->messageBroker = $messageBroker;
        $this->commandBus = $commandBus;
    }

    public function create(array $data): User
    {
        $command = new CreateUserCommand($data['email'], $data['first_name'], $data['last_name']);
        $user = $this->commandBus->handle($command);
        // $user = User::create($data);
        $this->messageBroker->send($user->toJSON());

        return $user;
    }
}
