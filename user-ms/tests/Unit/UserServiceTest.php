<?php

namespace Tests\Unit;

use App\CommandBus;
use App\Models\User;
use App\Services\RabbitMQService;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create(): void
    {
        $mockedUser = User::factory()->make();
        $data = ['email' => 'test@test.test', 'first_name' => 'test_first', 'last_name' => 'test_last'];
        $mockMessageBroker = Mockery::mock(RabbitMQService::class);
        $mockMessageBroker->shouldReceive('send')->once();

        $mockCommandBus = Mockery::mock(CommandBus::class);
        $mockCommandBus->shouldReceive('handle')->once()->andReturn($mockedUser);

        $this->app->instance(RabbitMQService::class, $mockMessageBroker);
        $this->app->instance(CommandBus::class, $mockCommandBus);
        $service = $this->app->make(UserService::class);
        $result = $service->create($data);

        $this->assertEquals($result['email'], $mockedUser->email);
        $this->assertEquals($result['first_name'], $mockedUser->first_name);
        $this->assertEquals($result['last_name'], $mockedUser->last_name);
    }
}
