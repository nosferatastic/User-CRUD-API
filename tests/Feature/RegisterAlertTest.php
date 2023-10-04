<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery;

use \App\Models\User;
use \App\Contracts\RegisterAlertServiceInterface;

class RegisterAlertTest extends TestCase
{

    /**
     * Test that a successful use of the Register endpoint calls the register alert service interface's function for notifying
     */
    public function test_alert_service_called_on_registration(): void
    {
        //Create a mock object that implements the ExternalServiceInterface
        $mock = Mockery::mock(RegisterAlertServiceInterface::class);

        //We expect the notifyRegisteredUser function to get called twice as we are going to register two users
        $mock->shouldReceive('notifyRegisteredUser')
            ->twice()
            ->with(Mockery::type(User::class))
            ->andReturn(true);

        //Bind the mock object to the ExternalServiceInterface in the container so it will be called during registration.
        $this->app->instance(RegisterAlertServiceInterface::class, $mock);

        // Create an admin user to register with
        $admin = User::factory()->create(['role' => 'admin']);

        //Register two users
        $response = $this->withHeaders(['Authorization' => 'Bearer '.$admin->api_key])->post('/api/user/register', 
        ['email' => "newemail@example.com", "password" => "Pass123!", "name" => "Phil Hart"]);

        $response->assertStatus(201);

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$admin->api_key])->post('/api/user/register', 
        ['email' => "newemail1@example.com", "password" => "Pass123!", "name" => "Phil Hart"]);

        $response->assertStatus(201);

    }
}
