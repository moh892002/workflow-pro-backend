<?php

namespace Tests\Unit;

use App\Http\Resources\UserResource;
use App\Models\User;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    public function test_user_resource_includes_deleted_at_field(): void
    {
        $user = new User([
            'id' => 1,
            'fullname' => 'Jane Doe',
            'deleted_at' => null,
        ]);

        $resource = new UserResource($user);
        $data = $resource->resolve();

        $this->assertArrayHasKey('deleted_at', $data);
        $this->assertNull($data['deleted_at']);
    }
}
