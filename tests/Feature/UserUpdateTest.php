<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use \App\Models\User;

class UserUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_update_function_returns_success_and_updates_for_admin(): void
    {
        // Create an admin user and a normal user
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$admin->api_key])->post('/api/user/'.$admin->id.'/update', 
        ['name' => "Updated Name", 'phone_number' => "555-1010-22"]);

        $response->assertStatus(200);

        $response->assertJson([
            'user' => [
                'id' => $admin->id,
                'name' => "Updated Name",
                'email' => $admin->email,
                'phone_number' => "555-1010-22",
                'role' => $admin->role
            ]
        ]);
    }

    public function test_update_function_returns_unauthorised_for_user_updating_another_user(): void
    {
        // Create an admin user and a normal user
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$user->api_key])->post('/api/user/'.$admin->id.'/update', 
        ['name' => "Updated Name", 'phone_number' => "555-1010-22"]);

        $response->assertStatus(401);

        $response->assertJson(
            [
                'error' => "You do not have permission to perform this action."
            ]
        );
    }

    /**
     * Test that a user is allowed to update their own information (name, email used)
     */
    public function test_update_function_returns_success_and_updates_for_user_updating_same_user(): void
    {
        // Create an admin user and a normal user
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$user->api_key])->post('/api/user/'.$user->id.'/update', 
        ['name' => "Updated Name", 'email' => 'newemail@buzz.com']);

        $response->assertStatus(200);

        $response->assertJson([
            'user' => [
                'id' => $user->id,
                'name' => "Updated Name",
                'email' => 'newemail@buzz.com',
                'phone_number' => $user->phone_number,
                'role' => $user->role
            ]
        ]);
    }

    /**
     * Test that a user is not able to update their own ROLE to admin
     */
    public function test_user_cannot_update_own_role_to_admin(): void
    {
        // Create an admin user and a normal user
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$user->api_key])->post('/api/user/'.$user->id.'/update', 
        ['role' => 'admin']);

        $response->assertStatus(200);

        $response->assertJson([
            'user' => [
                'role' => 'user'
            ]
        ]);
    }

    /**
     * Test that an admin can update role
     */
    public function test_admin_can_update_role(): void
    {
        // Create an admin user and a normal user
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$admin->api_key])->post('/api/user/'.$user->id.'/update', 
        ['role' => 'admin']);

        $response->assertStatus(200);

        $response->assertJson([
            'user' => [
                'role' => 'admin'
            ]
        ]);
    }

    /**
     * A basic feature test example.
     */
    public function test_update_function_rejects_invalid_request(): void
    {
        // Create an admin user and a normal user
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$user->api_key, 'Accept' => 'application/json'])->post('/api/user/'.$user->id.'/update', 
        ['name' => "Updated Name", 'email' => 'newemail', 'role' => "superuser"]);

        $response->assertStatus(422);

        $response->assertJson([
            'errors' => [
                "email" => [
                    "The email field must be a valid email address."
                ],
                "role" => [
                    "The selected role is invalid."
                ]
            ]
        ]);
    }

    /**
     * Test that trying to update a non-existent user returns a suitable error
     */
    public function test_cannot_update_a_non_existent_user(): void
    {
        // Create an admin user and a normal user
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$admin->api_key])->post('/api/user/'.($user->id+1).'/update', 
        ['name' => "Updated Name"]);

        $response->assertStatus(404);

        $response->assertJson(
            [
                'error' => "This user does not exist."
            ]
        );
    }
}
