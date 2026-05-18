<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function index(Request $request){
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        // Get users directly without caching paginator objects
        $users = User::with('department')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
        ] , 200);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'fullname' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:ADMIN,HR_MANAGER,EMPLOYEE',
            'department_id' => 'nullable|exists:departments,id',
            'job_title' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'username' => 'required|unique:users,username',
            'salary' => 'required|integer',
        ]);

        // dd($validated);
        if($request->hasFile('image')){
            $userDir = public_path('images/users');
            $imageName = time().'.'.$request->image->extension();
            $request->image->move($userDir, $imageName);
            $validated['image'] = 'users/' . $imageName;
        }
        // Hash::make

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'data' => $user,
        ], 201);
    }

    public function show($id){
        $user = User::with('department')->findOrFail($id);
        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $user,
        ], 200);
    }

    public function update(Request $request, $id){
        $user = User::findOrFail($id);
        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $validated = $request->validate([
            'fullname' => 'sometimes|required|string',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:6',
            'role' => 'sometimes|required|in:ADMIN,HR_MANAGER,EMPLOYEE',
            'department_id' => 'nullable|exists:departments,id',
            'job_title' => 'sometimes|required|string',
            'image' => 'nullable|image|max:2048',
            'username' => 'sometimes|required|unique:users,username,' . $id,
            'salary' => 'sometimes|required|integer',
        ]);


        if($request->hasFile('image')){
            $userDir = public_path('images/users');
//            if (!file_exists($userDir)) {
//                mkdir($userDir, 0755, true);
//            }
            $imageName = time().'.'.$request->image->extension();
            $request->image->move($userDir, $imageName);
            $validated['image'] = 'users/' . $imageName;

            $oldImagePath = $user->image ?? null;
            if($oldImagePath && file_exists(public_path('images/' . $oldImagePath))){
                unlink(public_path('images/' . $oldImagePath));
            }

        }

        $user->update($validated);
        return response()->json([
            'success' => true,
            'data' => $user,
        ], 200);
    }

    public function destroy($id){
        $user = User::find($id);
        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->delete();

        if($user->image && file_exists(public_path('images/' . $user->image))){
            unlink(public_path('images/' . $user->image));
        }

        return response()->json([
            'success' => true,
            'message' => $user->username .' deleted successfully',
        ], 200);
    }
}
