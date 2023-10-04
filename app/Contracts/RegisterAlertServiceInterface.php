<?php

namespace App\Contracts;

interface RegisterAlertServiceInterface
{
    //This method will transmit details of the new user to external service
    public function notifyRegisteredUser($user);
}