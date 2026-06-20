<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $this->authorize('viewAny', User::class);

        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        $users = User::with('department')->paginate($perPage, ['*'], 'page', $page);

        return $this->success(UserResource::collection($users), null, 200, JSON_UNESCAPED_SLASHES);
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $user = $this->userService->create($request->validated());

        return $this->created($user, null, JSON_UNESCAPED_SLASHES);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        return $this->success(new UserResource($user->load('department')), null, 200, JSON_UNESCAPED_SLASHES);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $this->userService->update($user, $request->validated());

        return $this->success($user->load('department'), null, 200, JSON_UNESCAPED_SLASHES);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $this->userService->delete($user);

        return $this->message($user->username . ' deleted successfully');
    }
}
