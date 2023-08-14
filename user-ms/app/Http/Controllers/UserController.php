<?php

namespace App\Http\Controllers;

use App\CommandBus;
use App\Commands\CreateUserCommand;
use App\Http\Requests\UserPostRequest;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function create(UserPostRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->create($request->all());
        } catch (Exception $e) {
            dd($e->getMessage());
            throw new BadRequestException();
        }

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], Response::HTTP_CREATED);
    }
}
