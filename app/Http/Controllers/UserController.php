<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        // Get users directly without caching paginator objects
        $users = User::with('department')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
        ], 200)->setEncodingOptions(JSON_UNESCAPED_SLASHES);
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'data' => $user,
        ], 201)->setEncodingOptions(JSON_UNESCAPED_SLASHES);
    }

    public function show($id)
    {
        $user = User::with('department')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ], 200)->setEncodingOptions(JSON_UNESCAPED_SLASHES);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validated();

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'data' => $user,
        ], 200)->setEncodingOptions(JSON_UNESCAPED_SLASHES);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->delete();

        if ($user->image && file_exists(public_path('images/'.$user->image))) {
            unlink(public_path('images/'.$user->image));
        }

        return response()->json([
            'success' => true,
            'message' => $user->username.' deleted successfully',
        ], 200);
    }
}
