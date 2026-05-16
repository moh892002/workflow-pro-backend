<?php

namespace App\Modules\Users\Services;

// use App\Modules\Users\Models\User;
use App\Models\User;
use App\Modules\Users\Actions\UploadUserImageAction;
use App\Modules\Users\Actions\DeleteUserImageAction;
use Illuminate\Http\Request;

class UserService
{
    public function __construct(
        protected UploadUserImageAction $uploadImage,
        protected DeleteUserImageAction $deleteImage,
    ) {}

    public function getAllUsers()
    {
        return User::with('department')->get();
    }

    public function createUser(array $data, Request $request)
    {
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage->handle(
                $request->file('image')
            );
        }

        return User::create($data);
    }

    public function findUser($id)
    {
        return User::with('department')
            ->findOrFail($id);
    }

    public function updateUser($id, array $data, Request $request)
    {
        $user = User::findOrFail($id);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage->handle(
                $request->file('image')
            );
        }

        $user->update($data);

        return $user;
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        $username = $user->username;

        $user->delete();

        $this->deleteImage->handle($user->image);

        return "{$username} deleted successfully";
    }
}