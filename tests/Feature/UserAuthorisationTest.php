<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use \App\Models\User;

class UserAuthorisationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a successful use of the Register endpoint adds the relevant user to the users table in database
     */
    public function test_register_user_adds_to_database(): void
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);

        //Before the call, check we don't have this user
        $this->assertDatabaseMissing('users', [
            'email' => "newemail@example.com"
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$admin->api_key])->post('/api/user/register', 
        ['email' => "newemail@example.com", "password" => "Pass123!", "name" => "Phil Hart"]);

        $response->assertStatus(201);

        $response->assertJson([
            'user' => [
                'email' => 'newemail@example.com'
            ],
        ]);

        //Now we should have it in the database
        $this->assertDatabaseHas('users', [
            'email' => "newemail@example.com"
        ]);
    }

    /**
     * Test that you cannot register a new user without being logged in
     */
    public function test_must_be_logged_in_to_register_users(): void
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->post('/api/user/register', 
        ['email' => "newemail@example.com", "password" => "Pass123!", "name" => "Phil Hart"]);

        $response->assertStatus(401);

        $response->assertJson([
            'error' => 'Invalid authorisation.'
        ]);
    }

    /**
     * Test that you cannot register a new user without being admin role
     */
    public function test_must_be_admin_to_register_users(): void
    {
        // Create an admin user and regular user
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$user->api_key])->post('/api/user/register', 
        ['email' => "newemail@example.com", "password" => "Pass123!", "name" => "Phil Hart"]);

        $response->assertStatus(401);

        $response->assertJson([
            'error' => 'Unauthorised.'
        ]);
    }

    /**
     * Test that the Register endpoint rejects invalid data and returns validation assertions about that
     */
    public function test_register_user_validation_rejects_invalid_data(): void
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->withHeaders(['Authorization' => 'Bearer '.$admin->api_key, 'Accept' => 'application/json'])->post('/api/user/register', 
        ['email' => "newemailcom", "password" => "Pass123!", "role" => "superuser"]);

        $response->assertStatus(422);

        $response->assertJson([
            'errors' => [
                "email" => [
                    "The email field must be a valid email address."
                ],
                "name" => [
                    "The name field is required."
                ],
                "role" => [
                    "The selected role is invalid."
                ]
            ]
        ]);
    }

    /**
     * Test that the Login endpoint returns a successful response when using it with valid login details
     */
    public function test_can_login_as_existing_user(): void
    {
        // Create an admin user and a normal user
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders(['Accept' => 'application/json'])->post('/api/user/login', 
        ['email' => $user->email, "password" => "password"]);

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Logged in successfully.',
            'user_id' => 2,
        ]);
    }

    /**
     * Test that the Login endpoint rejects invalid login details
     */
    public function test_reject_invalid_login_details(): void
    {
        // Create a normal user
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders(['Accept' => 'application/json'])->post('/api/user/login', 
        ['email' => $user->email, "password" => "notcorrect"]);

        $response->assertStatus(401);

        $response->assertJson([
            'error' => 'Invalid login details.',
        ]);
    }

    /**
     * Test that the API key returned from Login endpoint can be used to be authorised as that user
     */
    public function test_api_key_from_login_works(): void
    {
        // Create a normal user
        $user = User::factory()->create(['role' => 'user']);

        //Perform login and confirm it was successful
        $response = $this->withHeaders(['Accept' => 'application/json'])->post('/api/user/login', 
        ['email' => $user->email, "password" => "password"]);
        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Logged in successfully.',
            'user_id' => $user->id,
        ]);

        $api_key = $response->json()['api_key'];

        //We can test by retrieving this user. If it works, it means it works as the API key is authorised to do this (and so presumably is this user)
        $response = $this->withHeaders(['Authorization' => 'Bearer '.$api_key])->get('/api/user/'.$response->json()['user_id']);

        $response->assertStatus(200);

        //Check that user returned matches
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
}
