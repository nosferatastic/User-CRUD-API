<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

use \App\Models\User;

use Validator;

/*
 * Class contains functions that allow retrieval and update of user accounts
 */
class UserController extends BaseController
{

    /*
     * Endpoint to retrieve a user. Must be logged in. If not admin, can only view own info
     * 
     * @param User $user: User to be returned (retrieved via Route Model binding)
     */
    public function get(User $user): JsonResponse
    {
        if ($user) {
            //Is the user authorised? 
            //Must be either the same as user requested, or an admin. See UserPolicy
            if (auth()->user()->can('view',$user)) {
                return response()->json($user, 200);
            } else {
                //If not, return unauthorised
                return response()->json(['error' => 'You do not have permission to perform this action.'], 401);
            }
        } else {
            //User does not exist
            return response()->json(['error' => 'Invalid request.'], 404);
        }
    }

    /**
     * Endpoint to retrieve index of users
     */
    public function index(): JsonResponse
    {
        //If the user is an admin, they can get all users. See UserPolicy
            if (auth()->user()->can('viewAny',User::class)) {
            //Return all users
            return response()->json(User::all(), 200);
        } else {
            //If not an admin, they're not authorised to use this
            return response()->json(['error' => 'You do not have permission to perform this action.'], 401);
        }
    }

    /**
     * Endpoint to update existing user
     * 
     * @param User $user: User to be updated (retrieved via Route Model binding)
     */
    public function update(Request $request, User $user): JsonResponse
    {
        //If the user is authorised to update, proceed. See UserPolicy
        if (auth()->user()->can('update',$user)) {
            //Validate the request
            $request->validate([
                'name' => 'sometimes|string',
                'email' => 'sometimes|email|unique:users,email,' . $user->id,
                'password' => 'sometimes|string',
                'phone_number' => 'sometimes|string',
                'role' => 'sometimes|in:user,admin'
            ]);

            //If a regular user, they are not allowed to update their own role so we extract request body here & remove role if not admin
            $requestData = $request->all();
            if($user->role != "admin") {
                unset($requestData->role);
            }

            //If successful so far, update user
            $user->update($requestData);

            //Return 
            return response()->json(['message' => "Successfully updated.", 'user' => $user], 200);
        } else {
            //If not an admin and not the same as the user being updated, they're not authorised to use this
            return response()->json(['error' => 'You do not have permission to perform this action.'], 401);
        }
    }
}
