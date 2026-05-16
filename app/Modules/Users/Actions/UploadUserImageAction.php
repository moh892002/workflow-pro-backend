<?php

namespace App\Modules\Users\Actions;

class UploadUserImageAction
{
    public function handle($image)
    {
        $userDir = public_path('images/users');

        $imageName = time() . '.' . $image->extension();

        $image->move($userDir, $imageName);

        return 'users/' . $imageName;
    }
}