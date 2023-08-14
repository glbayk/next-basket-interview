<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Mockery;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_create(): void
    {
        $mockedUser = User::factory()->make();
        $mockUserService = Mockery::mock(UserService::class);
        $mockUserService->shouldReceive('create')->once()->andReturn($mockedUser);

        $this->app->instance(UserService::class, $mockUserService);
        $response = $this->post('/api/users', [
            'email' => $mockedUser->email,
            'first_name' => $mockedUser->first_name,
            'last_name' => $mockedUser->last_name,
        ]);

        $response->assertJson(fn (AssertableJson $json) =>
        $json->has('data')
            ->where('data.email', $mockedUser->email)
            ->where('data.first_name', $mockedUser->first_name)
            ->where('data.last_name', $mockedUser->last_name)
            ->etc());
        $response->assertStatus(201);
    }
}
