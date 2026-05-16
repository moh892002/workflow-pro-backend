<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Users\Requests\StoreUserRequest;
use App\Modules\Users\Requests\UpdateUserRequest;
use App\Modules\Users\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->userService->getAllUsers(),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser(
            $request->validated(),
            $request
        );

        return response()->json([
            'success' => true,
            'data' => $user,
        ], 201);
    }

    public function show($id)
    {
        $user = $this->userService->findUser($id);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = $this->userService->updateUser(
            $id,
            $request->validated(),
            $request
        );

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    public function destroy($id)
    {
        $message = $this->userService->deleteUser($id);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }
}