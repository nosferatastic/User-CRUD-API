<?php

namespace App\Services;

use App\Contracts\RegisterAlertServiceInterface;
use \App\Models\User;
use GuzzleHttp\Client;

class StubNotificationService implements RegisterAlertServiceInterface {

    /**
     * This function is a stub that theoretically sends a notification to an external
     * service when a user is registered
     */
    public function notifyRegisteredUser($user)
    {
        //This is just a stub pending actual implementation of external service

        /*
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://example.com/api/users', [
            'json' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'role' => $user->role
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            return true;
        } else {
            return false;
        }
        */
        return true;
    }
}