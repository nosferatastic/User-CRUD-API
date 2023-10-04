# University API
 A small RESTful API developed in Laravel, providing CRUD operations for User accounts, with PHPUnit tests and proposed use of the Laravel service container to manage an external service. 

This API is developed in Laravel. It involves the authentication of user accounts which can be created, read, and updated (no delete yet!). It also involves the management of two user roles, `user` and `admin`, who have differing permissions.

# Getting Started
You will first need composer installed, and to run `composer install` to install required packages, and then copy the `.env.example` file to `.env`.

The simplest way to initialise the API's database is to run the artisan commands
`php artisan migrate`
and
`php artisan db:seed`
within the project folder. This will generate an SQLite file stored in `database/database.sqlite` which is pre-populated with an admin user account (with email `testuser@testmail.com` and password `Pass123!`).
The API returns request responses in JSON format. When an API request returns an error, an error message will be sent in the JSON response.

# Running Tests
I have included a PHPUnit test suite which verifies expected behaviours. This can be run from the CLI using `php artisan test`.

# Launching

In order to launch the API locally, firstly run the above initialising commands. Then run `php artisan serve` within the project folder - you will be told when the development server starts where the local server URL is. You must have PHP (ideally 8.3 for compatibility) installed.

# Documentation

Postman documentation is accessible [by clicking here](https://documenter.getpostman.com/view/15715244/2s9YJdX3MQ). It contains information on the available API routes and possible outputs.

# Authentication
The API uses Bearer tokens for authentication, using the "api_key" value returned on successful login or registration API calls (the `/api/user/login` or `/api/user/register` endpoints).
A bearer token is required for the  `/api/user/register`, `/api/user/{user}`, `/api/users` , and `/api/user/{user}/update` endpoints as they require authorisation.
You must be authorised as a User with `admin` role to retrieve or update any User information other than your own. Similarly, only an `admin` role User can use the `/api/user/register` endpoint to create a new User account.

