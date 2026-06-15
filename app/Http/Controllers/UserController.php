<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        $users = User::with('department')->paginate($perPage, ['*'], 'page', $page);

        return $this->success(UserResource::collection($users), null, 200, JSON_UNESCAPED_SLASHES);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->create($request->validated());

        return $this->created($user, null, JSON_UNESCAPED_SLASHES);
    }

    public function show($id)
    {
        $user = User::with('department')->findOrFail($id);

        return $this->success(new UserResource($user), null, 200, JSON_UNESCAPED_SLASHES);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $this->userService->update($user, $request->validated());

        return $this->success($user, null, 200, JSON_UNESCAPED_SLASHES);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (! $user) {
            return $this->error('User not found', 404);
        }

        $this->userService->delete($user);

        return $this->message($user->username . ' deleted successfully');
    }
}
