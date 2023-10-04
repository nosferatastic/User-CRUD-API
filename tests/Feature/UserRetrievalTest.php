<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use \App\Models\User;

class UserRetrievalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the Index function returns info on all users when you are an admin
     */
    public function test_index_function_returns_users_for_admin(): void
    {
        // Create an admin user and a normal user
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$admin->api_key])->get('/api/users');

        $response->assertStatus(200);

        $response->assertJson([
            [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'phone_number' => $admin->phone_number,
                'role' => $admin->role
            ],
            [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'role' => $user->role
            ],
            [
                'id' => $user2->id,
                'name' => $user2->name,
                'email' => $user2->email,
                'phone_number' => $user2->phone_number,
                'role' => $user2->role
            ]
        ]);
    }

    /**
     * Test that a regular non-admin user cannot use the index function
     */
    public function test_index_function_returns_unauthorised_for_user(): void
    {
        // Create an admin user and a normal user
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$user->api_key])->get('/api/users');

        $response->assertStatus(401);

        $response->assertJson([
            'error' => "You do not have permission to perform this action."
        ]);
    }

    /**
     * Test that an admin user can use the GET user function to retrieve another user
     */
    public function test_get_function_returns_user_profile_for_admin(): void
    {
        // Create an admin user and a normal user
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$admin->api_key])->get('/api/user/'.$user->id);

        $response->assertStatus(200);

        $response->assertJson(
            [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'role' => $user->role
            ]
        );
    }

    /**
     * Test that a non-admin user can GET their own user profile
     */
    public function test_get_function_returns_user_profile_for_same_user(): void
    {
        // Create an admin user and a normal user
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$user->api_key])->get('/api/user/'.$user->id);

        $response->assertStatus(200);

        $response->assertJson(
            [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'role' => $user->role
            ]
        );
    }

    /**
     * Test that a non-admin user cannot GET another user profile
     */
    public function test_get_function_returns_unauthorised_for_user_getting_another_user(): void
    {
        // Create an admin user and a normal user
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$user->api_key])->get('/api/user/'.$admin->id);

        $response->assertStatus(401);

        $response->assertJson(
            [
                'error' => "You do not have permission to perform this action."
            ]
        );
    }

    /**
     * Test that trying to GET a non-existent user returns a suitable error
     */
    public function test_cannot_get_a_non_existent_user(): void
    {
        // Create an admin user and a normal user
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$admin->api_key])->get('/api/user/'.($user->id+1));

        $response->assertStatus(404);

        $response->assertJson(
            [
                'error' => "This user does not exist."
            ]
        );
    }
}
