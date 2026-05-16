<?php

namespace App\Modules\Users\Actions;

class DeleteUserImageAction
{
    public function handle(?string $imagePath): void
    {
        if (
            $imagePath &&
            file_exists(public_path('images/' . $imagePath))
        ) {
            unlink(public_path('images/' . $imagePath));
        }
    }
}