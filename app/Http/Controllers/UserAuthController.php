<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

use \App\Contracts\RegisterAlertServiceInterface;
use \App\Models\User;

use Validator;

/*
 * Class contains functions that allow login/registration of user accounts
 */
class UserAuthController extends BaseController
{

    //External service that is used to send registration alerts
    protected $registerAlertService;

    /**
     * This constructor will take our registration alert service (to notify external service when user registered) and store it in the controller
     */
    public function __construct(RegisterAlertServiceInterface $registerAlertService)
    {
        $this->registerAlertService = $registerAlertService;
    }

    /**
     * Register a user and return their auth token.
     * 
     * @param Request $request : request object. Must contain email, name, password matching desired formats
     */
    public function register(Request $request): JsonResponse
    {
        //If user not admin, they cannot create a new user.
        if (! auth()->user()->can('create', User::class)) {
            return response()->json(['error' => "Unauthorised."], 401);
        }
        //User must have a name, email, password, phone number, role
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'phone_number' => 'nullable|string',
            'role' => 'nullable|in:user,admin'
        ]);
        //Create & Store User
        $user = User::create($request->all());
        $user->save();

        $this->registerAlertService->notifyRegisteredUser($user);

        //Return response with User ID & Token
        $success = [
            'message' => "User account created.",
            'user_id' => $user->id,
            'user' => $user,
            'api_key' => $user->api_key
        ];
        return response()->json($success, 201);
    }


    /**
     * Log in for an existing user and return their auth token.
     * 
     * @param Request $request : request object. Must contain email, password
     */
    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            //Return response with User ID & API Token
            $success = [
                'message' => "Logged in successfully.",
                'user_id' => $user->id,
                'api_key' => $user->api_key
            ];
            return response()->json($success, 200);
        } else {
            //Handle failed registration
            return response()->json(['error' => "Invalid login details."], 401);
        }
    }
}
